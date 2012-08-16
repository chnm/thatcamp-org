<?php
/*
Plugin Name: Cite This
Plugin URI: http://trac-hg.assembla.com/llbbsc/wiki/wpCT
Description: Citations provider for WordPress
Version: 0.3.1
Author: Yu-Jie Lin
Author URI: http://www.livibetter.com/
*/
/*
 * Copyright 2007, 2008 Yu-Jie Lin
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 3 of the License, or (at your option)
 * any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc., 51
 * Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 *
 * Author       : Yu-Jie Lin
 * Email        : lb08 AT livibetter DOT com
 * Website      : http://www.livibetter.com
 * Creation Date: 7/12/2007 12:46:46 UTC+8
 */

require_once('libCiteThis.php');
require_once('OptionsPage.php');

function GetCitationsBlockStaticHTML($post, $styles) {
    $details = GetDetails($post);
    $citations = GetCitations($details, $styles);
    return '<div id="citations-' . $post->ID . '" class="citations">' . GetCitationsHTML($citations) . '</div>';
    }

function GetCitationsBlockNonStaticHTML($post, $isDynamic, $hasPopup) {
    $html = '<div id="citations-' . $post->ID . '" class="citations">';
    if ($isDynamic) // Append dynamic method
        $html .= '<a class="citation-manual-dynamic" onclick="ManualLoad(' . $post->ID . ')">Cite this...</a>';
    else // Append manual load link
        $html .= '<a class="citation-manual" href="' . get_permalink_citations($post) . '" rel="nofollow">Cite this...</a>';

    if ($hasPopup) // Append popup link
        $html .= ' <a class="citation-new-window" href="' . get_permalink_citations_new_window($post) . '" rel="nofollow">(new window)</a>';

    $html .= '</div>';
    return $html;
    }

function ProcessCitations() {
	global $wp, $wp_query;

	$gOptions = get_option('CTgeneralOptions');

	$isSingle = is_single();
    foreach($wp_query->posts as $post) {
        // set options
        $mode    = ($isSingle)?$gOptions['singleMode']             :$gOptions['loopMode'];
        $dynamic = ($isSingle)?$gOptions['singleModeManualDynamic']:$gOptions['loopModeManualDynamic'];
        $popup   = ($isSingle)?$gOptions['singleModePopup']        :$gOptions['loopModePopup'];
        $styles = null;

        $postMeta = get_post_meta($post->ID, 'CT', true); // Only first 'CT' affects
        if ($postMeta!='') {
            if ($postMeta=='disable') continue; // CT is disabled by empty post meta 'CT'
            parse_str($postMeta);//, $options);

            $dynamic = $dynamic || $dynamic=='true';
            $popup = $popup || $popup=='true';
            if ($styles!==null) $styles = explode(',', $styles);
            }

        if ($mode=='disable') continue;

        if ($isSingle && $wp->is_citation === true) // This is caused by permalink/citations/
            $post->post_content = GetCitationsBlockStaticHTML($post, $styles);
        else {
            if ($mode=='auto') // Append citations after post content
                $html = GetCitationsBlockStaticHTML($post, $styles);
            elseif ($mode=='manual')
                $html = GetCitationsBlockNonStaticHTML($post, $dynamic, $popup);
            else
                continue;

            if ($isSingle || strpos($post->post_content, '<!--more-->')===false)
                $post->post_content .= "\n" . $html; // Append citations after post content
            else // Insert citations before more tag.
                $post->post_content = str_replace('<!--more-->', "\n".$html.'<!--more-->', $post->post_content);
            }
        }
    setup_postdata($wp_query->posts[0]); // Reset first post.
	}

/* Widget
=========================================================================== */

function CTWidget($args) {
    if (!is_single()&&!is_page()) return;
    global $wp;
    if ($wp->is_citation) return;

    $gOptions = get_option('CTgeneralOptions');
    if ($gOptions['singleMode']=='auto') return;

    extract($args);
    global $post;

    echo $before_widget;
    echo $before_title . 'Cite this...' . $after_title;
    if ($gOptions['widgetModeManualDynamic']) {
        // Append JS...
        $html .= '<a class="citation-manual-dynamic" onclick="ManualLoad(' . $post->ID . ')">Get citation text</a>';
        }
    else {
        // Append manual load link
        $html .= '<a class="citation-manual" href="' . get_permalink_citations($post) . '">Get citation text</a>';
        }
    if ($gOptions['widgetModePopup']) {
        // Append popup link
        $html .= ' <a class="citation-new-window" href="' . get_permalink_citations_new_window($post) . '" rel="nofollow">(new window)</a>';
        }
    echo $html;
    echo $after_widget;
    }

add_action('plugins_loaded', 'CTWidgetRegister');
function CTWidgetRegister() {
    if ( function_exists('register_sidebar_widget') )
        register_sidebar_widget('Cite This', 'CTWidget');
    }

/*
    Adds rewrite rules to make the following patterns are valid in WP.
    Wanted patterns:
        /citations
        /citations/
*/
add_action('generate_rewrite_rules', 'AddCTRewriteRules');
function AddCTRewriteRules($wp_rewrite) {
	$newRules = array('(.*)/citations(.*)$' => $wp_rewrite->preg_index(1) . '/');
	$wp_rewrite->rules = $newRules + $wp_rewrite->rules;
	}

/*
    Intercepts after 'parse_request' executed.
    If the matched_rule belongs to Cite This', then decide the citations
    providing mode. And hook loop_start for replacing with or appending
    citations.
*/
add_action('parse_request', 'CTIntercept');
function CTIntercept($wp) {
    // Hook loop_start
    add_action('loop_start', 'ProcessCitations');
    if (strpos($_SERVER['REQUEST_URI'], '&citations')) {
        if ($_GET['citations'] == 'new')
            $wp->is_citation_new = true;
        elseif ($_GET['citations'] == 'partial')
            $wp->is_citation_partial = true;
        else
  	        $wp->is_citation = true;
  	    return;
        }

	if ($wp->matched_rule=='(.*)/citations(.*)$') {
		// Decide the providing mode
		if (preg_match('/(.*)\/citations\/new\/?$/', $_SERVER['REQUEST_URI'])) {
    		$wp->is_citation_new = true;
		    }
		if (preg_match('/(.*)\/citations\/?$/', $_SERVER['REQUEST_URI'])) {
		    // Decide by options
    		$wp->is_citation = true;
            }
		// Do parse_request again
		$user_home = @parse_url(get_option('home'));
		$_SERVER['REQUEST_URI'] = $user_home['path'] . $wp->matched_query;
		$wp->parse_request($wp->extra_query_vars);
		}
	}

add_action('wp', 'CTInterceptNew');
function CTInterceptNew($wp) {
    if (isset($wp->is_citation_new)) {
        $_GET['mode'] = 'full';
        require('Dynamic.php');
        die('');
        }
    if (isset($wp->is_citation_partial)) {
        require('Dynamic.php');
        die('');
        }
    }

/* Admin
=========================================================================== */

function GetDefaultCitationStyles() {
    $citations = array();
    $citations['apa']['name'] = 'APA style';
    $citations['apa']['style'] = '%pagename%. (%date:Y, F j%). In <i>%publisher%</i>. Retrieved %retdate:H:s, F j, Y%, from <a href="%permalink%">%permalink%</a>';
    $citations['apa']['styleURI'] = 'http://en.wikipedia.org/wiki/APA_style';
    $citations['apa']['show'] = false;

    $citations['mla']['name'] = 'MLA style';
    $citations['mla']['style'] = '%author%, "%pagename%." <i>%publisher%</i>. %date:j M Y%. %institution%. Accessed %retdate:j M Y% &lt;<a href="%permalink%">%permalink%</a>&gt;.';
    $citations['mla']['styleURI'] = 'http://en.wikipedia.org/wiki/The_MLA_style_manual';
    $citations['mla']['show'] = true;

    $citations['mhra']['name'] = 'MHRA style';
    $citations['mhra']['style'] = '%author%, &#39;%pagename%&#39;, <i>%publisher%</i>, %date:j F Y, H:s% UTC, &lt;<a href="%permalink%">%permalink%</a>&gt; [accessed %retdate:j F Y%]';
    $citations['mhra']['styleURI'] = 'http://en.wikipedia.org/wiki/MHRA_Style_Guide';
    $citations['mhra']['show'] = false;

    $citations['chicago']['name'] = 'Chicago style';
    $citations['chicago']['style'] = '%author%, "%pagename%." <i>%publisher%</i>, <a href="%permalink%">%permalink%</a> [accessed %retdate:F j, Y%].';
    $citations['chicago']['styleURI'] = 'http://www.chicagomanualofstyle.org/';
    $citations['chicago']['show'] = true;

    $citations['cbecse']['name'] = 'CBE/CSE style';
    $citations['cbecse']['style'] = '%author%, %pagename% [Internet]. %publisher%; %date: Y F j, H:s% UTC [cited %retdate: Y M j%]. Available from: <a href="%permalink%">%permalink%</a>.';
    $citations['cbecse']['styleURI'] = 'http://en.wikipedia.org/wiki/Council_of_Science_Editors';
    $citations['cbecse']['show'] = false;

    $citations['bluebook']['name'] = 'Bluebook style';
    $citations['bluebook']['style'] = '%pagename%, <a href="%permalink%">%permalink%</a> (last visited %retdate:M. j, Y%).';
    $citations['bluebook']['styleURI'] = 'http://en.wikipedia.org/wiki/Bluebook';
    $citations['bluebook']['show'] = false;

    $citations['ama']['name'] = 'AMA style';
    $citations['ama']['style'] = '%author%, %pagename%. %publisher%. %date:F j, Y, H:s% UTC. Available at: <a href="%permalink%">%permalink%</a>. Accessed %retdate:F j, Y%.';
    $citations['ama']['styleURI'] = 'http://en.wikipedia.org/wiki/American_Medical_Association';
    $citations['ama']['show'] = false;
    return $citations;
    }

function GetDefaultGeneralOptions() {
    $gOptions = array();
    $gOptions['institution'] = '';
    $gOptions['singleMode'] = 'auto';
    $gOptions['singleModeManualDynamic'] = true;
    $gOptions['singleModePopup'] = true;
    $gOptions['loopMode'] = 'manual';
    $gOptions['loopModeManualDynamic'] = true;
    $gOptions['loopModePopup'] = true;
    $gOptions['widgetModeManualDynamic'] = true;
    $gOptions['widgetModePopup'] = true;
    return $gOptions;
    }

add_action('admin_menu', 'CTAdminMenu');
function CTAdminMenu() {
    if (function_exists('add_submenu_page'))
        add_submenu_page('plugins.php', __('Cite This'), __('Cite This'), 'manage_options', __FILE__, 'CTOptions');

    // Check options
    if (get_option('CTcitationStyles')===false) { // Add default values
        $citations = GetDefaultCitationStyles();
        add_option('CTcitationStyles', $citations);
        }
    if (get_option('CTgerneralOptions')===false && get_option('CTgeneralOptions')===false) {
        $gOptions = GetDefaultGeneralOptions();
        add_option('CTgeneralOptions', $gOptions);
        }
    if (get_option('CTgerneralOptions')!==false) {
        add_option('CTgeneralOptions', get_option('CTgerneralOptions'));
		delete_option('CTgerneralOptions');
        }

	$citations = get_option('CTcitationStyles');
	$gOptions = get_option('CTgeneralOptions');
	if (!is_array($citations)) {
		// Prior to 0.2.3
		update_option('CTcitationStyles', unserialize($citations));
		update_option('CTgeneralOptions', unserialize($gOptions));
		}
    }

register_deactivation_hook(__FILE__, 'CTDeactivate');
function CTDeactivate() {
    if ($_GET['by'] == 'plugin') {
        delete_option('CTgeneralOptions');
		delete_option('CTcitationStyles');
		}
    }

add_action('wp_head', 'AddCTJSCSS');
function AddCTJSCSS() {
    echo '<link rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/CiteThis/CiteThis.css" type="text/css"/>';

    wp_print_scripts(array('jquery'));
?>
<script type="text/javascript">
//<![CDATA[
function ManualLoad(postID) {
    if (jQuery('div#citations-'+postID+'.citations').size()==0) {
        jQuery('<div id="citations-'+postID+'" class="citations"><\/div>').appendTo(jQuery('div#post-'+postID));
        }
    // Show indicator
    jQuery('<div class="loading">Loading...<\/div>').appendTo(jQuery('div#citations-'+postID+'.citations'));
    jQuery.getScript("<?php echo get_bloginfo('wpurl'); ?>/?p=" + postID + "&citations=partial");
    }
jQuery(document).ready(function() {
    jQuery("a.citation-new-window").attr({"target": "_blank"});
    });
//]]>
</script>
<?php
    }
?>

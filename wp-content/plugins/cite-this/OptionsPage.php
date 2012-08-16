<?php
/*
 * Copyright 2007, 2008 Yu-Jie Lin
 *
 * This file is part of Cite this.
 *
 * Cite this is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 3 of the License, or (at your option)
 * any later version.
 *
 * Cite this is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * Author       : Yu-Jie Lin
 * Email        : lb08 AT livibetter DOT com
 * Website      : http://www.livibetter.com
 */

function CTOptions() {
    // Load options
    $gOptions = get_option('CTgeneralOptions');
    $citations = get_option('CTcitationStyles');

    if (isset($_POST['manage'])) {
        switch($_POST['manage']) {
        case 'Reset All Options':
			$citations = GetDefaultCitationStyles();
			$gOptions = GetDefaultGeneralOptions();
	        update_option('CTcitationStyles', $citations);
            update_option('CTgeneralOptions', $gOptions);
            echo '<div id="message" class="updated fade"><p>All options are resetted!</p></div>';
            break;
        case 'Deactivate Plugin':
            $plugin_file = dirname(plugin_basename(__FILE__)) . '/CiteThis.php';
            wp_redirect(str_replace('&#038;', '&', wp_nonce_url("plugins.php?action=deactivate&plugin=$plugin_file", "deactivate-plugin_$plugin_file")) . '&by=plugin');
            break;
            }
        }
    elseif (isset($_POST['updateGeneralOptions'])) {
        switch($_POST['updateGeneralOptions']) {
        case 'Save':
            $gOptions = array();
            $gOptions['institution'] = $_POST['institution'];
            $gOptions['singleMode'] = $_POST['singleMode'];
            $gOptions['singleModeManualDynamic'] = isset($_POST['singleModeManualDynamic']);
            $gOptions['singleModePopup'] = isset($_POST['singleModePopup']);
            $gOptions['loopMode'] = $_POST['loopMode'];
            $gOptions['loopModeManualDynamic'] = isset($_POST['loopModeManualDynamic']);
            $gOptions['loopModePopup'] = isset($_POST['loopModePopup']);
            $gOptions['widgetModeManualDynamic'] = isset($_POST['widgetModeManualDynamic']);
            $gOptions['widgetModePopup'] = isset($_POST['widgetModePopup']);
            update_option('CTgeneralOptions', $gOptions);
            echo '<div id="message" class="updated fade"><p>General options saved!</p></div>';
            break;
        case 'Reset':
            $gOptions = GetDefaultGeneralOptions();
            update_option('CTgeneralOptions', $gOptions);
            echo '<div id="message" class="updated fade"><p>General options reseted!</p></div>';
            break;
            }
        }
    elseif (isset($_POST['updateCitationStyles'])) {
        switch($_POST['updateCitationStyles']) {
        case 'Save':
            $order = explode(',', $_POST['order']);
            $citations = array();
            foreach($order as $name) {
                $id = $_POST[$name . '-id'];
                if ($id=='') {
                    $id = $_POST[$name . '-name'];
                    if ($id=='') continue;
                    echo "<div id=\"message\" class=\"updated fade\"><p>Missing ID: <b>$id</b>, assigned ";
                    $id = sanitize_title($id);
                    echo "<b>$id</b>.</p></div>";
                    }
                else{
                    $oldId = $id;
                    $id = sanitize_title($id);
                    if ($oldId!=$id)
                        echo "<div id=\"message\" class=\"updated fade\"><p>Renamed ID: <b>$oldId</b> to <b>$id</b>.</p></div>";
                    }

                $oldId = $id;
                while (array_key_exists($id, $citations)) // id duplicated
                    $id .= strval(rand(0,9)); // make new one
                if ($oldId!=$id)
                    echo "<div id=\"message\" class=\"updated fade\"><p>Duplicated ID: <b>$oldId</b>, renamed to <b>$id</b>.</p></div>";

                $citations[$id]['name'] = $_POST[$name . '-name'];
                $citations[$id]['style'] = $_POST[$name . '-style'];
                $citations[$id]['styleURI'] = $_POST[$name . '-styleURI'];
                $citations[$id]['show'] = ($_POST[$name . '-show'] == 'true') ? true : false;
                }
            update_option('CTcitationStyles', $citations);
            echo '<div id="message" class="updated fade"><p>Citation styles updated!</p></div>';
            break;
        case 'Reset':
            $citations = GetDefaultCitationStyles();
            update_option('CTcitationStyles', $citations);
            echo '<div id="message" class="updated fade"><p>Citation styles reseted!</p></div>';
            break;
            }
        }
?>

    <div class="wrap">
        <h2>Cite This Options</h2>
	    <div id="poststuff">
            <div id="moremeta">
                <div id="grabit" class="dbx-group">
                    <fieldset id="aboutBox" class="dbx-box">
                        <h3 class="dbx-handle">About this plugin</h3>
                        <div class="dbx-content">
                        <ul>
                            <li><a href="<?php echo CT_WEBSITE ?>">Plugin's Website</a></li>
                            <li><a href="http://wordpress.org/extend/plugins/cite-this/">WordPress Extend</a></li>
                            <li><a href="<?php echo CT_SUPPORT ?>">Get Support</a></li>
                            <li><a href="http://www.livibetter.com/">Author's Website</a></li>
							<li><a href="http://www.livibetter.com/blog/donate/">Donate</a>
                        </ul>
                        </div>
                    </fieldset>
                    <fieldset id="management" class="dbx-box">
                        <h3 class="dbx-handle">Management</h3>
                        <div class="dbx-content">
                            <form method="post" action="">
                                <input type="submit" name="manage" value="Reset All Options" style="font-weight:bold;"/>
                                <p style="margin-left: 20px; margin-right: 10px;"><small>Reverts all options to defaults.</small></p>
                                <input type="submit" name="manage" value="Deactivate Plugin" style="font-weight:bold;"/>
                                <p style="margin-left: 20px; margin-right: 10px;"><small>Be careful! This will remove all your settings for this plugin! If you do not want to lose settings, please use Plugins page to deactivate this plugin.</small></p>
                            </form>
                        </div>
                    </fieldset>
                    <fieldset id="tagsCitationStyles" class="dbx-box">
                        <h3 class="dbx-handle">Tags for Citation Styles</h3>
                        <div class="dbx-content">
                            <dl>
                            <dt>%pagename%</dt>
                            <dd>The post title.</dd>
                            <dt>%author%</dt>
                            <dd>Name of post's author.</dd>
                            <dt>%publisher%</dt>
                            <dd>This blog's name.</dd>
                            <dt>%institution%</dt>
                            <dd>Institution associated with this blog.</dd>
                            <dt>%date:format%</dt>
                            <dd>Published date or last updated date. <a href="http://www.php.net/date">format</a></dd>
                            <dt>%retdate:format%</dt>
                            <dd>Requesting date. <a href="http://www.php.net/date">format</a></dd>
                            <dt>%permalink%</dt>
                            <dd>URI to the post.</dd>
                            <dt>&amp;lt;</dt>
                            <dd>Display &lt;</dd>
                            <dt>&amp;gt;</dt>
                            <dd>Display &gt;</dd>
                            <dt>&amp;#39;</dt>
                            <dd>Display &#39; (single quote)</dd>
                            </dl>
                        </div>
                    </fieldset>
                </div>
            </div>
            <div id="advancedstuff" class="dbx-group">
                <div class="dbx-b-ox-wrapper">
                    <fieldset id="CTgeneralOptions"class="dbx-box">
                        <div class="dbx-h-andle-wrapper">
                            <h3 class="dbx-handle">General Options</h3>
                        </div>
                        <div class="dbx-c-ontent-wrapper">
                            <div class="dbx-content">
                                <form method="post" action="">
                                <div>
                                Institution associated with this blog: <input name="institution" type="text" size="20" value="<?php echo $gOptions['institution']; ?>"/>
                                <h4>Providing method in Single Post Mode:</h4>
                                <blockquote>
                                    <input type="radio" name="singleMode" id="singleModeDisable" value="disable" <?php if($gOptions['singleMode'] == 'disable') echo 'checked="checked"'; ?>/> <label for="singleModeDisable">Disable</label><br/>
                                    <input type="radio" name="singleMode" id="singleModeAuto" value="auto" <?php if($gOptions['singleMode'] == 'auto') echo 'checked="checked"'; ?>/> <label for="singleModeAuto">Automatic</label><br/>
                                    <input type="radio" name="singleMode" id="singleModeManual" value="manual" <?php if($gOptions['singleMode'] == 'manual') echo 'checked="checked"'; ?>/> <label for="singleModeManual">Manual</label><br/>
                                    <blockquote>
                                        <input type="checkbox" name="singleModeManualDynamic" id="singleModeManualDynamic" <?php if($gOptions['singleModeManualDynamic']) echo 'checked="checked"'; ?>/> <label for="singleModeManualDynamic">Use dynamic method</label><br/>
                                        <input type="checkbox" name="singleModePopup" id="singleModePopup" <?php if($gOptions['singleModePopup']) echo 'checked="checked"'; ?>/> <label for="singleModePopup">Show a popup option</label>
                                    </blockquote>
                                </blockquote>
                                <h4>Providing method in Multi-post Mode:</h4>
                                <blockquote>
                                    <input type="radio" name="loopMode" id="loopModeDisable" value="disable" <?php if($gOptions['loopMode'] == 'disable') echo 'checked="checked"'; ?>/> <label for="loopModeDisable">Disable</label><br/>
                                    <input type="radio" name="loopMode" id="loopModeAuto" value="auto" <?php if($gOptions['loopMode'] == 'auto') echo 'checked="checked"'; ?>/> <label for="loopModeAuto">Automatic</label><br/>
                                    <input type="radio" name="loopMode" id="loopModeManual" value="manual" <?php if($gOptions['loopMode'] == 'manual') echo 'checked="checked"'; ?>/> <label for="loopModeManual">Manual</label><br/>
                                    <blockquote>
                                        <input type="checkbox" name="loopModeManualDynamic" id="loopModeManualDynamic" <?php if($gOptions['loopModeManualDynamic']) echo 'checked="checked"'; ?>/> <label for="loopModeManualDynamic">Use dynamic method</label><br/>
                                        <input type="checkbox" name="loopModePopup" id="loopModePopup" <?php if($gOptions['loopModePopup']) echo 'checked="checked"'; ?>/> <label for="loopModePopup">Show a popup option</label>
                                    </blockquote>
                                </blockquote>
                                <h4>Providing method in Widget Mode:</h4>
                                <blockquote>
                                    <input type="checkbox" name="widgetModeManualDynamic" id="widgetModeManualDynamic" <?php if($gOptions['widgetModeManualDynamic']) echo 'checked="checked"'; ?>/> <label for="widgetModeManualDynamic">Use dynamic method</label><br/>
                                    <input type="checkbox" name="widgetModePopup" id="widgetModePopup" <?php if($gOptions['widgetModePopup']) echo 'checked="checked"'; ?>/> <label for="widgetModePopup">Show a popup option</label>
                                </blockquote>
                                <p class="submit">
                                    <input type="submit" name="updateGeneralOptions" value="Save"/>
									<input type="submit" name="updateGeneralOptions" value="Reset"/>
                                </p>
                                </div>
                                </form>
                            </div>
                        </div>
                    </fieldset>
                </div>
                <div class="dbx-b-ox-wrapper">
                    <fieldset id="CTcitationStyles"class="dbx-box">
                        <div class="dbx-h-andle-wrapper">
                            <h3 class="dbx-handle">Citation Styles</h3>
                        </div>
                        <div class="dbx-c-ontent-wrapper">
                            <div class="dbx-content">
                                <form method="post" action="">
                                <div id="citationStylesContainer">
                                        <span class="noborder">&nbsp;&nbsp;</span>
                                        <input disabled="disabled" class="noborder" name="citation-show" type="text" size="6" value="Show?"/>
                                        <input disabled="disabled" class="noborder" name="citation-id" type="text" size="10" value="ID"/>
                                        <input disabled="disabled" class="noborder" name="citation-name" type="text" size="10" value="Name"/>
                                        <input disabled="disabled" class="noborder" name="citation-styleURI" type="text" size="30" value="URI"/>
                                        <input disabled="disabled" class="noborder" name="citation-style" type="text" size="30" value="Style"/>
                                <div id="citationStyles">
    <?php
        foreach($citations as $name => $citation) {
    ?>
                                    <div class="citation" id="citation-<?php echo $name; ?>">
                                        <span class="handle">&#11021;</span>
                                        <select name="citation-<?php echo $name; ?>-show">
                                            <option <?php if($citation['show'] == false) echo 'selected="selected"'; ?> value="false">Hide</option>
                                            <option <?php if($citation['show'] ==  true) echo 'selected="selected"'; ?> value="true" >Show</option>
                                        </select>
                                        <input name="citation-<?php echo $name; ?>-id" type="text" size="10" value="<?php echo $name; ?>"/>
                                        <input name="citation-<?php echo $name; ?>-name" type="text" size="10" value="<?php echo htmlspecialchars(stripslashes($citation['name'])); ?>"/>
                                        <input name="citation-<?php echo $name; ?>-styleURI" type="text" size="30" value="<?php echo htmlspecialchars(stripslashes($citation['styleURI'])); ?>"/>
                                        <input name="citation-<?php echo $name; ?>-style" type="text" size="30" value="<?php echo htmlspecialchars(stripslashes($citation['style'])); ?>"/>
                                        <input type="button" value="Remove" onclick="RemoveCitation(this.parentNode)"/>
                                    </div>
    <?php
            }
    ?>
                                </div>
                                <br/>
                                <p class="submit">
                                    <input type="hidden" name="order" value=""/>
                                    <input type="hidden" name="updateCitationStyles" value=""/>
                                    <input type="button" value="Add" onclick="AddCitation()"/>
                                    <input type="button" value="Save" onclick="SubmitCitationStyles(this.form)"/>
                                    <input type="button" value="Reset" onclick="ResetCitationStyles(this.form)"/>
                                </p>
                                </div>
                                </form>
                            </div>

                        </div>
                    </fieldset>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">

    //<![CDATA[

jQuery('#citationStyles').Sortable({
    accept : 'citation',
    handle : '.handle',
    opacity: 	0.5,
    fit :	false
    })

function RemoveCitation(citation){
    jQuery('#'+citation.id).remove();
    }

function AddCitation(){
    var id;
    do {
        id = 'citation-' + Math.round(10000*Math.random()).toString();
    } while (jQuery('#'+id).size()>0);

    var newCitation = '<div class="citation" id="' + id + '">\n' +
                      '<span class="handle">&#11021;<\/span>\n' +
                      '<select name="' + id + '-show"><option value="false">Hide<\/option><option selected="selected" value="true" >Show<\/option><\/select>\n' +
                      '<input name="' + id + '-id" type="text" size="10" value=""\/>\n' +
                      '<input name="' + id + '-name" type="text" size="10" value=""\/>\n' +
                      '<input name="' + id + '-styleURI" type="text" size="30" value=""\/>\n' +
                      '<input name="' + id + '-style" type="text" size="30" value=""\/>\n' +
                      '<input type="button" value="Remove" onclick="RemoveCitation(this.parentNode)"\/>\n' +
                      '<\/div>';
    jQuery('#citationStyles')
        .append(newCitation)
        .SortableAddItem(document.getElementById(id));
    }

function SubmitCitationStyles(form) {
   var serial = jQuery.SortSerialize('citationStyles');
    form.updateCitationStyles.value = 'Save';
    form.order.value = serial.o['citationStyles'].join(',');
    form.submit();
    }
function ResetCitationStyles(form) {
    form.updateCitationStyles.value = 'Reset';
    form.submit();
    }
    //]]>
    </script>
<?php
    }
function CTAddOptionsJS() {
    wp_print_scripts(array('interface'));
    }
function CTAddOptionsStyle() {
    echo '<link rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/CiteThis/OptionsPage.css" type="text/css"/>';
    }
// Add JS and stylesheet
add_action('admin_print_scripts-plugins_page_CiteThis/CiteThis', 'CTAddOptionsJS');
add_action('admin_head-plugins_page_CiteThis/CiteThis', 'CTAddOptionsStyle');
?>

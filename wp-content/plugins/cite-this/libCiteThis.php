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
 * Creation Date: 7/19/2007 01:21 UTC+8
 */

/* Constants
=========================================================================== */

define('CT_WEBSITE', 'http://trac-hg.assembla.com/llbbsc/wiki/wpCT');
define('CT_SUPPORT', 'http://www.livibetter.com/it/forum/llbb-small-creations');

/* Get Bibliographic Details
=========================================================================== */

function GetDetails($post) {
	$gOptions = get_option('CTgeneralOptions');
	// Decide author's name
	$authorName = 'Unknown author';
	$author = get_userdata($post->post_author); error_log(print_r($author, true));
	if ($author->first_name && $author->last_name)
		$authorName = "$author->first_name $author->last_name";
	elseif ($author->nickname)
		$authorName = $author->nickname;
	elseif ($author->display_name)
		$authorName = $author->display_name;
	// Decide date
	if ($post->post_modified_gmt !== null)
		$date = $post->post_modified_gmt;
	else
		$date = $post->post_date_gmt;

	$deatils = array();
	$details['pagename'] = ($post->post_title=='')?'Unnamed article':$post->post_title;
	$details['author'] = $authorName;
	$details['publisher'] = get_bloginfo('name');
	$details['institution'] = $gOptions['institution'];
	$details['date'] = mysql2date('U', ($post->post_modified_gmt > $post->post_date_gmt)
									  ? $post->post_modified_gmt : $post->post_date_gmt);
	$details['retdate'] = gmmktime();
	$details['permalink'] = get_permalink($post->ID);
	return $details;
	}

function GetCitations($details, $styles=null) {
	$citations = get_option('CTcitationStyles');
	// Generate substitutes
	$subs = array();
	foreach($details as $tag => $value)
		$subs["%$tag%"] = $value;

	$newCitations = array();
	foreach($citations as $name => $citation) {
		if ($citation['show']==false) continue;
		if ($styles!==null)
			if (!in_array($name, $styles)) continue;

		// Parse all date tags
		preg_match_all('/%date:(.*?)%/', $citation['style'], $matches);
		if (count($matches[0])>0)
			for($i=0; $i<count($matches[0]); $i++) {
				$datetag = $matches[0][$i];
				$datefmt = $matches[1][$i];
				if (array_key_exists($datetag, $subs)) continue;
				$subs[$datetag] = gmdate($datefmt, $details['date']);
				}
		preg_match_all('/%retdate:(.*?)%/', $citation['style'], $matches);
		if (count($matches[0])>0)
			for($i=0; $i<count($matches[0]); $i++) {
				$datetag = $matches[0][$i];
				$datefmt = $matches[1][$i];
				if (array_key_exists($datetag, $subs)) continue;
				$subs[$datetag] = gmdate($datefmt, $details['retdate']);
				}

		$newCitations[$name] = $citation;
		$newCitations[$name]['text'] = str_replace(array_keys($subs), array_values($subs), $citation['style']);
		}
	return $newCitations;
	}

function GetCitationsHTML($citations) {
	$html = '<h4 class="uppercase">Cite this post</h4>';
	$html .= '<dl class="citations">';
	foreach($citations as $name => $citation) {
		if ($citation['show'] == false) continue;
		$html .= '<dt class="citation"><span style="font-weight:bold;">' . $citation['name'] . '</span></dt>';
		$html .= '<dd class="citation">' . stripslashes($citation['text']) . '</dd>';
		}
	$html .= '</dl>';
	return $html;
	}

/*
Supplementary functions
*/
function get_permalink_citations($post=null) {
	if ($post===null) global $post;

	$permalink = get_permalink($post->ID);
	if (strpos($permalink, '?')===false) {
		if ($permalink[strlen($permalink)-1]=='/')
			return $permalink . 'citations/';
		else
			return $permalink . '/citations/';
		}
	else
		return $permalink . '&citations';
	}

function get_permalink_citations_new_window($post=null) {
	if ($post===null) global $post;

	$permalink = get_permalink($post->ID);
	if (strpos($permalink, '?')===false) {
		if ($permalink[strlen($permalink)-1]=='/')
			return $permalink . 'citations/new/';
		else
			return $permalink . '/citations/new/';
		}
	else
		return $permalink . '&citations=new';
	}
?>

<?php
/*
Plugin Name: ScholarPress Coins
Plugin URI: http://www.scholarpress.net/coins/
Description: Makes your blog posts readable by various COinS interpreters.
Version: 1.3
Author: Sean Takats, Jeremy Boggs
Author URI: http://chnm.gmu.edu

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

add_filter('the_content', 'scholarpress_coins_add_coins_metadata');

function scholarpress_coins_add_coins_metadata($content)
{
    global $post, $authordata;

    $coinsTitle = 'ctx_ver=Z39.88-2004'
                . '&amp;rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3Adc'
                . '&amp;rfr_id=info%3Asid%2Focoins.info%3Agenerator'
                . '&amp;rft.type='
                . '&amp;rft.format=text'
                . '&amp;rft.title='.urlencode($post->post_title)
                . '&amp;rft.source='.urlencode(get_bloginfo('name'))
                . '&amp;rft.date='.get_the_time('Y-m-d')
                . '&amp;rft.identifier='.urlencode(get_permalink($post->ID))
                . '&amp;rft.language=English';

    if ($cats = get_the_category()) {
        foreach((get_the_category()) as $cat) {
            $coinsTitle .= '&amp;rft.subject='.urlencode($cat->cat_name);
        }
    }

    $authorLast = $authordata->last_name;
    $authorFirst = $authordata->first_name;

    if (!empty($authorLast) &&  !empty($authorFirst)) {
        $coinsTitle = $coinsTitle
                    . '&amp;rft.aulast='.urlencode($authorLast)
                    . '&amp;rft.aufirst='.urlencode($authorFirst);
    } else {
        $coinsTitle = $coinsTitle
                    . '&amp;rft.au='.urlencode($authordata->display_name);
    }

    $coinsTitle = apply_filters('scholarpress_coins_span_title', $coinsTitle);

    $content = '<span class="Z3988" title="'.$coinsTitle.'"></span>' . $content;

    return $content;
}
?>

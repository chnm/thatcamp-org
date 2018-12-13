<?php
/**
 * Nasty junk left over from very old days of the plugin
 * This code may or may not be upgraded in the future
 * 
 * Please don't shoot me for any errors found in this file ;)
 * 
 * @package    WordPress
 * @subpackage Multi-level Navigation
 */

function mln_legacy_menu( $which ) {
	
	if ( get_mlnmenu_option( 'generator' ) == 'Theme CSS' )
		echo '<div id="menu_wrapper' . $which . '"><div id="menu' . $which . '">';
	else
		echo '<div id="pixopoint_menu_wrapper' . $which . '"><div id="pixopoint_menu' . $which . '">';

	// Start buffering
	ob_start();

	// Create giant menu items array
	if ( $which == '1' ) {
		$menuitems = array(
			get_mlnmenu_option( 'menuitem1' ),
			get_mlnmenu_option( 'menuitem2' ),
			get_mlnmenu_option( 'menuitem3' ),
			get_mlnmenu_option( 'menuitem4' ),
			get_mlnmenu_option( 'menuitem5' ),
			get_mlnmenu_option( 'menuitem6' ),
			get_mlnmenu_option( 'menuitem7' ),
			get_mlnmenu_option( 'menuitem8' ),
			get_mlnmenu_option( 'menuitem9' ),
			get_mlnmenu_option( 'menuitem10' )
		);
		$which_id = '';
	}
	elseif ( $which == '2' ) {
		$menuitems = array(
			get_mlnmenu_option( '2_menuitem1' ),
			get_mlnmenu_option( '2_menuitem2' ),
			get_mlnmenu_option( '2_menuitem3' ),
			get_mlnmenu_option( '2_menuitem4' ),
			get_mlnmenu_option( '2_menuitem5' ),
			get_mlnmenu_option( '2_menuitem6' ),
			get_mlnmenu_option( '2_menuitem7' ),
			get_mlnmenu_option( '2_menuitem8' ),
			get_mlnmenu_option( '2_menuitem9' ),
			get_mlnmenu_option( '2_menuitem10' )
		);
		$which_id = '_2';
	}

	// Main menu option
	?>
		<ul class="sf-menu" id="suckerfishnav<?php echo $which_id; ?>"><?php
	foreach( $menuitems as $key=> $menuitem ) {
		switch ( $menuitem ) {
			case "Pages":                                     mln_pages();                      break;
			case "Pages (single dropdown)":                   mln_pagesdropdown();              break;
			case "Categories":                                mln_category();                   break;
			case "Categories (single dropdown)":              mln_categoriesdropdown();         break;
			case "Home":                                      mln_home();                       break;
			case "Links - no categories":                     mln_blogroll();                   break;
			case "Links - no categories (single dropdown)":   mln_blogrolldropdown();           break;
			case "Links - with categories":                   mln_blogrollcategories();         break;
			case "Links - with categories (single dropdown)": mln_blogrollcategoriesdropdown(); break;
			case "Archives - months":                         mln_archivesmonths();             break;
			case "Archives - years":                          mln_archivesyears();              break;
			case "Archives - months (single dropdown)":       mln_archivesmonthsdropdown();     break;
			case "Archives - years (single dropdown)":        mln_archivesyearsdropdown();      break;
			case "Recent Comments (single dropdown)":         mln_recentcomments();             break;
			case "Custom 1":                                  mln_custom();                     break;
			case "Custom 2":                                  mln_custom2();                    break;
			case "Custom 3":                                  mln_custom3();                    break;
			case "Custom 4":                                  mln_custom4();                    break;
			case "Recent Posts (single dropdown)":            mln_recentposts();                break;
			}
	}

	// Grab content from buffer
	$content = ob_get_contents();
	ob_end_clean();

	// Strip title tags
	if ( get_mlnmenu_option( 'titletags' ) == 'on' ) {
		$content = preg_replace( '/title=\"(.*?)\"/','', $content );
		$content = preg_replace( '/title=\'(.*?)\'/','', $content );
	}

	// Out content
	echo $content;

	?></ul>
	</div>
</div><?php

}



// Backwards support for old function (also strips out comment tags to allow inserting this function inside IE conditional comments
function suckerfish() {
	ob_start();
	pixopoint_menu(1);
	$suckerfish_html = ob_get_contents();
	ob_end_clean();
	$suckerfish_html = str_replace('<!-- Multi-level Navigational Plugin by PixoPoint Web Development ... https://geek.hellyer.kiwi/multi-level-navigation/ -->', '', $suckerfish_html);
	echo $suckerfish_html;
}
// Old functions for REALLY OLD  versions of the plugin ... why do some people not want to use the new way of handling menu contents?
function suckerfish1() {echo '<ul id="suckerfishnav">'.wp_list_pages('title_li=').'</ul>';}
function suckerfish2() {echo '<ul id="suckerfishnav"><li><a href="'.bloginfo('url').'/">Home</a></li>'.wp_list_pages('title_li=').'</ul>';}
function suckerfish3() {echo '<ul id="suckerfishnav"><li><a href="#">Pages</a><ul>'/wp_list_pages('title_li=') , '</ul></li><li><a href="#">Archives</a><ul>'.wp_get_archives().'</ul></li><li><a href="#">Categories</a><ul>'.wp_list_categories('title_li=').'</ul></li><li><a href="#">Links</a> <ul>'.wp_list_bookmarks('title_li=&categorize=0').'</ul></li></ul>';}
function suckerfish4() {echo '<ul id="suckerfishnav">'.wp_list_pages('title_li=').'<li><a href="#">Archives</a><ul>'.wp_get_archives().'</ul></li><li><a href="#">Categories</a><ul>'.wp_list_categories('title_li=').'</ul></li></ul>';}
function suckerfish5() {echo '<ul id="suckerfishnav"><li><a href="'.bloginfo('url').'/">Home</a></li>'.wp_list_pages('title_li=').'<li><a href="#">Archives</a><ul>'.wp_get_archives().'</ul></li><li><a href="#">Categories</a><ul>'.wp_list_categories('title_li=').'</ul></li></ul>';}






// Functions for displaying various menu contents
function mln_pages() {$suckerfish_depthpages = get_mlnmenu_option('depthpages');switch ($suckerfish_depthpages){case "Top level only":$suckerfish_depthpagesecho = '&depth=1';break;case "No nesting":$suckerfish_depthpagesecho = '&depth=-1';break;case "1 level of children":$suckerfish_depthpagesecho = '&depth=2';break;case "2 levels of children":$suckerfish_depthpagesecho = '&depth=3';break;case "Infinite":$suckerfish_depthpagesecho = '&depth=0';break;case "":$suckerfish_depthpagesecho = '&depth=0';break;}
	echo preg_replace(
			'@{"><a [\/\?a-zA-Z0-9\-\.\:\"\=\_ >]+</a>([\t\n]+)<ul}@',
			" haschildren\\0",
			wp_list_pages(
				'title_li=&'.get_mlnmenu_option('includeexcludepages').'='. get_mlnmenu_option('excludepages').'&echo=0'.$suckerfish_depthpagesecho
			)
	);
}
function mln_pagesdropdown() {
	$suckerfish_depthpages = get_mlnmenu_option('depthpages');
	switch ($suckerfish_depthpages){
		case "Top level only":$suckerfish_depthpagesecho = '&depth=1';break;
		case "No nesting":$suckerfish_depthpagesecho = '&depth=-1';break;
		case "1 level of children":$suckerfish_depthpagesecho = '&depth=2';break;
		case "2 levels of children":$suckerfish_depthpagesecho = '&depth=3';break;
		case "Infinite":$suckerfish_depthpagesecho = '&depth=0';break;
		case "":$suckerfish_depthpagesecho = '&depth=0';break;
	}
	if (is_page())
		$class=' class="current_page_parent current_page_item"';
	else
		$class = '';
	echo '<li'.$class.'><a href="'; if (get_mlnmenu_option('pagesurl') != '') {echo get_mlnmenu_option('pagesurl');}
	echo '">' . get_mlnmenu_option('pagestitle') . '</a><ul>', ereg_replace("\"><a [/\?a-zA-Z0-9\-\.\:\"\=\_ >]+</a>([\t\n]+)<ul"," haschildren\\0",wp_list_pages('title_li=&'.get_mlnmenu_option('includeexcludepages').'='. get_mlnmenu_option('excludepages').'&echo=0'.$suckerfish_depthpagesecho)) , "</ul></li>\n";}
// Gregs function pagesdropdown() {if (is_page()) $class=' class="current_page_parent current_page_item"'; echo '<li'.$class.'><a href="">' . get_mlnmenu_option('pagestitle') . '</a><ul>', ereg_replace("\"><a [/\?a-zA-Z0-9\-\.\:\"\=\_ >]+</a>([\t\n]+)<ul"," haschildren\\0",wp_list_pages('title_li=&exclude='. get_mlnmenu_option('excludepages').'&echo=0')) , "</ul></li>\n";}
function mln_category() {
	if (get_mlnmenu_option('categorycount') == 'on') {$suckerfish_categorycount = 'show_count=1';}
	if (get_mlnmenu_option('categoryshowempty') == 'on') {$suckerfish_categoryshowempty = '&hide_empty=0';}
	$suckerfish_depthcategories = get_mlnmenu_option('depthcategories');switch ($suckerfish_depthcategories){case "Top level only":$suckerfish_depthcategoriesecho = '&depth=1';break;case "No nesting":$suckerfish_depthcategoriesecho = '&depth=-1';break;case "1 level of children":$suckerfish_depthcategoriesecho = '&depth=2';break;case "2 levels of children":$suckerfish_depthcategoriesecho = '&depth=3';break;case "Infinite":$suckerfish_depthcategoriesecho = '&depth=0';break;case "":$suckerfish_depthcategoriesecho = '&depth=0';break;}
	$suckerfish_categoryorder=get_mlnmenu_option('categoryorder');switch ($suckerfish_categoryorder){case "Ascending ID #":$suckerfish_categoryorderecho = '&orderby=id&order=ASC';break;case "Decending ID #":$suckerfish_categoryorderecho = '&orderby=id&order=DESC';break;case "Ascending Name":$suckerfish_categoryorderecho = '&orderby=name&order=ASC';break;case "Decending Name":$suckerfish_categoryorderecho = '&orderby=name&order=DESC';break;case "":$suckerfish_categoryorderecho = '&orderby=name&order=DESC';break;}
	wp_list_categories('title_li=&'.$suckerfish_categorycount.$suckerfish_categoryshowempty.'&'.get_mlnmenu_option('includeexcludecategories').'='.get_mlnmenu_option('excludecategories').$suckerfish_depthcategoriesecho);
	}
function mln_categoriesdropdown() {



	if (get_mlnmenu_option('categorycount') == 'on')
		$suckerfish_categorycount = 'show_count=1';
	else
		$suckerfish_categorycount = '';
	if (get_mlnmenu_option('categoryshowempty') == 'on')
		$suckerfish_categoryshowempty = '&hide_empty=0';
	else
		$suckerfish_categoryshowempty  = '';
	$suckerfish_depthcategories = get_mlnmenu_option('depthcategories');switch ($suckerfish_depthcategories){case "Top level only":$suckerfish_depthcategoriesecho = '&depth=1';break;case "No nesting":$suckerfish_depthcategoriesecho = '&depth=-1';break;case "1 level of children":$suckerfish_depthcategoriesecho = '&depth=2';break;case "2 levels of children":$suckerfish_depthcategoriesecho = '&depth=3';break;case "Infinite":$suckerfish_depthcategoriesecho = '&depth=0';break;case "":$suckerfish_depthcategoriesecho = '&depth=0';break;}
	$suckerfish_categoryorder=get_mlnmenu_option('categoryorder');switch ($suckerfish_categoryorder){case "Ascending ID #":$suckerfish_categoryorderecho = '&orderby=id&order=ASC';break;case "Decending ID #":$suckerfish_categoryorderecho = '&orderby=id&order=DESC';break;case "Ascending Name":$suckerfish_categoryorderecho = '&orderby=name&order=ASC';break;case "Decending Name":$suckerfish_categoryorderecho = '&orderby=name&order=DESC';break;case "":$suckerfish_categoryorderecho = '&orderby=name&order=DESC';break;}
	if (is_category()) {$suckerfish_class=' class="categories haschildren current_page_parent current_page_item"';}
	else {$suckerfish_class=' class="categories haschildren"';}
	echo '<li'.$suckerfish_class.'><a href="'; if (get_mlnmenu_option('categoriesurl') != '') {echo get_mlnmenu_option('categoriesurl');} echo '">' . get_mlnmenu_option('categoriestitle') . '</a><ul>' , implode("</a>\n<ul",explode("</a>\n<ul",str_replace("\t",'',wp_list_categories('title_li='.$suckerfish_categoryshowempty.'&'.$suckerfish_categorycount.'&'.get_mlnmenu_option('includeexcludecategories').'='. get_mlnmenu_option('excludecategories').'&echo=0'.$suckerfish_categoryorderecho.$suckerfish_depthcategoriesecho)))) , "</ul></li>\n";
	}
// Gregs function categoriesdropdown() {if (is_category()) {$suckerfish_class=' class="current_page_parent current_page_item"';} echo '<li'.$suckerfish_class.'><a href="">' . get_mlnmenu_option('categoriestitle') . '</a><ul>' , implode("Z</a>\n<ul",explode("</a>\n<ul",str_replace("\t",'',wp_list_categories('title_li=&exclude='. get_mlnmenu_option('excludecategories').'&echo=0')))) , "</ul></li>\n";}
function mln_home() {
	if (is_home())
		$suckerfish_class=' class="current_page_item"';
	else	
		$suckerfish_class = '';
	echo '<li'.$suckerfish_class.'><a href="'; if (get_mlnmenu_option('homeurl') != '') {echo get_mlnmenu_option('homeurl');} else {echo bloginfo('url').'/';} echo '">' . get_mlnmenu_option('hometitle') . '</a></li>';}
function mln_blogroll() {wp_list_bookmarks('title_li=&categorize=0');}
function mln_blogrolldropdown() {echo '<li><a href="'; if (get_mlnmenu_option('blogrollurl') != '') {echo get_mlnmenu_option('blogrollurl');} echo '">' . get_mlnmenu_option('blogrolltitle') . '</a> <ul>' , wp_list_bookmarks('title_li=&categorize=0') , '</ul></li>';}
function mln_blogrollcategories() {wp_list_bookmarks('title_li=&title_before=<a href="">&title_after=</a>&categorize=1&before=<li>&after=</li>&show_images=0&show_description=0&orderby=url');}
function mln_blogrollcategoriesdropdown() {echo '<li><a href="'; if (get_mlnmenu_option('blogrollurl') != '') {echo get_mlnmenu_option('blogrollurl');} echo '">' . get_mlnmenu_option('blogrolltitle') . '</a> <ul>' , wp_list_bookmarks('title_li=&title_before=<a href="">&title_after=</a>&categorize=1&before=<li>&after=</li>&show_images=0&show_description=0&orderby=url') , '</ul></li>';}
function mln_archivesmonths() {wp_get_archives('type=monthly');}
function mln_archivesyears() {wp_get_archives('type=yearly');}
function mln_archivesmonthsdropdown() {
	if (is_month()) {$suckerfish_class=' class="current_page_parent current_page_item"';}
	$suckerfish_class = '';
	
	echo '<li'.$suckerfish_class.'><a href="'; if (get_mlnmenu_option('archivesurl') != '') {echo get_mlnmenu_option('archivesurl');} echo '">' . get_mlnmenu_option('archivestitle') . '</a><ul>' , wp_get_archives('type=monthly') , '</ul></li>';}
function mln_archivesyearsdropdown() {if (is_year()) {$suckerfish_class=' class="current_page_parent current_page_item"';}echo '<li'.$suckerfish_class.'><a href="'; if (get_mlnmenu_option('archivesurl') != '') {echo get_mlnmenu_option('archivesurl');} echo '">' . get_mlnmenu_option('archivestitle') . '</a><ul>' , wp_get_archives('type=yearly') , '</ul></li>';}
function mln_recentcomments() {echo '<li><a href="'; if (get_mlnmenu_option('recentcommentsurl') != '') {echo get_mlnmenu_option('recentcommentsurl');} echo '">' . get_mlnmenu_option('recentcommentstitle') . '</a>'; global $wpdb; $sql = "SELECT DISTINCT ID, post_title, post_password, comment_ID, comment_post_ID, comment_author, comment_date_gmt, comment_approved, comment_type,comment_author_url, SUBSTRING(comment_content,1,30) AS com_excerpt FROM $wpdb->comments LEFT OUTER JOIN $wpdb->posts ON ($wpdb->comments.comment_post_ID = $wpdb->posts.ID) WHERE comment_approved = '1' AND comment_type = '' AND post_password = '' ORDER BY comment_date_gmt DESC LIMIT ".get_mlnmenu_option('recentcommentsnumber'); $comments = $wpdb->get_results($sql); $output = $pre_HTML; $output .= "\n<ul>"; foreach ($comments as $comment) {$output .= "\n<li><a href=\"" . get_permalink($comment->ID) . "#comment-" . $comment->comment_ID . "\" title=\"on " . $comment->post_title . "\">".strip_tags($comment->comment_author) .":" . " " . strip_tags($comment->com_excerpt) ."</a></li>"; } $output .= "\n</ul>"; $output .= $post_HTML; echo $output; echo '</li>';}
function mln_custom() {echo get_mlnmenu_option('custommenu');}
function mln_custom2() {echo get_mlnmenu_option('custommenu2');}
function mln_custom3() {echo get_mlnmenu_option('custommenu3');}
function mln_custom4() {echo get_mlnmenu_option('custommenu4');}
function mln_recentposts() {echo '<li><a href="'; if (get_mlnmenu_option('recentpostsurl') != '') {echo get_mlnmenu_option('recentpostsurl');} echo '">' . get_mlnmenu_option('recentpoststitle') . '</a><ul>';query_posts('showposts='.get_mlnmenu_option('recentpostsnumber'));?><?php while (have_posts()) : the_post(); ?><?php echo '<li><a href="'; the_permalink(); echo '">'; the_title(); echo '</a></li>'; ?><?php endwhile;?><?php wp_reset_query(); ?><?php echo '</ul>';}
function mln_pages_excludechildren() {$args = array('post_type' => 'page','post_parent' => get_mlnmenu_option('excludepages'), ); $suckerfish_excludepageschildren .= get_mlnmenu_option('excludepages').','; if(get_mlnmenu_option('excludepages') != ''){$attachments = get_children($args);} if ($attachments) {foreach ($attachments as $post) {$suckerfish_excludepageschildren .= $post->ID.',';} } echo '', ereg_replace("\"><a [/\?a-zA-Z0-9\-\.\:\"\=\_ >]+</a>([\t\n]+)<ul"," haschildren\\0",wp_list_pages('title_li=&exclude='.$suckerfish_excludepageschildren.'&echo=0')) , '';}
function mln_pagesdropdown_excludechildren() {$args = array('post_type' => 'page','post_parent' => get_mlnmenu_option('excludepages'), ); $suckerfish_excludepageschildren .= get_mlnmenu_option('excludepages').','; if(get_mlnmenu_option('excludepages') != ''){$attachments = get_children($args);} if ($attachments) {foreach ($attachments as $post) {$suckerfish_excludepageschildren .= $post->ID.',';} } if (is_page()) $class=' class="current_page_parent current_page_item"'; echo '<li'.$class.'><a href="'; if (get_mlnmenu_option('pagesurl') != '') {echo get_mlnmenu_option('pagesurl');} echo '">' . get_mlnmenu_option('pagestitle') . '</a><ul>', ereg_replace("\"><a [/\?a-zA-Z0-9\-\.\:\"\=\_ >]+</a>([\t\n]+)<ul"," haschildren\\0",wp_list_pages('title_li=&exclude='.$suckerfish_excludepageschildren.'&echo=0')) , "</ul></li>\n"; }

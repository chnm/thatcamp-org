<?php
function graphene_options_display() { 
    
    global $graphene_settings;
    ?>
        
    <input type="hidden" name="graphene_display" value="true" />
        
        <?php /* Header Options */ ?>
        <div class="postbox">
            <div class="head-wrap">
                <div title="Click to toggle" class="handlediv"><br /></div>
        		<h3 class="hndle"><?php _e( 'Header Display Options', 'graphene' ); ?></h3>
            </div>
            <div class="panel-wrap inside">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="light_header"><?php _e( 'Use light-coloured header bars', 'graphene' ); ?></label>
                        </th>
                        <td><input type="checkbox" name="graphene_settings[light_header]" id="light_header" <?php checked( $graphene_settings['light_header'] ); ?> value="true" /></td>
                    </tr>
                    <tr class="non-essential-option">
                        <th scope="row">
                            <label for="link_header_img"><?php _e( 'Link header image to front page', 'graphene' ); ?></label>
                        </th>
                        <td><input type="checkbox" name="graphene_settings[link_header_img]" id="link_header_img" <?php checked( $graphene_settings['link_header_img'] ); ?> value="true" /><br />
                            <span class="description"><?php _e( 'Check this if you disable the header texts and want the header image to be linked to the front page.', 'graphene' ); ?></span>
                        </td>
                    </tr>
                    <tr class="non-essential-option">
                        <th scope="row">
                            <label for="featured_img_header"><?php _e( 'Disable Featured Image replacing header image', 'graphene' ); ?></label>
                        </th>
                        <td><input type="checkbox" name="graphene_settings[featured_img_header]" id="featured_img_header" <?php checked( $graphene_settings['featured_img_header'] ); ?> value="true" /><br />
                            <span class="description"><?php _e( 'Check this to prevent the posts Featured Image replacing the header image regardless of the featured image dimension.', 'graphene' ); ?></span>
                        </td>
                    </tr>
                    <tr class="non-essential-option">
                        <th scope="row">
                            <label for="use_random_header_img"><?php _e( 'Use random header image', 'graphene' ); ?></label>
                        </th>
                        <td><input type="checkbox" name="graphene_settings[use_random_header_img]" id="use_random_header_img" <?php checked( $graphene_settings['use_random_header_img'] ); ?> value="true" /><br />
                            <span class="description">
								<?php _e( 'Check this to show a random header image (random image taken from the available default header images).', 'graphene' ); ?><br />
                                <?php _e( '<strong>Note:</strong> only works on pages where a specific header image is not defined.', 'graphene' ); ?></span>
                        </td>
                    </tr>
                    <tr class="non-essential-option">
                        <th scope="row">
                            <label for="header_img_height"><?php _e( 'Header image height', 'graphene' ); ?></label>
                        </th>
                        <td>
                        	<input type="text" name="graphene_settings[header_img_height]" id="header_img_height" value="<?php echo $graphene_settings['header_img_height']; ?>" size="3" /> px
                        </td>
                    </tr>
                    
                    <tr class="non-essential-option">
                        <th scope="row">
                            <label for="search_box_location"><?php _e( 'Search box location', 'graphene' ); ?></label>
                        </th>
                        <td>
                            <select name="graphene_settings[search_box_location]" id="search_box_location">
                                <option value="top_bar" <?php selected( $graphene_settings['search_box_location'], 'top_bar' ); ?>><?php _e("Top bar", 'graphene' ); ?></option>
                                <option value="nav_bar" <?php selected( $graphene_settings['search_box_location'], 'nav_bar' ); ?>><?php _e("Navigation bar", 'graphene' ); ?></option>
                                <option value="disabled" <?php selected( $graphene_settings['search_box_location'], 'disabled' ); ?>><?php _e("Disable search box", 'graphene' ); ?></option>             
                            </select>
                        </td>
                    </tr>
                </table>
                
                <p class="submit clearfix"><input type="submit" class="button" value="<?php _e( 'Save All Options', 'graphene' ); ?>" /></p>
            </div>
        </div>
        
        
        <?php /* Column Options */ ?>
        <div class="postbox non-essential-option">
            <div class="head-wrap">
                <div title="Click to toggle" class="handlediv"><br /></div>
        		<h3 class="hndle"><?php _e( 'Column Options', 'graphene' ); ?></h3>
            </div>
            <div class="panel-wrap inside">
                <table class="form-table">
                    <tr>
                        <th scope="col" style="width:150px;">
                            <label><?php _e( 'Column mode', 'graphene' ); ?></label>
                        </th>
                    </tr>
                    <tr>
                        <td>
                            <div class="column-options">
                            	<p>                           
                                <label>
                                    <input type="radio" name="graphene_settings[column_mode]" value="one_column" <?php checked( $graphene_settings['column_mode'], 'one_column' ); ?>/>
                                    <img src="<?php echo get_template_directory_uri(); ?>/admin/images/template-onecolumn.png" alt="<?php _e( 'One column', 'graphene' ); ?>" title="<?php _e( 'One column', 'graphene' ); ?>" />                                
                                </label>
                                </p>
                                
                                <p>
                                <label>
                                    <input type="radio" name="graphene_settings[column_mode]" value="two_col_left" <?php checked( $graphene_settings['column_mode'], 'two_col_left' ); ?>/>
                                    <img src="<?php echo get_template_directory_uri(); ?>/admin/images/template-twocolumnsleft.png" alt="<?php _e( 'Two columns (with sidebar right)', 'graphene' ); ?>" title="<?php _e( 'Two columns (with sidebar right)', 'graphene' ); ?>" />
                                </label>
                                <label>
                                    <input type="radio" name="graphene_settings[column_mode]" value="two_col_right" <?php checked( $graphene_settings['column_mode'], 'two_col_right' ); ?>/>
                                    <img src="<?php echo get_template_directory_uri(); ?>/admin/images/template-twocolumnsright.png" alt="<?php _e( 'Two columns (with sidebar left)', 'graphene' ); ?>" title="<?php _e( 'Two columns (with sidebar left)', 'graphene' ); ?>" />
                                </label>
                                </p>
                                
                                <p>
                                <label>
                                    <input type="radio" name="graphene_settings[column_mode]" value="three_col_left" <?php checked( $graphene_settings['column_mode'], 'three_col_left' ); ?>/>
                                    <img src="<?php echo get_template_directory_uri(); ?>/admin/images/template-threecolumnsleft.png" alt="<?php _e( 'Three columns (with two sidebars right)', 'graphene' ); ?>" title="<?php _e( 'Three columns (with two sidebars right)', 'graphene' ); ?>" />
                                </label>
                                <label>
                                    <input type="radio" name="graphene_settings[column_mode]" value="three_col_right" <?php checked( $graphene_settings['column_mode'], 'three_col_right' ); ?>/>
                                    <img src="<?php echo get_template_directory_uri(); ?>/admin/images/template-threecolumnsright.png" alt="<?php _e( 'Three columns (with two sidebars left)', 'graphene' ); ?>" title="<?php _e( 'Three columns (with two sidebars left)', 'graphene' ); ?>" />
                                </label>
                                <label>
                                    <input type="radio" name="graphene_settings[column_mode]" value="three_col_center" <?php checked( $graphene_settings['column_mode'], 'three_col_center' ); ?>/>
                                    <img src="<?php echo get_template_directory_uri(); ?>/admin/images/template-threecolumnscenter.png" alt="<?php _e( 'Three columns (with sidebars left and right)', 'graphene' ); ?>" title="<?php _e( 'Three columns (with sidebars left and right)', 'graphene' ); ?>" />
                                </label>      
                                </p>                            
                            </div>                                                                                                              
                        </td>
                    </tr>
                    
					<?php if ( class_exists( 'BBPress' ) ) : ?>
                    <tr>
                        <th scope="col" style="width:150px;">
                            <label><?php _e( 'bbPress column mode', 'graphene' ); ?></label>
                        </th>
                    </tr>
                    <tr>
                        <td>
                            <div class="column-options">
                            	<p>                           
                                <label>
                                    <input type="radio" name="graphene_settings[bbp_column_mode]" value="one_column" <?php checked( $graphene_settings['bbp_column_mode'], 'one_column' ); ?>/>
                                    <img src="<?php echo get_template_directory_uri(); ?>/admin/images/template-onecolumn.png" alt="<?php _e( 'One column', 'graphene' ); ?>" title="<?php _e( 'One column', 'graphene' ); ?>" />                                
                                </label>
                                </p>
                                
                                <p>
                                <label>
                                    <input type="radio" name="graphene_settings[bbp_column_mode]" value="two_col_left" <?php checked( $graphene_settings['bbp_column_mode'], 'two_col_left' ); ?>/>
                                    <img src="<?php echo get_template_directory_uri(); ?>/admin/images/template-twocolumnsleft.png" alt="<?php _e( 'Two columns (with sidebar right)', 'graphene' ); ?>" title="<?php _e( 'Two columns (with sidebar right)', 'graphene' ); ?>" />
                                </label>
                                <label>
                                    <input type="radio" name="graphene_settings[bbp_column_mode]" value="two_col_right" <?php checked( $graphene_settings['bbp_column_mode'], 'two_col_right' ); ?>/>
                                    <img src="<?php echo get_template_directory_uri(); ?>/admin/images/template-twocolumnsright.png" alt="<?php _e( 'Two columns (with sidebar left)', 'graphene' ); ?>" title="<?php _e( 'Two columns (with sidebar left)', 'graphene' ); ?>" />
                                </label>
                                </p>
                                
                                <p>
                                <label>
                                    <input type="radio" name="graphene_settings[bbp_column_mode]" value="three_col_left" <?php checked( $graphene_settings['bbp_column_mode'], 'three_col_left' ); ?>/>
                                    <img src="<?php echo get_template_directory_uri(); ?>/admin/images/template-threecolumnsleft.png" alt="<?php _e( 'Three columns (with two sidebars right)', 'graphene' ); ?>" title="<?php _e( 'Three columns (with two sidebars right)', 'graphene' ); ?>" />
                                </label>
                                <label>
                                    <input type="radio" name="graphene_settings[bbp_column_mode]" value="three_col_right" <?php checked( $graphene_settings['bbp_column_mode'], 'three_col_right' ); ?>/>
                                    <img src="<?php echo get_template_directory_uri(); ?>/admin/images/template-threecolumnsright.png" alt="<?php _e( 'Three columns (with two sidebars left)', 'graphene' ); ?>" title="<?php _e( 'Three columns (with two sidebars left)', 'graphene' ); ?>" />
                                </label>
                                <label>
                                    <input type="radio" name="graphene_settings[bbp_column_mode]" value="three_col_center" <?php checked( $graphene_settings['bbp_column_mode'], 'three_col_center' ); ?>/>
                                    <img src="<?php echo get_template_directory_uri(); ?>/admin/images/template-threecolumnscenter.png" alt="<?php _e( 'Three columns (with sidebars left and right)', 'graphene' ); ?>" title="<?php _e( 'Three columns (with sidebars left and right)', 'graphene' ); ?>" />
                                </label>      
                                </p>                            
                            </div>                                                                                                              
                        </td>
                    </tr>
                    <?php endif; ?>
                </table>
                
                <p class="submit clearfix"><input type="submit" class="button" value="<?php _e( 'Save All Options', 'graphene' ); ?>" /></p>
            </div>
        </div>
        
        
        <?php /* Column Width */ ?>
        <div class="postbox non-essential-option column-width">
            <div class="head-wrap">
                <div title="Click to toggle" class="handlediv"><br /></div>
        		<h3 class="hndle"><?php _e( 'Column Width Options', 'graphene' ); ?></h3>
            </div>
            <div class="panel-wrap inside">
            	<p><?php _e( 'Note: Leave values empty to reset to the default values.', 'graphene' ); ?></p>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label><?php _e( 'Container width', 'graphene' ); ?></label>
                        </th>
                        <td>
                        	<input type="text" class="code" size="8" name="graphene_settings[container_width]" id="container_width" value="<?php echo $graphene_settings['container_width']; ?>" /> px
                            <input type="hidden" class="code" size="8" name="graphene_settings[grid_width]" id="grid_width" value="<?php echo ( $graphene_settings['container_width'] - $graphene_settings['gutter_width'] * 32 ) / 16; ?>" />
                        </td>
                    </tr>
                    <tr>
                    	<th></th>
                        <td>
                        	<div id="container_width-slider"></div>
                            <div class="alignleft slider-legend">800 px</div>
                            <div class="alignright slider-legend">1400 px</div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label><?php _e( 'Column width', 'graphene' ); ?><br />(<?php _e( 'two-column mode', 'graphene' ); ?>)</label>
                        </th>
                        <td>
                        	<div class="width-wrap">
                            	<?php _e( 'Content', 'graphene' ); ?><br />
                            	<input type="text" class="code" size="8" name="graphene_settings[column_width][two_col][content]" id="column_width_2col_content" value="<?php echo $graphene_settings['column_width']['two_col']['content']; ?>" /> px
                            </div>
                        	<div class="width-wrap">
                            	<?php _e( 'Sidebar', 'graphene' ); ?><br />
                        		<input type="text" class="code" size="8" name="graphene_settings[column_width][two_col][sidebar]" id="column_width_2col_sidebar" value="<?php echo $graphene_settings['column_width']['two_col']['sidebar']; ?>" /> px
                            </div>
                        </td>
                    </tr>
                    <tr>
                    	<th></th>
                        <td>
                            <div id="column_width_2col-slider"></div>
                            <div class="alignleft slider-legend">0 px</div>
                            <div class="column_width-max-legend alignright slider-legend"><?php echo $graphene_settings['container_width']; ?> px</div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label><?php _e( 'Column width', 'graphene' ); ?><br />(<?php _e( 'three-column mode', 'graphene' ); ?>)</label>
                        </th>
                        <td>
                        	<div class="width-wrap">
                            	<?php _e( 'Left sidebar', 'graphene' ); ?><br />
                        		<input type="text" class="code" size="8" name="graphene_settings[column_width][three_col][sidebar_left]" id="column_width_sidebar_left" value="<?php echo $graphene_settings['column_width']['three_col']['sidebar_left']; ?>" /> px
                            </div>
                            <div class="width-wrap">
                            	<?php _e( 'Content', 'graphene' ); ?><br />
                            	<input type="text" class="code" size="8" name="graphene_settings[column_width][three_col][content]" id="column_width_content" value="<?php echo $graphene_settings['column_width']['three_col']['content']; ?>" /> px
                            </div>
                            <div class="width-wrap">
                            	<?php _e( 'Right sidebar', 'graphene' ); ?><br />
                            	<input type="text" class="code" size="8" name="graphene_settings[column_width][three_col][sidebar_right]" id="column_width_sidebar_right" value="<?php echo $graphene_settings['column_width']['three_col']['sidebar_right']; ?>" /> px
                            </div>
                        </td>
                    </tr>
                    <tr>
                    	<th></th>
                        <td>
                            <div id="column_width-slider"></div>
                            <div class="alignleft slider-legend">0 px</div>
                            <div class="column_width-max-legend alignright slider-legend"><?php echo $graphene_settings['container_width']; ?> px</div>
                        </td>
                    </tr>
                </table>
                
                <p class="submit clearfix"><input type="submit" class="button" value="<?php _e( 'Save All Options', 'graphene' ); ?>" /></p>
            </div>
        </div>
        
        <?php /* Posts Display Options */ ?>
        <div class="postbox non-essential-option">
            <div class="head-wrap">
                <div title="Click to toggle" class="handlediv"><br /></div>
        		<h3 class="hndle"><?php _e( 'Posts Display Options', 'graphene' ); ?></h3>
            </div>
            <div class="panel-wrap inside">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="hide_post_author"><?php _e( 'Hide post author', 'graphene' ); ?></label>
                        </th>
                        <td><input type="checkbox" name="graphene_settings[hide_post_author]" id="hide_post_author" <?php checked( $graphene_settings['hide_post_author'] ); ?> value="true" /></td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="post_date_display"><?php _e( 'Post date display', 'graphene' ); ?></label>
                        </th>
                        <td>
                            <select name="graphene_settings[post_date_display]" id="post_date_display">
                                <option value="hidden" <?php selected( $graphene_settings['post_date_display'], 'hidden' ); ?>><?php _e( 'Hidden', 'graphene' ); ?></option>
                                <option value="icon_no_year" <?php selected( $graphene_settings['post_date_display'], 'icon_no_year' ); ?>><?php _e( 'As an icon (without the year)', 'graphene' ); ?></option>
                                <option value="icon_plus_year" <?php selected( $graphene_settings['post_date_display'], 'icon_plus_year' ); ?>><?php _e( 'As an icon (including the year)', 'graphene' ); ?></option>
                                <option value="text" <?php selected( $graphene_settings['post_date_display'], 'text' ); ?>><?php _e( 'As inline text', 'graphene' ); ?></option>
                            </select><br />
                        </td>
                    </tr>                    
                    <tr>
                        <th scope="row">
                            <label for="hide_post_cat"><?php _e( 'Hide post categories', 'graphene' ); ?></label>
                        </th>
                        <td><input type="checkbox" name="graphene_settings[hide_post_cat]" id="hide_post_cat" <?php checked( $graphene_settings['hide_post_cat'] ); ?> value="true" /></td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="hide_post_tags"><?php _e( 'Hide post tags', 'graphene' ); ?></label>
                        </th>
                        <td><input type="checkbox" name="graphene_settings[hide_post_tags]" id="hide_post_tags" <?php checked( $graphene_settings['hide_post_tags'] ); ?> value="true" /></td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="hide_post_commentcount"><?php _e( 'Hide post comment count', 'graphene' ); ?></label><br />
                            <small><?php _e( 'Only affects posts listing (such as the front page) and not single post view.', 'graphene' ); ?></small>                        
                        </th>
                        <td><input type="checkbox" name="graphene_settings[hide_post_commentcount]" id="hide_post_commentcount" <?php checked( $graphene_settings['hide_post_commentcount'] ); ?> value="true" /></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="show_post_avatar"><?php _e("Show post author's <a href=\"http://en.gravatar.com/\">gravatar</a>", 'graphene' ); ?></label></th>
                        <td><input type="checkbox" name="graphene_settings[show_post_avatar]" id="show_post_avatar" <?php checked( $graphene_settings['show_post_avatar'] ); ?> value="true" /></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="show_post_author"><?php _e("Show post author's info", 'graphene' ); ?></label></th>
                        <td><input type="checkbox" name="graphene_settings[show_post_author]" id="show_post_author" <?php checked( $graphene_settings['show_post_author'] ); ?> value="true" /></td>
                    </tr>
                </table>
                
                <p class="submit clearfix"><input type="submit" class="button" value="<?php _e( 'Save All Options', 'graphene' ); ?>" /></p>
            </div>
        </div>
        
        
        <?php /* Excerpts Display Options */ ?>
        <div class="postbox non-essential-option">
            <div class="head-wrap">
                <div title="Click to toggle" class="handlediv"><br /></div>
        		<h3 class="hndle"><?php _e( 'Excerpts Display Options', 'graphene' ); ?></h3>
            </div>
            <div class="panel-wrap inside">
            	<table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="posts_show_excerpt"><?php _e( 'Show excerpts in front page', 'graphene' ); ?></label>
                        </th>
                        <td><input type="checkbox" name="graphene_settings[posts_show_excerpt]" id="posts_show_excerpt" <?php checked( $graphene_settings['posts_show_excerpt'] ); ?> value="true" /></td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="archive_full_content"><?php _e( 'Show full content in archive pages', 'graphene' ); ?></label>
                        </th>
                        <td>
                        	<input type="checkbox" name="graphene_settings[archive_full_content]" id="archive_full_content" <?php checked( $graphene_settings['archive_full_content'] ); ?> value="true" /><br />
                            <span class="description"><?php _e( 'Note: Archive pages include the archive for category, tags, time, and search results pages. Enabling this option will cause the full content of posts and pages listed in those archives to displayed instead of the excerpt, and truncated by the Read More tag if used.', 'graphene' ); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="show_excerpt_more"><?php _e("Show More link for manual excerpts", 'graphene' ); ?></label></th>
                        <td><input type="checkbox" name="graphene_settings[show_excerpt_more]" id="show_excerpt_more" <?php checked( $graphene_settings['show_excerpt_more'] ); ?> value="true" /></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="excerpt_html_tags"><?php _e("Retain these HTML tags in excerpts", 'graphene' ); ?></label></th>
                        <td>
                        	<input type="text" class="widefat code" name="graphene_settings[excerpt_html_tags]" id="excerpt_html_tags" value="<?php echo $graphene_settings['excerpt_html_tags']; ?>" /><br />
                        	<span class="description"><?php _e("Enter the HTML tags you'd like to retain in excerpts. For example, enter <code>&lt;p&gt;&lt;ul&gt;&lt;li&gt;</code> to retain <code>&lt;p&gt;</code>, <code>&lt;ul&gt;</code>, and <code>&lt;li&gt;</code> HTML tags.", 'graphene' ); ?></span>
                        </td>
                    </tr>
                </table>
                
                <p class="submit clearfix"><input type="submit" class="button" value="<?php _e( 'Save All Options', 'graphene' ); ?>" /></p>
            </div>
        </div>
      
        
        
        <?php /* Comments Display Options */ ?>
        <div class="postbox non-essential-option">
            <div class="head-wrap">
                <div title="Click to toggle" class="handlediv"><br /></div>
        		<h3 class="hndle"><?php _e( 'Comments Display Options', 'graphene' ); ?></h3>
            </div>
            <div class="panel-wrap inside">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="hide_allowedtags"><?php _e( 'Hide allowed tags in comment form', 'graphene' ); ?></label>
                        </th>
                        <td><input type="checkbox" name="graphene_settings[hide_allowedtags]" id="hide_allowedtags" <?php checked( $graphene_settings['hide_allowedtags'] ); ?> value="true" /></td>
                    </tr>
                </table>
                
                <p class="submit clearfix"><input type="submit" class="button" value="<?php _e( 'Save All Options', 'graphene' ); ?>" /></p>
            </div>
        </div>
        
            
        <?php /* Text Style Options */ ?>
        <div class="postbox non-essential-option">
            <div class="head-wrap">
                <div title="Click to toggle" class="handlediv"><br /></div>
        		<h3 class="hndle"><?php _e( 'Text Style Options', 'graphene' ); ?></h3>
            </div>
            <div class="panel-wrap inside">
            	<h4><?php _e( 'Google Webfonts', 'graphene' ); ?></h4>
                <table class="form-table">
                        <tr>
                            <th scope="row"><label for="graphene-webfont-families"><?php _e( 'Font families', 'graphene' ); ?></label></th>
                            <td>
                            	<textarea class="code" cols="40" rows="7" id="graphene-webfont-families" name="graphene_settings[webfont_families]"><?php echo $graphene_settings['webfont_families']; ?></textarea><br />
								<span class="description"><?php _e( 'Enter the Google Webfont font family string in the format that is used in the Webfont Loader, one font family per line.', 'graphene' ); ?></span><br />
				                <span class="description"><?php _e( 'Example:', 'graphene' ); ?> <code>Pontano+Sans::latin,latin-ext</code></span>
                            </td>
                        </tr>
                </table>
                
                <p><?php _e( 'The following options are CSS properties, so any valid CSS values for each particular property can be used.', 'graphene' ); ?></p>
                <p><?php _e( 'Some example CSS properties values:', 'graphene' ); ?></p>
                <table class="graphene-code-example">
                    <tr>
                        <th scope="row"><?php _e( 'Text font:', 'graphene' ); ?></th>
                        <td><code>arial</code>, <code>tahoma</code>, <code>georgia</code>, <code>'Trebuchet MS'</code></td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e( 'Text size and line height:', 'graphene' ); ?></th>
                        <td><code>12px</code>, <code>12pt</code>, <code>12em</code></td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e( 'Text weight:', 'graphene' ); ?></th>
                        <td><code>normal</code>, <code>bold</code>, <code>100</code>, <code>700</code></td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e( 'Text style:', 'graphene' ); ?></th>
                        <td><code>normal</code>, <code>italic</code>, <code>oblique</code></td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e( 'Text colour:', 'graphene' ); ?></th>
                        <td><code>blue</code>, <code>navy</code>, <code>red</code>, <code>#ff0000</code></td>
                    </tr>
                </table>
                
                <h4><?php _e( 'Header Text', 'graphene' ); ?></h4>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="header_title_font_type"><?php _e( 'Title text font', 'graphene' ); ?></label>
                        </th>
                        <td><input type="text" class="code" name="graphene_settings[header_title_font_type]" id="header_title_font_type" value="<?php echo $graphene_settings['header_title_font_type']; ?>" /></td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="header_title_font_size"><?php _e( 'Title text size', 'graphene' ); ?></label>
                        </th>
                        <td><input type="text" class="code" name="graphene_settings[header_title_font_size]" id="header_title_font_size" value="<?php echo $graphene_settings['header_title_font_size']; ?>" /></td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="header_title_font_weight"><?php _e( 'Title text weight', 'graphene' ); ?></label>
                        </th>
                        <td><input type="text" class="code" name="graphene_settings[header_title_font_weight]" id="header_title_font_weight" value="<?php echo $graphene_settings['header_title_font_weight']; ?>" /></td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="header_title_font_lineheight"><?php _e( 'Title text line height', 'graphene' ); ?></label>
                        </th>
                        <td><input type="text" class="code" name="graphene_settings[header_title_font_lineheight]" id="header_title_font_lineheight" value="<?php echo $graphene_settings['header_title_font_lineheight']; ?>" /></td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="header_title_font_style"><?php _e( 'Title text style', 'graphene' ); ?></label>
                        </th>
                        <td><input type="text" class="code" name="graphene_settings[header_title_font_style]" id="header_title_font_style" value="<?php echo $graphene_settings['header_title_font_style']; ?>" /></td>
                    </tr>
                </table>
                
                <table class="form-table" style="margin-top:30px;">               
                    <tr>
                        <th scope="row">
                            <label for="header_desc_font_type"><?php _e( 'Description text font', 'graphene' ); ?></label>
                        </th>
                        <td><input type="text" class="code" name="graphene_settings[header_desc_font_type]" id="header_desc_font_type" value="<?php echo $graphene_settings['header_desc_font_type']; ?>" /></td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="header_desc_font_size"><?php _e( 'Description text size', 'graphene' ); ?></label>
                        </th>
                        <td><input type="text" class="code" name="graphene_settings[header_desc_font_size]" id="header_desc_font_size" value="<?php echo $graphene_settings['header_desc_font_size']; ?>" /></td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="header_desc_font_weight"><?php _e( 'Description text weight', 'graphene' ); ?></label>
                        </th>
                        <td><input type="text" class="code" name="graphene_settings[header_desc_font_weight]" id="header_desc_font_weight" value="<?php echo $graphene_settings['header_desc_font_weight']; ?>" /></td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="header_desc_font_lineheight"><?php _e( 'Description text line height', 'graphene' ); ?></label>
                        </th>
                        <td><input type="text" class="code" name="graphene_settings[header_desc_font_lineheight]" id="header_desc_font_lineheight" value="<?php echo $graphene_settings['header_desc_font_lineheight']; ?>" /></td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="header_desc_font_style"><?php _e( 'Description text style', 'graphene' ); ?></label>
                        </th>
                        <td><input type="text" class="code" name="graphene_settings[header_desc_font_style]" id="header_desc_font_style" value="<?php echo $graphene_settings['header_desc_font_style']; ?>" /></td>
                    </tr>
                </table>
                
                <h4><?php _e( 'Content Text', 'graphene' ); ?></h4>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="content_font_type"><?php _e( 'Text font', 'graphene' ); ?></label>
                        </th>
                        <td><input type="text" class="code" name="graphene_settings[content_font_type]" id="content_font_type" value="<?php echo $graphene_settings['content_font_type']; ?>" /></td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="content_font_size"><?php _e( 'Text size', 'graphene' ); ?></label>
                        </th>
                        <td><input type="text" class="code" name="graphene_settings[content_font_size]" id="content_font_size" value="<?php echo $graphene_settings['content_font_size']; ?>" /></td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="content_font_lineheight"><?php _e( 'Text line height', 'graphene' ); ?></label>
                        </th>
                        <td><input type="text" class="code" name="graphene_settings[content_font_lineheight]" id="content_font_lineheight" value="<?php echo $graphene_settings['content_font_lineheight']; ?>" /></td>
                    </tr>
                </table>
                    
                <h4><?php _e( 'Link Text', 'graphene' ); ?></h4>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="link_decoration_normal"><?php _e( 'Text decoration (normal state )', 'graphene' ); ?></label>
                        </th>
                        <td><input type="text" class="code" name="graphene_settings[link_decoration_normal]" id="link_decoration_normal" value="<?php echo $graphene_settings['link_decoration_normal']; ?>" /></td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="link_decoration_hover"><?php _e( 'Text decoration (hover state )', 'graphene' ); ?></label>
                        </th>
                        <td><input type="text" class="code" name="graphene_settings[link_decoration_hover]" id="link_decoration_hover" value="<?php echo $graphene_settings['link_decoration_hover']; ?>" /></td>
                    </tr>
                </table>
                
                <p class="submit clearfix"><input type="submit" class="button" value="<?php _e( 'Save All Options', 'graphene' ); ?>" /></p>
            </div>
        </div>
		
        
        <?php /* Footer Widget Display Options */ ?>
        <div class="postbox non-essential-option">
            <div class="head-wrap">
                <div title="Click to toggle" class="handlediv"><br /></div>
        		<h3 class="hndle"><?php _e( 'Footer Widget Display Options', 'graphene' ); ?></h3>
            </div>
            <div class="panel-wrap inside">        
                <table class="form-table">
                    <tr>
                        <th scope="row" style="width:260px;">
                            <label for="footerwidget_column"><?php _e( 'Number of columns to display', 'graphene' ); ?></label>
                        </th>
                        <td><input type="text" class="code" name="graphene_settings[footerwidget_column]" id="footerwidget_column" value="<?php echo $graphene_settings['footerwidget_column']; ?>" maxlength="2" size="3" /></td>
                    </tr>
                    <?php if ( $graphene_settings['alt_home_footerwidget'] ) : ?>
                    <tr>
                        <th scope="row">
                            <label for="alt_footerwidget_column"><?php _e( 'Number of columns to display for front page footer widget', 'graphene' ); ?></label>
                        </th>
                        <td><input type="text" class="code" name="graphene_settings[alt_footerwidget_column]" id="alt_footerwidget_column" value="<?php echo $graphene_settings['alt_footerwidget_column']; ?>" maxlength="2" size="3" /></td>
                    </tr>
                    <?php endif; ?>
                </table>
                
                <p class="submit clearfix"><input type="submit" class="button" value="<?php _e( 'Save All Options', 'graphene' ); ?>" /></p>
            </div>
        </div>
            
        
        <?php /* Navigation Menu Display Options */ ?>
        <div class="postbox non-essential-option">
            <div class="head-wrap">
                <div title="Click to toggle" class="handlediv"><br /></div>
        		<h3 class="hndle"><?php _e( 'Navigation Menu Display Options', 'graphene' ); ?></h3>
            </div>
            <div class="panel-wrap inside">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="navmenu_child_width"><?php _e( 'Dropdown menu item width', 'graphene' ); ?></label>
                        </th>
                        <td><input type="text" class="code" name="graphene_settings[navmenu_child_width]" id="navmenu_child_width" value="<?php echo $graphene_settings['navmenu_child_width']; ?>" maxlength="3" size="3" /> px</td>
                    </tr>                    
                    <tr>
                        <th scope="row">
                            <label for="disable_menu_desc"><?php _e( 'Disable description in Header Menu', 'graphene' ); ?></label>
                        </th>
                        <td><input type="checkbox" name="graphene_settings[disable_menu_desc]" id="disable_menu_desc" <?php checked( $graphene_settings['disable_menu_desc'] ); ?> value="true" data-toggleOptions="true" /></td>
                    </tr>
                </table>
                <table class="form-table<?php if ( $graphene_settings['disable_menu_desc'] == true ) echo ' hide'; ?>">
                    <tr>
                        <th scope="row">
                            <label for="navmenu_home_desc"><?php _e( 'Description for default menu "Home" item', 'graphene' ); ?></label>
                        </th>
                        <td>
                        	<input type="text" size="60" name="graphene_settings[navmenu_home_desc]" id="navmenu_home_desc" value="<?php echo $graphene_settings['navmenu_home_desc']; ?>" /><br />
                            <span class="description"><?php _e( 'Only required if you need a description in the navigation menu and you are not using a custom menu.', 'graphene' ); ?></span>
                        </td>
                    </tr>
                </table>
                
                <p class="submit clearfix"><input type="submit" class="button" value="<?php _e( 'Save All Options', 'graphene' ); ?>" /></p>
            </div>
        </div>
        
            
        <?php /* Miscellaneous Display Options */ ?>
        <div class="postbox">
            <div class="head-wrap">
                <div title="Click to toggle" class="handlediv"><br /></div>
        		<h3 class="hndle"><?php _e( 'Miscellaneous Display Options', 'graphene' ); ?></h3>
            </div>
            <div class="panel-wrap inside">
                <h4 class="non-essential-option"><?php _e( 'Site title options', 'graphene' ); ?></h4>
                <p class="non-essential-option"><?php _e( 'Use these tags to customise your own site title structure: <code>#site-name</code>, <code>#site-desc</code>, <code>#post-title</code>', 'graphene' ); ?></p>
                <table class="form-table non-essential-option">
                	<tr>
                        <th scope="row" style="width:250px;">
                        	<label for="custom_site_title_frontpage"><?php _e("Custom front page site title", 'graphene' ); ?></label>
                        </th>
                        <td>
                        	<input type="text" name="graphene_settings[custom_site_title_frontpage]" id="custom_site_title_frontpage" class="widefat code" value="<?php echo stripslashes( $graphene_settings['custom_site_title_frontpage'] ); ?>" />
                            <span class="description"><?php _e( 'Defaults to <code>#site-name &raquo; #site-desc</code>. The <code>#post-title</code> tag cannot be used here.', 'graphene' ); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" style="width:250px;">
                        	<label for="custom_site_title_content"><?php _e("Custom content pages site title", 'graphene' ); ?></label>
                        </th>
                        <td>
                        	<input type="text" name="graphene_settings[custom_site_title_content]" id="custom_site_title_content" class="widefat code" value="<?php echo stripslashes( $graphene_settings['custom_site_title_content'] ); ?>" />
                            <span class="description"><?php _e( 'Defaults to <code>#post-title &raquo; #site-name</code>.', 'graphene' ); ?></span>
                        </td>
                    </tr>
                </table>
                
                <h4><?php _e( 'Favicon options', 'graphene' ); ?></h4>
                <table class="form-table">
                    <tr>
                        <th scope="row" style="width:250px;">
                        	<label for="favicon_url"><?php _e( 'Favicon URL', 'graphene' ); ?></label>
                        </th>
                        <td>
                        	<input type="text" class="widefat code" value="<?php echo esc_url( $graphene_settings['favicon_url'] ); ?>" name="graphene_settings[favicon_url]" id="favicon_url" />
                                <span class="description"><a href="#" class="upload_image_button"><?php _e( 'Upload or select image from gallery', 'graphene' );?></a> - <?php _e( 'Simply enter the full URL to your favicon file here to enable favicon. Make sure you include the <code>http://</code> in front of the URL as well. Or use the WordPress media uploader to upload an image, or select one from the media library.', 'graphene' ); ?></span>                                
                        </td>
                    </tr>
                </table>
                
                <h4><?php _e( 'WordPress Editor options', 'graphene' ); ?></h4>
                <table class="form-table">
                    <tr>
                        <th scope="row" style="width:250px;">
                        	<label for="disable_editor_style"><?php _e( 'Disable custom editor styles', 'graphene' ); ?></label>
                        </th>
                        <td><input type="checkbox" name="graphene_settings[disable_editor_style]" id="disable_editor_style" <?php checked( $graphene_settings['disable_editor_style'] ); ?> value="true" /></td>
                    </tr>
                </table>
                
                <p class="submit clearfix"><input type="submit" class="button" value="<?php _e( 'Save All Options', 'graphene' ); ?>" /></p>
            </div>
        </div>
                    
                    
        <?php /* Custom CSS */ ?>
        <div class="postbox non-essential-option">
            <div class="head-wrap">
            	<div title="Click to toggle" class="handlediv"><br /></div>
            	<h3 class="hndle"><?php _e( 'Custom CSS', 'graphene' ); ?></h3>
            </div>
            <div class="panel-wrap inside">
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="custom_css"><?php _e( 'Custom CSS styles', 'graphene' ); ?></label></th>
                        <td>
                        	<span class="description"><?php _e("You can enter your own CSS codes below to modify any other aspects of the theme's appearance that is not included in the options.", 'graphene' ); ?></span>
                        	<textarea name="graphene_settings[custom_css]" id="custom_css" cols="60" rows="20" class="widefat code"><?php echo stripslashes( $graphene_settings['custom_css'] ); ?></textarea>
                        </td>
                    </tr>
                </table>
                
                <p class="submit clearfix"><input type="submit" class="button" value="<?php _e( 'Save All Options', 'graphene' ); ?>" /></p>
            </div>
        </div>                  
        
<?php } // Closes the graphene_options_display() function definition 
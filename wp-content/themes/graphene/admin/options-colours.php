<?php
function graphene_options_colours() { 
    global $graphene_settings;
    ?>
        
    <input type="hidden" name="graphene_colours" value="true" />
        
        <p><?php _e("Changing colours for your website involves a lot more than just trial and error. Simply mixing and matching colours without regard to their compatibility may do more damage than good to your site's aesthetics.", 'graphene' ); ?>
        <?php printf( __("It's generally a good idea to stick to colours from colour pallettes that are aesthetically pleasing. Try the %s website for a kickstart on some colour palettes you can use.", 'graphene' ), '<a href="http://www.colourlovers.com/palettes/">COLOURlovers</a>' ); ?></p>
        <p><?php printf( __( "When you've got the perfect combination, you can even share it with fellow Graphene theme users through the %s.", 'graphene' ), '<a href="http://forum.graphene-theme.com/">' . __( 'Support Forum', 'graphene' ) . '</a>' ); ?></p>
        <p><?php _e( '<strong>Note:</strong> The previews work best on modern Gecko- and Webkit-based browsers, such as Mozilla Firefox and Google Chrome.', 'graphene' ); ?></p>
        <p><?php _e( '<strong>Note:</strong> To reset any of the colours to their default value, just click on "Clear" beside the colour field and save the settings. The theme will automatically revert the value to the default colour.', 'graphene' ); ?></p>
        
        <h4><?php _e( 'Colour presets', 'graphene' ); ?></h4>
        <p>
            <?php _e( 'These are some colour presets that you may use, either as is or tweak them further to your liking.', 'graphene' ); ?>
        </p>
        <?php /* These colour presets are handled using javascript. See graphene/admin/js/admin.js */ ?>
        <select class="colour-presets" name="graphene_settings[colour_preset]">
            <option value="default" <?php selected( $graphene_settings['colour_preset'], 'default' ); ?>><?php _e( 'Default', 'graphene' ); ?></option>
            <option value="dream-magnet" <?php selected( $graphene_settings['colour_preset'], 'dream-magnet' ); ?>><?php _e( 'Dream Magnet', 'graphene' ); ?></option>
            <option value="curiosity-killed" <?php selected( $graphene_settings['colour_preset'], 'curiosity-killed' ); ?>><?php _e( 'Curiosity Killed', 'graphene' ); ?></option>
            <option value="zesty-orange" <?php selected( $graphene_settings['colour_preset'], 'zesty-orange' ); ?>><?php _e( 'Zesty Orange', 'graphene' ); ?></option>
        </select>
        
        <div class="postbox non-essential-option" style="margin-top: 20px;">
            <div class="head-wrap">
                <div title="Click to toggle" class="handlediv"><br /></div>
                <h3 class="hndle"><?php _e( 'Top bar', 'graphene' ); ?></h3>
            </div>
            <div class="panel-wrap inside">
                <table class="form-table">
                	<tr>
                        <th scope="row"><?php _e( 'Top bar preview', 'graphene' ); ?></th>
                        <td><div class="top-bar-preview"><div class="top-bar">
                				<div class="profiles">
                                <a class="mysocial social-rss" href="#"><img src="<?php echo get_template_directory_uri(); ?>/images/social/rss.png"></a></div></div><div class="header"></div></div>
                        </td>
                    </tr>
                    <?php                                                 
                        $colour_opts = array(
                            'top_bar_top_bg' => array( 'title' => __( 'Top background', 'graphene' ) ),
							'top_bar_bottom_bg' => array( 'title' => __( 'Bottom background', 'graphene' ) ),
							'top_bar_border' => array( 'title' => __( 'Bottom border', 'graphene' ) ),
							'top_bar_header_border' => array( 'title' => __( 'Header top border', 'graphene' ) ),
                        );
                        foreach ( $colour_opts as $key => $colour_opt) :
                    ?>
                    <tr>
                        <th scope="row"><label for="<?php echo $key; ?>"><?php echo $colour_opt['title']; ?></label></th>
                        <td>
                            <input type="text" class="code color" name="graphene_settings[<?php echo $key; ?>]" id="<?php echo $key; ?>" value="<?php echo $graphene_settings[$key]; ?>" />
                            <a href="#" class="clear-color"><?php _e( 'Clear', 'graphene' ); ?></a>
                            <div class="colorpicker" id="picker_<?php echo $key; ?>"></div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
                
                <p class="submit clearfix"><input type="submit" class="button" value="<?php _e( 'Save All Options', 'graphene' ); ?>" /></p>
            </div>
        </div>
        
        <div class="postbox non-essential-option">
            <div class="head-wrap">
                <div title="Click to toggle" class="handlediv"><br /></div>
                <h3 class="hndle"><?php _e( 'Primary menu', 'graphene' ); ?></h3>
            </div>
            <div class="panel-wrap inside">
                <h4><?php _e( 'Default state', 'graphene' ); ?></h4>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e( 'Menu preview', 'graphene' ); ?></th>
                        <td><div class="primary-menu-preview"><div id="header-menu-wrap" class="clearfix"><ul id="header-menu" class="clearfix"><li class="menu-item current-menu-item menu-item-ancestor"><a href="#"><strong><?php _e( 'Active menu item', 'graphene' ); ?></strong><span><?php _e( 'Menu item description', 'graphene' ); ?></span></a></li><li class="menu-item menu-item-ancestor normal-menu-item"><a href="#"><strong><?php _e( 'Menu item', 'graphene' ); ?></strong><span><?php _e( 'Menu item description', 'graphene' ); ?></span></a></li></ul></div><div class="menu-bottom-shadow"></div></div></td>
                    </tr>
                    <?php                                                 
                        $colour_opts = array(
                            'menu_primary_top_bg' => array( 'title' => __( 'Top background', 'graphene' ) ),
                            'menu_primary_bottom_bg' => array( 'title' => __( 'Bottom background', 'graphene' ) ),
                            'menu_primary_border' => array( 'title' => __( 'Bottom border', 'graphene' ) ),
                            'menu_primary_item' => array( 'title' => __( 'Menu item', 'graphene' ) ),
                            'menu_primary_description' => array( 'title' => __( 'Menu item description', 'graphene' ) ),
                        );
                                                
                        foreach ( $colour_opts as $key => $colour_opt) :
                    ?>
                    <tr>
                        <th scope="row"><label for="<?php echo $key; ?>"><?php echo $colour_opt['title']; ?></label></th>
                        <td>
                            <input type="text" class="code color" name="graphene_settings[<?php echo $key; ?>]" id="<?php echo $key; ?>" value="<?php echo $graphene_settings[$key]; ?>" />
                            <a href="#" class="clear-color"><?php _e( 'Clear', 'graphene' ); ?></a>
                            <div class="colorpicker" id="picker_<?php echo $key; ?>"></div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
                
                <h4><?php _e( 'Hover and active state', 'graphene' ); ?></h4>
                <table class="form-table">
                    <?php                                                 
                        $colour_opts = array(
                            'menu_primary_active_top_bg' => array( 'title' => __( 'Top background', 'graphene' ) ),
                            'menu_primary_active_bottom_bg' => array( 'title' => __( 'Bottom background', 'graphene' ) ),
                            'menu_primary_active_item' => array( 'title' => __( 'Menu item', 'graphene' ) ),
                            'menu_primary_active_description' => array( 'title' => __( 'Menu item description', 'graphene' ) ),
                        );
                                                
                        foreach ( $colour_opts as $key => $colour_opt) :
                    ?>
                    <tr>
                        <th scope="row"><label for="<?php echo $key; ?>"><?php echo $colour_opt['title']; ?></label></th>
                        <td>
                            <input type="text" class="code color" name="graphene_settings[<?php echo $key; ?>]" id="<?php echo $key; ?>" value="<?php echo $graphene_settings[$key]; ?>" />
                            <a href="#" class="clear-color"><?php _e( 'Clear', 'graphene' ); ?></a>
                            <div class="colorpicker" id="picker_<?php echo $key; ?>"></div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
                
                <h4><?php _e( 'Dropdown menu (default state)', 'graphene' ); ?></h4>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e( 'Menu preview', 'graphene' ); ?></th>
                        <td><div class="primary-menu-preview dropdown">
                        <ul class="sub-menu">
                            <li class="menu-item menu-item-ancestor normal-menu-item"><a href="#"><?php _e( 'Dropdown menu item', 'graphene' ); ?></a></li>
                            <li class="menu-item menu-item-ancestor current-menu-item"><a href="#"><?php _e( 'Active menu item', 'graphene' ); ?></a></li>
                            <li class="menu-item menu-item-ancestor normal-menu-item"><a href="#"><?php _e( 'Dropdown menu item', 'graphene' ); ?></a></li>
                        </ul>
                        
                        </div></td>
                    </tr>
                    <?php                                                 
                        $colour_opts = array(
                            'menu_primary_dd_top_bg' => array( 'title' => __( 'Top background', 'graphene' ) ),
                            'menu_primary_dd_bottom_bg' => array( 'title' => __( 'Bottom background', 'graphene' ) ),
                            'menu_primary_dd_item' => array( 'title' => __( 'Menu item', 'graphene' ) ),
                            'menu_primary_dd_shadow' => array( 'title' => __( 'Box shadow', 'graphene' ) ),
                        );
                                                
                        foreach ( $colour_opts as $key => $colour_opt) :
                    ?>
                    <tr>
                        <th scope="row"><label for="<?php echo $key; ?>"><?php echo $colour_opt['title']; ?></label></th>
                        <td>
                            <input type="text" class="code color" name="graphene_settings[<?php echo $key; ?>]" id="<?php echo $key; ?>" value="<?php echo $graphene_settings[$key]; ?>" />
                            <a href="#" class="clear-color"><?php _e( 'Clear', 'graphene' ); ?></a>
                            <div class="colorpicker" id="picker_<?php echo $key; ?>"></div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
                
                <h4><?php _e( 'Dropdown menu (hover and active state)', 'graphene' ); ?></h4>
                <table class="form-table">
                    <?php                                                 
                        $colour_opts = array(
                            'menu_primary_dd_active_top_bg' => array( 'title' => __( 'Top background', 'graphene' ) ),
                            'menu_primary_dd_active_bottom_bg' => array( 'title' => __( 'Bottom background', 'graphene' ) ),
                            'menu_primary_dd_active_item' => array( 'title' => __( 'Menu item', 'graphene' ) ),
                        );
                                                
                        foreach ( $colour_opts as $key => $colour_opt) :
                    ?>
                    <tr>
                        <th scope="row"><label for="<?php echo $key; ?>"><?php echo $colour_opt['title']; ?></label></th>
                        <td>
                            <input type="text" class="code color" name="graphene_settings[<?php echo $key; ?>]" id="<?php echo $key; ?>" value="<?php echo $graphene_settings[$key]; ?>" />
                            <a href="#" class="clear-color"><?php _e( 'Clear', 'graphene' ); ?></a>
                            <div class="colorpicker" id="picker_<?php echo $key; ?>"></div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
                
                <p class="submit clearfix"><input type="submit" class="button" value="<?php _e( 'Save All Options', 'graphene' ); ?>" /></p>
            </div>
        </div>
        
        
        <div class="postbox non-essential-option" style="margin-top: 20px;">
            <div class="head-wrap">
                <div title="Click to toggle" class="handlediv"><br /></div>
                <h3 class="hndle"><?php _e( 'Secondary menu', 'graphene' ); ?></h3>
            </div>
            <div class="panel-wrap inside">
                <h4><?php _e( 'Default state', 'graphene' ); ?></h4>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e( 'Menu preview', 'graphene' ); ?></th>
                        <td><div class="secondary-menu-preview"><div class="clearfix" id="secondary-menu-wrap"><ul class="clearfix" id="secondary-menu">
                                <li class="menu-item menu-item-ancestor current-menu-item"><a href="#"><?php _e( 'Active menu item', 'graphene' ); ?></a></li>
                                <li class="menu-item menu-item-ancestor normal-menu-item"><a href="#"><?php _e( 'Menu item', 'graphene' ); ?></a></li>
                            </ul></div><div class="menu-bottom-shadow"></div></div>
                        </td>
                    </tr>
                    <?php                                                 
                        $colour_opts = array(
                            'menu_sec_bg' => array( 'title' => __( 'Background', 'graphene' ) ),
                            'menu_sec_border' => array( 'title' => __( 'Top border', 'graphene' ) ),
                            'menu_sec_item' => array( 'title' => __( 'Menu item', 'graphene' ) ),
                        );
                                                
                        foreach ( $colour_opts as $key => $colour_opt) :
                    ?>
                    <tr>
                        <th scope="row"><label for="<?php echo $key; ?>"><?php echo $colour_opt['title']; ?></label></th>
                        <td>
                            <input type="text" class="code color" name="graphene_settings[<?php echo $key; ?>]" id="<?php echo $key; ?>" value="<?php echo $graphene_settings[$key]; ?>" />
                            <a href="#" class="clear-color"><?php _e( 'Clear', 'graphene' ); ?></a>
                            <div class="colorpicker" id="picker_<?php echo $key; ?>"></div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
                
                <h4><?php _e( 'Active and hover state', 'graphene' ); ?></h4>
                <table class="form-table">
                    <?php                                                 
                        $colour_opts = array(
                            'menu_sec_active_bg' => array( 'title' => __( 'Background', 'graphene' ) ),
                            'menu_sec_active_item' => array( 'title' => __( 'Menu item', 'graphene' ) ),
                        );
                                                
                        foreach ( $colour_opts as $key => $colour_opt) :
                    ?>
                    <tr>
                        <th scope="row"><label for="<?php echo $key; ?>"><?php echo $colour_opt['title']; ?></label></th>
                        <td>
                            <input type="text" class="code color" name="graphene_settings[<?php echo $key; ?>]" id="<?php echo $key; ?>" value="<?php echo $graphene_settings[$key]; ?>" />
                            <a href="#" class="clear-color"><?php _e( 'Clear', 'graphene' ); ?></a>
                            <div class="colorpicker" id="picker_<?php echo $key; ?>"></div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
                
                <h4><?php _e( 'Dropdown menu (default state)', 'graphene' ); ?></h4>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e( 'Menu preview', 'graphene' ); ?></th>
                        <td><div class="secondary-menu-preview"><ul class="clearfix sub-menu">
                                <li class="menu-item menu-item-ancestor normal-menu-item"><a href="#"><?php _e( 'Menu item', 'graphene' ); ?></a></li>
                                <li class="menu-item menu-item-ancestor current-menu-item"><a href="#"><?php _e( 'Active menu item', 'graphene' ); ?></a></li>
                                <li class="menu-item menu-item-ancestor normal-menu-item"><a href="#"><?php _e( 'Menu item', 'graphene' ); ?></a></li>
                            </ul></div>
                        </td>
                    </tr>
                    <?php                                                 
                        $colour_opts = array(
                            'menu_sec_dd_top_bg' => array( 'title' => __( 'Top background', 'graphene' ) ),
                            'menu_sec_dd_bottom_bg' => array( 'title' => __( 'Bottom background', 'graphene' ) ),
                            'menu_sec_dd_item' => array( 'title' => __( 'Menu item', 'graphene' ) ),
                            'menu_sec_dd_shadow' => array( 'title' => __( 'Box shadow', 'graphene' ) ),
                        );
                                                
                        foreach ( $colour_opts as $key => $colour_opt) :
                    ?>
                    <tr>
                        <th scope="row"><label for="<?php echo $key; ?>"><?php echo $colour_opt['title']; ?></label></th>
                        <td>
                            <input type="text" class="code color" name="graphene_settings[<?php echo $key; ?>]" id="<?php echo $key; ?>" value="<?php echo $graphene_settings[$key]; ?>" />
                            <a href="#" class="clear-color"><?php _e( 'Clear', 'graphene' ); ?></a>
                            <div class="colorpicker" id="picker_<?php echo $key; ?>"></div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
                
                <h4><?php _e( 'Dropdown menu (hover and active state)', 'graphene' ); ?></h4>
                <table class="form-table">
                    <?php                                                 
                        $colour_opts = array(
                            'menu_sec_dd_active_top_bg' => array( 'title' => __( 'Top background', 'graphene' ) ),
                            'menu_sec_dd_active_bottom_bg' => array( 'title' => __( 'Bottom background', 'graphene' ) ),
                            'menu_sec_dd_active_item' => array( 'title' => __( 'Menu item', 'graphene' ) ),
                        );
                                                
                        foreach ( $colour_opts as $key => $colour_opt) :
                    ?>
                    <tr>
                        <th scope="row"><label for="<?php echo $key; ?>"><?php echo $colour_opt['title']; ?></label></th>
                        <td>
                            <input type="text" class="code color" name="graphene_settings[<?php echo $key; ?>]" id="<?php echo $key; ?>" value="<?php echo $graphene_settings[$key]; ?>" />
                            <a href="#" class="clear-color"><?php _e( 'Clear', 'graphene' ); ?></a>
                            <div class="colorpicker" id="picker_<?php echo $key; ?>"></div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
                
                <p class="submit clearfix"><input type="submit" class="button" value="<?php _e( 'Save All Options', 'graphene' ); ?>" /></p>
            </div>
        </div>
        
        
        <div class="postbox non-essential-option">
            <div class="head-wrap">
                <div title="Click to toggle" class="handlediv"><br /></div>
                <h3 class="hndle"><?php _e( 'Content area', 'graphene' ); ?></h3>
            </div>
            <div class="panel-wrap inside">
                <table class="form-table">
                    <?php                                                 
                        $colour_opts = array(
                            'bg_content_wrapper' => array( 'title' => __( 'Main content wrapper background', 'graphene' ) ),
                            'bg_content' => array( 'title' => __( 'Post and pages content background', 'graphene' ) ),
                            'bg_meta_border' => array( 'title' => __( 'Post meta and footer border', 'graphene' ) ),
                            'bg_post_top_border' => array( 'title' => __( 'Post and pages top border', 'graphene' ) ),
                            'bg_post_bottom_border' => array( 'title' => __( 'Post and pages bottom border', 'graphene' ) ),
			    'bg_sticky_content' => array( 'title' => __( 'Sticky posts content background', 'graphene' ) ),
			    'bg_child_page_content' => array( 'title' => __( 'Child pages content background', 'graphene' ) ),
                        );
                                                
                        foreach ( $colour_opts as $key => $colour_opt) :
                    ?>
                    <tr>
                        <th scope="row"><label for="<?php echo $key; ?>"><?php echo $colour_opt['title']; ?></label></th>
                        <td>
                            <input type="text" class="code color" name="graphene_settings[<?php echo $key; ?>]" id="<?php echo $key; ?>" value="<?php echo $graphene_settings[$key]; ?>" />
                            <a href="#" class="clear-color"><?php _e( 'Clear', 'graphene' ); ?></a>
                            <div class="colorpicker" id="picker_<?php echo $key; ?>"></div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
                
                <p class="submit clearfix"><input type="submit" class="button" value="<?php _e( 'Save All Options', 'graphene' ); ?>" /></p>
            </div>
        </div>
        
        <div class="postbox non-essential-option">
            <div class="head-wrap">
                <div title="Click to toggle" class="handlediv"><br /></div>
                <h3 class="hndle"><?php _e( 'Widgets', 'graphene' ); ?></h3>
            </div>
            <div class="panel-wrap inside">
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e( 'Widget preview', 'graphene' ); ?></th>
                        <td><div class="sidebar graphene"><div class="sidebar-wrap"><h3><?php _e( 'Widget title', 'graphene' ); ?></h3><ul><li><?php _e( 'List item', 'graphene' ); ?> 1</li><li><?php _e( 'List item', 'graphene' ); ?> 2</li><li><a href="#"><?php _e( 'List item', 'graphene' ); ?> 3</a></li></ul></div></div></td>
                    </tr>
                    <?php 
                        $colour_opts = array(
                            'bg_widget_item' => array( 'title' => __( 'Widget item background', 'graphene' ) ),
                            'bg_widget_list' => array( 'title' => __( 'Widget item list border', 'graphene' ) ),
                            'bg_widget_header_border' => array( 'title' => __( 'Widget header border', 'graphene' ) ),
                            'bg_widget_title' => array( 'title' => __( 'Widget title colour', 'graphene' ) ),
                            'bg_widget_title_textshadow' => array( 'title' => __( 'Widget title text shadow colour', 'graphene' ) ),
                            'bg_widget_header_bottom' => array( 'title' => __( 'Widget header gradient bottom colour', 'graphene' ) ),
                            'bg_widget_header_top' => array( 'title' => __( 'Widget header gradient top colour', 'graphene' ) ),
                            'bg_widget_box_shadow' => array( 'title' => __( 'Widget box shadow', 'graphene' ) ),
                        );

                        foreach ( $colour_opts as $key => $colour_opt) :
                    ?>
                    <tr>
                        <th scope="row"><label for="<?php echo $key; ?>"><?php echo $colour_opt['title']; ?></label></th>
                        <td>
                            <input type="text" class="code color" name="graphene_settings[<?php echo $key; ?>]" id="<?php echo $key; ?>" value="<?php echo $graphene_settings[$key]; ?>" />
                            <a href="#" class="clear-color"><?php _e( 'Clear', 'graphene' ); ?></a>
                            <div class="colorpicker" id="picker_<?php echo $key; ?>"></div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    
                </table>
                
                <p class="submit clearfix"><input type="submit" class="button" value="<?php _e( 'Save All Options', 'graphene' ); ?>" /></p>
            </div>
        </div>
        
        
        <div class="postbox non-essential-option">
            <div class="head-wrap">
                <div title="Click to toggle" class="handlediv"><br /></div>
                <h3 class="hndle"><?php _e( 'Slider', 'graphene' ); ?></h3>
            </div>
            <div class="panel-wrap inside">
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e( 'Slider background preview', 'graphene' ); ?></th>
                        <td><div id="grad-box"></div></td>
                    </tr>
                    <?php 
                        $colour_opts = array(
                            'bg_slider_top' => array( 'title' => __( 'Slider top left colour', 'graphene' ) ),
                            'bg_slider_bottom' => array( 'title' => __( 'Slider bottom right colour', 'graphene' ) ),
                        );

                        foreach ( $colour_opts as $key => $colour_opt) :
                    ?>
                    <tr>
                        <th scope="row"><label for="<?php echo $key; ?>"><?php echo $colour_opt['title']; ?></label></th>
                        <td>
                            <input type="text" class="code color" name="graphene_settings[<?php echo $key; ?>]" id="<?php echo $key; ?>" value="<?php echo $graphene_settings[$key]; ?>" />
                            <a href="#" class="clear-color"><?php _e( 'Clear', 'graphene' ); ?></a>
                            <div class="colorpicker" id="picker_<?php echo $key; ?>"></div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    
                </table>
                
                <p class="submit clearfix"><input type="submit" class="button" value="<?php _e( 'Save All Options', 'graphene' ); ?>" /></p>
            </div>
        </div>
                
        
        <div class="postbox non-essential-option">
            <div class="head-wrap">
                <div title="Click to toggle" class="handlediv"><br /></div>
                <h3 class="hndle"><?php _e( 'Block buttons', 'graphene' ); ?></h3>
            </div>
            <div class="panel-wrap inside">
                <table class="form-table">
                    <tr class="colour-preview">
                        <th scope="row"><?php _e( 'Block button preview', 'graphene' ); ?></th>
                        <td><a class="block-button" href="#"><?php _e( 'Button label', 'graphene' ); ?></a></td>
                    </tr>
                    <?php 
                        $colour_opts = array(
                            'bg_button' => array( 'title' => __( 'Button background colour', 'graphene' ) ),
                            'bg_button_label' => array( 'title' => __( 'Button label colour', 'graphene' ) ),
                            'bg_button_label_textshadow' => array( 'title' => __( 'Button label text shadow', 'graphene' ) ),
                            'bg_button_box_shadow' => array( 'title' => __( 'Button box shadow', 'graphene' ) ),
                        );

                        foreach ( $colour_opts as $key => $colour_opt) :
                    ?>
                    <tr>
                        <th scope="row"><label for="<?php echo $key; ?>"><?php echo $colour_opt['title']; ?></label></th>
                        <td>
                            <input type="text" class="code color" name="graphene_settings[<?php echo $key; ?>]" id="<?php echo $key; ?>" value="<?php echo $graphene_settings[$key]; ?>" />
                            <a href="#" class="clear-color"><?php _e( 'Clear', 'graphene' ); ?></a>
                            <div class="colorpicker" id="picker_<?php echo $key; ?>"></div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    
                </table>
                
                <p class="submit clearfix"><input type="submit" class="button" value="<?php _e( 'Save All Options', 'graphene' ); ?>" /></p>
            </div>
        </div>
                
        
        <div class="postbox non-essential-option">
            <div class="head-wrap">
                <div title="Click to toggle" class="handlediv"><br /></div>
                <h3 class="hndle"><?php _e( 'Archive title', 'graphene' ); ?></h3>
            </div>
            <div class="panel-wrap inside">
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e( 'Archive title preview', 'graphene' ); ?></th>
                        <td><div class="archive-title-preview"><span class="page-title"><?php _e( 'Archive title', 'graphene' ); ?>: <span><?php _e( 'Sample title', 'graphene' ); ?></span></span></div></td>
                    </tr>
                    <?php 
                        $colour_opts = array(
                            'bg_archive_left' => array( 'title' => __( 'Archive background gradient left colour', 'graphene' ) ),
                                                        'bg_archive_right' => array( 'title' => __( 'Archive background gradient right colour', 'graphene' ) ),
                            'bg_archive_label' => array( 'title' => __( 'Archive label colour', 'graphene' ) ),
                            'bg_archive_text' => array( 'title' => __( 'Archive text colour', 'graphene' ) ),
                                                        'bg_archive_textshadow' => array( 'title' => __( 'Archive label and text shadow colour', 'graphene' ) ),
                        );

                        foreach ( $colour_opts as $key => $colour_opt) :
                    ?>
                    <tr>
                        <th scope="row"><label for="<?php echo $key; ?>"><?php echo $colour_opt['title']; ?></label></th>
                        <td>
                            <input type="text" class="code color" name="graphene_settings[<?php echo $key; ?>]" id="<?php echo $key; ?>" value="<?php echo $graphene_settings[$key]; ?>" />
                            <a href="#" class="clear-color"><?php _e( 'Clear', 'graphene' ); ?></a>
                            <div class="colorpicker" id="picker_<?php echo $key; ?>"></div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    
                </table>
                
                <p class="submit clearfix"><input type="submit" class="button" value="<?php _e( 'Save All Options', 'graphene' ); ?>" /></p>
            </div>
        </div>
        
        
        <div class="postbox non-essential-option">
            <div class="head-wrap">
                <div title="Click to toggle" class="handlediv"><br /></div>
                <h3 class="hndle"><?php _e( 'Comments area', 'graphene' ); ?></h3>
            </div>
            <div class="panel-wrap inside">
                <table class="form-table">
                    <?php 
                        $colour_opts = array(
                            'bg_comments' => array( 'title' => __( 'Comments background', 'graphene' ) ),
                            'comments_text_colour' => array( 'title' => __( 'Comments text', 'graphene' ) ),
                            'threaded_comments_border' => array( 'title' => __( 'Threaded comments border', 'graphene' ) ),
                            'bg_author_comments' => array( 'title' => __( 'Author comments background', 'graphene' ) ),
                            'bg_author_comments_border' => array( 'title' => __( 'Author comments top border', 'graphene' ) ),
                            'author_comments_text_colour' => array( 'title' => __( 'Author comments text', 'graphene' ) ),
                            'bg_comment_form' => array( 'title' => __( 'Comment form background', 'graphene' ) ),
                            'comment_form_text' => array( 'title' => __( 'Comment form text', 'graphene' ) ),
                        );
                        foreach ( $colour_opts as $key => $colour_opt) :
                    ?>
                    <tr>
                        <th scope="row"><label for="<?php echo $key; ?>"><?php echo $colour_opt['title']; ?></label></th>
                        <td>
                            <input type="text" class="code color" name="graphene_settings[<?php echo $key; ?>]" id="<?php echo $key; ?>" value="<?php echo $graphene_settings[$key]; ?>" />
                            <a href="#" class="clear-color"><?php _e( 'Clear', 'graphene' ); ?></a>
                            <div class="colorpicker" id="picker_<?php echo $key; ?>"></div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
                
                <p class="submit clearfix"><input type="submit" class="button" value="<?php _e( 'Save All Options', 'graphene' ); ?>" /></p>
            </div>
        </div>
        
        
        <div class="postbox non-essential-option">
            <div class="head-wrap">
                <div title="Click to toggle" class="handlediv"><br /></div>
                <h3 class="hndle"><?php _e( 'Content text', 'graphene' ); ?></h3>
            </div>
            <div class="panel-wrap inside">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="content_font_colour"><?php _e( 'Content text', 'graphene' ); ?></label>
                        </th>
                        <td>
                            <input type="text" class="code color" name="graphene_settings[content_font_colour]" id="content_font_colour" value="<?php echo $graphene_settings['content_font_colour']; ?>" />
                            <a href="#" class="clear-color"><?php _e( 'Clear', 'graphene' ); ?></a>
                            <div class="colorpicker" id="picker_content_font_colour"></div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="content_font_colour"><?php _e( 'Title text', 'graphene' ); ?></label>
                        </th>
                        <td>
                            <input type="text" class="code color" name="graphene_settings[title_font_colour]" id="title_font_colour" value="<?php echo $graphene_settings['title_font_colour']; ?>" />
                            <a href="#" class="clear-color"><?php _e( 'Clear', 'graphene' ); ?></a>
                            <div class="colorpicker" id="picker_title_font_colour"></div>
                        </td>
                    </tr>
                    <?php 
                        $colour_opts = array(
                            'link_colour_normal' => array( 'title' => __( 'Link colour (normal state)', 'graphene' ) ),
                            'link_colour_visited' => array( 'title' => __( 'Link colour (visited state)', 'graphene' ) ),
                            'link_colour_hover' => array( 'title' => __( 'Link colour (hover state)', 'graphene' ) ),
                        );

                        foreach ( $colour_opts as $key => $colour_opt) :
                    ?>
                    <tr>
                        <th scope="row"><label for="<?php echo $key; ?>"><?php echo $colour_opt['title']; ?></label></th>
                        <td>
                            <input type="text" class="code color" name="graphene_settings[<?php echo $key; ?>]" id="<?php echo $key; ?>" value="<?php echo $graphene_settings[$key]; ?>" />
                            <a href="#" class="clear-color"><?php _e( 'Clear', 'graphene' ); ?></a>
                            <div class="colorpicker" id="picker_<?php echo $key; ?>"></div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
                
                <p class="submit clearfix"><input type="submit" class="button" value="<?php _e( 'Save All Options', 'graphene' ); ?>" /></p>
            </div>
        </div> 
        
        <div class="postbox non-essential-option" style="margin-top: 20px;">
            <div class="head-wrap">
                <div title="Click to toggle" class="handlediv"><br /></div>
                <h3 class="hndle"><?php _e( 'Footer', 'graphene' ); ?></h3>
            </div>
            <div class="panel-wrap inside">
                <table class="form-table">
                	<tr>
                        <th scope="row"><?php _e( 'Footer preview', 'graphene' ); ?></th>
                        <td>
                        	<div class="footer-preview">
                            	<div class="graphene-footer">
                                    <div class="graphene-copyright"><p class="heading">Copyright</p><p>&copy; 2012 Graphene Theme.</p></div>
                                    <div class="footer-menu-wrap">
                                        <ul class="footer-menu">
                                            <li class="menu-item menu-item-ancestor"><a href="#">Menu item</a>
                                            <ul class="sub-menu">
                                                <li class="menu-item"><a href="#">Submenu item</a></li>
                                                <li class="menu-item"><a href="#">Submenu item</a></li>
                                                <li class="menu-item"><a href="#">Submenu item</a></li>
                                                <li class="menu-item"><a href="#">Submenu item</a></li>
                                            </ul>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="graphene-developer"><p>Powered by <a href="http://wordpress.org/">WordPress</a> and the <a href="http://www.khairul-syahir.com/wordpress-dev/graphene-theme">Graphene Theme</a>.</p></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php                                                 
                        $colour_opts = array(
                            'footer_bg' => array( 'title' => __( 'Background', 'graphene' ) ),
							'footer_heading' => array( 'title' => __( 'Heading', 'graphene' ) ),
							'footer_text' => array( 'title' => __( 'Normal text', 'graphene' ) ),
							'footer_link' => array( 'title' => __( 'Link text', 'graphene' ) ),
							'footer_submenu_text' => array( 'title' => __( 'Submenu text', 'graphene' ) ),
							'footer_submenu_border' => array( 'title' => __( 'Submenu border', 'graphene' ) ),
                        );
                        foreach ( $colour_opts as $key => $colour_opt) :
                    ?>
                    <tr>
                        <th scope="row"><label for="<?php echo $key; ?>"><?php echo $colour_opt['title']; ?></label></th>
                        <td>
                            <input type="text" class="code color" name="graphene_settings[<?php echo $key; ?>]" id="<?php echo $key; ?>" value="<?php echo $graphene_settings[$key]; ?>" />
                            <a href="#" class="clear-color"><?php _e( 'Clear', 'graphene' ); ?></a>
                            <div class="colorpicker" id="picker_<?php echo $key; ?>"></div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
                
                <p class="submit clearfix"><input type="submit" class="button" value="<?php _e( 'Save All Options', 'graphene' ); ?>" /></p>
            </div>
        </div>       
        
<?php } // Closes the graphene_options_colours() function definition 

<?php
function graphene_options_advanced() { 
    global $graphene_settings;
    ?>
        
    <input type="hidden" name="graphene_advanced" value="true" />    
    
    <?php /* Site Preview */ ?>
    <div class="postbox">
        <div class="head-wrap">
            <div title="Click to toggle" class="handlediv"><br /></div>
            <h3 class="hndle"><?php _e( 'Preview', 'graphene' ); ?></h3>
        </div>
        <div class="panel-wrap inside">
            <table class="form-table">
                <tr>
                    <td>
                        <input type="checkbox" name="graphene_settings[enable_preview]" id="enable_preview" <?php checked( $graphene_settings['enable_preview'] ); ?> value="true" />
                        <label for="enable_preview"><?php _e( 'Enable preview of your site on the Graphene Theme Options page', 'graphene' ); ?></label>
                    </td>
                </tr>
            </table>
            
            <p class="submit clearfix"><input type="submit" class="button" value="<?php _e( 'Save All Options', 'graphene' ); ?>" /></p>
        </div>
    </div>
    
    
    <?php /* <head> tags */ ?>
    <div class="postbox">
        <div class="head-wrap">
            <div title="Click to toggle" class="handlediv"><br /></div>
            <h3 class="hndle"><?php esc_html_e( 'Custom <head> Tags', 'graphene' ); ?></h3>
        </div>
        <div class="panel-wrap inside">
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="head_tags"><?php esc_html_e( 'Code to insert into the <head> element', 'graphene' ); ?></label></th>
                    <td><textarea name="graphene_settings[head_tags]" id="head_tags" cols="60" rows="7" class="widefat code"><?php echo htmlentities( stripslashes( $graphene_settings['head_tags'] ) ); ?></textarea></td>
                </tr>
            </table>
            
            <p class="submit clearfix"><input type="submit" class="button" value="<?php _e( 'Save All Options', 'graphene' ); ?>" /></p>
        </div>
    </div>  
    
    
    <?php /* Action hooks widgets areas */ ?>
    <div class="postbox">
        <div class="head-wrap">
            <div title="Click to toggle" class="handlediv"><br /></div>
            <h3 class="hndle"><?php _e( 'Action Hooks Widget Areas', 'graphene' ); ?></h3>
        </div>
        <div class="panel-wrap inside">
        	<p><?php _e("This option enables you to place virtually any content to every nook and cranny in the theme, by attaching widget areas to the theme's action hooks.", 'graphene' ); ?></p>
            <p><?php _e("All action hooks available in the Graphene Theme are listed below. Click on the filename to display all the action hooks available in that file. Then, tick the checkbox next to an action hook to make a widget area available for that action hook.", 'graphene' ); ?></p>
            
            <ul class="graphene-action-hooks">    
                <?php                
                $actionhooks = graphene_get_action_hooks();
                foreach ( $actionhooks as $actionhook) : 
                    $file = $actionhook['file']; 
                ?>
                    <li>
                        <p class="hooks-file"><a href="#" class="toggle-widget-hooks" title="<?php _e( 'Click to show/hide the action hooks for this file', 'graphene' ); ?>"><?php echo $file; ?></a></p>
                        <ul class="hooks-list">
                            <li class="widget-hooks<?php if(count(array_intersect( $actionhook['hooks'], $graphene_settings['widget_hooks'] ) ) == 0) echo ' hide'; ?>">
								<?php foreach ( $actionhook['hooks'] as $hook) : ?>
                                    <input type="checkbox" name="graphene_settings[widget_hooks][]" value="<?php echo $hook; ?>" id="hook_<?php echo $hook; ?>" <?php if ( in_array( $hook, $graphene_settings['widget_hooks'] ) ) echo 'checked="checked"'; ?> /> <label for="hook_<?php echo $hook; ?>"><?php echo $hook; ?></label><br />
                                <?php endforeach; ?>
                            </li>
                        </ul>
                    </li>
                <?php endforeach; ?>
            </ul>
            
            <p class="submit clearfix">
            	<a href="themes.php?page=graphene_options&tab=advanced&rescan_hooks=true" class="button"><?php _e( 'Rescan action hooks', 'graphene' ); ?></a>
            	<input type="submit" class="button" value="<?php _e( 'Save All Options', 'graphene' ); ?>" />
            </p>
        </div>
    </div>
    
    <?php /* Move generated CSS */ ?>
    <div class="postbox">
        <div class="head-wrap">
            <div title="Click to toggle" class="handlediv"><br /></div>
            <h3 class="hndle"><?php _e( 'Move generated CSS', 'graphene' ); ?></h3>
        </div>
        <div class="panel-wrap inside">
        	<?php $styles = graphene_custom_style( false, true, true ); ?>
        	<p>
				<?php printf( __( "If you're using a child theme, you can move the CSS codes generated by the theme into the child theme's %s file.", 'graphene' ), '<code>style.css</code>' ); ?>
                <?php printf( __( "The CSS codes are generated from the various customisation options in the theme's options pages, including any codes you have entered in the Custom CSS option.", 'graphene' ), '<code>style.css</code>' ); ?>
            </p>
            <p>
                <?php printf( __( "This will reduce up to %.1f KB on every page load for your site.", 'graphene' ), strlen( $styles ) / 1024 ); ?>
            </p>
            <p>
            	<?php  printf( __( "You will need to update your child theme's %s file everytime you change any options that rely on CSS to be implemented, such as column widths and colours.", 'graphene' ), '<code>style.css</code>' ); ?>
            </p>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="disable_css_generate"><?php _e( 'I have moved the generated CSS', 'graphene' ); ?></label>
                    </th>
                    <td><input type="checkbox" name="graphene_settings[disable_css_generate]" id="disable_css_generate" <?php checked( $graphene_settings['disable_css_generate'] ); ?> value="true" /></td>
                </tr>
                <tr>
                    <th scope="row"><?php _e( 'CSS codes generated by the theme', 'graphene' ); ?></th>
                    <td class="generated-css-wrap">
                    	<a href="#" class="select-all" rel="generated-css"><?php _e( 'Select all', 'graphene' ); ?></a>
                    	<div class="widefat code" id="generated-css"><?php echo $styles; ?></div>
                    </td>
                </tr>
            </table>
            
            <p class="submit clearfix"><input type="submit" class="button" value="<?php _e( 'Save All Options', 'graphene' ); ?>" /></p>
        </div>
    </div>
    
<?php } // Closes the graphene_options_advanced() function definition
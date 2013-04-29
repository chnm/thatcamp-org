<?php
/**
 * This file adds a new meta box to the Edit Post and Edit Page screens that contain
 * additional post- and page-specific options for use with the theme
 *
 * @package Graphene
 * @since Graphene 1.1
*/

/** 
 * Add the custom meta box 
*/
function graphene_add_meta_box(){
	add_meta_box( 'graphene_custom_meta', __( 'Graphene post-specific options','graphene' ), 'graphene_custom_meta', 'post', 'normal', 'high' );
	add_meta_box( 'graphene_custom_meta', __( 'Graphene page-specific options','graphene' ), 'graphene_custom_meta', 'page', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'graphene_add_meta_box' );




/**
 * Add or update the options
*/
function graphene_save_custom_meta( $post_id ){

	/* Verify this came from our screen and with proper authorization */
	if ( isset( $_POST['graphene_save_custom_meta'] ) ) {
		if ( ! wp_verify_nonce( $_POST['graphene_save_custom_meta'], 'graphene_save_custom_meta' ) ) return $post_id;
	} else {
		return $post_id;
	}
  
	/* Don't do anything if it's an autosave */
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return $post_id;
  
	/* Check permissions */
	if ( 'page' == $_POST['post_type']) {
		if ( ! current_user_can( 'edit_page', $post_id ) ) return $post_id;
	} else {
		if ( ! current_user_can( 'edit_post', $post_id ) ) return $post_id;
	}

	/* OK, we're authenticated: process the data */
	$graphene_meta = array();
	if ( in_array( $_POST['graphene_slider_img'], array( 'disabled', 'featured_image', 'post_image', 'custom_url' ) ) )
		$graphene_meta['slider_img'] = $_POST['graphene_slider_img'];
	if ( in_array( $_POST['graphene_show_addthis'], array( 'show', 'hide' ) ) )
		$graphene_meta['show_addthis'] = $_POST['graphene_show_addthis'];
	if ( $_POST['graphene_slider_imgurl'] )
		$graphene_meta['slider_imgurl'] = esc_url_raw( $_POST['graphene_slider_imgurl'] );
	if ( $_POST['graphene_slider_url'] )
	    $graphene_meta['slider_url'] = esc_url_raw( $_POST['graphene_slider_url'] );
	
	/* Post-specific options */
	if ( 'post' == $_POST['post_type'] ) {
		if ( in_array( $_POST['graphene_post_date_display'], array( 'hidden', 'icon_no_year', 'icon_plus_year', 'text' ) ) )
			$graphene_meta['post_date_display'] = $_POST['graphene_post_date_display'];
	}
	
	/* Page-specific options */
	if ( 'page' == $_POST['post_type']) {
		if ( $_POST['graphene_nav_description'] )
			$graphene_meta['nav_description'] = wp_kses_post( $_POST['graphene_nav_description'] );
	}
	
	$graphene_meta_defaults = graphene_custom_fields_defaults();
	foreach ( $graphene_meta as $key => $value ){
		if ( $value == $graphene_meta_defaults[$key] ) unset( $graphene_meta[$key] );
	}
	
	if ( $graphene_meta ) update_post_meta( $post_id, '_graphene_meta', $graphene_meta );
	else delete_post_meta( $post_id, '_graphene_meta' );
}
add_action( 'save_post', 'graphene_save_custom_meta' );


/**
 * Display the custom meta box content
*/
function graphene_custom_meta( $post ){ 

	// Use nonce for verification
	wp_nonce_field( 'graphene_save_custom_meta', 'graphene_save_custom_meta' );
	
	/* Get the current settings */
	$graphene_meta = get_post_meta( $post->ID, '_graphene_meta', true );
	if ( ! $graphene_meta ) $graphene_meta = array();
	$graphene_meta = array_merge( graphene_custom_fields_defaults(), $graphene_meta );	
	?>
    
	<p><?php _e( "These settings will only be applied to this particular post or page you're editing. They will override the global settings set in the Graphene Options or Graphene Display options page.", 'graphene' ); ?></p>
    <h4><?php _e( 'Slider options', 'graphene' ); ?></h4>    
    <table class="form-table">
    	<tr>
            <th scope="row">
                <label for="graphene_slider_img"><?php _e( 'Slider image', 'graphene' ); ?></label>
            </th>
            <td>
                <select id="graphene_slider_img" name="graphene_slider_img">
                	<option value="" <?php selected( $graphene_meta['slider_img'], '' ); ?>><?php _e( 'Use global setting', 'graphene' ); ?></option>
                    <option value="disabled" <?php selected( $graphene_meta['slider_img'], 'disabled' ); ?>><?php _e("Don't show image", 'graphene' ); ?></option>
                    <option value="featured_image" <?php selected( $graphene_meta['slider_img'], 'featured_image' ); ?>><?php _e('Featured Image', 'graphene' ); ?></option>
                    <option value="post_image" <?php selected( $graphene_meta['slider_img'], 'post_image' ); ?>><?php _e('First image in post', 'graphene' ); ?></option>
                    <option value="custom_url" <?php selected( $graphene_meta['slider_img'], 'custom_url' ); ?>><?php _e('Custom URL', 'graphene' ); ?></option>
                </select>
            </td>
        </tr>        
        <tr>
            <th scope="row">
                <label for="graphene_slider_imgurl"><?php _e( 'Custom slider image URL', 'graphene' ); ?></label>
            </th>
            <td>
                <input type="text" id="graphene_slider_imgurl" name="graphene_slider_imgurl" class="widefat code" value="<?php echo $graphene_meta['slider_imgurl']; ?>" size="60" /><br />
                <span class="description"><?php _e( 'Make sure you select Custom URL in the slider image option above to use this custom url.', 'graphene' ); ?></span>                        
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="graphene_slider_url"><?php _e( 'Custom slider URL', 'graphene' ); ?></label>
            </th>
            <td>
                <input type="text" id="graphene_slider_url" name="graphene_slider_url" class="widefat code" value="<?php echo $graphene_meta['slider_url']; ?>" size="60" /><br />
                <span class="description"><?php _e( 'Use this to override the link that is used in the slider.', 'graphene' ); ?></span>                        
            </td>
        </tr>
    </table>
    <h4><?php _e( 'Display options', 'graphene' ); ?></h4>
    <table class="form-table">
    	<tr>
            <th scope="row">
                <label for="graphene_show_addthis"><?php _e( 'AddThis Social Sharing button', 'graphene' ); ?></label>
            </th>
            <td>
                <select id="graphene_show_addthis" name="graphene_show_addthis">
                	<option value="" <?php selected( $graphene_meta['show_addthis'], '' ); ?>><?php _e( 'Use global setting', 'graphene' ); ?></option>
                    <option value="show" <?php selected( $graphene_meta['show_addthis'], 'show' ); ?>><?php _e( 'Show button', 'graphene' ); ?></option>
                    <option value="hide" <?php selected( $graphene_meta['show_addthis'], 'hide' ); ?>><?php _e( 'Hide button', 'graphene' ); ?></option>
                </select>
            </td>
        </tr>
        
        <?php if ( 'post' == $post->post_type) : ?>
        <tr>
            <th scope="row">
                <label for="graphene_post_date_display"><?php _e( 'Post date display', 'graphene' ); ?></label>
            </th>
            <td>
                <select id="graphene_post_date_display" name="graphene_post_date_display">
                	<option value="" <?php selected( $graphene_meta['post_date_display'], '' ); ?>><?php _e( 'Use global setting', 'graphene' ); ?></option>
                    <option value="hidden" <?php selected( $graphene_meta['post_date_display'], 'hidden' ); ?>><?php _e( 'Hidden', 'graphene' ); ?></option>
                    <option value="icon_no_year" <?php selected( $graphene_meta['post_date_display'], 'icon_no_year' ); ?>><?php _e( 'As an icon (without the year)', 'graphene' ); ?></option>
                    <option value="icon_plus_year" <?php selected( $graphene_meta['post_date_display'], 'icon_plus_year' ); ?>><?php _e( 'As an icon (including the year)', 'graphene' ); ?></option>
                    <option value="text" <?php selected( $graphene_meta['post_date_display'], 'text' ); ?>><?php _e( 'As inline text', 'graphene' ); ?></option>
                </select>
            </td>
        </tr>
        <?php endif; ?>
        
    </table>
    <?php if ( 'page' == $post->post_type): ?>
    <h4><?php _e( 'Navigation options', 'graphene' ); ?></h4>
    <table class="form-table">
    	<tr>
            <th scope="row">
                <label for="graphene_nav_description"><?php _e( 'Description', 'graphene' ); ?></label>
            </th>
            <td>
                <input type="text" id="graphene_nav_description" name="graphene_nav_description" value="<?php echo $graphene_meta['nav_description']; ?>" size="60" /><br />
                <span class="description"><?php _e( 'Only required if you need a description in the navigation menu and you are not using a custom menu.', 'graphene' ); ?></span>                        
            </td>
        </tr>
    </table>
     <?php endif; ?>
<?php	
}


/**
 * Set the default values for the custom fields
 *
 * @return array Array containing default key-value pairs of the theme's custom fields
 *
 * @package Graphene
 * @since Graphene 1.8
 */
function graphene_custom_fields_defaults(){
	$defaults = array(
					'slider_img' 		=> '',
					'show_addthis' 		=> '',
					'slider_imgurl' 	=> '',
					'slider_url' 		=> '',
					'post_date_display' => '',
					'nav_description'	=> '',
				);
	return $defaults;
}
?>
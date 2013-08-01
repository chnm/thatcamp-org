<?php 
/**
 * Export the theme's options
 */
function graphene_export_options(){
    
    global $graphene_settings, $graphene_defaults;
    
    ob_clean();
	
	/* Check authorisation */
	$authorised = true;
	// Check nonce
	if ( ! wp_verify_nonce( $_POST['graphene-export'], 'graphene-export' ) ) { 
		$authorised = false;
	}
	// Check permissions
	if ( ! current_user_can( 'edit_theme_options' ) ){
		$authorised = false;
	}
	if ( $authorised ) {
    
		$is_colours = ( isset( $_POST['graphene_export_colours'] ) ) ? true : false;
		$name = ( $is_colours ) ? 'graphene-colour-presets.txt' : 'graphene_options.txt';
		
		if ( ! $is_colours ) {
			$data = $graphene_settings;
			if ( array_key_exists( 'template_dir', $data ) ) unset( $data['template_dir'] );
		} else {
			if ( empty( $_POST['presets'] ) ) wp_die( __( 'ERROR: You have not selected any colour presets to be exported.', 'graphene' ) );;
			$presets = $_POST['presets'];
			
			$data = array();
			foreach ( $presets as $preset ) {
				$data['colour_presets'][$preset] = $graphene_settings['colour_presets'][$preset];
			}
		}
		/* Only export options that have different values than the default values - disabled for now
		foreach ( $data as $key => $value ){
			if ( $graphene_defaults[$key] === $value || $value === '' ) {
				unset( $data[$key] );
			}
		}
		*/
		
		unset( $data['db_version'] );
		
		$data = json_encode( $data );
		$size = strlen( $data );
	
		header( 'Content-Type: text/plain' );
		header( 'Content-Disposition: attachment; filename="'.$name.'"' );
		header( "Content-Transfer-Encoding: binary" );
		header( 'Accept-Ranges: bytes' );
	
		/* The three lines below basically make the download non-cacheable */
		header( "Cache-control: private" );
		header( 'Pragma: private' );
		header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
	
		header( "Content-Length: " . $size);
		print( $data );
	
	} else {
		wp_die( __( 'ERROR: You are not authorised to perform that operation', 'graphene' ) );
	}

    die();   
}

if ( isset( $_POST['graphene_export'] ) ){
	add_action( 'init', 'graphene_export_options' );
}


/**
 * This file manages the theme settings uploading and import operations.
 * Uses WP_Filesystem
*/
function graphene_import_form(){            
    
    $bytes = apply_filters( 'import_upload_size_limit', wp_max_upload_size() );
    $size = size_format( $bytes, 2 );
    $upload_dir = wp_upload_dir();
    if ( ! empty( $upload_dir['error'] ) ) :
        ?><div class="error"><p><?php _e( 'Before you can upload your import file, you will need to fix the following error:', 'graphene' ); ?></p>
            <p><strong><?php echo $upload_dir['error']; ?></strong></p></div><?php
    else :
    ?>
    <div class="wrap">
        <div id="icon-tools" class="icon32"><br></div>
        <h2><?php echo __( 'Import Graphene Theme Options', 'graphene' );?></h2>    
        <form enctype="multipart/form-data" id="import-upload-form" method="post" action="">
            <p>
                <label for="upload"><?php _e( 'Choose a file from your computer:', 'graphene' ); ?></label> (<?php printf( __( 'Maximum size: %s', 'graphene' ), $size ); ?>)
                <input type="file" id="upload" name="import" size="25" />
                <input type="hidden" name="action" value="save" />
                <input type="hidden" name="max_file_size" value="<?php echo $bytes; ?>" />
                <?php wp_nonce_field( 'graphene-import', 'graphene-import' ); ?>
                <input type="hidden" name="graphene_import_confirmed" value="true" />
            </p>
            <input type="submit" class="button" value="<?php _e( 'Upload file and import', 'graphene' ); ?>" />            
        </form>
    </div> <!-- end wrap -->
    <?php
    endif;
} // Closes the graphene_import_form() function definition


/**
 * This file manages the colour presets uploading and import operations.
 *
 * @package Graphene
 * @since Graphene 1.9
*/
function graphene_import_colour_presets(){            
    
    $bytes = apply_filters( 'import_upload_size_limit', wp_max_upload_size() );
    $size = size_format( $bytes, 2 );
    $upload_dir = wp_upload_dir();
    if ( ! empty( $upload_dir['error'] ) ) :
        ?><div class="error"><p><?php _e( 'Before you can upload your import file, you will need to fix the following error:', 'graphene' ); ?></p>
            <p><strong><?php echo $upload_dir['error']; ?></strong></p></div><?php
    else :
    ?>
    <div class="wrap">
        <div id="icon-tools" class="icon32"><br></div>
        <h2><?php echo __( 'Import Graphene Theme Colour Presets', 'graphene' );?></h2>    
        <form enctype="multipart/form-data" id="import-upload-form" method="post" action="<?php echo admin_url( 'themes.php?page=graphene_options&tab=colours' ); ?>">
            <p>
                <label for="upload"><?php _e( 'Choose a Graphene theme\'s colour presets export file from your computer:', 'graphene' ); ?></label><br />
                <input type="file" id="upload" name="import" size="25" /> <span class="description">(<?php printf( __( 'Maximum size: %s', 'graphene' ), $size ); ?>)</span>
                <input type="hidden" name="action" value="save" />
                <input type="hidden" name="max_file_size" value="<?php echo $bytes; ?>" />
                <?php wp_nonce_field( 'graphene-import', 'graphene-import' ); ?>
                <input type="hidden" name="graphene_import_confirmed" value="true" />
                <input type="hidden" name="graphene_import_colour_presets_confirmed" value="true" />
            </p>
            <input type="submit" class="button-primary" value="<?php _e( 'Upload file and import', 'graphene' ); ?>" />
        </form>
    </div> <!-- end wrap -->
    <?php
    endif;
}


/**
 * This file manages the colour presets export operations.
 *
 * @package Graphene
 * @since Graphene 1.9
*/
function graphene_export_colour_presets(){
	global $graphene_settings;
	?>
	<div class="wrap">
        <div id="icon-tools" class="icon32"><br></div>
        <h2><?php echo __( 'Export Graphene Theme Colour Presets', 'graphene' );?></h2>    
        <form enctype="multipart/form-data" id="import-upload-form" method="post" action="">
            <p>
                <?php _e( 'Please select the colour presets to be exported.', 'graphene' ); ?>
                <ul>
                <?php foreach ( $graphene_settings['colour_presets'] as $key => $preset ) : ?>
                	<li><input type="checkbox" name="presets[]" value="<?php echo $key; ?>" id="preset-<?php echo $key; ?>" /> <label for="preset-<?php echo $key; ?>"><?php echo $preset['name']; ?></label></li>
                <?php endforeach; ?>
                </ul>
            </p>
            <?php wp_nonce_field( 'graphene-export', 'graphene-export' ); ?>
            <input type="hidden" name="graphene_export" value="true" />
            <input type="hidden" name="graphene_export_colours" value="true" />
            <input type="submit" class="button-primary" value="<?php _e( 'Export colour presets', 'graphene' ); ?>" />
			<p><a href="<?php echo admin_url( 'themes.php?page=graphene_options&tab=colours' ); ?>"><?php _e( '&laquo; Return to Graphene Options', 'graphene' ); ?></a></p>
        </form>
    </div>
    <?php
}


/**
 * Manages file upload and settings import
 */
function graphene_import_file() {
    global $graphene_settings;
    
    /* Check authorisation */
    $authorised = true;
    // Check nonce
    if ( ! wp_verify_nonce( $_POST['graphene-import'], 'graphene-import' ) ) {$authorised = false;}
    // Check permissions
    if ( ! current_user_can( 'edit_theme_options' ) ){ $authorised = false; }
    
    // If the user is authorised, import the theme's options to the database
    if ( $authorised) { 
		
		$is_colours = ( isset( $_POST['graphene_import_colour_presets_confirmed'] ) ) ? true : false;
		$title = ( ! $is_colours ) ? __( 'Import Graphene Theme Options', 'graphene' ) : __( 'Import Graphene Theme Colour Presets', 'graphene' );
		?>
        <div class="wrap">
        <div id="icon-tools" class="icon32"><br></div>
        <h2><?php echo $title; ?></h2>
        <?php
        // make sure there is an import file uploaded
        if ( isset( $_FILES['import'] ) ) {
			
			$form_fields = array( 'import' );
			$method = '';
			
			$url = wp_nonce_url( 'themes.php?page=graphene_options', 'graphene-import' );
			
			// Get file writing credentials
			if (false === ( $creds = request_filesystem_credentials( $url, $method, false, false, $form_fields ) ) ) {
				return true;
			}
			
			if ( ! WP_Filesystem( $creds ) ) {
				// our credentials were no good, ask the user for them again
				request_filesystem_credentials( $url, $method, true, false, $form_fields );
				return true;
			}
			
			// Write the file if credentials are good
			$upload_dir = wp_upload_dir();
			$filename = trailingslashit( $upload_dir['path'] ) . 'graphene_options.txt';
				 
			// by this point, the $wp_filesystem global should be working, so let's use it to create a file
			global $wp_filesystem;
			if ( ! $wp_filesystem->move( $_FILES['import']['tmp_name'], $filename, true) ) {
				echo 'Error saving file!';
				return;
			}
			
			$file = $_FILES['import'];
			
			if ( $file['type'] == 'text/plain' ) {
				$data = $wp_filesystem->get_contents( $filename );
				// try to read the file
				if ( $data !== FALSE ){
					$settings = json_decode( $data, true );
					// try to read the settings array
					if ( ! $is_colours ) {
						if ( isset( $settings['db_version'] ) ) {
							$settings = array_merge( $graphene_settings, $settings );
							update_option( 'graphene_settings', $settings );
							echo '<p>' . __( 'Options import completed.', 'graphene' ) . '</p>';
							echo '<p><a href="' . admin_url( 'themes.php?page=graphene_options' ) . '">' . __( '&laquo; Return to Graphene Options', 'graphene' ) . '<a></p>';
						} else { // else: try to read the settings array
							echo '<p><strong>'.__( 'Sorry, there has been an error.', 'graphene' ).'</strong><br />';
							echo __( 'The uploaded file does not contain valid Graphene options.', 'graphene' ).'</p>';
						}
					} else {
						if ( isset( $settings['colour_presets'] ) ) {
							$graphene_settings['colour_presets'] = array_merge( $graphene_settings['colour_presets'], $settings['colour_presets'] );
							update_option( 'graphene_settings', $graphene_settings );
							echo '<p>' . __( 'Colour presets import completed.', 'graphene' ) . '</p>';
							echo '<p><a href="' . admin_url( 'themes.php?page=graphene_options&tab=colours' ) . '">' . __( '&laquo; Return to Graphene Options', 'graphene' ) . '<a></p>';
						} else { // else: try to read the settings array
							echo '<p><strong>'.__( 'Sorry, there has been an error.', 'graphene' ).'</strong><br />';
							echo __( 'The uploaded file does not contain valid Graphene colour presets.', 'graphene' ).'</p>';
						}
					}
				} 
				else { // else: try to read the file
					echo '<p><strong>'.__( 'Sorry, there has been an error.', 'graphene' ).'</strong><br />';
					echo __( 'The uploaded file could not be read.', 'graphene' ).'</p>';
				} 
			}
			else { // else: make sure the file uploaded was a plain text file
				echo '<p><strong>'.__( 'Sorry, there has been an error.', 'graphene' ).'</strong><br />';
				echo __( 'The uploaded file is not supported.', 'graphene' ).'</p>';
			}
			
			// Delete the file after we're done
			$wp_filesystem->delete( $filename);
			
        }
        else { // else: make sure there is an import file uploaded           
            echo '<p>'.__( 'File is empty. Please upload something more substantial. This error could also be caused by uploads being disabled in your php.ini or by post_max_size being defined as smaller than upload_max_filesize in php.ini.', 'graphene' ).'</p>';        
        }
        echo '</div> <!-- end wrap -->';
    }
    else {
        wp_die( __( 'ERROR: You are not authorised to perform that operation', 'graphene' ) );            
    }           
} // Closes the graphene_import_file() function definition 
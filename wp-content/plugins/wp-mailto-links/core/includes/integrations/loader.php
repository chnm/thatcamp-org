<?php
/**
 *
 * Load our custom marketing integrations
 *
 */

 // Exit if accessed directly.
if ( !defined( 'ABSPATH' ) ) exit;

class WPMT_Integrations_Loader{

    function __construct(){
        $this->load_integrations();
    }

    public function load_integrations(){

        $integrations = array(
            'divi_theme' => 'divi_theme.php',
            'oxygen_builder' => 'oxygen_builder.php',
        );
        
        foreach ( $integrations as $plugin_id => $plugin_file ) :
        
            $plugin_file = 'classes/' . $plugin_file;
            $full_path = WPMT_PLUGIN_DIR . 'core/includes/integrations/' . $plugin_file;
        
            if ( true === apply_filters( 'wpmt/integrations/' . $plugin_id, true ) ){
                if( file_exists( $full_path ) ){
                    include( $plugin_file );
                }
            }
        
        endforeach;

    }

}
<?php

if( ! function_exists( 'wpml_filter' ) ){

    function wpml_filter( $content ){
        $protect_using = (string) WPMT()->settings->get_setting( 'protect_using', true );
        return WPMT()->validate->filter_content( $content, $protect_using );
    }
    
}

if( ! function_exists( 'wpml_mailto' ) ){

    function wpml_mailto( $email, $display = null, $atts = array() ){
        if (is_array($display)) {
            // backwards compatibility (old params: $display, $attrs = array())
            $atts   = $display;
            $display = $email;
        } else {
            $atts['href'] = 'mailto:'.$email;
        }

        return WPMT()->validate->create_protected_mailto($display, $atts);
    }

}
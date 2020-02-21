<?php

/**
 * Class WP_Mailto_Links_Validate
 *
 * The main validation functionality for the plugin.
 * Here is where the logic happens.
 *
 * @since 3.0.0
 * @package WPMT
 * @author Ironikus <info@ironikus.com>
 */

class WP_Mailto_Links_Validate{

	/**
	 * The main page name for our admin page
	 *
	 * @var string
	 * @since 3.0.0
	 */
	private $page_name;

	/**
	 * The main page title for our admin page
	 *
	 * @var string
	 * @since 3.0.0
	 */
	private $page_title;

	/**
	 * Our WP_Mailto_Links_Run constructor.
	 */
	function __construct(){
		$this->page_name    			= WPMT()->settings->get_page_name();
		$this->page_title   			= WPMT()->settings->get_page_title();
		$this->final_outout_buffer_hook = WPMT()->settings->get_final_outout_buffer_hook();
		$this->at_identifier            = WPMT()->settings->get_at_identifier();
	}

	/**
	 * ######################
	 * ###
	 * #### FILTERS
	 * ###
	 * ######################
	 */

     /**
      * The main page filter function
      *
      * @param string $content - the content that needs to be filtered
      * @param bool $convertPlainEmails - wether plain emails should be preserved or not
      * @return string - The filtered content
      */
    public function filter_page( $content, $protect_using ){

        $content = $this->filter_soft_dom_attributes( $content, 'char_encode' );

        $htmlSplit = preg_split( '/(<body(([^>]*)>))/is', $content, null, PREG_SPLIT_DELIM_CAPTURE );
        
        if ( count( $htmlSplit ) < 4 ) {
            return $content;
        }

        switch( $protect_using ){
            case 'with_javascript':
            case 'without_javascript':
            case 'char_encode':
                $head_encoding_method = 'char_encode';
                break;
            default:
                $head_encoding_method = 'default';
                break;
        }

        //Filter head area
        $filtered_head = $this->filter_plain_emails( $htmlSplit[0], null, $head_encoding_method );
        
        //Filter body
        //Soft attributes always need to be protected using only the char encode method since otherwise the logic breaks
        $filtered_body = $this->filter_soft_attributes( $htmlSplit[4], 'char_encode' );
        $filtered_body = $this->filter_content( $filtered_body, $protect_using );

        $filtered_content = $filtered_head . $htmlSplit[1] . $filtered_body;

        //Revalidate filtered emails that should not be encoded
        $filtered_content = $this->temp_encode_at_symbol( $filtered_content, true );

        return $filtered_content;
    }

    /**
     * Filter content
     * 
     * @param string  $content
     * @param integer $protect_using
     * @return string
     */
    public function filter_content( $content, $protect_using ){
        $filtered = $content;
        $self = $this;
        $convert_plain_to_mailto = (bool) WPMT()->settings->get_setting( 'convert_plain_to_mailto', true, 'filter_body' );
        $convert_plain_to_image = (bool) WPMT()->settings->get_setting( 'convert_plain_to_image', true, 'filter_body' );

        //Soft attributes always need to be protected using only the char encode method since otherwise the logic breaks
        $filtered = $this->filter_soft_attributes( $filtered, 'char_encode' );

        switch( $protect_using ){
            case 'char_encode':
                $filtered = $this->filter_plain_emails( $filtered, null, 'char_encode' );
                break;
            case 'strong_method':
                $filtered = $this->filter_plain_emails( $filtered );
                break;
            case 'without_javascript':
                $filtered = $this->filter_input_fields( $filtered, $protect_using );
                $filtered = $this->filter_mailto_links( $filtered, 'without_javascript' );

                if( $convert_plain_to_image ){
                    $replace_by = 'convert_image';
                } else {
                    $replace_by = 'use_css';
                }

                if( $convert_plain_to_mailto ){
                    if( ! ( function_exists( 'et_fb_enabled' ) && et_fb_enabled() ) ){
                        $filtered = $this->filter_plain_emails( $filtered, function ( $match ) use ( $self ) {
                            return $self->create_protected_mailto( $match[0], array( 'href' => 'mailto:' . $match[0] ), 'without_javascript' );
                        }, $replace_by);
                    } else {
                        $filtered = $this->filter_plain_emails( $filtered, null, $replace_by );
                    }
                } else {
                    $filtered = $this->filter_plain_emails( $filtered, null, $replace_by );
                }
                
                break;
            case 'with_javascript':
                $filtered = $this->filter_input_fields( $filtered, $protect_using );
                $filtered = $this->filter_mailto_links( $filtered );

                if( $convert_plain_to_image ){
                    $replace_by = 'convert_image';
                } else {
                    $replace_by = 'use_javascript';
                }

                if( $convert_plain_to_mailto ){
                    if( ! ( function_exists( 'et_fb_enabled' ) && et_fb_enabled() ) ){
                        $filtered = $this->filter_plain_emails( $filtered, function ( $match ) use ( $self ) {
                            return $self->create_protected_mailto( $match[0], array( 'href' => 'mailto:' . $match[0] ), 'with_javascript' );
                        }, $replace_by);
                    } else {
                        $filtered = $this->filter_plain_emails( $filtered, null, $replace_by );
                    }
                } else {
                    $filtered = $this->filter_plain_emails( $filtered, null, $replace_by );
                }

                break;
        }

        //Revalidate filtered emails that should not bbe encoded
        $filtered = $this->temp_encode_at_symbol( $filtered, true );

        return $filtered;
    }

    /**
     * Emails will be replaced by '*protected email*'
     * @param string           $content
     * @param string|callable  $replace_by  Optional
     * @param string           $protection_method  Optional
     * @param mixed            $security_check  Optional
     * @return string
     */
    public function filter_plain_emails($content, $replace_by = null, $protection_method = 'default', $security_check = 'default' ){

        if( $security_check === 'default' ){
            $security_check = (bool) WPMT()->settings->get_setting( 'security_check', true );
        }

        if ( $replace_by === null ) {
            $replace_by = WPMT()->helpers->translate( WPMT()->settings->get_setting( 'protection_text', true ), 'email-protection-text' );
        }

        $self = $this;

        return preg_replace_callback( WPMT()->settings->get_email_regex(), function ( $matches ) use ( $replace_by, $protection_method, $security_check, $self ) {
            // workaround to skip responsive image names containing @
            $extention = strtolower( $matches[4] );
            $excludedList = array('.jpg', '.jpeg', '.png', '.gif');

            if ( in_array( $extention, $excludedList ) ) {
                return $matches[0];
            }

            if ( is_callable( $replace_by ) ) {
                return call_user_func( $replace_by, $matches, $protection_method );
            }

            if( $protection_method === 'char_encode' ){
                $protected_return = antispambot( $matches[0] );
            } elseif( $protection_method === 'convert_image' ){

                $image_link = $self->generate_email_image_url( $matches[0] );
                if( ! empty( $image_link ) ){
                    $protected_return = '<img src="' . $image_link . '" />';
                } else {
                    $protected_return = antispambot( $matches[0] );
                }
                
            } elseif( $protection_method === 'use_javascript' ){
                $protection_text = WPMT()->helpers->translate( WPMT()->settings->get_setting( 'protection_text', true ), 'email-protection-text' );
                $protected_return = $this->dynamic_js_email_encoding( $matches[0], $protection_text );
            } elseif( $protection_method === 'use_css' ){
                $protection_text = WPMT()->helpers->translate( WPMT()->settings->get_setting( 'protection_text', true ), 'email-protection-text' );
                $protected_return = $this->encode_email_css( $matches[0], $protection_text );
            } else {
                $protected_return = $replace_by;
            }

            // mark link as successfully encoded (for admin users)
            if ( current_user_can( WPMT()->settings->get_admin_cap( 'frontend-display-security-check' ) ) && $security_check ) {
                $protected_return .= '<i class="wpml-encoded dashicons-before dashicons-lock" title="' . WPMT()->helpers->translate( 'Email encoded successfully!', 'frontend-security-check-title' ) . '"></i>';
            }

            return $protected_return;
            
        }, $content );
    }

    /**
     * Filter passed input fields 
     * 
     * @param string $content
     * @return string
     */
    public function filter_input_fields( $content, $encoding_method = 'default' ){
        $self = $this;
        $strong_encoding = (bool) WPMT()->settings->get_setting( 'input_strong_protection', true, 'filter_body' );

        $callback_encode_input_fields = function ( $match ) use ( $self, $encoding_method, $strong_encoding ) {
            $input = $match[0];
            $email = $match[2];

            //Only allow strong encoding if javascript is supported
            if( $encoding_method === 'without_javascript' ){
                $strong_encoding = false;
            }

            return $self->encode_input_field( $input, $email, $strong_encoding );
        };

        $regexpInputField = '/<input([^>]*)value=["\'][\s+]*' . WPMT()->settings->get_email_regex( true ) . '[\s+]*["\']([^>]*)>/is';

        return preg_replace_callback( $regexpInputField, $callback_encode_input_fields, $content );
    }

    /**
     * @param string $content
     * @return string
     */
    public function filter_mailto_links( $content, $protection_method = null ){
        $self = $this;

        $callbackEncodeMailtoLinks = function ( $match ) use ( $self, $protection_method ) {
            $attrs = shortcode_parse_atts( $match[1] );
            return $self->create_protected_mailto( $match[4], $attrs, $protection_method );
        };

        $regexpMailtoLink = '/<a[\s+]*(([^>]*)href=["\']mailto\:([^>]*)["\'])>(.*?)<\/a[\s+]*>/is';

        return preg_replace_callback( $regexpMailtoLink, $callbackEncodeMailtoLinks, $content );
    }

    /**
     * Emails will be replaced by '*protected email*'
     * 
     * @param string $content
     * @return string
     */
    public function filter_rss( $content, $protection_type ){
        
        if( $protection_type === 'strong_method' ) {
            $filtered = $this->filter_plain_emails( $content );
        } else {
            $filtered = $this->filter_plain_emails( $content, null, 'char_encode' );
        }
        
        return $filtered;
    }

    /**
     * Filter plain emails using soft attributes
     * 
     * @param string $content - the content that should be soft filtered
     * @param string $protection_method - The method (E.g. char_encode)
     * @return string
     */
    public function filter_soft_attributes( $content, $protection_method ){
        $soft_attributes = WPMT()->settings->get_soft_attribute_regex();

        foreach( $soft_attributes as $ident => $regex ){

            $array = array();
            preg_match_all( $regex, $content, $array ) ;

            foreach( $array as $single ){
                $content = str_replace( $single, $this->filter_plain_emails( $single, null, $protection_method, false ), $content );
            }

        }

        return $content;
    }

    /**
     * Filter plain emails using soft dom attributes
     * 
     * @param string $content - the content that should be soft filtered
     * @param string $protection_method - The method (E.g. char_encode)
     * @return string
     */
    public function filter_soft_dom_attributes( $content, $protection_method ){

        $no_script_tags = (bool) WPMT()->settings->get_setting( 'no_script_tags', true, 'filter_body' );
        $no_attribute_validation = (bool) WPMT()->settings->get_setting( 'no_attribute_validation', true, 'filter_body' );

        if( class_exists( 'DOMDocument' ) ){
            $dom = new DOMDocument();
            @$dom->loadHTML($content);

            //Filter html attributes
            if( ! $no_attribute_validation ){
                $allNodes = $dom->getElementsByTagName('*');
                foreach( $allNodes as $snote ){
                    if( $snote->hasAttributes() ) {
                        foreach( $snote->attributes as $attr ) {
                            if( $attr->nodeName == 'href' || $attr->nodeName == 'src' ){
                                continue;
                            }
    
                            if( strpos( $attr->nodeValue, '@' ) !== FALSE ){
                                $single_tags = array();
                                preg_match_all( '/' . $attr->nodeName . '="([^"]*)"/i', $content, $single_tags );
    
                                foreach( $single_tags as $single ){
                                    $content = str_replace( $single, $this->filter_plain_emails( $single, null, $protection_method, false ), $content );
                                }
                            }
                        }
                    }
                }
            }
    
            //Soft-encode scripts
            $script = $dom->getElementsByTagName('script');
            if( ! $no_script_tags ){
                foreach($script as $item){
                    $content = str_replace( $item->nodeValue, $this->filter_plain_emails( $item->nodeValue, null, $protection_method, false ), $content );
                }
            } else {
                foreach($script as $item){
                    $content = str_replace( $item->nodeValue, $this->temp_encode_at_symbol( $item->nodeValue ), $content );
                }
            }
            
        }
        
        return $content;
    }

    /**
	 * ######################
	 * ###
	 * #### ENCODINGS
	 * ###
	 * ######################
	 */

     public function temp_encode_at_symbol( $content, $decode = false ){
         if( $decode ){
            return str_replace( $this->at_identifier, '@', $content );
         }

        return str_replace( '@', $this->at_identifier, $content );
     }

      /**
     * ASCII method
     *
     * @param string $value
     * @param string $protection_text
     * @return string
     */
    public function encode_ascii($value, $protection_text) {
        $mail_link = $value;

        // first encode, so special chars can be supported
        $mail_link = WPMT()->helpers->encode_uri_components( $mail_link );
        
        $mail_letters = '';

        for ($i = 0; $i < strlen($mail_link); $i ++) {
            $l = substr($mail_link, $i, 1);

            if (strpos($mail_letters, $l) === false) {
                $p = rand(0, strlen($mail_letters));
                $mail_letters = substr($mail_letters, 0, $p) .
                $l . substr($mail_letters, $p, strlen($mail_letters));
            }
        }

        $mail_letters_enc = str_replace("\\", "\\\\", $mail_letters);
        $mail_letters_enc = str_replace("\"", "\\\"", $mail_letters_enc);

        $mail_indices = '';
        for ($i = 0; $i < strlen($mail_link); $i ++) {
            $index = strpos($mail_letters, substr($mail_link, $i, 1));
            $index += 48;
            $mail_indices .= chr($index);
        }

        $mail_indices = str_replace("\\", "\\\\", $mail_indices);
        $mail_indices = str_replace("\"", "\\\"", $mail_indices);

        $element_id = 'wpmt-' . mt_rand( 0, 1000000 ) . '-' . mt_rand(0, 1000000);

        return '<span id="'. $element_id . '"></span>'
                . '<script type="text/javascript">'
                . '(function(){'
                . 'var ml="'. $mail_letters_enc .'",mi="'. $mail_indices .'",o="";'
                . 'for(var j=0,l=mi.length;j<l;j++){'
                . 'o+=ml.charAt(mi.charCodeAt(j)-48);'
                . '}document.getElementById("' . $element_id . '").innerHTML = decodeURIComponent(o);' // decode at the end, this way special chars can be supported
                . '}());'
                . '</script><noscript>'
                . $protection_text
                . '</noscript>';
    }

    /**
     * Escape encoding method
     *
     * @param string $value
     * @param string $protection_text
     * @return string
     */
    public function encode_escape( $value, $protection_text ) {
        $element_id = 'wpmt-' . mt_rand( 0, 1000000 ) . '-' . mt_rand( 0, 1000000 );
        $string = '\'' . $value . '\'';

        //Validate escape sequences
        $string = preg_replace('/\s+/S', " ", $string);

        // break string into array of characters, we can't use string_split because its php5 only
        $split = preg_split( '||', $string );
        $out = '<span id="'. $element_id . '"></span>'
             . '<script type="text/javascript">' . 'document.getElementById("' . $element_id . '").innerHTML = ev' . 'al(decodeURIComponent("';

              foreach( $split as $c ) {
                // preg split will return empty first and last characters, check for them and ignore
                if( ! empty( $c ) || $c === '0' ) {
                  $out .= '%' . dechex( ord( $c ) );
                }
              }

              $out .= '"))' . '</script><noscript>'
                   . $protection_text
                   . '</noscript>';

        return $out;
    }

    /**
     * Encode email in input field
     * @param string $input
     * @param string $email
     * @return string
     */
    public function encode_input_field( $input, $email, $strongEncoding = false ){  
        
        $security_check = (bool) WPMT()->settings->get_setting( 'security_check', true );

        if ( $strongEncoding === false ) {
            // encode email with entities (default wp method)
            $sub_return = str_replace( $email, antispambot( $email ), $input );

            if ( current_user_can( WPMT()->settings->get_admin_cap( 'frontend-display-security-check' ) ) && $security_check ) {
                $sub_return .= '<i class="wpml-encoded dashicons-before dashicons-lock" title="' . WPMT()->helpers->translate( 'Email encoded successfully!', 'frontend-security-check-title' ) . '"></i>';
            }

            return $sub_return;
        }

        // add data-enc-email after "<input"
        $inputWithDataAttr = substr( $input, 0, 6 );
        $inputWithDataAttr .= ' data-enc-email="' . $this->get_encoded_email( $email ) . '"';
        $inputWithDataAttr .= substr( $input, 6 );

        // mark link as successfully encoded (for admin users)
        if ( current_user_can( WPMT()->settings->get_admin_cap( 'frontend-display-security-check' ) ) && $security_check ) {
            $inputWithDataAttr .= '<i class="wpml-encoded dashicons-before dashicons-lock" title="' . WPMT()->helpers->translate( 'Email encoded successfully!', 'frontend-security-check-title' ) . '"></i>';
        }

        // remove email from value attribute
        $encInput = str_replace( $email, '', $inputWithDataAttr );

        return $encInput;
    }

    /**
     * Get encoded email, used for data-attribute (translate by javascript)
     * 
     * @param string $email
     * @return string
     */
    public function get_encoded_email( $email ){
        $encEmail = $email;

        // decode entities
        $encEmail = html_entity_decode( $encEmail );

        // rot13 encoding
        $encEmail = str_rot13( $encEmail );

        // replace @
        $encEmail = str_replace( '@', '[at]', $encEmail );

        return $encEmail;
    }

    /**
     * Create a protected email
     * 
     * @param string $display
     * @param array $attrs Optional
     * @return string
     */
    public function create_protected_mailto( $display, $attrs = array(), $protection_method = null ){
        $email     = '';
        $class_ori = ( empty( $attrs['class'] ) ) ? '' : $attrs['class'];
        $custom_class = (string) WPMT()->settings->get_setting( 'class_name', true );
        $activated_protection = ( in_array( (int) WPMT()->settings->get_setting( 'protect', true ), array( 1, 2 ) ) ) ? true : false;
        $security_check = (string) WPMT()->settings->get_setting( 'security_check', true );

        // set user-defined class
        if ( $custom_class && strpos( $class_ori, $custom_class ) === FALSE ) {
            $attrs['class'] = ( empty( $attrs['class'] ) ) ? $custom_class : $attrs['class'] . ' ' . $custom_class;
        }

        // check title for email address
        if ( ! empty( $attrs['title'] ) ) {
            $attrs['title'] = $this->filter_plain_emails( $attrs['title'], '{{email}}' ); // {{email}} will be replaced in javascript
        }

        // set ignore to data-attribute to prevent being processed by WPEL plugin
        $attrs['data-wpel-link'] = 'ignore';

        // create element code
        $link = '<a ';

        foreach ( $attrs AS $key => $value ) {
            if ( strtolower( $key ) == 'href' && $activated_protection ) {
                if( $protection_method === 'without_javascript' ){
                    $link .= $key . '="' . antispambot( $value ) . '" ';
                } else {
                    // get email from href
                    $email = substr($value, 7);

                    $encoded_email = $this->get_encoded_email( $email );

                    // set attrs
                    $link .= 'href="javascript:;" ';
                    $link .= 'data-enc-email="' . $encoded_email . '" ';
                }
                
            } else {
                $link .= $key . '="' . $value . '" ';
            }
        }

        // remove last space
        $link = substr( $link, 0, -1 );

        $link .= '>';

        $link .= ( $activated_protection && preg_match( WPMT()->settings->get_email_regex(), $display) > 0 ) ? $this->get_protected_display( $display, $protection_method ) : $display;

        $link .= '</a>';

        // filter
        $link = apply_filters( 'wpml_mailto', $link, $display, $email, $attrs );

        // just in case there are still email addresses f.e. within title-tag
        $link = $this->filter_plain_emails( $link, null, 'char_encode' );

        // mark link as successfully encoded (for admin users)
        if ( current_user_can( WPMT()->settings->get_admin_cap( 'frontend-display-security-check' ) ) && $security_check ) {
            $link .= '<i class="wpml-encoded dashicons-before dashicons-lock" title="' . WPMT()->helpers->translate( 'Email encoded successfully!', 'frontend-security-check-title' ) . '"></i>';
        }


        return $link;
    }

    /**
     * Create protected display combining these 3 methods:
     * - reversing string
     * - adding no-display spans with dummy values
     * - using the wp antispambot function
     *
     * @param string|array $display
     * @return string Protected display
     */
    public function get_protected_display( $display, $protection_method = null ){

        $convert_plain_to_image = (bool) WPMT()->settings->get_setting( 'convert_plain_to_image', true, 'filter_body' );
        $protection_text = WPMT()->helpers->translate( WPMT()->settings->get_setting( 'protection_text', true ), 'email-protection-text' );

        // get display out of array (result of preg callback)
        if ( is_array( $display ) ) {
            $display = $display[0];
        }

        if( $convert_plain_to_image ){
            return '<img src="' . $this->generate_email_image_url( $display ) . '" />';
        }

        if( $protection_method !== 'without_javascript' ){
            return $this->dynamic_js_email_encoding( $display, $protection_text );
        }

        return $this->encode_email_css( $display );
        
    }

    /**
     * Dynamic email encoding with certain javascript methods
     *
     * @param string $email
     * @param string $protection_text
     * @return the encoded email
     */
    public function dynamic_js_email_encoding( $email, $protection_text = null ){
        $return = $email;
        $rand = apply_filters( 'wpmt/validate/random_encoding', rand(0,2), $email, $protection_text );
        switch( $rand ){
            case 2:
                $return = $this->encode_escape( $return, $protection_text );
                break;
            case 1:
                $return = $this->encode_ascii( $return, $protection_text );
                break;
            default:
                $return = $this->encode_ascii( $return, $protection_text );
                break;
        }

        return $return;
    }

    public function encode_email_css( $display ){
        $deactivate_rtl = (bool) WPMT()->settings->get_setting( 'deactivate_rtl', true, 'filter_body' );

        $stripped_display = strip_tags( $display );
        $stripped_display = html_entity_decode( $stripped_display );

        $length = strlen( $stripped_display );
        $interval = ceil( min( 5, $length / 2 ) );
        $offset = 0;
        $dummy_data = time();
        $protected = '';
        $protection_classes = 'wpmt';

        if( $deactivate_rtl ){
            $rev = $stripped_display;
            $protection_classes .= ' wpmt-nrtl';
        } else {
            // reverse string ( will be corrected with CSS )
            $rev = strrev( $stripped_display );
            $protection_classes .= ' wpml-rtl';
        }
       

        while ( $offset < $length ) {
            $protected .= '<span class="wpml-sd">' . antispambot( substr( $rev, $offset, $interval ) ) . '</span>';

            // setup dummy content
            $protected .= '<span class="wpml-nodis">' . $dummy_data . '</span>';
            $offset += $interval;
        }

        $protected = '<span class="' . $protection_classes . '">' . $protected . '</span>';

        return $protected;
    }

    public function email_to_image( $email, $image_string_color = 'default', $image_background_color = 'default', $alpha_string = 0, $alpha_fill = 127, $font_size = 4 ){
        
        $setting_image_string_color = (string) WPMT()->settings->get_setting( 'image_color', true, 'image_settings' );
        $setting_image_background_color = (string) WPMT()->settings->get_setting( 'image_background_color', true, 'image_settings' );
        $image_text_opacity = (int) WPMT()->settings->get_setting( 'image_text_opacity', true, 'image_settings' );
        $image_background_opacity = (int) WPMT()->settings->get_setting( 'image_background_opacity', true, 'image_settings' );
        $image_font_size = (int) WPMT()->settings->get_setting( 'image_font_size', true, 'image_settings' );

        if( $image_background_color === 'default' ){
            $image_background_color = $setting_image_background_color;
        } else {
            $image_background_color = '0,0,0';
        }

        $colors = explode( ',', $image_background_color );
        $bg_red = $colors[0];
        $bg_green = $colors[1];
        $bg_blue = $colors[2];

        if( $image_string_color === 'default' ){
            $image_string_color = $setting_image_string_color;
        } else {
            $image_string_color = '0,0,0';
        }

        $colors = explode( ',', $image_string_color );
        $string_red = $colors[0];
        $string_green = $colors[1];
        $string_blue = $colors[2];

        if( ! empty( $image_text_opacity ) && $image_text_opacity >= 0 && $image_text_opacity <= 127 ){
            $alpha_string = intval( $image_text_opacity );
        }

        if( ! empty( $image_background_opacity ) && $image_background_opacity >= 0 && $image_background_opacity <= 127 ){
            $alpha_fill = intval( $image_background_opacity );
        }

        if( ! empty( $image_font_size ) && $image_font_size >= 1 && $image_font_size <= 5 ){
            $font_size = intval( $image_font_size );
        }

        $img = imagecreatetruecolor( imagefontwidth( $font_size ) * strlen( $email ), imagefontheight( $font_size ) );
        imagesavealpha( $img, true );
        imagefill( $img, 0, 0, imagecolorallocatealpha ($img, $bg_red, $bg_green, $bg_blue, $alpha_fill ) );
        imagestring( $img, $font_size, 0, 0, $email, imagecolorallocatealpha( $img, $string_red, $string_green, $string_blue, $alpha_string ) );
        ob_start();
        imagepng( $img );
        imagedestroy( $img );

        return ob_get_clean ();
    }

    public function generate_email_signature( $email, $secret ) {
        
        if( ! $secret ){
            return false;
        }

		$hash_signature = apply_filters( 'wpmt/validate/email_signature', 'sha256', $email );

		return base64_encode( hash_hmac( $hash_signature, $email, $secret, true ) );
	}

    public function generate_email_image_url( $email ) {
        
        if( empty( $email ) || ! is_email( $email ) ){
            return false;
        }

        $secret = WPMT()->settings->get_email_image_secret();
        $signature = $this->generate_email_signature( $email, $secret );
        $url = home_url() . '?wpmt_mail=' . urlencode( base64_encode( $email ) ) . '&wpmt_hash=' . urlencode( $signature );

		$url = apply_filters( 'wpmt/validate/generate_email_image_url', $url, $email );

		return $url;
	}

}

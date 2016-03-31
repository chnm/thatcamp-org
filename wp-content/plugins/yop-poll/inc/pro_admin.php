<?php

class YOP_POLL_Pro_Admin extends YOP_POLL_Abstract_Admin {

    private static $_instance = null;

    public static function get_instance() {
        if( self::$_instance == null ) {
            $class           = __CLASS__;
            self::$_instance = new $class;
        }

        return self::$_instance;
    }

    protected function __construct() {
        parent::__construct( 'pro' );
    }
    public function yop_poll_help() {
        $data                    = array();
        $data['title']    = __yop_poll( "Help!" );
        $data['REQUEST']  = $_REQUEST;
        $data['poll_url'] =YOP_POLL_URL;
        $this->display( 'help.html', $data );

    }
    public function manage_pages() {
        global $action;
        switch( $action ) {
            case "after-buy":
                $this->after_buy();
                break;
            case "do-buy":
                $this->do_buy();
                break;
            case "validate_licence";
                $this->check_licence();
            default:
                $this->before_buy();
                break;
        }
    }

    private function before_buy() {
        $data['title'] = __yop_poll( "Before You Buy" );
        $data['poll_url']=YOP_POLL_URL;
        wp_enqueue_script( 'yop-poll-admin-js', YOP_POLL_URL . '/js/polls/yop-poll-admin.js', array(
            'jquery',
            'jquery-ui-datepicker'
        ), YOP_POLL_VERSION, true );
        wp_enqueue_style( 'yop-poll-slider-css', YOP_POLL_URL . 'css/yop-poll-slider.css', array(), YOP_POLL_VERSION );
        wp_enqueue_script( 'yop-poll-slider-js', YOP_POLL_URL . 'js/yop-poll-slider.js', array(
            'jquery',
            'jquery-ui-dialog'
        ), YOP_POLL_VERSION, true );


        $this->display( 'pre_upgrade.html', $data );
    }
    public function check_licence(){
        $domain = isset( $_SERVER['HTTP_HOST'] ) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
       $domain=str_replace("www.","",$domain);
        if( isset( $_POST['licence'] ) && $_POST['licence'] != "" ) {
            if( check_admin_referer( 'yop-poll-before-buy' ) ) {
                $fields        = array(

                    "action"     => "check_license",

                    "pro_key"    => $_POST['licence'],

                    'domain'     => $domain,

                    'user-agent' => 'WordPress/;' . get_bloginfo( 'url' )

                );
                $url = "http://yop-poll.com/yop-poll-pro/";
                $fields_string = "";


                foreach( $fields as $key => $value ) {

                    $fields_string .= $key . '=' . $value . '&';

                }





                $request_string = array(

                    'body'       => array(

                        "action"  => "check_license",

                        "pro_key" =>  $_POST['licence'],

                        'domain'  => $domain,

                    ),

                    'user-agent' => 'WordPress/' . YOP_POLL_WP_VERSION . '; ' . get_bloginfo( 'url' )

                );




                // Start checking for an update

                $result = wp_remote_post( $url, $request_string );


                if( ! is_wp_error( $result ) && ( $result['response']['code'] == 200 ) ) {

                    $response = unserialize( $result['body'] );

                }

                if( is_object( $response ) && ! empty( $response ) ) // Feed the update data into WP updater

                {

                    if( isset( $response->status ) && $response->status == "200" ) {

                        if( isset( $response->error ) && '' != $response->error ) {

                            wp_die( $response->error );

                        }

                        else {

                            $pro_options            = get_option( 'yop_poll_pro' );

                            $pro_options['pro_key'] = $response->license;

                            update_option( 'yop_poll_pro', $pro_options );
                            update_option( "yop_poll_pro", $pro_options );
                            $download_link          = 'http://yop-poll.com/yop-poll-pro/upgrade.php?action=downloadPackage&domain='.$domain.'&rand='.$response->rand;

                            require_once YOP_POLL_PATH . "upgrade.php";

                        }

                    }

                    else {

                        wp_die( "Pro License Key is not Valid" );

                    }

                }

                else {

                    wp_die( "Pro License Key is not Valid" );

                }
            }
        }

    }
    public static function do_buy() {
        if( isset( $_POST['do_buy'] ) && ($_POST['do_buy'] == "Get License" || $_POST['do_buy']=="Upgrade to Pro for only $17"|| $_POST['do_buy']=="Upgrade To Pro For Only $17") ) {
            if( check_admin_referer( 'yop-poll-before-buy' ) ) {
                //get domain name
                $domain = isset( $_SERVER['HTTP_HOST'] ) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
                $domain=str_replace("www.","",$domain);
                $domain = urlencode( $domain );

                if( '' != $domain ) {
                    $rand_number                = rand( 10000, 99999 );
                    $pro_options                = get_option( "yop_poll_pro" );
                    $pro_options['rand_number'] = $rand_number;
                    update_option( "yop_poll_pro", $pro_options );
                    $optin_box_modal_options = get_option( 'yop_poll_optin_box_modal_options_yop' );
                    $email=isset($optin_box_modal_options['modal_email'])?$optin_box_modal_options['modal_email']:"johndoe@email.com";
                    $redirect_url            = urlencode( "https://yop-poll.com/yop-licence/upgrade.php?action=getPackageLink&domain=" .
                        urlencode( $domain ) . "&rand=" .
                        urlencode( $rand_number )."&yop_email=".
                        urlencode($email).
                        "&redirect_after=". urlencode( add_query_arg( array(
                        "page"   => "yop-polls-become-pro",
                        "action" => 'after-buy'
                    ), admin_url( 'admin.php' ) ) )
                    );
                    $paypalUrl    = "https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=RVNRVF7HWHR4Q&return=" .
                        "$redirect_url&rm=2&custom=" .
                        urlencode( $domain . ";" .
                            $rand_number. ";" .$email
                        ) . "";
                ;

                    echo "<p>" . __yop_poll( "
						Please wait while you are being redirected to PayPal<br>If you are not redirected within 10 seconds, please click " ) .
                        "<a href='$paypalUrl'>" . __yop_poll( "here" ) . "</a></p>";
                    echo "<script>window.location = '$paypalUrl';</script>";
                }
                else {
                    wp_die( __yop_poll( "There was an error while determining your domain name. Please try again later or contact support team if problem persists!" ) );
                }
            }


        }
    }

    public function after_buy() {
        sleep(8);
        $data['title'] = __yop_poll( "Finish Upgrade" );
        $domain        = isset( $_SERVER['HTTP_HOST'] ) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
        $domain=str_replace("www.","",$domain);
        $pro_options   = get_option( "yop_poll_pro" );
        $rand          = $pro_options['rand_number'];
        $ch            = curl_init( "http://yop-poll.com/yop-poll-pro/upgrade.php?action=getPackageLink&domain=" .
            urlencode( $domain ) . "&rand=" .
            urlencode( $rand )
        );

        curl_setopt( $ch, CURLOPT_HEADER, 0 );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );

        $result = curl_exec( $ch );
        curl_close( $ch );
        $result = json_decode( $result, true );
        if( isset( $result['error'] ) && "Error" == $result['error'] ) {
            $data['error'] = $result['message'];
            $this->display( 'after_upgrade.html', $data );
        }
        else {
            $pro_options            = get_option( "yop_poll_pro" );
            $pro_options['pro_key'] = $result['licence'];
            $data['success']        = $result['message'];
            $download_link          = $result['download_link'] . "&domain=$domain&rand=$rand";
            update_option( "yop_poll_pro", $pro_options );
           require_once YOP_POLL_PATH . "upgrade.php";
           $this->display( 'after_upgrade.html', $data );
        }
    }

    private function parse_api_result( $result ) {
        $pattern = '\[(\[?)(response)\b([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)';
        if( preg_match( "/$pattern/s", $result, $matches ) ) {
            $response = json_decode( $matches[5], true );
            if( $response ) {
                $this->api_response    = $response;
                $this->api_status      = $response['status'];
                $this->api_error_msg   = $response['error_message'];
                $this->api_success_msg = $response['success_message'];
                $this->api_return_data = $response['return_data'];
                if( $this->api_status == 'success' ) {
                    return true;
                }
                else {
                    $this->error = $this->api_error_msg;
                    return false;
                }
            }
        }
        $this->error = __( 'Invalid Response From Api Server!', 'yop_poll' );

        return false;
    }

    public function curl_post( $url, array $post = null, array $options = array() ) {
        $defaults = array(
            CURLOPT_POST           => 1,
            CURLOPT_HEADER         => 0,
            CURLOPT_URL            => $url,
            CURLOPT_FRESH_CONNECT  => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE   => 1,
            CURLOPT_TIMEOUT        => 4,
            CURLOPT_POSTFIELDS     => http_build_query( $post )
        );

        $ch = curl_init();
        curl_setopt_array( $ch, ( $options + $defaults ) );
        if( ! $result = curl_exec( $ch ) ) {
            $this->error = curl_error( $ch );

            return false;
        }
        curl_close( $ch );

        return $result;
    }

    public function curl_get( $url, array $get = null, array $options = array() ) {
        $defaults = array(
            CURLOPT_URL            => $url . ( strpos( $url, '?' ) === false ? '?' : '' ) . http_build_query( $get ),
            CURLOPT_HEADER         => 0,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 4
        );

        $ch = curl_init();
        curl_setopt_array( $ch, ( $options + $defaults ) );
        if( ! $result = curl_exec( $ch ) ) {
            $this->error = curl_error( $ch );

            return false;
        }
        curl_close( $ch );

        return $result;
    }
}
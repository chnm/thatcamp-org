<?php
abstract class Yop_Poll_Plugin{
    protected $_config;
    public function __construct( Yop_Poll_Config $config ) {
        $this->init();
    }
    abstract protected function init();
    protected function add_action( $action, $function = '', $priority = 10, $accepted_args = 1 ) {
        add_action( $action, array( $this, $function == '' ? $action : $function ), $priority, $accepted_args );
    }
    protected function remove_action( $action, $function = '', $priority = 10 ) {
        remove_action( $action, array( $this, $function == '' ? $action : $function ), $priority );
    }
    protected function add_filter( $filter, $function, $priority = 10, $accepted_args = 1 ) {
        add_filter( $filter, array( $this, $function == '' ? $filter : $function ), $priority, $accepted_args );
    }
    protected function remove_filter( $filter, $function = '', $priority = 10 ) {
        remove_filter( $filter, array( $this, $function == '' ? $filter : $function ), $priority );
    }
    protected function wp_ajax_action( $function = '', $priv = true, $nopriv = false, $priority = 1 ) {
        $request = $this->make_request( $function );
        if ( $request ){
            if ( $priv ){
                add_action( 'wp_ajax_yop_poll_' . $request[1], array( $request[0], $request[1] ), $priority, 1 );
            }
            if ( $nopriv ){
                add_action( 'wp_ajax_nopriv_yop_poll_' . $request[1], array( $request[0], $request[1] ), $priority, 1 );
            }
        }
    }
    private function make_request( $function = '' ) {
        $request = array();
        if ( '' == $function ){
            return false;
        }
        if ( is_array( $function ) ){
            $request[0] = $function[0];
            $request[1] = $function[1];
        }
        if ( is_string( $function ) ){
            $request[0] = $this;
            $request[1] = $function;
        }
        return $request;
    }
}

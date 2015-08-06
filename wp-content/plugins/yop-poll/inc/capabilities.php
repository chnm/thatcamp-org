<?php

class YOP_POLL_Capabilities{

    private static $_instance = NULL;
    private $capabilities = array(
        'manage_yop_polls_options'       => array(
            'administrator' => true,
            'editor'        => true,
            'author'        => false,
            'contributor'   => false,
            'subscriber'    => false
        ),
        'manage_yop_polls_imports'       => array(
            'administrator' => true,
            'editor'        => true,
            'author'        => false,
            'contributor'   => false,
            'subscriber'    => false
        ),
        'manage_yop_polls_bans'          => array(
            'administrator' => true,
            'editor'        => false,
            'author'        => false,
            'contributor'   => false,
            'subscriber'    => false
        ),
        'delete_yop_polls'               => array(
            'administrator' => true,
            'editor'        => false,
            'author'        => false,
            'contributor'   => false,
            'subscriber'    => false
        ),
        'delete_own_yop_polls'           => array(
            'administrator' => true,
            'editor'        => true,
            'author'        => true,
            'contributor'   => false,
            'subscriber'    => false
        ),
        'edit_yop_polls'                 => array(
            'administrator' => true,
            'editor'        => true,
            'author'        => false,
            'contributor'   => false,
            'subscriber'    => false
        ),
        'edit_own_yop_polls'             => array(
            'administrator' => true,
            'editor'        => true,
            'author'        => true,
            'contributor'   => false,
            'subscriber'    => false
        ),
        'clone_yop_polls'                => array(
            'administrator' => true,
            'editor'        => true,
            'author'        => false,
            'contributor'   => false,
            'subscriber'    => false
        ),
        'clone_own_yop_polls'            => array(
            'administrator' => true,
            'editor'        => true,
            'author'        => true,
            'contributor'   => false,
            'subscriber'    => false
        ),
        'view_yop_polls_logs'            => array(
            'administrator' => true,
            'editor'        => true,
            'author'        => false,
            'contributor'   => false,
            'subscriber'    => false
        ), 'view_yop_polls_imports'      => array(
            'administrator' => true,
            'editor'        => true,
            'author'        => false,
            'contributor'   => false,
            'subscriber'    => false
        ),
        'view_own_yop_polls_logs'        => array(
            'administrator' => true,
            'editor'        => true,
            'author'        => true,
            'contributor'   => false,
            'subscriber'    => false
        ),
        'view_yop_polls_results'         => array(
            'administrator' => true,
            'editor'        => true,
            'author'        => false,
            'contributor'   => false,
            'subscriber'    => false
        ),
        'view_own_yop_polls_results'     => array(
            'administrator' => true,
            'editor'        => true,
            'author'        => true,
            'contributor'   => false,
            'subscriber'    => false
        ),
        'reset_yop_polls_stats'          => array(
            'administrator' => true,
            'editor'        => true,
            'author'        => false,
            'contributor'   => false,
            'subscriber'    => false
        ),
        'reset_own_yop_polls_stats'      => array(
            'administrator' => true,
            'editor'        => true,
            'author'        => true,
            'contributor'   => false,
            'subscriber'    => false
        ),
        'delete_yop_polls_logs'          => array(
            'administrator' => true,
            'editor'        => true,
            'author'        => false,
            'contributor'   => false,
            'subscriber'    => false
        ),
        's'                              => array(
            'administrator' => true,
            'editor'        => true,
            'author'        => true,
            'contributor'   => false,
            'subscriber'    => false
        ),
        'edit_yop_polls_templates'       => array(
            'administrator' => true,
            'editor'        => true,
            'author'        => false,
            'contributor'   => false,
            'subscriber'    => false
        ),
        'edit_own_yop_polls_templates'   => array(
            'administrator' => true,
            'editor'        => true,
            'author'        => true,
            'contributor'   => false,
            'subscriber'    => false
        ),
        'delete_yop_polls_templates'     => array(
            'administrator' => true,
            'editor'        => true,
            'author'        => false,
            'contributor'   => false,
            'subscriber'    => false
        ),
        'delete_own_yop_polls_templates' => array(
            'administrator' => true,
            'editor'        => true,
            'author'        => true,
            'contributor'   => false,
            'subscriber'    => false
        ),
        'clone_yop_polls_templates'      => array(
            'administrator' => true,
            'editor'        => true,
            'author'        => false,
            'contributor'   => false,
            'subscriber'    => false
        ),
        'clone_own_yop_polls_templates'  => array(
            'administrator' => true,
            'editor'        => true,
            'author'        => true,
            'contributor'   => false,
            'subscriber'    => false
        ),
        'import_wp_polls'  => array(
            'administrator' => true,
            'editor'        => true,
            'author'        => true,
            'contributor'   => false,
            'subscriber'    => false
        ),
        'add_yop_poll_votes'  => array(
            'administrator' => true,
            'editor'        => true,
            'author'        => true,
            'contributor'   => false,
            'subscriber'    => false
        ),
        'become_yop_poll_pro'            => array(
            'administrator' => true,
            'editor'        => false,
            'author'        => false,
            'contributor'   => false,
            'subscriber'    => false

        ),
        'help_yop_poll_page'            => array(
            'administrator' => true,
            'editor'        => true,
            'author'        => true,
            'contributor'   => false,
            'subscriber'    => false

        )
    );

    public static function get_instance() {
        if ( self::$_instance == NULL ){
            $class           = __CLASS__;
            self::$_instance = new $class;
        }
        return self::$_instance;
    }

    private function __construct() {
    }

    private function __clone() {
    }

    public function install_capabilities() {
        $roles = $GLOBALS['wp_roles']->roles;
        global $wp_roles;

        if ( ! isset( $roles ) ) {
            $wp_roles = new WP_Roles();
            $roles=$wp_roles->get_names();
        }
        if ( is_array( $roles) ){
            foreach ( $roles as $key => $value ) {
                $this->add_role_capabilities( $key );
            }
        }
    }

    public function uninstall_capabilities() {
        $roles = $GLOBALS['wp_roles']->roles;
        if ( is_array( $roles ) ){
            foreach ( $roles as $key => $value ) {
                $this->remove_role_capabilities( $key );
            }
        }
    }

    public function add_role_capabilities( $role_name ) {

        foreach ( $this->capabilities as $capability => $roles ) {
            if ( isset( $roles[$role_name] ) ){
                if ( $roles[$role_name] ){
                    $this->add_capability( $role_name, $capability );
                }
            }
            /*else
            $this->remove_capability( $role_name, $capability );*/
        }
    }

    public function remove_role_capabilities( $role_name ) {
        foreach ( $this->capabilities as $capability => $roles ) {
            $this->remove_capability( $role_name, $capability );
        }
    }

    public function add_capability( $role_name, $capability ) {
        $role = get_role( $role_name );
        if ( $role ){
            $role->add_cap( $capability );
        }
    }

    public function remove_capability( $role_name, $capability ) {
        $role = get_role( $role_name );
        if ( $role ){
            $role->remove_cap( $capability );
        }
    }
}
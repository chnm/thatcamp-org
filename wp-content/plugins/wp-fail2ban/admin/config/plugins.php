<?php

/**
 * Settings - Plugins
 *
 * @package wp-fail2ban
 * @since 4.2.0
 */
namespace org\lecklider\charles\wordpress\wp_fail2ban;

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Tab: Plugins
 *
 * @since 4.2.0
 */
class TabPlugins extends Tab
{
    /**
     * {@inheritDoc}
     */
    public function __construct()
    {
        add_action( 'admin_init', [ $this, 'admin_init' ], 100 );
        parent::__construct( 'plugins', 'Plugins' );
    }
    
    /**
     * {@inheritDoc}
     *
     * @since 4.0.0
     */
    public function admin_init()
    {
        // phpcs:disable Generic.Functions.FunctionCallArgumentSpacing
        add_settings_section(
            'wp-fail2ban-plugins',
            __( 'Event Class Facilities' ),
            [ $this, 'sectionLoggingEventClasses' ],
            'wp-fail2ban-plugins'
        );
        add_settings_field(
            'plugins-log-auth',
            parent::doc_link( 'WP_FAIL2BAN_PLUGIN_LOG_AUTH', __( 'Authentication' ) ),
            [ $this, 'auth' ],
            'wp-fail2ban-plugins',
            'wp-fail2ban-plugins'
        );
        add_settings_field(
            'plugins-log-comment',
            parent::doc_link( 'WP_FAIL2BAN_PLUGIN_LOG_COMMENT', __( 'Comment' ) ),
            [ $this, 'comment' ],
            'wp-fail2ban-plugins',
            'wp-fail2ban-plugins'
        );
        add_settings_field(
            'plugins-log-password',
            parent::doc_link( 'WP_FAIL2BAN_PLUGIN_LOG_PASSWORD', __( 'Password' ) ),
            [ $this, 'password' ],
            'wp-fail2ban-plugins',
            'wp-fail2ban-plugins'
        );
        add_settings_field(
            'plugins-log-rest',
            parent::doc_link( 'WP_FAIL2BAN_PLUGIN_LOG_REST', __( 'REST' ) ),
            [ $this, 'rest' ],
            'wp-fail2ban-plugins',
            'wp-fail2ban-plugins'
        );
        add_settings_field(
            'plugins-log-spam',
            parent::doc_link( 'WP_FAIL2BAN_PLUGIN_LOG_SPAM', __( 'Spam' ) ),
            [ $this, 'spam' ],
            'wp-fail2ban-plugins',
            'wp-fail2ban-plugins'
        );
        add_settings_field(
            'plugins-log-xmlrpc',
            parent::doc_link( 'WP_FAIL2BAN_PLUGIN_LOG_XMLRPC', __( 'XML-RPC' ) ),
            [ $this, 'xmlrpc' ],
            'wp-fail2ban-plugins',
            'wp-fail2ban-plugins'
        );
        // phpcs:enable
    }
    
    /**
     * {@inheritDoc}
     *
     * @since 4.2.0
     */
    public function render()
    {
        parent::render();
    }
    
    /**
     * {@inheritDoc}
     *
     * @since 4.2.0
     *
     * @param array $settings   {@inheritDoc}
     * @param array $input      {@inheritDoc}
     *
     * @return array    {@inheritDoc}
     */
    public function sanitize( array $settings, array $input = null )
    {
        return $settings;
    }
    
    /**
     * Section summary.
     *
     * @since 4.2.0
     */
    public function sectionLoggingEventClasses()
    {
        echo  __( 'Facilities to use for plugin-generated messages. The defaults follow the Core defaults.' ) ;
    }
    
    /**
     * Auth
     *
     * @since 4.2.0
     */
    public function auth()
    {
        $this->log( 'WP_FAIL2BAN_PLUGIN_LOG_AUTH', 'WP_FAIL2BAN_PLUGIN_AUTH_LOG' );
    }
    
    /**
     * Comment
     *
     * @since 4.2.0
     */
    public function comment()
    {
        $this->log( 'WP_FAIL2BAN_PLUGIN_LOG_COMMENT', 'WP_FAIL2BAN_PLUGIN_COMMENT_LOG' );
    }
    
    /**
     * Password
     *
     * @since 4.2.0
     */
    public function password()
    {
        $this->log( 'WP_FAIL2BAN_PLUGIN_LOG_PASSWORD', 'WP_FAIL2BAN_PLUGIN_PASSWORD_LOG' );
    }
    
    /**
     * REST
     *
     * @since 4.2.0
     */
    public function rest()
    {
        $this->log( 'WP_FAIL2BAN_PLUGIN_LOG_REST', 'WP_FAIL2BAN_PLUGIN_REST_LOG' );
    }
    
    /**
     * Spam
     *
     * @since 4.2.0
     */
    public function spam()
    {
        $this->log( 'WP_FAIL2BAN_PLUGIN_LOG_SPAM', 'WP_FAIL2BAN_PLUGIN_SPAM_LOG' );
    }
    
    /**
     * XML-RPC
     *
     * @since 4.2.0
     */
    public function xmlrpc()
    {
        $this->log( 'WP_FAIL2BAN_PLUGIN_LOG_XMLRPC', 'WP_FAIL2BAN_PLUGIN_XMLRPC_LOG' );
    }

}
new TabPlugins();
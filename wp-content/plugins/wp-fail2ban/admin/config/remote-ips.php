<?php

/**
 * Settings - Remote IPs
 *
 * @package wp-fail2ban
 * @since 4.0.0
 */
namespace org\lecklider\charles\wordpress\wp_fail2ban;

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Tab: Remote IPs
 *
 * @since 4.0.0
 */
class TabRemoteIPs extends Tab
{
    /**
     * {@inheritDoc}
     *
     * @since 4.0.0
     */
    public function __construct()
    {
        add_action( 'admin_init', [ $this, 'admin_init' ] );
        parent::__construct( 'remote-ips', 'Remote IPs' );
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
            'wp-fail2ban-proxies',
            __( 'Proxies' ),
            [ $this, 'section' ],
            'wp-fail2ban-remote-ips'
        );
        add_settings_field(
            'remote-ips-proxies',
            parent::doc_link( 'WP_FAIL2BAN_PROXIES', __( 'IP list' ) ),
            [ $this, 'proxies' ],
            'wp-fail2ban-remote-ips',
            'wp-fail2ban-proxies'
        );
        // phpcs:enable
    }
    
    /**
     * {@inheritDoc}
     *
     * @since 4.0.0
     *
     * @param array $settings
     * @param array $input
     */
    public function sanitize( array $settings, array $input = null )
    {
        return $settings;
    }
    
    /**
     * Section blurb.
     *
     * @since 4.0.0
     */
    public function section()
    {
        echo  '' ;
    }
    
    /**
     * Proxies.
     *
     * @since 4.0.0
     */
    public function proxies()
    {
        $value = '';
        if ( defined( 'WP_FAIL2BAN_PROXIES' ) ) {
            
            if ( is_array( WP_FAIL2BAN_PROXIES ) ) {
                $value = join( "\n", WP_FAIL2BAN_PROXIES );
            } else {
                $value = join( "\n", array_map( 'trim', explode( ',', WP_FAIL2BAN_PROXIES ) ) );
            }
        
        }
        printf( '<fieldset><textarea class="code" cols="20" rows="10" disabled="disabled">%s</textarea></fieldset>', esc_html( $value ) );
    }

}
new TabRemoteIPs();
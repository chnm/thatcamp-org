<?php

/**
 * Settings - Block
 *
 * @package wp-fail2ban
 * @since 4.0.0
 */
namespace org\lecklider\charles\wordpress\wp_fail2ban;

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Tab: Block
 *
 * @since 4.0.0
 */
class TabBlock extends Tab
{
    /**
     * {@inheritDoc}
     *
     * @since 4.0.0
     */
    public function __construct()
    {
        add_action( 'admin_init', [ $this, 'admin_init' ] );
        parent::__construct( 'block', 'Users' );
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
            'wp-fail2ban-block',
            __( 'Block' ),
            [ $this, 'section' ],
            'wp-fail2ban-block'
        );
        add_settings_field(
            'block-user-enumeration',
            parent::doc_link( 'WP_FAIL2BAN_BLOCK_USER_ENUMERATION', __( 'User Enumeration' ) ),
            [ $this, 'userEnumeration' ],
            'wp-fail2ban-block',
            'wp-fail2ban-block'
        );
        add_settings_field(
            'block-users',
            parent::doc_link( 'WP_FAIL2BAN_BLOCKED_USERS', __( 'Usernames' ) ),
            [ $this, 'usernames' ],
            'wp-fail2ban-block',
            'wp-fail2ban-block'
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
     * {@inheritDoc}
     *
     * @since 4.0.0
     */
    public function section()
    {
        echo  '' ;
    }
    
    /**
     * User Enumeration
     *
     * @since 4.0.0
     */
    public function userEnumeration()
    {
        printf( '<input type="checkbox" disabled="disabled" %s>', checked( WP_FAIL2BAN_BLOCK_USER_ENUMERATION, true, false ) );
    }
    
    /**
     * Blocked usernames
     *
     * @since 4.0.0
     */
    public function usernames()
    {
        
        if ( defined( 'WP_FAIL2BAN_BLOCKED_USERS' ) ) {
            
            if ( is_array( WP_FAIL2BAN_BLOCKED_USERS ) ) {
                $value = join( ', ', WP_FAIL2BAN_BLOCKED_USERS );
            } else {
                $value = WP_FAIL2BAN_BLOCKED_USERS;
            }
        
        } else {
            $value = '';
        }
        
        printf( '<input class="regular-text" type="text" disabled="disabled" value="%s">', esc_attr( $value ) );
    }

}
new TabBlock();
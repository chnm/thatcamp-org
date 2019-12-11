<?php

/**
 * Settings - Logging
 *
 * @package wp-fail2ban
 * @since 4.0.0
 */
namespace org\lecklider\charles\wordpress\wp_fail2ban;

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Tab: Logging
 *
 * @since 4.0.0
 */
class TabLogging extends Tab
{
    /**
     * {@inheritDoc}
     */
    public function __construct()
    {
        add_action( 'admin_init', [ $this, 'admin_init' ], 100 );
        parent::__construct( 'logging', 'Logging' );
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
            'wp-fail2ban-logging',
            __( 'What & Where' ),
            [ $this, 'sectionWhatWhere' ],
            'wp-fail2ban-logging'
        );
        add_settings_field(
            'logging-log-authentication',
            parent::doc_link( 'WP_FAIL2BAN_AUTH_LOG', __( 'Authentication' ) ),
            [ $this, 'authentication' ],
            'wp-fail2ban-logging',
            'wp-fail2ban-logging'
        );
        add_settings_field(
            'logging-log-comments',
            parent::doc_link( 'WP_FAIL2BAN_LOG_COMMENTS', __( 'Comments' ) ),
            [ $this, 'comments' ],
            'wp-fail2ban-logging',
            'wp-fail2ban-logging'
        );
        add_settings_field(
            'logging-log-spam',
            parent::doc_link( 'WP_FAIL2BAN_LOG_SPAM', __( 'Spam' ) ),
            [ $this, 'spam' ],
            'wp-fail2ban-logging',
            'wp-fail2ban-logging'
        );
        add_settings_field(
            'logging-log-password-request',
            parent::doc_link( 'WP_FAIL2BAN_LOG_PASSWORD_REQUEST', __( 'Password Requests' ) ),
            [ $this, 'passwordRequest' ],
            'wp-fail2ban-logging',
            'wp-fail2ban-logging'
        );
        add_settings_field(
            'logging-log-pingbacks',
            parent::doc_link( 'WP_FAIL2BAN_LOG_PINGBACKS', __( 'Pingbacks' ) ),
            [ $this, 'pingbacks' ],
            'wp-fail2ban-logging',
            'wp-fail2ban-logging'
        );
        // phpcs:enable
    }
    
    /**
     * {@inheritDoc}
     *
     * @since 4.0.0
     */
    public function render()
    {
        parent::render();
    }
    
    /**
     * {@inheritDoc}
     *
     * @since 4.0.0
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
     * @since 4.0.0
     */
    public function sectionWhatWhere()
    {
        echo  '' ;
    }
    
    /**
     * Authentication.
     *
     * @since 4.0.0
     */
    public function authentication()
    {
        printf( '<label>%s: %s</label>', __( 'Use facility' ), $this->getLogFacilities( 'WP_FAIL2BAN_AUTH_LOG', true ) );
    }
    
    /**
     * Comments.
     *
     * @since 4.0.0
     */
    public function comments()
    {
        add_filter(
            'wp_fail2ban_log_WP_FAIL2BAN_LOG_COMMENTS',
            [ $this, 'commentsExtra' ],
            10,
            3
        );
        $this->log(
            'WP_FAIL2BAN_LOG_COMMENTS',
            'WP_FAIL2BAN_COMMENT_LOG',
            '',
            [ 'comments-extra', 'logging-comments-extra-facility' ]
        );
    }
    
    /**
     * Comments extra helper - checked.
     *
     * @since 4.0.0
     *
     * @param int   $value  Value to check
     */
    protected function commentExtraChecked( $value )
    {
        if ( !defined( 'WP_FAIL2BAN_LOG_COMMENTS_EXTRA' ) ) {
            return '';
        }
        return checked( $value & WP_FAIL2BAN_LOG_COMMENTS_EXTRA, $value, false );
    }
    
    /**
     * Comments extra helper - disabled.
     *
     * @since 4.0.0
     */
    protected function commentExtraDisabled()
    {
        return 'disabled="disabled';
    }
    
    /**
     * Comments extra.
     *
     * @since 4.0.0
     *
     * @param string $html          HTML prefixed to output
     * @param string $define_name   Not used
     * @param string $define_log    Not used
     *
     * @return string
     */
    public function commentsExtra( $html, $define_name, $define_log )
    {
        $fmt = <<<___HTML___
<table>
  <tr>
    <th>%s</th>
    <td>
      <fieldset id="comments-extra" disabled="disabled">
        <label><input type="checkbox" %s> %s</label><br>
        <label><input type="checkbox" %s> %s</label><br>
        <label><input type="checkbox" %s> %s</label><br>
        <label><input type="checkbox" %s> %s</label><br>
        <label><input type="checkbox" %s> %s</label>
      </fieldset>
    </td>
  </tr>
  <tr>
    <th>%s</th>
    <td>%s</td>
  </tr>
</table>
___HTML___;
        return $html . sprintf(
            $fmt,
            parent::doc_link( 'WP_FAIL2BAN_LOG_COMMENTS_EXTRA', __( 'Also log:' ) ),
            $this->commentExtraChecked( WPF2B_EVENT_COMMENT_NOT_FOUND ),
            __( 'Post not found' ),
            $this->commentExtraChecked( WPF2B_EVENT_COMMENT_CLOSED ),
            __( 'Comments closed' ),
            $this->commentExtraChecked( WPF2B_EVENT_COMMENT_TRASH ),
            __( 'Trash post' ),
            $this->commentExtraChecked( WPF2B_EVENT_COMMENT_DRAFT ),
            __( 'Draft post' ),
            $this->commentExtraChecked( WPF2B_EVENT_COMMENT_PASSWORD ),
            __( 'Password-protected post' ),
            parent::doc_link( 'WP_FAIL2BAN_COMMENT_EXTRA_LOG', __( 'Use facility:' ) ),
            $this->getLogFacilities( 'WP_FAIL2BAN_COMMENT_EXTRA_LOG', false )
        );
    }
    
    /**
     * Password request
     *
     * @since 4.0.0
     */
    public function passwordRequest()
    {
        $this->log( 'WP_FAIL2BAN_LOG_PASSWORD_REQUEST', 'WP_FAIL2BAN_PASSWORD_REQUEST_LOG' );
    }
    
    /**
     * Pingbacks
     *
     * @since 4.0.0
     */
    public function pingbacks()
    {
        $this->log( 'WP_FAIL2BAN_LOG_PINGBACKS', 'WP_FAIL2BAN_PINGBACK_LOG' );
    }
    
    /**
     * Spam
     *
     * @since 4.0.0
     */
    public function spam()
    {
        $this->log( 'WP_FAIL2BAN_LOG_SPAM', 'WP_FAIL2BAN_SPAM_LOG' );
    }

}
new TabLogging();
<?php

/**
 * Settings - syslog
 *
 * @package wp-fail2ban
 * @since 4.0.0
 */
namespace org\lecklider\charles\wordpress\wp_fail2ban;

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Tab: Syslog
 *
 * @since 4.0.0
 */
class TabSyslog extends Tab
{
    /**
     * {@inheritDoc}
     */
    public function __construct()
    {
        add_action( 'admin_init', [ $this, 'admin_init' ], 100 );
        parent::__construct( 'syslog', '<tt>syslog</tt>' );
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
            'wp-fail2ban-connection',
            __( 'Connection' ),
            [ $this, 'sectionConnection' ],
            'wp-fail2ban-syslog'
        );
        add_settings_field(
            'logging-connection',
            parent::doc_link( 'WP_FAIL2BAN_OPENLOG_OPTIONS', __( 'Options' ) ),
            [ $this, 'connection' ],
            'wp-fail2ban-syslog',
            'wp-fail2ban-connection'
        );
        add_settings_section(
            'wp-fail2ban-workarounds',
            __( 'Workarounds' ),
            [ $this, 'sectionWorkarounds' ],
            'wp-fail2ban-syslog'
        );
        add_settings_field(
            'logging-workarounds',
            parent::doc_link( '../syslog', __( 'Options' ) ),
            [ $this, 'workarounds' ],
            'wp-fail2ban-syslog',
            'wp-fail2ban-workarounds'
        );
        // phpcs:enable
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
     * Connection section blurb.
     *
     * @since 4.0.0
     */
    public function sectionConnection()
    {
        echo  '' ;
    }
    
    /**
     * Connection.
     *
     * @since 4.0.0
     */
    public function connection()
    {
        $class = '';
        $fmt = <<<___STR___
<fieldset>
  <label><input type="checkbox" disabled="disabled" %s> <code>LOG_CONS</code></label><br>
  <label><input type="checkbox" disabled="disabled" %s> <code>LOG_PERROR</code></label><br>
  <label><input type="checkbox" disabled="disabled" %s> <code>LOG_PID</code> <em>(%s)</em></label><br>
  <label><input type="radio" disabled="disabled" %s> <code>LOG_NDELAY</code> <em>(%s)</em></label><br>
  <label><input type="radio" disabled="disabled" %s> <code>LOG_ODELAY</code></label>
</fieldset>
___STR___;
        printf(
            $fmt,
            checked( WP_FAIL2BAN_OPENLOG_OPTIONS & LOG_CONS, LOG_CONS, false ),
            checked( WP_FAIL2BAN_OPENLOG_OPTIONS & LOG_PERROR, LOG_PERROR, false ),
            checked( WP_FAIL2BAN_OPENLOG_OPTIONS & LOG_PID, LOG_PID, false ),
            __( 'default' ),
            checked( WP_FAIL2BAN_OPENLOG_OPTIONS & LOG_NDELAY, LOG_NDELAY, false ),
            __( 'default' ),
            checked( WP_FAIL2BAN_OPENLOG_OPTIONS & LOG_ODELAY, LOG_ODELAY, false )
        );
    }
    
    /**
     * Workarounds section blurb.
     *
     * @since 4.0.0
     */
    public function sectionWorkarounds()
    {
        echo  '' ;
    }
    
    /**
     * Workarounds.
     *
     * @since 4.0.0
     */
    public function workarounds()
    {
        $fmt = <<<___STR___
<fieldset>
<label><input type="checkbox" disabled="disabled" %s> %s</label>
<br>
<label><input type="checkbox" disabled="disabled" %s> %s</label>
<br>
<label><input type="checkbox" disabled="disabled" %s> %s</label>
</fieldset>
___STR___;
        printf(
            $fmt,
            checked( @WP_FAIL2BAN_SYSLOG_SHORT_TAG, true, false ),
            __( 'Short Tag' ),
            checked( @WP_FAIL2BAN_HTTP_HOST, true, false ),
            __( 'Specify Host' ),
            checked( @WP_FAIL2BAN_TRUNCATE_HOST, true, false ),
            __( 'Truncate Host' )
        );
    }

}
new TabSyslog();
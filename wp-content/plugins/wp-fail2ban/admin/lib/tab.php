<?php

/**
 * Tab base class
 *
 * @package wp-fail2ban-premium
 * @since 4.0.0
 */
namespace org\lecklider\charles\wordpress\wp_fail2ban;

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Base Tab class
 *
 * @since 4.0.0
 */
abstract class Tab
{
    /**
     * @var array  Array of Tab objects
     */
    protected static  $tabs = array() ;
    /**
     * @var string Active tab slug
     */
    protected static  $active_tab ;
    /**
     * @var string  Tab slug
     */
    protected  $tab_slug ;
    /**
     * @var string  Tab name
     */
    protected  $tab_name ;
    /**
     * Hook: admin_init
     *
     * @since 4.0.0
     */
    public abstract function admin_init();
    
    /**
     * Sanitize and store form fields
     *
     * @since 4.0.0
     *
     * @param array $settings   Settings to update
     * @param array $input      Form fields
     *
     * @return array    $settings
     */
    public abstract function sanitize( array $settings, array $input = null );
    
    /**
     * Contruct.
     *
     * @since 4.0.0
     *
     * @param string    $slug   Tab slug
     * @param string    $name   Tab name
     */
    public function __construct( $slug, $name )
    {
        $this->tab_slug = $slug;
        $this->tab_name = $name;
        self::$tabs[$slug] = $this;
    }
    
    /**
     * Getter - slug
     *
     * @since 4.0.0
     *
     * @return string   Tab slug
     */
    public function getSlug()
    {
        return $this->tab_slug;
    }
    
    /**
     * Getter - name
     *
     * @since 4.0.0
     *
     * @return string   Tab name
     */
    public function getName()
    {
        return $this->tab_name;
    }
    
    /**
     * Render settings section
     *
     * @since 4.0.0
     */
    public function render()
    {
        do_settings_sections( 'wp-fail2ban-' . $this->tab_slug );
    }
    
    /**
     * Helper - tab
     *
     * @since 4.0.0
     *
     * @param string $slug  Tab slug
     *
     * @return Tab  Tab
     */
    public static function getTab( $slug )
    {
        return self::$tabs[$slug];
    }
    
    /**
     * Helper - current tab
     *
     * @since 4.0.0
     *
     * @param string    $default    Default slug
     *
     * @return Tab  Tab
     */
    public static function getActiveTab( $default = null )
    {
        if ( !empty(self::$active_tab) ) {
            return self::$active_tab;
        }
        return self::$active_tab = ( array_key_exists( @$_GET['tab'], self::$tabs ) ? self::$tabs[$_GET['tab']] : self::$tabs[$default] );
    }
    
    /**
     * Helper - tab name
     *
     * @since 4.0.0
     *
     * @param string    $slug   Tab slug
     *
     * @return string   Tab name
     */
    public static function getTabName( $slug )
    {
        return self::getTab( $slug )->getName();
    }
    
    /**
     * Link to documentation
     *
     * @since 4.2.0
     *
     * @param string $define
     * @param string $name
     *
     * @return string
     */
    public static function doc_link( $define, $name )
    {
        static  $wp_f2b_ver ;
        if ( empty($wp_f2b_ver) ) {
            $wp_f2b_ver = substr( WP_FAIL2BAN_VER, 0, strrpos( WP_FAIL2BAN_VER, '.' ) );
        }
        return sprintf(
            '<a href="https://docs.wp-fail2ban.com/en/%s/defines/constants/%s.html" style="text-decoration: none;" target="_blank" title="Documentation"><span class="dashicons dashicons-external" style="vertical-align: text-bottom"></span></a> %s',
            $wp_f2b_ver,
            $define,
            $name
        );
    }
    
    /**
     * Helper - drop-down list of facilities
     *
     * @since 4.0.0
     *
     * @param string    $def        Name of define for selected value
     * @param bool      $_enabled   Enabled?
     */
    protected function getLogFacilities( $def, $_enabled = false )
    {
        $enabled = false;
        $facilities = [
            LOG_AUTH     => 'LOG_AUTH',
            LOG_AUTHPRIV => 'LOG_AUTHPRIV',
            LOG_CRON     => 'LOG_CRON',
            LOG_DAEMON   => 'LOG_DAEMON',
            LOG_KERN     => 'LOG_KERN',
            LOG_LOCAL0   => 'LOG_LOCAL0',
            LOG_LOCAL1   => 'LOG_LOCAL1',
            LOG_LOCAL2   => 'LOG_LOCAL2',
            LOG_LOCAL3   => 'LOG_LOCAL3',
            LOG_LOCAL4   => 'LOG_LOCAL4',
            LOG_LOCAL5   => 'LOG_LOCAL5',
            LOG_LOCAL6   => 'LOG_LOCAL6',
            LOG_LOCAL7   => 'LOG_LOCAL7',
            LOG_LPR      => 'LOG_LPR',
            LOG_MAIL     => 'LOG_MAIL',
            LOG_NEWS     => 'LOG_NEWS',
            LOG_SYSLOG   => 'LOG_SYSLOG',
            LOG_USER     => 'LOG_USER',
            LOG_UUCP     => 'LOG_UUCP',
        ];
        $default = constant( "DEFAULT_{$def}" );
        $value = ( defined( $def ) ? constant( $def ) : $default );
        $str = '<select disabled="disabled">';
        foreach ( $facilities as $facility => $name ) {
            $str .= sprintf(
                '<option value="%s" %s>%s%s</option>',
                $facility,
                selected( $value, $facility, false ),
                $name,
                ( $facility == $default ? __( ' (default)' ) : '' )
            );
        }
        $str .= '</select>';
        return $str;
    }
    
    /**
     * Log helper - enable/disable+facility
     *
     * @since 4.2.0     Moved to Tab
     * @since 4.0.0
     *
     * @param string    $define_name    Name of define to enable logging
     * @param string    $define_log     Name of define for log facility
     * @param string    $description    Description
     * @param array     $toggle         Array of IDs to sync toggle state
     */
    protected function log(
        $define_name,
        $define_log,
        $description = '',
        array $toggle = array()
    )
    {
        $enabled = defined( $define_name ) && true === constant( $define_name );
        $fmt = <<<___FMT___
<label><input type="checkbox" disabled="disabled" %s> Enable logging</label>,
<label>use facility: %s</label>
<p class="description">%s</p>
___FMT___;
        $html = sprintf(
            $fmt,
            checked( $enabled, true, false ),
            $this->getLogFacilities( $define_log ),
            $description
        );
        echo  apply_filters(
            "wp_fail2ban_log_{$define_name}",
            $html,
            $define_name,
            $define_log
        ) ;
    }

}
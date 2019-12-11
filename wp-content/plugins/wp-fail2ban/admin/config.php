<?php

/**
 * Config
 *
 * @package wp-fail2ban
 * @since 4.0.0
 */
namespace org\lecklider\charles\wordpress\wp_fail2ban;

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
require_once 'lib/tab.php';
foreach ( glob( __DIR__ . '/config/*.php' ) as $filename ) {
    require_once $filename;
}
/**
 * Render Security settings.
 *
 * @since 4.2.6
 */
function security()
{
    $tabs = [
        'logging',
        'syslog',
        'block',
        'remote-ips'
    ];
    if ( version_compare( PHP_VERSION, '5.6.0', '>=' ) ) {
        $tabs[] = 'plugins';
    }
    $page = 'wp-fail2ban';
    if ( wf_fs()->is_premium() ) {
        $page .= '-premium';
    }
    render_tabs( $tabs, 'logging', $page );
}

/**
 * Render Settings.
 *
 * @since 4.2.6
 */
function settings()
{
    $tabs = [];
    
    if ( !function_exists( '\\add_security_page' ) ) {
        $tabs = [
            'logging',
            'syslog',
            'block',
            'remote-ips'
        ];
        if ( version_compare( PHP_VERSION, '5.6.0', '>=' ) ) {
            $tabs[] = 'plugins';
        }
    }
    
    $default = 'logging';
    render_tabs( $tabs, $default, 'wpf2b-settings' );
}

/**
 * Render Tabs.
 *
 * @since 4.2.6
 *
 * @param array     $tabs       List of slugs of tabs to render
 * @param string    $default    Default tab slug
 * @param string    $menu       Menu slug
 */
function render_tabs( array $tabs, $default, $menu )
{
    ?>
<div class="wrap">
    <?php 
    echo  apply_filters( __METHOD__ . '.title', "<h1>WP fail2ban</h1>" ) ;
    ?>
  <hr class="wp-header-end">

  <h2 class="nav-tab-wrapper wp-clearfix">
    <?php 
    $active_tab = Tab::getActiveTab( $default );
    foreach ( $tabs as $slug ) {
        $class = 'nav-tab';
        if ( $active_tab->getSlug() == $slug ) {
            $class .= ' nav-tab-active';
        }
        $params = apply_filters( __METHOD__ . '.params', [
            'page' => $menu,
            'tab'  => $slug,
        ] );
        printf(
            '<a class="%s" href="?%s">%s</a>',
            $class,
            http_build_query( $params ),
            Tab::getTabName( $slug )
        );
    }
    ?>
  </h2>

  <form action="options.php?tab=<?php 
    echo  $active_tab->getSlug() ;
    ?>" method="post">
    <?php 
    settings_fields( 'wp-fail2ban' );
    $active_tab->render();
    echo  '<hr><p>' . __( '<strong>Note:</strong> The Free version of <em>WP fail2ban</em> is configured by defining constants in <tt>wp-config.php</tt>; these tabs display those values.' ) . '<br>' . __( 'Upgrade to the Premium version to enable this interface.' ) . '</p>' ;
    ?>
  </form>
</div>
    <?php 
}

/**
 * Proxy for api.wp-fail2ban.com
 *
 * @since 4.2.6
 */
function remote_tools()
{
    global  $current_user ;
    ?>
<div class="wrap">
<h1>Remote Tools (&beta;)</h1>
<hr class="wp-header-end">
    <?php 
    
    if ( function_exists( '\\org\\lecklider\\charles\\wordpress\\wp_fail2ban\\addons\\remote_tools\\tab' ) ) {
        \org\lecklider\charles\wordpress\wp_fail2ban\addons\remote_tools\tab();
    } else {
        ?>
    <h2 class="nav-tab-wrapper wp-clearfix">
        <a class="nav-tab nav-tab-active" href="#">Overview</a>
    </h2>
    <div class="card">
        <h2>Remote Tools Add-on</h2>
        <p>This add-on provides features that make life with WP fail2ban easier, all from a remote server. This gives access to valuable but infrequently used tools without bloating the core plugin.</p>
        <p>The first of these is a <strong>Custom Filter Tool</strong> (CFT).</p>
        <blockquote>
            <p>The filter files included are intended only as a starting point for those who want <em>WPf2b</em> to work “out of the box”.</p>
            <p>There is no &ldquo;one size fits all&rdquo; configuration possible for <em>fail2ban</em> - what may be a soft failure for one site should be treated as a hard failure for another, and vice versa.</p>
        </blockquote>
        <p>You could simply edit the filter files included, but it&lsquo;s surprisingly easy to make a mistake; I learned this the hard way with earlier versions of <em>WPf2b</em>.... The CFT removes most of the opportunities for human error - always a good thing!</p>
        <hr>
        <p>The Remote Tools Add-on is available from the <a href="<?php 
        echo  admin_url( 'admin.php?page=wp-fail2ban-addons' ) ;
        ?>">Add-Ons menu</a>.</p>
    </div>
<?php 
    }
    
    ?>
</div>
    <?php 
}

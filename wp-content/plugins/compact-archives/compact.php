<?php
/*
Plugin Name: WPBeginner's Compact Archives
Plugin URI: http://www.wpbeginner.com
Description: Displays a compact monthly archive instead of the default long list. Either display it as a block suitable for the body of a page or in a form compact enough for a sidebar.
Version: 3.0.9
Author: WPBeginner
Author URI: http://www.wpbeginner.com
Text Domain: compact-archives
Domain Path: /languageswp
*/

/*
Maintained and supported by WPBeginner, this plugin was originally developed by Rob Marsh. Copyright 2008  Rob Marsh, SJ  (http://rmarsh.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
define( 'WPB_COMPACT_ARCHIVES_VERSION', '3.0.9' );
/*
	Display the monthly archive of posts in a more compact form than the usual long list.

	If $style == 'block' the display will be wide enough to fill the main column of a page:

		2006: Jan Feb Mar Apr May Jun Jul Aug Sep Oct Nov Dec
		2005: Jan Feb Mar Apr May Jun Jul Aug Sep Oct Nov Dec
		2004: Jan Feb Mar Apr May Jun Jul Aug Sep Oct Nov Dec

	If $style == 'initial' (the default) the display will fit into a sidebar.

				2006: J F M A M J J A S O N D
				2005: J F M A M J J A S O N D
				2004: J F M A M J J A S O N D

	If $style == 'numeric' the display will show month numbers.

				2006: 01 02 03 04 05 06 07 08 09 10 11 12
				2005: 01 02 03 04 05 06 07 08 09 10 11 12
				2004: 01 02 03 04 05 06 07 08 09 10 11 12

	$before and $after wrap each line of output. The default values are suitable for such use as:

		<ul>
			<?php compact_archive(); ?>
		</ul>

	Should work fine with whatever kind of permalink you are using.
	The month abbreviations should adapt to the locale set in wp-config.php.
	The year link at the start of each line is wrapped in <strong></strong> and months with no posts
	are wrapped in <span class="emptymonth"></span> so you can differentiate them visually

	If my Post Output Plugin is installed the Compact Archive output will be cached for efficiency.

*/
function compact_archive( $style='initial', $before='<li>', $after='</li>' ) {
 	$result = false;
	// if the Plugin Output Cache is installed we can cheat...
	if ( defined( 'POC_CACHE_4' ) ) {
		$key = 'c_a' . $style . $before . $after;
		poc_cache_timer_start();
		$result = poc_cache_fetch( $key );
		if ( $result ) $cache_time = sprintf( '<!-- Compact Archive took %.3f milliseconds from the cache -->', 1000 * poc_cache_timer_start() );
	}
	// ... otherwise we do it the hard way
	if ( false === $result ) {
		$result = utf8_encode( get_compact_archive( $style, $before, $after ) );
		if ( defined( 'POC_CACHE_4' ) ) {
			poc_cache_store( $key, $result );
			$cache_time = sprintf( '<!-- Compact Archive took %.3f milliseconds -->', 1000 * poc_cache_timer_start() );
		}
	}

	echo $result;

	if ( defined( 'POC_CACHE_4' ) ) echo  $cache_time;

}

/********************************************************************************************************
	Stuff below this point is not meant to be used directly
*********************************************************************************************************/

function get_compact_archive( $style='initial', $before='<li>', $after='</li>' ) {
	global $wpdb, $wp_version;
	if ( defined( 'WPLANG' ) ) {
		setlocale( LC_ALL, WPLANG ); // set localization language
	}
	$below21 = version_compare( $wp_version, '2.1', '<' );
	// WP 2.1 changed the way post_status and post_type fields work
	if ( $below21 ) {
		$now = current_time( 'mysql' );
		$results = $wpdb->get_results( "SELECT DISTINCT YEAR(post_date) AS year, MONTH(post_date) AS month FROM " . $wpdb->posts . " WHERE post_date <'" . $now . "' AND post_status='publish' AND post_password='' ORDER BY year DESC, month DESC" );
	} else {
		$results = $wpdb->get_results( "SELECT DISTINCT YEAR(post_date) AS year, MONTH(post_date) AS month FROM " . $wpdb->posts . " WHERE post_type='post' AND post_status='publish' AND post_password='' ORDER BY year DESC, month DESC" );
	}
	if ( !$results ) {
		return $before . __( 'Archive is empty' ) . $after;
	}
	$dates = array();
	foreach ( $results as $result ) {
		$dates[$result->year][$result->month] = 1;
	}
	unset( $results );
	$result = '';
	foreach ( $dates as $year => $months ) {
		$result .= $before . '<strong><a href="' . get_year_link( $year ) . '">' . $year . '</a>: </strong> ';
		for ( $month = 1; $month <= 12; $month += 1 ) {
			$month_has_posts = ( isset( $months[$month] ) );
			$dummydate = strtotime( "$month/01/2001" );
			// get the month name; strftime() localizes
			$month_name = strftime( "%B", $dummydate );
			switch ( $style ) {
			case 'initial':
				$month_abbrev = $month_name[0]; // the inital of the month
				break;
			case 'block':
				$month_abbrev = strftime( "%b", $dummydate ); // get the short month name; strftime() localizes
				break;
			case 'numeric':
				$month_abbrev = strftime( "%m", $dummydate ); // get the month number, e.g., '04'
				break;
			default:
				$month_abbrev = $month_name[0]; // the inital of the month
			}
			if ( $month_has_posts ) {
				$result .= '<a href="' . get_month_link( $year, $month ) . '" title="' . $month_name . ' ' . $year . '">' . $month_abbrev . '</a> ';
			} else {
				$result .= '<span class="emptymonth">' . $month_abbrev . '</span> ';
			}
		}
		$result .= $after."\n";
	}
	return $result;
}

// Compact Archive Shortcode

function compact_archives_shortcode( $atts ) {
	extract( shortcode_atts( array(
			'style' => 'initial',
			'before' => '<li>',
			'after' => '</li>'
		), $atts ) );

	if ($before == "<li>")	:
		$wrap = "<ul>";
	endif;

	if ( $after == "</li>" ) :
		$wrap_end = "</ul>";
	endif;

	$string = $wrap . get_compact_archive( $style, $before, $after ) . $wrap_end;

	return $string;

}

add_shortcode( 'compact_archive', 'compact_archives_shortcode' );

// Compact Archive Widget
// Thanks to Aldo Latino http://www.aldolat.it/

function wpb_caw_load_widget() {
	register_widget( 'WPBeginner_CAW_Widget' );
}
add_action( 'widgets_init', 'wpb_caw_load_widget' );

/**
 * Create the widget
 *
 */
class WPBeginner_CAW_Widget extends WP_Widget {

	function __construct() {
		$widget_ops = array(
			'classname'   => 'wpb_caw_widget',
			'description' => __( 'Create a widget for Compact Archives plugin', 'compact-archives' )
		);
		parent::__construct( 'wpb-caw-widget', __( 'Compact Archives Widget', 'compact-archives' ), $widget_ops );
	}

	function widget( $args, $instance ) {
		extract( $args );

		$title = apply_filters( 'widget_title', $instance['title'] );
		$widget_style = $instance['style'];
		switch( $instance['text_style'] ) {
		    case 'none' :
		        $text_style = '';
		        break;
		    case 'uppercase' :
		        $text_style = ' style="text-transform: uppercase;"';
		        break;
		    case 'capitalize':
		        $text_style = ' style="text-transform: capitalize;"';
		        break;
		}

		echo $before_widget;
		if ( $title ) echo $before_title . $title . $after_title; ?>
		<ul class="compact-archives"<?php echo $text_style; ?>>
			<?php compact_archive( $style = $widget_style ); ?>
		</ul>
		<?php echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['style'] = $new_instance['style'];
		$instance['text_style'] = $new_instance['text_style'];
		return $instance;
	}

	function form( $instance ) {
		$defaults = array(
			'title'      => __( 'Archives by Month', 'caw-domain' ),
			'style'      => 'initial',
			'text_style' => 'uppercase'
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
		$style = $instance['style'];
		$text_style = $instance['text_style'];

/**
 * Check if Compact Archives Widget plugin is installed.
 * Ask user to deactivate it if it is installed.
 *
 */

	if ( is_plugin_active( 'compact-archives-widget/compact-archives-widget.php' ) ) { ?>
			<p style="background-color: #FFD5D5; padding: 10px;">
				<?php printf( __( '%3$sNotice.%4$s<br />The %3$sCompact Archives Widget%4$s plugin is active on your WordPress and it\'s not needed anymore . Please, %1$sdeactivate it from here%2$s.', 'compact-archives' ),
					'<a href="' . admin_url( 'plugins.php' ) . '">',
					'</a>',
					'<strong>',
					'</strong>'
				); ?>
			</p>
		<?php } ?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id('title') ); ?>">
					<?php _e( 'Title:', 'compact-archives' ); ?>
				</label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'style' ); ?>">
					<?php _e( 'Select the style:', 'compact-archives' ); ?>
				</label><br />
				<select name="<?php echo $this->get_field_name( 'style' ); ?>" >
					<option <?php selected( 'initial', $style ); ?> value="initial">
						<?php _e( 'Initials', 'compact-archives' ); ?>
					</option>
					<option <?php selected( 'block', $style ); ?> value="block">
						<?php _e( 'Block', 'compact-archives' ); ?>
					</option>
					<option <?php selected( 'numeric', $style ); ?> value="numeric">
						<?php _e( 'Numeric', 'compact-archives' ); ?>
					</option>
				</select>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'text_style' ); ?>">
					<?php _e( 'Transform text:', 'compact-archives' ); ?>
				</label>
				<select name="<?php echo $this->get_field_name( 'text_style' ); ?>" >
					<option <?php selected( 'None', $text_style ); ?> value="none">
						<?php _e( 'None transformation', 'compact-archives' ); ?>
					</option>
					<option <?php selected( 'uppercase', $text_style ); ?> value="uppercase">
						<?php _e( 'UPPERCASE', 'compact-archives' ); ?>
					</option>
					<option <?php selected( 'capitalize', $text_style ); ?> value="capitalize">
						<?php _e( 'Capitalize', 'compact-archives' ); ?>
					</option>
				</select>
			</p>
		<?php
	}
}

/**
 * Make plugin available for i18n
 * Translations must be archived in the /languages directory
 *
 *
 */

function wpb_caw_load_languages() {
	load_plugin_textdomain( 'compact-archives', false, dirname( plugin_basename( __FILE__ ) ) . '/languageswp/');
}

add_action( 'plugins_loaded', 'wpb_caw_load_languages' );

// Check if Gutenberg available.
if ( has_action( 'enqueue_block_editor_assets' ) ) {

	// Enqueue the scripts for the WPBeginner Compact Archive block.
	add_action( 'enqueue_block_editor_assets', 'load_wpbca_block_files' );

	// Register the functions for rendering the WPBeginner Compact Archive dynamic block.
	add_action( 'init', 'wpb_compact_archive_block' );
}

/**
 * Enqueue guten block script.
 */
function load_wpbca_block_files() {
	// Scripts.
	wp_enqueue_script( 'wpb-compact-archive-block-script', plugin_dir_url( __FILE__ ) . 'assets/blocks.js', array( 'wp-blocks', 'wp-i18n', 'wp-editor', 'wp-element', 'wp-components' ), WPB_COMPACT_ARCHIVES_VERSION, true );
	wp_localize_script( 'wpb-compact-archive-block-script', 'wpbca_block_vars',
			array(
					'plugin_name' => 'WPBeginner\'s Compact Archives',
					'label_initial' => __( 'Initial', 'compact-archives' ),
					'label_block' => __( 'Block', 'compact-archives' ),
					'label_numeric' => __( 'Numeric', 'compact-archives' ),
					'label_sel_archive_type' => __( 'Select Archive Type', 'compact-archives' ),
					'label_sel_archive_type_case' => __( 'Transform Text', 'compact-archives' ),
					'label_none' => __( 'None', 'compact-archives' ),
					'label_capitalize' => __( 'Capitalize', 'compact-archives' ),
					'label_uppercase' => __( 'UPPERCASE', 'compact-archives' ),
					'placeholder' => __( 'Enter a title (Leave empty to disable)', 'compact-archives' ),
					'ca_initial' => get_compact_archive( 'initial' ),
					'ca_block' => get_compact_archive( 'block' ),
					'ca_numeric' => get_compact_archive( 'numeric' )
			)
	);

	// Styles.
	wp_register_style( 'wpb-compact-archive-block-style', plugins_url( '/assets/blocks.css', __FILE__ ), array(), WPB_COMPACT_ARCHIVES_VERSION );
	wp_enqueue_style( 'wpb-compact-archive-block-style' );
}

/**
 * Register the Dynamic callback for the block.
 */
function wpb_compact_archive_block() {
	if ( function_exists( 'register_block_type' ) ) {
		register_block_type( 'wpb-compact-archive/wpb-compact-archive-block', array(
				'editor_script' => 'wpb-compact-archive-block-script',
				'render_callback' => 'wpb_compact_archive_block_render_callback'
		) );
	}
}

/**
 * Render the gutenberg block content.
 *
 * @param $attribute Array.
 * @param $content String.
 */
function wpb_compact_archive_block_render_callback( $attributes, $content ) {

	// Set defaults.
	if ( ! isset( $attributes['compact_archive_type'] ) ) {
		$attributes['compact_archive_type'] = 'block';
	}
	if ( ! isset( $attributes['compact_archive_text_case'] ) ) {
		$attributes['compact_archive_text_case'] = 'none';
	}
	if ( ! isset( $attributes['compact_archive_title'] ) ) {
		$attributes['compact_archive_title'] = '';
	}

	// Get compact archives.
	$archives = get_compact_archive( $attributes['compact_archive_type'] );

	// Default styles.
	$style = ( ! empty( $attributes['compact_archive_title'] ) ) ? 'list-style-type: none; margin: 10px 0 20px 0;' : 'list-style-type: none; margin: 20px 0 20px 0;';

	// Chosen styles.
	if ( $attributes['compact_archive_text_case'] === 'capitalize' ) {
		$style .= 'text-transform: capitalize;';
	} else if ( $attributes['compact_archive_text_case'] === 'uppercase' ) {
		$style .= 'text-transform: uppercase;';
	}

	// Append styles. (Do not escape)
	return ( ! empty( $attributes['compact_archive_title'] ) ? $attributes['compact_archive_title'] : '' ) . '<ul style="' . $style . '">' . $archives . '</ul>';
}
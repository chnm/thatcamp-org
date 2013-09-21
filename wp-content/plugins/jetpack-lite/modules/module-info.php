<?php
/**
 * "Learn More" information blocks for all modules live in this file.
 *
 * jetpack_module_more_info_<module-slug> hooks are for pre-connection information
 * jetpack_module_more_info_connected_<module-slug> hooks are used once the user
 * 		is connected to show them links to admin panels, usage info etc.
 */

// Shortlinks
function wpme_more_info() { ?>
	<div class="jp-info-img">
		<a href="http://wp.me/sf2B5-shorten">
			<img class="jp-info-img" src="<?php echo plugins_url( basename( dirname( dirname( __FILE__ ) ) ) . '/_inc/images/screenshots/shortlinks.png' ) ?>" alt="<?php esc_attr_e( 'WP.me Shortlinks', 'jetpack' ) ?>" width="300" height="150" />
		</a>
	</div>

	<h4><?php esc_html_e( 'WP.me Shortlinks' , 'jetpack' ); ?></h4>
	<p><?php esc_html_e( "Instead of typing or copy-pasting long URLs, you can now get a short and simple link to your posts and pages. This uses the super compact wp.me domain name, and gives you a unique URL you can use that will be safe and reliable.", 'jetpack' ) ?></p>
	<p><?php esc_html_e( "It&#8217;s perfect for use on Twitter, Facebook, and cell phone text messages where every character counts.", 'jetpack' ) ?></p>
<?php
}
add_action( 'jetpack_module_more_info_shortlinks', 'wpme_more_info' );

function wpme_more_info_connected() { ?>
	<div class="jp-info-img">
		<a href="http://wp.me/sf2B5-shorten">
			<img class="jp-info-img" src="<?php echo plugins_url( basename( dirname( dirname( __FILE__ ) ) ) . '/_inc/images/screenshots/shortlinks.png' ) ?>" alt="<?php esc_attr_e( 'WP.me Shortlinks', 'jetpack' ) ?>" width="300" height="150" />
		</a>
	</div>

	<h4><?php esc_html_e( 'WP.me Shortlinks' , 'jetpack' ); ?></h4>
	<p><?php esc_html_e( "Instead of typing or copy-pasting long URLs, you can now get a short and simple link to your posts and pages. This uses the super compact wp.me domain name, and gives you a unique URL you can use that will be safe and reliable.", 'jetpack' ) ?></p>
	<p><?php esc_html_e( "To use shortlinks, go to any already published post (or publish something new!). A &#8220;Get Shortlink&#8221; button will be visible under the Post title. When you click it, a dialog box will appear with the shortlink and you can copy and paste to Twitter, Facebook or wherever your heart desires.", 'jetpack' ) ?></p>
<?php
}
add_action( 'jetpack_module_more_info_connected_shortlinks', 'wpme_more_info_connected' );

function wpme_load_more_link( $description ) {
	echo '<a class="button-secondary more-info-link" href="http://wp.me/sf2B5-shorten">' . esc_html__( 'Learn More', 'jetpack' ) . '</a>';
}
add_filter( 'jetpack_learn_more_button_shortlinks', 'wpme_load_more_link' );


// WordPress.com Stats
function stats_more_info() { ?>
	<div class="jp-info-img">
		<a href="http://en.support.wordpress.com/stats/">
			<img class="jp-info-img" src="<?php echo plugins_url( basename( dirname( dirname( __FILE__ ) ) ) . '/_inc/images/screenshots/stats.png' ) ?>" alt="<?php esc_attr_e( 'WordPress.com Stats', 'jetpack' ) ?>" width="300" height="150" />
		</a>
	</div>

	<h4><?php esc_html_e( 'WordPress.com Stats' , 'jetpack' ); ?></h4>
	<p><?php esc_html_e( 'There are many plugins and services that provide statistics, but data can be overwhelming. WordPress.com Stats makes the most popular metrics easy to understand through a clear and attractive interface.', 'jetpack' ) ?></p>
<?php
}
add_action( 'jetpack_module_more_info_stats', 'stats_more_info' );

function stats_more_info_connected() { ?>
	<div class="jp-info-img">
		<a href="http://en.support.wordpress.com/stats/">
			<img class="jp-info-img" src="<?php echo plugins_url( basename( dirname( dirname( __FILE__ ) ) ) . '/_inc/images/screenshots/stats.png' ) ?>" alt="<?php esc_attr_e( 'WordPress.com Stats', 'jetpack' ) ?>" width="300" height="150" />
		</a>
	</div>

	<h4><?php esc_html_e( 'WordPress.com Stats' , 'jetpack' ); ?></h4>
	<p><?php esc_html_e( 'There are many plugins and services that provide statistics, but data can be overwhelming. WordPress.com Stats makes the most popular metrics easy to understand through a clear and attractive interface.', 'jetpack' ) ?></p>
	<p><?php printf( __( 'You can <a href="%s">view your stats dashboard here</a>.', 'jetpack' ), admin_url( 'admin.php?page=stats' ) ); ?></p>
<?php
}
add_action( 'jetpack_module_more_info_connected_stats', 'stats_more_info_connected' );

function stats_load_more_link( $description ) {
	echo '<a class="button-secondary more-info-link" href="http://en.support.wordpress.com/stats/">' . esc_html__( 'Learn More', 'jetpack' ) . '</a>';
}
add_filter( 'jetpack_learn_more_button_stats', 'stats_load_more_link' );

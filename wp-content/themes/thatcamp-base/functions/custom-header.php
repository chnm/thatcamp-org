<?php
/**
 * Implements an optional custom header.
 * See http://codex.wordpress.org/Custom_Headers
 *
 * @package WordPress
 * @subpackage thatcampbase
 * @since thatcampbase (1.0)
 */

/**
 * Sets up the WordPress core custom header arguments and settings.
 *
 * @uses add_theme_support() to register support for 3.4 and up.
 * @uses thatcampbase_header_style() to style front-end.
 * @uses thatcampbase_admin_header_style() to style wp-admin form.
 * @uses thatcampbase_admin_header_image() to add custom markup to wp-admin form.
 *
 * @since thatcampbase (1.0)
 */

if ( ! function_exists( 'thatcampbase_custom_header_setup' ) ) :
function thatcampbase_custom_header_setup() {
	$args = array(
		// Text color and image (empty to use none).
		'default-text-color'     => '444',
		'default-image'          => '',

		// Set height and width, with a maximum value for the width.
		'height'                 => 200,
		'width'                  => 1140,
		'max-width'              => 2000,

		// Support flexible height and width.
		'flex-height'            => true,
		'flex-width'             => true,

		// Random image rotation off by default.
		'random-default'         => false,

		// Callbacks for styling the header and the admin preview.
		'wp-head-callback'       => 'thatcampbase_header_style',
		'admin-head-callback'    => 'thatcampbase_admin_header_style',
		'admin-preview-callback' => 'thatcampbase_admin_header_image',
	);

	add_theme_support( 'custom-header', $args );
}
add_action( 'after_setup_theme', 'thatcampbase_custom_header_setup' );
endif;



/**
 * Styles the header text displayed on the blog.
 *
 * get_header_textcolor() options: 444 is default, hide text (returns 'blank'), or any hex value.
 *
 * @since thatcampbase (1.0)
 */
function thatcampbase_header_style() {
	$text_color = get_header_textcolor();

	// If no custom options for text are set, let's bail
	if ( $text_color == get_theme_support( 'custom-header', 'default-text-color' ) )
		return;

	// If we get this far, we have custom styles.
	?>
	<style type="text/css">
	<?php
		// Has the text been hidden?
		if ( ! display_header_text() ) :
	?>
		.site-title,
		.site-description {
			position: absolute !important;
			clip: rect(1px 1px 1px 1px); /* IE7 */
			clip: rect(1px, 1px, 1px, 1px);
		}
	<?php
		// If the user has set a custom color for the text, use that.
		else :
	?>
		.site-title a,
		.site-description {
			color: #<?php echo $text_color; ?> !important;
		}
	<?php endif; ?>
	</style>
	<?php
}

/**
 * Styles the header image displayed on the Appearance > Header admin panel.
 *
 * @since thatcampbase (1.0)
 */
function thatcampbase_admin_header_style() {
?>
	<style type="text/css">
	.appearance_page_custom-header #headimg {
		border: none;
	}
	#headimg h1,
	#headimg h2 {
		line-height: 1.6;
		margin: 0;
		padding: 0;
	}
	#headimg h1 {
		font-size: 30px;
	}
	#headimg h1 a {
		color: #515151;
		text-decoration: none;
	}
	#headimg h1 a:hover {
		color: #21759b;
	}
	#headimg h2 {
		color: #777;
		font: normal 13px/1.8 "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", sans-serif;
		margin-bottom: 24px;
	}
	#headimg img {
		max-width: <?php echo get_theme_support( 'custom-header', 'max-width' ); ?>px;
	}
	</style>
<?php
}

/**
 * Outputs markup to be displayed on the Appearance > Header admin panel.
 * This callback overrides the default markup displayed there.
 *
 * @since thatcampbase (1.0)
 */
function thatcampbase_admin_header_image() {
	?>
	<div id="headimg">
		<?php
		if ( ! display_header_text() )
			$style = ' style="display:none;"';
		else
			$style = ' style="color:#' . get_header_textcolor() . ';"';
		?>
		<h1><a id="name"<?php echo $style; ?> onclick="return false;" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
		<h2 id="desc"<?php echo $style; ?>><?php bloginfo( 'description' ); ?></h2>
		<?php $header_image = get_header_image();
		if ( ! empty( $header_image ) ) : ?>
			<img src="<?php echo esc_url( $header_image ); ?>" class="header-image" width="<?php echo get_custom_header()->width; ?>" height="<?php echo get_custom_header()->height; ?>" alt="" />
		<?php endif; ?>
	</div>
<?php }

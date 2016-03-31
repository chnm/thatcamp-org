<?php
/**
 * The front page template.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @since 1.0.0
 */
get_header();

global $paged;
$bavotasan_theme_options = bavotasan_theme_options();

if ( get_query_var( 'paged' ) ) {
	$paged = get_query_var( 'paged' );
} elseif ( get_query_var( 'page' ) ) {
	$paged = get_query_var( 'page' );
} else {
	$paged = 1;
}

if ( 2 > $paged ) {
	if ( get_header_image() ) : ?>
		<img class="header-img" src="<?php header_image(); ?>" height="<?php echo get_custom_header()->height; ?>" width="<?php echo get_custom_header()->width; ?>" alt="" />
	<?php
	endif;

	// Display jumbo headline widgetized area
	if ( is_active_sidebar( 'jumbo-headline' ) ) {
		?>
		<div class="home-top">
			<div class="container">
				<div class="row">
					<div class="home-jumbotron col-md-8 col-md-offset-2">
						<?php dynamic_sidebar( 'jumbo-headline' ); ?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	// Featured sticky post section
	$sticky = get_option( 'sticky_posts' );
	rsort( $sticky );
	$sticky = array_slice( $sticky, 0, 10 );
	if ( $sticky ) {
		$query = new WP_Query( array(
			'posts_per_page' => 10,
			'post__in' => $sticky,
			'ignore_sticky_posts' => 1,
		) );

		if ( $query->have_posts() ) {
			?>
			<div class="sticky-container clearfix">
				<?php while ( $query->have_posts() ) : $query->the_post();
					$img_size = bavotasan_sticky_variables( 'img_size', $query );
					$class = bavotasan_sticky_variables( 'class', $query );
				?>
				<div class="item <?php echo $class; ?>">
					<a href="<?php the_permalink(); ?>">
						<?php
						$rand = rand(0,6);
						$p_class = bavotasan_color_array( $rand );
						if ( has_post_thumbnail() ) {
							the_post_thumbnail( $img_size );
						} else {
							echo '<img src="' . BAVOTASAN_THEME_URL . '/library/images/' . $img_size . '.png" alt="" />';
							$p_class = $p_class . ' show';
						}
						?>
						<p class="<?php echo $p_class; ?>">
							<span>
								<small><?php echo get_the_date(); ?></small>
								<?php the_title(); ?>
							</span>
						</p>
					</a>
				</div>
				<?php endwhile; ?>
			</div>
			<?php
		}
		wp_reset_query();
	}
}

if ( 'page' == get_option( 'show_on_front' ) ) {
	include( get_page_template() );
} else {
	?>
	<div class="container">
		<div class="row">
			<div id="primary" <?php bavotasan_primary_attr(); ?>>
		        <?php
				if ( have_posts() ) {
					while ( have_posts() ) : the_post();
						get_template_part( 'content', get_post_format() );
					endwhile;

					bavotasan_pagination();
				} else {
					get_template_part( 'content', 'none' );
				}
				?>
			</div><!-- #primary -->
			<?php get_sidebar(); ?>
		</div>
	</div>
<?php
}

get_footer();
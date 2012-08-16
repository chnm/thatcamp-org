<?php 
	$kt =& get_instance();
			
	$featured_cat = (int)$kt->config->item('featured_category', 'layout');
	$featured_total = (int)$kt->config->item('featured_total', 'layout');
	$featured_query = new WP_Query('cat='. $featured_cat . '&showposts=' . $featured_total . '&orderby=date'); 
	
	if ($featured_query->have_posts() && $featured_cat > 0 && $featured_total > 0) :
?>
<div id="featured">
	<h2 class="sideheading">Featured</h2>
	<ul>
		<?php
		while ($featured_query->have_posts()) : $featured_query->the_post(); 
		
		$thumb = get_post_meta($featured_query->post->ID, 'thumbnail', $single = true); 
		?>
		
		<li class="featured_post">
			<div class="featured_thumb">
				<span><?php the_time('M d, Y'); ?></span>  
				
					<a class="<?php if ($thumb === '') echo 'feat'; ?>" href="<?php the_permalink() ?>" rel="bookmark" title="Link to <?php the_title(); ?>">
						
				<?php if ($thumb !== '') { ;?>
				<img src="<?php echo $thumb ; ?>" alt="<?php the_title(); ?>" />
				<?php } ;?>
					</a>
				
			</div>
			
			<h1>
				<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>">
					<?php echo the_title(); ?>
				</a>
			</h1>
			
			<?php kreative_excerpt(15) ;?>
		</li>
		<?php endwhile; ?>
	</ul>	 
</div>
<?php endif; wp_reset_query(); ?>
<?php
/**
 * The Footer widget areas.
 *
 * @package WordPress
 * @subpackage Boilerplate
 * @since Boilerplate 1.0
 */
?>

					<ul class="xoxo" id="sponsorship-footer">
					<li id="sponsors" class="widget-container widget_text">
					<div class="textwidget">
					
					<h3 class="sponsorship-text">Our Sponsors</h3>
					
					<a class="sponsor" id="ohc" href="http://www.ohiohumanities.org/"><img alt="ohc"  src="<?php echo home_url( '/wp-content/themes/thatcampoha/images/logos/' ); ?>ohc.png"/></a>
					<a class="sponsor" id="csu" href="http://csuohio.edu/"><img alt="csu"  src="<?php echo home_url( '/wp-content/themes/thatcampoha/images/logos/' ); ?>CSU.png"/></a>
					<a class="sponsor" id="cphdh" href="http://csudigitalhumanities.org/"><img alt="cphdh" src="<?php echo home_url( '/wp-content/themes/thatcampoha/images/logos/' ); ?>CPHDH.png"/></a>
					<a class="sponsor" id="oha" href="http://www.oralhistory.org/"><img alt="oha" src="<?php echo home_url( '/wp-content/themes/thatcampoha/images/logos/' ); ?>OHA.jpg"/></a>
					<a class="sponsor" id="chnm" href="http://chnm.gmu.edu/"><img alt="chnm" src="<?php echo home_url( '/wp-content/themes/thatcampoha/images/logos/' ); ?>CHNM.png"/></a>
					<a class="sponsor" id="thatcamp" href="http://thatcampdev.info/"><img alt="thatcamp" src="<?php echo home_url( '/wp-content/themes/thatcampoha/images/logos/' ); ?>THATCAMP.gif"/></a>
					
					<p class="sponsorship-text">THATCamp Oral History is sponsored by the <a class="sponsor"  href="http://www.oralhistory.org/">Oral History Association</a>, the <a class="sponsor"  href="http://www.ohiohumanities.org/">Ohio Humanities Council</a> and the <a class="sponsor"  href="http://csudigitalhumanities.org/">Center for Public History + Digital Humanities</a> at <a class="sponsor"  href="http://csuohio.edu/">Cleveland State University</a>. <a class="sponsor"  href="http://thatcampdev.info/">THATCamp</a> is a registered trademark of the <a class="sponsor" href="http://chnm.gmu.edu/">Center for History and New Media</a> at <a class="sponsor"  href="http://gmu.edu/">George Mason University</a>.</p>
					<?php 
					
					echo '<h3 class="page-dropdown">Pages</h3>';
					
					$args = array(
				    'child_of'     => 0,
				    'sort_order'   => 'ASC',
				    'sort_column'  => 'post_title',
				    'hierarchical' => 1,
				    'post_type' => 'page',
				    'name'=>'page-dropdown',
				    'selected'=> 0
				    );
				    
				    wp_dropdown_pages($args); 

	    			?> 
					</div>
					</li>
					</ul>
					
					


<?php
	/* The footer widget area is triggered if any of the areas
	 * have widgets. So let's check that first.
	 *
	 * If none of the sidebars have widgets, then let's bail early.
	 */
	if (   ! is_active_sidebar( 'first-footer-widget-area'  )
		&& ! is_active_sidebar( 'second-footer-widget-area' )
		&& ! is_active_sidebar( 'third-footer-widget-area'  )
		&& ! is_active_sidebar( 'fourth-footer-widget-area' )
	)
		return;
	// If we get this far, we have widgets. Let do this.
?>

<?php if ( is_active_sidebar( 'first-footer-widget-area' ) ) : ?>
					<ul class="xoxo" id="first">
						<?php dynamic_sidebar( 'first-footer-widget-area' ); ?>
					</ul>
<?php endif; ?>

<?php if ( is_active_sidebar( 'second-footer-widget-area' ) ) : ?>
					<ul class="xoxo" id="second">
						<?php dynamic_sidebar( 'second-footer-widget-area' ); ?>
					</ul>
					<!-- for testing 
					<ul class="xoxo">
					<li id="sponsors" class="widget-container widget_text">
					<div class="textwidget">
					<?php// echo wp_list_pages();?>
					</div>
					</li>
					</ul>	
					-->									
<?php endif; ?>

<?php if ( is_active_sidebar( 'third-footer-widget-area' ) ) : ?>
					<ul class="xoxo" id="third">
						<?php dynamic_sidebar( 'third-footer-widget-area' ); ?>
					</ul>
<?php endif; ?>

<?php if ( is_active_sidebar( 'fourth-footer-widget-area' ) ) : ?>
					<ul class="xoxo" id="fourth">
						<?php dynamic_sidebar( 'fourth-footer-widget-area' ); ?>
					</ul>
<?php endif; ?>
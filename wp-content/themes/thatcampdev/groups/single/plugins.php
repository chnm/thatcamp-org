<?php get_header( 'thatcamp' ); ?>

	
	<div id="primary" class="main-content">
	<div id="content" role="main">
			<?php if ( bp_has_groups() ) : while ( bp_groups() ) : bp_the_group(); ?>

			<?php do_action( 'bp_before_group_plugin_template' ); ?>

			<div id="item-header">
				<?php locate_template( array( 'groups/single/group-header.php' ), true ); ?>
			</div>

			<div id="item-nav">
				<div class="item-list-tabs no-ajax" id="object-nav" role="navigation">
					<ul>
						<?php bp_get_options_nav(); ?>

						<?php do_action( 'bp_group_plugin_options_nav' ); ?>
					</ul>
				</div>
			</div>
			<div id="item-body">

				<?php do_action( 'bp_before_group_body' ); ?>

				<?php do_action( 'bp_template_content' ); ?>

				<?php do_action( 'bp_after_group_body' ); ?>
			</div>

			<?php do_action( 'bp_after_group_plugin_template' ); ?>

			<?php endwhile; endif; ?>

		</div>
	</div>
<?php get_sidebar( 'buddypress' ); ?>
<?php get_footer( 'thatcamp' ); ?>
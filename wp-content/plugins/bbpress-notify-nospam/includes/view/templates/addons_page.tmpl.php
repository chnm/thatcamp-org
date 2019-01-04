
<h1><?php _e( 'bbPress Notify (No-Spam) Add-Ons', 'bbPress_Notify_noSpam' ) ;?></h1>
<h1 class="screen-reader-text"><?php _e( 'Add-On list', 'bbPress_Notify_noSpam' ) ;?></h1>

<p><?php _e("bbPress Notify (No-Spam) is a great plugin for notifications, but you already knew that (or you wouldn't be using it, right?). 
What makes it even greater are the several add-on extensions available. Check out all of the options below, as there's bound to be something you like.", 'bbPress_Notify_noSpam' ) ;?></p> 

<hr>
<div class="wp-list-table widefat plugin-install">
	<div id="the-list">
	<?php foreach ( (array) $stash->addons as $p ) : ?>
		<div class="plugin-card plugin-card-<?php echo $p->slug ?>">
			<div class="plugin-card-top">
				<div class="name column-name">
					<h3>
						<a href="<?php echo esc_attr( $p->permalink );?>" target="_new"><?php echo esc_html( $p->name ); ?></a>
						<img src="<?php esc_attr_e( $p->image );?>" class="plugin-icon" alt="<?php esc_attr_e( $p->name); ?>">
					</h3>
				</div>
				<div class="action-links">
					<ul class="plugin-action-buttons">
						<?php if ( $p->is_installed && $p->is_active ): ?>
							<li>
								<button type="button" class="button button-disabled" disabled="disabled"><?php _e( 'Active' );?></button>
							</li>
							<?php $plugin_page = apply_filters( $p->local->TextDomain . '_plugin_page', null ); ?>
								<?php if ( $p->local->license_page ) : ?>
							<li>
								<a href="<?php esc_attr_e( $p->local->license_page );?>"><?php _e( 'Manage License', 'bbPress_Notify_noSpam' ) ; ?></a>
							</li>
								<?php endif;?>
							<?php else: ?>
							<li>
								<a href="<?php echo esc_attr( $p->permalink );?>" class="button button-primary" target="_new"><?php _e( 'More Details' ); ?></a>
							</li>
						<?php endif; ?>
					</ul>
				</div>
				<div class="desc column-description">
					<?php echo $p->short_description;?>
				</div>
			</div>
			<div class="plugin-card-bottom">
				<div class="vers column-rating">
					<ul>
					<?php if ( $p->is_installed ) :?>
						<li> <strong><?php _e('Installed version:', 'bbPress_Notify_noSpam' ) ; ?></strong> <?php echo $p->local->Version; ?></li>
							<?php if ( $p->update_available ) : ?>
						<li> <span class="dashicons dashicons-warning"></span><?php _e('Update available', 'bbPress_Notify_noSpam' ) ; ?></li>
							<?php endif; ?>
					<?php endif; ?>
					</ul>
				</div>
				<div class="column-updated">
					<ul>
						<li> <strong><?php _e('Latest version:', 'bbPress_Notify_noSpam' ) ; ?></strong> <?php echo $p->version; ?></li>
					<?php if ( $p->recommended ) :?>
						<li>
							<span class="dashicons dashicons-yes"></span> <?php _e( '<strong>Recommended!</strong>', 'bbPress_Notify_noSpam') ?>
						</li>
					<?php endif;?>
					</ul>
				</div>
			</div>
		</div>
	<?php endforeach; ?>


	</div>
</div>
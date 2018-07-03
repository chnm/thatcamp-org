<?php
/**
 * BuddyPress Cover Images main template.
 *
 * This template is used to inject the BuddyPress Backbone views
 * dealing with cover images.
 *
 * It's also used to create the common Backbone views.
 *
 * @since 2.4.0
 * @version 3.1.0
 */

?>

<div class="bp-cover-image"></div>
<div class="bp-cover-image-status"></div>
<div class="bp-cover-image-manage"></div>

<?php bp_attachments_get_template_part( 'uploader' ); ?>

<script id="tmpl-bp-cover-image-delete" type="text/html">
	<# if ( 'user' === data.object ) { #>
		<p><?php esc_html_e( "If you'd like to delete your current cover image, use the delete Cover Image button.", 'buddypress' ); ?></p>
		<button type="button" class="button edit" id="bp-delete-cover-image">
			<?php
			echo esc_html_x( 'Delete My Cover Image', 'button', 'buddypress' );
			?>
		</button>
	<# } else if ( 'group' === data.object ) { #>
		<p><?php esc_html_e( "If you'd like to remove the existing group cover image but not upload a new one, please use the delete group cover image button.", 'buddypress' ); ?></p>
		<button type="button" class="button edit" id="bp-delete-cover-image">
			<?php
			echo esc_html_x( 'Delete Group Cover Image', 'button', 'buddypress' );
			?>
		</button>
	<# } else { #>
		<?php
			/**
			 * Fires inside the cover image delete frontend template markup if no other data.object condition is met.
			 *
			 * @since 3.0.0
			 */
			do_action( 'bp_attachments_cover_image_delete_template' ); ?>
	<# } #>
</script>

<?php
	/**
	 * Fires after the cover image main frontend template markup.
	 *
	 * @since 3.0.0
	 */
	do_action( 'bp_attachments_cover_image_main_template' ); ?>

<div class="bbpnns-warnings">
<?php if ( ! empty( $stash->warnings ) ) : ?>

	<?php foreach ( $stash->warnings as $w ) : ?>
		<div class="notice notice-warning inline">
			<p><?php echo $w; ?></p>
		</div>	
	<?php endforeach; ?>

<?php endif; ?>
</div>
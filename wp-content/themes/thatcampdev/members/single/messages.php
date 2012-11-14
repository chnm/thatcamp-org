<?php

/**
 * BuddyPress - Users Messages
 *
 * @package logicalbones
 * @since logicalbones 1.0
 */

?>

<div class="item-list-tabs no-ajax" role="navigation">
	
	<?php if ( bp_is_messages_inbox() || bp_is_messages_sentbox() ) : ?>

		<div class="message-search"><?php bp_message_search_form(); ?></div>

	<?php endif; ?>

</div>

<?php

	if ( bp_is_current_action( 'compose' ) ) :
		get_template_part( 'members/single/messages/compose');

	elseif ( bp_is_current_action( 'view' ) ) :
		get_template_part( 'members/single/messages/single'); 

	else :
		do_action( 'bp_before_member_messages_content' ); ?>

	<div class="messages" role="main">

		<?php
			if ( bp_is_current_action( 'notices' ) )
				get_template_part( 'members/single/messages/notices', 'loop');
			else
				get_template_part( 'members/single/messages/messages', 'loop');
		?>

	</div>

	<?php do_action( 'bp_after_member_messages_content' ); ?>

<?php endif; ?>

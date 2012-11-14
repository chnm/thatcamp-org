<?php

/**
 * BuddyPress - Users Settings
 *
 * @package logicalbones
 * @since logicalbones 1.0
 */

?>

<?php

if ( bp_is_current_action( 'notifications' ) ) :
	get_template_part( 'members/single/settings/notifications');

elseif ( bp_is_current_action( 'delete-account' ) ) :
	get_template_part( 'members/single/settings/delete', 'account');

elseif ( bp_is_current_action( 'general' ) ) :
	get_template_part( 'members/single/settings/general');

else :
	get_template_part( 'members/single/plugins');

endif;

?>

<?php 
function widget_login() { ?>
        <?php global $user_ID, $user_identity, $user_level ?>
        <?php if ( $user_ID ) : ?>
        <h2><?php _e('Control Panel', "feed-me-seymour"); ?></h2>
        <ul>
            <li><?php _e('Logged in as:', "feed-me-seymour"); ?><strong> <?php echo $user_identity ?></strong></li>
            <li><a href="<?php echo admin_url(); ?>"><?php _e('Dashboard', "feed-me-seymour"); ?></a></li>

            <?php if ( $user_level >= 1 ) : ?>
            <li><a href="<?php echo admin_url('post-new.php'); ?>"><?php _e('Write', "feed-me-seymour"); ?></a></li>
            <?php endif // $user_level >= 1 ?>

            <li><a href="<?php echo admin_url('profile.php'); ?>"><?php _e('Profile', "feed-me-seymour"); ?></a></li>
            <li><a href="<?php echo wp_logout_url( get_permalink() ); ?>"><?php _e('Log Out', "feed-me-seymour"); ?></a></li>
        </ul>

        <?php else : ?>

        <h2><?php _e('User Login', "feed-me-seymour"); ?></h2>
            <?php wp_login_form(); ?>
        <ul>
            <?php if ( get_option('users_can_register') ) { ?><li><a href="<?php echo home_url(); ?>/wp-register.php"><?php _e('Register', "feed-me-seymour"); ?></a></li><?php } ?>
            <li><a href="<?php echo home_url(); ?>/wp-login.php?action=lostpassword"><?php _e('Lost your password', "feed-me-seymour"); ?></a></li>
        </ul>
        <?php endif // get_option('users_can_register') ?>
<?php
}

function widget_myLogin($args) {
	extract($args); 
	echo $before_widget;
	widget_login();
	echo $after_widget; 
}

wp_register_sidebar_widget( 'user_login', __('FMS - User Login', "feed-me-seymour"), 'widget_myLogin', array('description' => __('User login and control panel', "feed-me-seymour")) );
?>
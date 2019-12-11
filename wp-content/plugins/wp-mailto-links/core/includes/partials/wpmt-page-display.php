<?php
/**
 * Main Template
 */

$currentScreen = get_current_screen();
$columnCount = (1 == $currentScreen->get_columns()) ? 1 : 2;
$mulsitie_slug = ( is_multisite() ) ? 'network/' : '';

//Update settings
if( isset( $_POST[ $this->page_name . '_nonce' ] ) ){
    if( ! wp_verify_nonce( $_POST[ $this->page_name . '_nonce' ], $this->page_name ) ){
        wp_die( WPMT()->helpers->translate( 'You don\'t have permission to update these settings.', 'admin-settings' ) );
    }

    if( ! current_user_can( WPMT()->settings->get_admin_cap( 'admin-update-settings' ) ) ){
        wp_die( WPMT()->helpers->translate( 'You don\'t have permission to update these settings.', 'admin-settings' ) );
    }

    if( isset( $_POST[ $this->settings_key ] ) && is_array( $_POST[ $this->settings_key ] ) ){
        $check = update_option( $this->settings_key, $_POST[ $this->settings_key ] );
        if( $check ){
            WPMT()->settings->reload_settings();
            $update_notice = WPMT()->helpers->create_admin_notice( 'Settings successfully saved.', 'success', true );
            $this->display_notices[] = $update_notice;
        } else {
            $update_notice = WPMT()->helpers->create_admin_notice( 'No changes were made to your settings with your last save.', 'info', true );
            $this->display_notices[] = $update_notice;
        }
    }

}

?>

<div class="wrap">
    <h1><?php echo get_admin_page_title() ?></h1>

    <?php if( ! empty( $this->display_notices ) ) : ?>
        <div class="wpmt-admin-notices">
            <?php foreach( $this->display_notices as $single_notice ) : ?>
                <?php echo $single_notice; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <?php settings_fields( $this->page_name ); ?>

        <input type="hidden" name="<?php echo $this->page_name; ?>_nonce" value="<?php echo wp_create_nonce( $this->page_name ) ?>">

        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-<?php echo $columnCount; ?>">
                <?php include( 'widgets/main.php' ); ?>

                <div id="postbox-container-1" class="postbox-container">
                    <?php include( 'widgets/sidebar.php' ); ?>
                </div>
            </div>
        </div>
    </form>
</div>

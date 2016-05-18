<div class="wrap">
    <h1><?php echo get_admin_page_title() ?></h1>
    <?php
        if ($showUpdatedMessage):
            settings_errors();
        endif;
    ?>

    <form method="post" action="options.php">
        <?php settings_fields($option->getOptionGroup()); ?>

        <input type="hidden" name="<?php echo $id; ?>_nonce" value="<?php echo wp_create_nonce($id) ?>">
        <?php wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false); ?>
        <?php wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false); ?>

        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-<?php echo $columnCount; ?>">
                <?php echo $bodyContent; ?>

                <div id="postbox-container-1" class="postbox-container">
                    <?php do_meta_boxes('', 'side', ''); ?>
                </div>

                <div id="postbox-container-2" class="postbox-container">
                    <?php do_meta_boxes('', 'normal', ''); ?>
                    <?php do_meta_boxes('', 'advanced', ''); ?>
                </div>
            </div> <!-- #post-body -->
        </div> <!-- #poststuff -->
    </form>
</div>

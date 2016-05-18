<fieldset>
    <table class="form-table">
        <tr>
            <th scope="row">
                <?php _e('Choose Mail Icon', 'wp-mailto-links'); ?>
            </th>
            <td colspan="3">
                <p>
                    <label>
                        <?php $fieldsView->radioField('mail_icon', ''); ?>
                        <span><?php _e('No icon', 'wp-mailto-links') ?></span>
                    </label>
                </p>

                <p>
                    <label>
                        <?php $fieldsView->radioField('mail_icon', 'image'); ?>
                        <span><?php _e('Image', 'wp-mailto-links') ?></span>
                    </label>
                </p>
                <div class="wrap-icon-images inside">
                    <div style="width:12%;float:left">
                    <?php for ($x = 1; $x <= 25; $x++): ?>
                        <label>
                            <?php $fieldsView->radioField('image', $x); ?>
                            <img src="<?php echo plugins_url('/public/images/mail-icon-'. $x .'.png', WP_MAILTO_LINKS_FILE); ?>">
                        </label>
                        <br>
                        <?php if ($x % 5 == 0): ?>
                    </div>
                    <div style="width:12%;float:left">
                        <?php endif; ?>
                    <?php endfor; ?>
                    </div>
                    <br class="clear">
                </div>

                <p>
                    <label>
                        <?php $fieldsView->radioField('mail_icon', 'dashicons'); ?>
                        <span><?php _e('Dashicons', 'wp-mailto-links') ?></span>
                    </label>
                    <span class="description inside"><?php _e('(Default WordPress icons)', 'wp-mailto-links') ?></span>
                </p>
                <div class="wrap-dashicons inside" style="font-family:'dashicons'">
                    <?php $fieldsView->selectField('dashicons', null, array(), 'select-dashicons'); ?>
                </div>

                <p>
                    <label>
                        <?php $fieldsView->radioField('mail_icon', 'fontawesome'); ?>
                        <span><?php _e('Font Awesome Icons', 'wp-mailto-links') ?></span>
                    </label>
                </p>
                <div class="wrap-fontawesome inside" style="font-family:'FontAwesome'">
                    <?php $fieldsView->selectField('fontawesome', null, array(), 'select-fontawesome'); ?>
                </div>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <?php _e('Show icon left side', 'wp-mailto-links'); ?>
            </th>
            <td colspan="3">
                <?php $fieldsView->checkField('show_icon_before', '1', '0'); ?>
                <?php $fieldsView->label('show_icon_before', __('Show icon in front of the text (on the left side)', 'wp-mailto-links')); ?>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <?php _e('Skip icon containing <code>&lt;img&gt;</code>', 'wp-mailto-links'); ?>
            </th>
            <td colspan="3">
                <?php $fieldsView->checkField('image_no_icon', '1', '0'); ?>
                <?php $fieldsView->label('image_no_icon', __('No icon for links already containing an <code>&lt;img&gt;</code>-tag', 'wp-mailto-links')); ?>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <?php _e('No-icon Class', 'wp-mailto-links'); ?>
            </th>
            <td colspan="3">
                <?php $fieldsView->textField('no_icon_class'); ?>
                <p class="description"><?php _e('Use this class when a mailto link should not show an icon.', 'wp-mailto-links') ?></p>
            </td>
        </tr>
    </table>

    <p>
        <?php echo $fieldsView->submitButton(); ?>
    </p>
</fieldset>

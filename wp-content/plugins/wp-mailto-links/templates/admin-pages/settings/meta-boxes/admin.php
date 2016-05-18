<fieldset>
    <table class="form-table">
    <tr>
        <th scope="row">
            <?php _e('Security Check', 'wp-mailto-links'); ?>
        </th>
        <td>
            <p>
                <?php $fieldsView->checkField('security_check', '1', '0'); ?>
                <?php $fieldsView->label('security_check', __('Mark emails on the site as successfully encoded', 'wp-mailto-links')); ?>
                <i class="dashicons-before dashicons-lock"></i>
                <span class="description inside"><?php _e('Only visible for admin users.', 'wp-mailto-links') ?></span>
            </p>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <?php _e('Admin Menu', 'wp-mailto-links'); ?>
        </th>
        <td>
            <p>
                <?php $fieldsView->checkField('own_admin_menu', '1', '0'); ?>
                <?php $fieldsView->label('own_admin_menu', __('Show this page in the main menu item', 'wp-mailto-links')); ?>
                <span class="description inside"><?php _e('Or else will be shown in "Settings"-menu.', 'wp-mailto-links') ?></span>
            </p>
        </td>
    </tr>
    </table>

    <p>
        <?php echo $fieldsView->submitButton(); ?>
    </p>
</fieldset>

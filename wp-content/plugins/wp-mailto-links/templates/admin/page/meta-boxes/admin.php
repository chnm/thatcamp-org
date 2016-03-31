<fieldset>
    <table class="form-table">
    <tr>
        <th scope="row">
            <?php _e('Security Check', 'wp-mailto-links'); ?>
        </th>
        <td>
            <p>
                <label>
                    <input type="checkbox"
                           name="<?php echo $option->getFieldName('security_check'); ?>"
                           value="1"
                           <?php checked('1', $option->getValue('security_check')); ?>>
                    <span><?php _e('Mark emails on the site as successfully encoded', 'wp-mailto-links') ?> <i class="dashicons-before dashicons-"></i></span>
                    <span class="description inside"><?php _e('Only for admin users.', 'wp-mailto-links') ?></span>
                </label>
            </p>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <?php _e('Admin Menu', 'wp-mailto-links'); ?>
        </th>
        <td>
            <p>
                <label>
                    <input type="checkbox"
                           name="<?php echo $option->getFieldName('own_admin_menu'); ?>"
                           value="1"
                           <?php checked('1', $option->getValue('own_admin_menu')); ?>>
                    <span><?php _e('Show this page in the main menu item', 'wp-mailto-links') ?></span>
                    <span class="description inside"><?php _e('Or else will be shown in "Settings"-menu.', 'wp-mailto-links') ?></span>
                </label>
            </p>
        </td>
    </tr>
    </table>

    <p>
        <input class="button button-primary button-large" type="submit" value="<?php _e('Save Changes') ?>">
    </p>
</fieldset>

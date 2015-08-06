<fieldset class="options">
    <table class="form-table">
    <tr>
        <th><?php WPML::_e('Admin menu position') ?></th>
        <td><label><input type="checkbox" id="<?php echo WPML::get('optionName') ?>[own_admin_menu]" name="<?php echo WPML::get('optionName') ?>[own_admin_menu]" value="1" <?php checked('1', (int) $values['own_admin_menu']); ?> />
                <span><?php WPML::_e('Show as main menu item') ?></span>
                <br/><span class="description"><?php WPML::_e('When disabled item will be shown under "General settings".') ?></span></label></td>
    </tr>
    </table>
</fieldset>
<p class="submit">
    <input class="button-primary" type="submit" disabled="disabled" value="<?php _e('Save Changes') ?>" />
</p>
<br class="clear" />

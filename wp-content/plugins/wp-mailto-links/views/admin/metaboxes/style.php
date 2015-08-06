<fieldset class="options">
    <table class="form-table">
    <tr>
        <th><?php WPML::_e('Show icon') ?>
        </th>
        <td>
            <div>
                <div style="width:15%;float:left">
                    <label><input type="radio" name="<?php echo WPML::get('optionName') ?>[icon]" value="0" <?php checked('0', (int) $values['icon']); ?> />
                    <span><?php WPML::_e('No icon') ?></span></label>
                <?php for ($x = 1; $x <= 25; $x++): ?>
                    <br/>
                    <label title="<?php echo sprintf(WPML::__( 'Icon %1$s: choose this icon to show for all mailto links or add the class \'mail-icon-%1$s\' to a specific link.' ), $x ) ?>"><input type="radio" name="<?php echo WPML::get('optionName') ?>[icon]" value="<?php echo $x ?>" <?php checked($x, (int) $values['icon']); ?> />
                    <img src="<?php echo WPML::url('images/mail-icon-'. $x .'.png')  ?>" /></label>
                    <?php if ($x % 5 == 0): ?>
                </div>
                <div style="width:12%;float:left">
                    <?php endif; ?>
                <?php endfor; ?>
                </div>
                <div style="width:29%;float:left;"><span class="description"><?php WPML::_e('Example:') ?></span>
                    <br/><img src="<?php echo WPML::url('images/link-icon-example.png') ?>"    />
                </div>
                <br style="clear:both" />
            </div>
        </td>
    </tr>
    <tr>
        <th><?php WPML::_e('Skip images') ?></th>
        <td><label><input type="checkbox" name="<?php echo WPML::get('optionName') ?>[image_no_icon]" value="1" <?php checked('1', (int) $values['image_no_icon']); ?> />
            <span><?php WPML::_e('Do not show icon for mailto links containing an image') ?></span></label>
        </td>
    </tr>
    <tr>
        <th><?php WPML::_e('No-icon Class') ?></th>
        <td><label><input type="text" class="regular-text" id="<?php echo WPML::get('optionName') ?>[no_icon_class]" name="<?php echo WPML::get('optionName') ?>[no_icon_class]" value="<?php echo $values['no_icon_class']; ?>" />
            <br/><span class="description"><?php WPML::_e('Use this class when a mailto link should not show an icon.') ?></span></label>
        </td>
    </tr>
    <tr>
        <th><?php WPML::_e('Additional Classes (optional)') ?></th>
        <td><label><input type="text" class="regular-text" id="<?php echo WPML::get('optionName') ?>[class_name]" name="<?php echo WPML::get('optionName') ?>[class_name]" value="<?php echo $values['class_name']; ?>" />
            <br/><span class="description"><?php WPML::_e('Add extra classes to mailto links (or leave blank).') ?></span></label></td>
    </tr>
    </table>
</fieldset>
<p class="submit">
    <input class="button-primary" type="submit" disabled="disabled" value="<?php _e('Save Changes' ) ?>" />
</p>
<br class="clear" />

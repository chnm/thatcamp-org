<fieldset class="options">
    <table class="form-table">
        <tr>
            <th scope="row">
                <?php _e('Choose Mail Icon', 'wp-mailto-links'); ?>
            </th>
            <td colspan="3">
                <p>
                    <label>
                        <input type="radio"
                               name="<?php echo $option->getFieldName('mail_icon'); ?>"
                               value=""
                               <?php checked('', $option->getValue('mail_icon')); ?>>
                        <span><?php _e('No icon', 'wp-mailto-links') ?></span>
                    </label>
                </p>

                <p>
                    <label>
                        <input type="radio"
                               name="<?php echo $option->getFieldName('mail_icon'); ?>"
                               value="image"
                               <?php checked('image', $option->getValue('mail_icon')); ?>>
                        <span><?php _e('Image', 'wp-mailto-links') ?></span>
                    </label>
                </p>
                <div class="wrap-icon-images inside">
                    <div style="width:12%;float:left">
                    <?php for ($x = 1; $x <= 25; $x++): ?>
                        <label>
                            <input type="radio"
                                   name="<?php echo $option->getFieldName('image'); ?>"
                                   value="<?php echo $x ?>"
                                   <?php checked((string) $x, $option->getValue('image')); ?>>
                            <img src="<?php echo WPML::glob('URL') . '/images/mail-icon-'. $x .'.png' ?>">
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
                        <input type="radio"
                               name="<?php echo $option->getFieldName('mail_icon'); ?>"
                               value="dashicons"
                               <?php checked('dashicons', $option->getValue('mail_icon')); ?>>
                        <span><?php _e('Dashicons', 'wp-mailto-links') ?></span>
                    </label>
                    <span class="description inside"><?php _e('(Default WordPress icons)', 'wp-mailto-links') ?></span>
                </p>
                <div class="wrap-dashicons inside">
                    <select class="select-dashicons"
                            name="<?php echo $option->getFieldName('dashicons'); ?>"
                            style="font-family:'dashicons'; font-size:1.5em;">
                    </select>
                </div>

                <p>
                    <label>
                        <input type="radio"
                               name="<?php echo $option->getFieldName('mail_icon'); ?>"
                               value="fontawesome"
                               <?php checked('fontawesome', $option->getValue('mail_icon')); ?>>
                        <span><?php _e('Font Awesome Icons', 'wp-mailto-links') ?></span>
                    </label>
                </p>
                <div class="wrap-fontawesome inside">
                    <select class="select-fontawesome" 
                            name="<?php echo $option->getFieldName('fontawesome'); ?>"
                            style="font-family:'FontAwesome'; font-size:1.5em; position:static;">
                    </select>
                </div>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <?php _e('Show icon left side', 'wp-mailto-links'); ?>
            </th>
            <td colspan="3">
                <label>
                    <input type="checkbox"
                           id="show_icon_before"
                           name="<?php echo $option->getFieldName('show_icon_before'); ?>"
                           value="1"
                           <?php checked('1', $option->getValue('show_icon_before')); ?>>
                    <span><?php _e('Show icon in front of the text (on the left side), ', 'wp-mailto-links') ?></span>
                </label>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <?php _e('Skip icon containing <code>&lt;img&gt;</code>', 'wp-mailto-links'); ?>
            </th>
            <td colspan="3">
                <label>
                    <input type="checkbox"
                           id="image_no_icon"
                           name="<?php echo $option->getFieldName('image_no_icon'); ?>"
                           value="1"
                           <?php checked('1', $option->getValue('image_no_icon')); ?>>
                    <span><?php _e('No icon for links already containing an <code>&lt;img&gt;</code>-tag', 'wp-mailto-links') ?></span>
                </label>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <?php _e('No-icon Class', 'wp-mailto-links'); ?>
            </th>
            <td colspan="3">
                <label>
                    <input type="text"
                           id="no_icon_class"
                           name="<?php echo $option->getFieldName('no_icon_class'); ?>"
                           class="regular-text"
                           value="<?php echo esc_attr($option->getValue('no_icon_class')); ?>">
                </label>
                <p class="description"><?php _e('Use this class when a mailto link should not show an icon.', 'wp-mailto-links') ?></p>
            </td>
        </tr>
    </table>

    <p>
        <input class="button button-primary button-large" type="submit" value="<?php _e('Save Changes') ?>">
    </p>
</fieldset>

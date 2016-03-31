<fieldset class="options">
    <p>
        <label>
            <input type="text"
                   id="class_name"
                   name="<?php echo $option->getFieldName('class_name'); ?>"
                   value="<?php echo esc_attr($option->getValue('class_name')); ?>">
        </label>
        <p class="description"><?php _e('Add extra classes to mailto links (or leave blank).', 'wp-mailto-links') ?></p>
    </p>

    <p>
        <input class="button button-primary button-large" type="submit" value="<?php _e('Save Changes') ?>">
    </p>
</fieldset>

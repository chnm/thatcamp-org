<fieldset>
    <p>
        <?php $fieldsView->textField('class_name', ''); ?>
        <p class="description"><?php _e('Add extra classes to mailto links (or leave blank).', 'wp-mailto-links') ?></p>
    </p>

    <p>
        <?php echo $fieldsView->submitButton(); ?>
    </p>
</fieldset>

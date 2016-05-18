<div id="post-body-content">
    <div class="stuffbox">
        <div class="inside">
            <fieldset>
                <table class="form-table">
                <tr>
                    <th scope="row">
                        <?php _e('Protect mailto links', 'wp-mailto-links'); ?>
                    </th>
                    <td>
                        <?php $fieldsView->checkField('protect', '1', '0'); ?>
                        <?php $fieldsView->label('protect', __('Protect mailto links against spambots', 'wp-mailto-links')); ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <?php _e('Protect plain emails', 'wp-mailto-links'); ?>
                    </th>
                    <td>
                        <p>
                            <label>
                                <?php $fieldsView->radioField('convert_emails', '0'); ?>
                                <span><?php _e('No, keep plain emails as they are', 'wp-mailto-links'); ?></span>
                            </label>
                        </p>
                        <p>
                            <label>
                                <?php $fieldsView->radioField('convert_emails', '1'); ?>
                                <span><?php _e('Yes, protect plain emails with protection text *', 'wp-mailto-links'); ?></span>
                                <span class="description inside"><?php _e('(Recommended)', 'wp-mailto-links') ?></span>
                            </label>
                        </p>
                        <p>
                            <label>
                                <?php $fieldsView->radioField('convert_emails', '2'); ?>
                                <span><?php _e('Yes, convert plain emails to mailto links', 'wp-mailto-links'); ?></span>
                            </label>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <?php _e('Set protection text *', 'wp-mailto-links'); ?>
                    </th>
                    <td colspan="3">
                        <?php $fieldsView->textField('protection_text'); ?>
                        <p class="description"><?php _e('This text will be shown for protected emailaddresses.', 'wp-mailto-links'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <?php _e('Also protect...', 'wp-mailto-links'); ?>
                    </th>
                    <td colspan="3">
                        <p>
                            <?php $fieldsView->checkField('filter_head', '1', '0'); ?>
                            <?php $fieldsView->label('filter_head', __('<code>&lt;head&gt;</code>-section by replacing emails with protection text *', 'wp-mailto-links')); ?>
                        </p>
                        <p>
                            <?php $fieldsView->checkField('filter_rss', '1', '0'); ?>
                            <?php $fieldsView->label('filter_rss', __('RSS feed by replacing emails with protection text *', 'wp-mailto-links')); ?>
                        </p>
                        <p>
                            <?php $fieldsView->checkField('input_strong_protection', '1', '0'); ?>
                            <?php $fieldsView->label('input_strong_protection', __('Strong protection for input form fields', 'wp-mailto-links')); ?>
                            <p class="description"><?php _e('Warning: this option could conflict with certain form plugins. Test it first.', 'wp-mailto-links'); ?></p>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <?php _e('Options have effect on', 'wp-mailto-links'); ?>
                    </th>
                    <td colspan="3">
                        <p>
                            <?php $fieldsView->checkField('filter_body', '1', '0'); ?>
                            <?php $fieldsView->label('filter_body', __('All contents', 'wp-mailto-links')); ?>
                            <span class="description inside"><?php _e('(the whole <code>&lt;body&gt;</code>)', 'wp-mailto-links'); ?></span>
                        </p>
                        <div class="inside">
                            <p>
                                <?php $fieldsView->checkField('filter_posts', '1', '0', 'filter-body-child'); ?>
                                <?php $fieldsView->label('filter_posts', __('Post contents', 'wp-mailto-links')); ?>
                            </p>
                            <p>
                                <?php $fieldsView->checkField('filter_comments', '1', '0', 'filter-body-child'); ?>
                                <?php $fieldsView->label('filter_comments', __('Comments', 'wp-mailto-links')); ?>
                            </p>
                            <p>
                                <?php $fieldsView->checkField('filter_widgets', '1', '0', 'filter-body-child'); ?>
                                <?php $fieldsView->label('filter_widgets', __('All widgets', 'wp-mailto-links')); ?>
                            </p>
                        </div>
                    </td>
                </tr>
                </table>
            </fieldset>

            <p>
                <?php echo $fieldsView->submitButton(); ?>
            </p>
        </div>
    </div>
</div>

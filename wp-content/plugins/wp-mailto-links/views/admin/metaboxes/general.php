<fieldset class="options">
    <table class="form-table">
    <tr>
        <th><?php WPML::_e('Protect mailto links') ?></th>
        <td><label><input type="checkbox" id="<?php echo WPML::get('optionName') ?>[protect]" name="<?php echo WPML::get('optionName') ?>[protect]" value="1" <?php checked('1', (int) $values['protect']); ?> />
            <span><?php WPML::_e('Protect mailto links against spambots') ?></span></label>
        </td>
    </tr>
    <tr>
        <th><?php WPML::_e('Protect plain emails') ?></th>
        <td><label><input type="radio" id="<?php echo WPML::get('optionName') ?>[convert_emails]" name="<?php echo WPML::get('optionName') ?>[convert_emails]" value="0" <?php checked('0', (int) $values['convert_emails']); ?> />
            <span><?php WPML::_e('No, keep plain emails as they are') ?></span></label>
            <br/><label><input type="radio" id="<?php echo WPML::get('optionName') ?>[convert_emails]" name="<?php echo WPML::get('optionName') ?>[convert_emails]" value="1" <?php checked('1', (int) $values['convert_emails']); ?> />
            <span><?php WPML::_e('Yes, protect plain emails with protection text *') ?></span> <span class="description"><?php WPML::_e('(Recommended)') ?></span></label>
            <br/><label><input type="radio" id="<?php echo WPML::get('optionName') ?>[convert_emails]" name="<?php echo WPML::get('optionName') ?>[convert_emails]" value="2" <?php checked('2', (int) $values['convert_emails']); ?> />
            <span><?php WPML::_e('Yes, convert plain emails to mailto links') ?></span></label>
        </td>
    </tr>
    <tr>
        <th><?php WPML::_e('Options have effect on') ?></th>
        <td>
            <label><input type="checkbox" name="<?php echo WPML::get('optionName') ?>[filter_body]" id="filter_body" value="1" <?php checked('1', (int) $values['filter_body']); ?> />
            <span><?php WPML::_e('All contents') ?></span> <span class="description"><?php WPML::_e('(the whole <code>&lt;body&gt;</code>)') ?></span></label>
            <br/>&nbsp;&nbsp;<label><input type="checkbox" name="<?php echo WPML::get('optionName') ?>[filter_posts]" id="filter_posts" value="1" <?php checked('1', (int) $values['filter_posts']); ?> />
                    <span><?php WPML::_e('Post contents') ?></span></label>
            <br/>&nbsp;&nbsp;<label><input type="checkbox" name="<?php echo WPML::get('optionName') ?>[filter_comments]" id="filter_comments" value="1" <?php checked('1', (int) $values['filter_comments']); ?> />
                    <span><?php WPML::_e('Comments') ?></span></label>
            <br/>&nbsp;&nbsp;<label><input type="checkbox" name="<?php echo WPML::get('optionName') ?>[filter_widgets]" id="filter_widgets" value="1" <?php checked('1', (int) $values['filter_widgets']); ?> />
                    <span><?php if (WPML_Admin::hasWidgetLogicFilter()) { WPML::_e('All widgets (uses the <code>widget_content</code> filter of the Widget Logic plugin)'); } else { WPML::_e('All text widgets'); } ?></span></label>
        </td>
    </tr>
    <tr>
        <th><?php WPML::_e('Also protect...') ?></th>
        <td><label><input type="checkbox" name="<?php echo WPML::get('optionName') ?>[filter_head]" value="1" <?php checked('1', (int) $values['filter_head']); ?> />
                <span><?php WPML::_e('<code>&lt;head&gt;</code>-section by replacing emails with protection text *') ?></span></label>
            <br/><label><input type="checkbox" name="<?php echo WPML::get('optionName') ?>[filter_rss]" value="1" <?php checked('1', (int) $values['filter_rss']); ?> />
                <span><?php WPML::_e('RSS feed by replacing emails with protection text *') ?></span></label>
            <br/><label><input type="checkbox" name="<?php echo WPML::get('optionName') ?>[input_strong_protection]" value="1" <?php checked('1', (int) $values['input_strong_protection']); ?> />
                <span><?php WPML::_e('Strong protection for input form fields') ?></span> <span class="description"><?php WPML::_e('(Warning: this option could conflict with certain form plugins. Test it first.)') ?></span></label>
        </td>
    </tr>
    <tr>
        <th><?php WPML::_e('Set protection text *') ?></th>
        <td><label><input type="text" id="protection_text" class="regular-text" name="<?php echo WPML::get('optionName') ?>[protection_text]" value="<?php echo $values['protection_text']; ?>" />
                <br/><span class="description"><?php WPML::_e('This text will be shown for protected emailaddresses.') ?></span>
            </label>
        </td>
    </tr>
    </table>
</fieldset>
<p class="submit">
    <input class="button-primary" type="submit" disabled="disabled" value="<?php _e('Save Changes' ) ?>" />
</p>
<br class="clear" />

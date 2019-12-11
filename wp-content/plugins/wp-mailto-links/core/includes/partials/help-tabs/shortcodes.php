<h3><?php echo WPMT()->helpers->translate( 'Shortcodes', 'help_tab-shortcodes' ); ?></h3>
<h4><code>[wpml_mailto email="..."]...[/wpml_mailto]</code></h4>
<p><?php echo WPMT()->helpers->translate( 'Create a protected mailto link in your posts:', 'help_tab-shortcodes' ); ?></p>
<p><code>[wpml_mailto email="info@myemail.com"]My Email[/wpml_mailto]</code>
</p>
<p><?php echo WPMT()->helpers->translate( 'It\'s also possible to add attributes to the mailto link, like a target:', 'help_tab-shortcodes' ); ?></p>
<p><code>[wpml_mailto email="info@myemail.com" target="_blank"]My Email[/wpml_mailto]</code>
</p>
<p></p>
<h4><code>[wpmt_protect]...[/wpmt_protect]</code></h4>
<p><?php echo WPMT()->helpers->translate( 'Protect content using our plugin that is not encodedby default (E.g. some ajax loaded values):', 'help_tab-shortcodes' ); ?></p>
<p><code>[wpmt_protect]YOUR CONTENT YOU WANT TO CHECK FOR EMAILS[/wpmt_protect]</code>
</p>
<p><?php echo WPMT()->helpers->translate( 'It\'s also possible to customize the encoding type using "protect_using". Possible values: char_encode, strong_method, without_javascript, with_javascript:', 'help_tab-shortcodes' ); ?></p>
<p><code>[wpmt_protect protect_using="..."]YOUR CONTENT YOU WANT TO CHECK FOR EMAILS[/wpmt_protect]</code>
</p>

<?php $pluginData = get_plugin_data( WPMT_PLUGIN_FILE ); ?>
<h3><i class="dashicons-before dashicons-email"></i>  <?php echo $pluginData['Name'] ?> - v<?php echo $pluginData['Version']; ?></h3>
<p>
    <?php echo WPMT()->helpers->translate( 'The plugin works out-of-the-box to protect your email addresses. All settings are default set to protect your email addresses automatically.', 'help_tab-general' ); ?>
</p>
<p>
    <?php echo WPMT()->helpers->translate( 'If you want to manually create protected mailto links, just use the shortcode (<code>[wpml_mailto]</code>) within your posts or use the template tags (<code>wpml_mailto()</code> or <code>wpml_filter()</code>) in your theme files.', 'help_tab-general' ); ?>
</p>
<p>
    <?php echo WPMT()->helpers->translate( 'To report problems or bugs or for support, please use <a href="https://wordpress.org/support/plugin/wp-mailto-links#postform" target="_new">the official forum</a>.', 'help_tab-general' ); ?>
</p>
<p>
    <?php echo WPMT()->helpers->translate( 'You can now also check your website protection using our email checker tool: <a href="https://ironikus.com/email-checker/" target="_blank">https://ironikus.com/email-checker/</a>.', 'help_tab-general' ); ?>
</p>
<p>
    Visit us at <a href="https://ironikus.com" target="_blank" title="Visit us at https://ironikus.com" >https://ironikus.com</a>
    <i class="dashicons-before dashicons-universal-access"></i>
</p>

<hr>
<h3><?php echo WPMT()->helpers->translate( 'Cheat Sheet', 'help_tab-general' ); ?> <i class="dashicons-before dashicons-smiley"></i></h3>
<table>
    <tr>
        <td><?php echo WPMT()->helpers->translate( 'Shortcode:', 'help_tab-general' ); ?></td>
        <td><code><b>[wpml_mailto</b> email="..."<b>]</b>...<b>[/wpml_mailto]</b></code></td>
    </tr>
    <tr>
        <td><?php echo WPMT()->helpers->translate( 'Template tags:', 'help_tab-general' ); ?></td>
        <td><code><b>wpml_mailto(</b> $email [, $display] [, $attrs] <b>)</b>;</code></td>
    </tr>
    <tr>
        <td></td>
        <td><code><b>wpml_filter(</b> $content <b>)</b>;</code></td>
    </tr>
</table>

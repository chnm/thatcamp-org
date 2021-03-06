<h3><?php echo WPMT()->helpers->translate( 'Template functions', 'help_tab-template-tags' ); ?></h3>

<h4><code>wpml_mailto( $email [, $display] [, $attrs] )</code></h4>
<p><?php echo WPMT()->helpers->translate( 'Create a protected mailto link:', 'help_tab-template-tags' ); ?></p>
<pre><code><&#63;php
    if (function_exists('wpml_mailto')) {
        echo wpml_mailto('info@somedomain.com', 'Mail Me');
    }
&#63;></code></pre>
<p><?php echo WPMT()->helpers->translate( 'You can pass 2 optional arguments: <code>$display</code>, <code>$attrs</code>', 'help_tab-template-tags' ); ?></p>

<h4><code>wpml_filter( $content )</code></h4>
<p><?php echo WPMT()->helpers->translate( 'Scans given content for protecting mailto links, shortcodes and plain emails according to the admin settings:', 'help_tab-template-tags' ); ?></p>
<pre><code><&#63;php
    if (function_exists('wpml_filter')) {
        echo wpml_filter('Filter some content to protect an emailaddress like info@somedomein.com.');
    }
&#63;></code></pre>

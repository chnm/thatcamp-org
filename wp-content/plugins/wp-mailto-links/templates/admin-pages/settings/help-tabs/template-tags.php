<h3><?php _e('Template functions', 'wp-mailto-links'); ?></h3>

<h4><code>wpml_mailto( $email [, $display] [, $attrs] )</code></h4>
<p><?php _e('Create a protected mailto link:', 'wp-mailto-links'); ?></p>
<pre><code><&#63;php
    if (function_exists('wpml_mailto')) {
        echo wpml_mailto('info@somedomain.com', 'Mail Me');
    }
&#63;></code></pre>
<p><?php _e('You can pass 2 optional arguments: <code>$display</code>, <code>$attrs</code>', 'wp-mailto-links'); ?></p>

<h4><code>wpml_filter( $content )</code></h4>
<p><?php _e('Scans given content for protecting mailto links, shortcodes and plain emails according to the admin settings:', 'wp-mailto-links'); ?></p>
<pre><code><&#63;php
    if (function_exists('wpml_filter')) {
        echo wpml_filter('Filter some content to protect an emailaddress like info@somedomein.com.');
    }
&#63;></code></pre>

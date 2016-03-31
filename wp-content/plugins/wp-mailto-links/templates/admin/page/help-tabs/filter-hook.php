<h3><?php _e('Filter Hook', 'wp-mailto-links'); ?> <span class="description"><?php _e('(deprecated)', 'wp-mailto-links'); ?></span></h3>

<h4><code>add_filter('wpml_mailto', 'func', 10, 4)</code></h4>
<p><?php _e('The wpml_mailto filter gives you the possibility to manipulate output of the mailto created by the plugin. F.e. make all mailto links bold:', 'wp-mailto-links'); ?></p>
<pre><code><&#63;php
// define filter function
function special_mailto($link, $display, $email, $attrs) {
    return '&lt;b&gt;'. $link .'&lt;/b&gt;';
}

// hook it to "wpml_mailto" filter
add_filter('wpml_mailto', 'special_mailto', 10, 4);
&#63;></code></pre>
<p><?php _e('Now all mailto links will be wrapped around a <code>&lt;b&gt;</code>-tag.', 'wp-mailto-links'); ?></p>

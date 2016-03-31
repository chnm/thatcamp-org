<h3><?php _e('Action Hook', 'wp-mailto-links'); ?> <span class="description"><?php _e('(deprecated)', 'wp-mailto-links'); ?></span></h3>

<h4><code>add_action('wpml_ready', 'func');</code></h4>
<p><?php _e('Add extra code after plugin is ready on the site, f.e. to add extra filters:', 'wp-mailto-links'); ?></p>
<pre><code><&#63;php
// define callback function
function extra_filters($filter_callback, $object) {
    add_filter('some_filter', $filter_callback);
}

// hook it to "wpml_ready" action
add_action('wpml_ready', 'extra_filters');
&#63;></code></pre>

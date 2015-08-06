<h3>Action Hooks</h3>

<h4>wpml_ready</h4>
<p>Add extra code after plugin is ready on the site, f.e. to add extra filters:</p>
<pre><code><&#63;php
add_action('wpml_ready', 'extra_filters');

function extra_filters($filter_callback, $object) {
    add_filter('some_filter', $filter_callback);
}
&#63;></code></pre>

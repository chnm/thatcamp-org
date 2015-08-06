<h3>Filter Hooks</h3>

<h4>wpml_mailto</h4>
<p>The wpml_mailto filter gives you the possibility to manipulate output of the mailto created by the plugin. F.e. make all mailto links bold:</p>
<pre><code><&#63;php
add_filter('wpml_mailto', 'special_mailto', 10, 4);

function special_mailto($link, $display, $email, $attrs) {
    return '&lt;b&gt;'. $link .'&lt;/b&gt;';
}
&#63;></code></pre>
<p>Now all mailto links will be wrapped around a &lt;b&gt;-tag.</p>

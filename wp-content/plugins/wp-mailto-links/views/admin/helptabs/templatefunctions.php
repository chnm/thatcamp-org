<h3>Template functions</h3>

<h4>wpml_mailto()</h4>
<p>Create a protected mailto link:</p>
<pre><code><&#63;php
if (function_exists('wpml_mailto')) {
    echo wpml_mailto('info@somedomain.com');
}
&#63;></code></pre>
<p>You can pass a few extra optional params (in this order): <code>display</code>, <code>attrs</code></p>

<h4>wpml_filter()</h4>
<p>Filter given content to protect mailto links, shortcodes and plain emails (according to the settings in admin):</p>
<pre><code><&#63;php
if (function_exists('wpml_filter')) {
    echo wpml_filter('Filter some content to protect an emailaddress like info@somedomein.com.');
}
&#63;></code></pre>

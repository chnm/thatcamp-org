<?php
/*
Plugin Name: CW Author Base
Plugin URI: http://clioweb.org
Description: Very simple plugin that lets you use change the author base for author URLs.
Author: Jeremy Boggs
Version: 1.0
Author URI: http://clioweb.org
*/

function cw_author_base_init() {
    global $wp_rewrite;
   	$cw_author_base = get_option('cw_author_base');
	if(!empty($cw_author_base)) {
    $wp_rewrite->author_base = $cw_author_base;
    $wp_rewrite->flush_rules();
	}
}

add_action('init','cw_author_base_init');

function cw_author_base_edit() {
	$cw_author_base = get_option('cw_author_base');
	if(!empty($cw_author_base)) {
		$author_url_base = $cw_author_base;
	} else {
		$author_url_base = 'author';
	}
	?>
	<div class="wrap">
	<h2>CW Author Base</h2>
    <p>You may enter a custom URL base for your author URLs. For example, using <code>users</code> as your author base would make your author links like http://example.org/users/username/. If you leave these blank the default <code>author</code> will be used.</p>
	<form method="post" action="options.php">
	<?php wp_nonce_field('update-options'); ?>

	<table class="form-table">

	<tr valign="top">
	<th scope="row">Author Base</th>
	<td><input type="text" name="cw_author_base" value="<?php echo $author_url_base; ?>" /></td>
	</tr>

	</table>

	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="page_options" value="cw_author_base" />

	<p class="submit">
	<input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" />
	</p>

	</form>
	</div>

<?php }
function cw_author_base_add_options_page() {
add_options_page('CW Author Base', 'CW Author Base', 'manage_options', 'testoptions', 'cw_author_base_edit');
}

add_action('admin_menu', 'cw_author_base_add_options_page');

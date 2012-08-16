<?php
/*
Plugin Name: Footnotes for WordPress
Plugin URI: http://projects.radgeek.com/wp-footnotes.php
Description: easy-to-use fancy footnotes for WordPress posts
Version: 2010.0822
Author: Charles Johnson
Author URI: http://radgeek.com/
License: GPL
*/

/**
 * @package FootnotesForWordPress
 * @version 2010.0822
 */
define('FFWP_VERSION', '2010.0822');

class FootnotesForWordPress {
	var $accumulated;

	function FootnotesForWordPress () { // constructor
		$this->accumulated = array();

		$url = $this->plugin_url();
		
		// Pre-register scripts and styles
		wp_register_script(
			'footnote-voodoo',
			"${url}footnote-voodoo.js",
			/*depends on=*/ array('jquery'),
			/*ver=*/ FFWP_VERSION
		);
		wp_register_style(
			'footnote-voodoo',
			"${url}footnote-voodoo.css",
			/*depends on=*/ array(),
			/*ver=*/ FFWP_VERSION
		);

		add_shortcode('ref', array(&$this, 'shortcode'));
		add_shortcode('backref', array(&$this, 'backref'));
		add_shortcode('references', array(&$this, 'discharge'));
		
		// Way downstream; needs to be after do_shortcode (priority 11), for one thing
		add_filter('the_content', array(&$this, 'the_content'), 1000, 2);
		
		add_action('init', array(&$this, 'add_scripts'));
		add_action('wp_head', array(&$this, 'add_inline_styles'));
	} /* FootnotesForWordPress constructor */

	var $plugin_path = NULL;
	function plugin_url () {
		if (is_null($this->plugin_path)) :
			preg_match (
				'|'.WP_PLUGIN_DIR.'/(.+)$|',
				dirname(__FILE__),
				$ref
			);
			
			if (isset($ref[1])) :
				$this->plugin_path = $ref[1];
			else : // Something went wrong. Let's just guess.
				$this->plugin_path = 'footnotes-for-wordpress';
			endif;
		endif;
		return trailingslashit(WP_PLUGIN_URL.'/'.$this->plugin_path);
	}
	function add_scripts () {
		wp_enqueue_script('footnote-voodoo');
		wp_enqueue_style('footnote-voodoo');
	}
	function add_inline_styles () {
	?>
<style type="text/css">

	.footnote-indicator:before {
		content: url(<?php print $this->plugin_url(); ?>footnoted.png);
		width: 10px;
		height: 10px;
	}
</style>
<script type="text/javascript">
	// Globals
	var tipUpUrl = 'url(<?php print $this->plugin_url(); ?>tip.png)';
	var tipDownUrl = 'url(<?php print $this->plugin_url(); ?>tip-down.png)';
</script>
	<?php
	}

	var $bullets = array();
	function shortcode ($atts, $content = NULL, $code = '') {
		global $post;

		// Get parameters
		$atts = shortcode_atts( array(
			"name" => NULL,
			'backlink-prefix' => 'to-',
		), $atts );

		$noteId = $atts['name'];
		if (!isset($this->bullets[$post->post_name])) :
			$this->bullets[$post->post_name] = array();
		endif;
		
		$bullet = (count($this->bullets[$post->post_name]) + 1);
		if (is_null($noteId) and !is_null($post)) :
			$noteId = $post->post_name.'-n-'.$bullet;
		endif;
		$this->bullets[$post->post_name][$noteId] = $bullet;
		
		// Allow any inside shortcodes to do their work.
		$content = do_shortcode($content);
		$note_marker = "<strong><sup>[$bullet]</sup></strong>";

		$note = <<<EON
<li class="footnote" id="$noteId">$note_marker $content <a class="note-return" href="#{$atts['backlink-prefix']}{$noteId}">&#x21A9;</a></li>
EON;
		$this->accumulated[] = $note;

		return '<sup>[<a href="#'.$noteId.'" class="footnoted" id="'.$atts['backlink-prefix'].$noteId.'">'.$bullet.'</a>]</sup>';
	} /* FootnotesForWordPress::shortcode */

	function backref ($atts = array(), $content = NULL, $code = '') {
		global $post;

		// Get parameters
		$atts = shortcode_atts( array(
			"name" => NULL,
			'backlink-prefix' => 'to-',
		), $atts );

		$bullet = $this->bullets[$post->post_name][$atts['name']];

		if (!is_null($atts['name'])) :
			$ret = '<sup>[<a href="#'.$atts['name'].'" class="footnoted">'.$bullet.'</a>]</sup>';
		else :
			$ret = '';
		endif;

		return $ret;
	}
	function discharge ($atts = array(), $content = NULL, $code = '') {
		// Get parameters
		$atts = shortcode_atts ( array(
			"class" => "footnotes",
		), $atts );

		$notes = '';
		if (count($this->accumulated) > 0) :
			$notes = "<ol class=\"{$atts['class']}\">\n\t"
				.implode("\n\t", $this->accumulated)
				."</ol>\n";
			$this->accumulated = array();
		endif;

		return $notes;
	} /* FootnotesForWordPress::discharge */

	function the_content ($content) {
		/* Discharge any remaining footnotes */
		$content .= "\n".$this->discharge();

		return $content;
	} /* FootnotesForWordPress::the_content() */
} /* class FootnotesForWordPress */

$footnotesForWordPress = new FootnotesForWordPress;


<?php
/*
Plugin Name: Add Attribution for FeedWordPress
Plugin URI: http://projects.radgeek.com/add-attribution-feedwordpress/
Description: enable FeedWordPress to add a prefix or a suffix to elements of syndicated posts, containing attribution information
Version: 2010.0207
Author: Charles Johnson
Author URI: http://radgeek.com/
License: GPL
*/

add_action(
	/*hook=*/ 'feedwordpress_admin_page_posts_meta_boxes',
	/*function=*/ 'add_meta_box_add_source_information',
	/*priority=*/ 100,
	/*arguments=*/ 1
);
add_action(
	/*hook=*/ 'feedwordpress_admin_page_posts_save',
	/*function=*/ 'add_source_information_save',
	/*priority=*/ 100,
	/*arguments=*/ 2
);
add_filter(
	/*hook=*/ 'the_title',
	/*function=*/ 'add_source_information_title',
	/*priority=*/ 11000,
	/*arguments=*/ 2
);
add_filter(
	/*hook=*/ 'get_the_excerpt',
	/*function=*/ 'add_source_information_excerpt',
	/*priority=*/ 11000,
	/*arguments=*/ 1
);
add_filter(
	/*hook=*/ 'the_content',
	/*function=*/ 'add_source_information_content',
	/*priority=*/ 11000,
	/*arguments=*/ 1
);
add_filter(
	/*hook=*/ 'the_content_rss',
	/*function=*/ 'add_source_information_content',
	/*priority=*/ 11000,
	/*arguments=*/ 1
);

function add_meta_box_add_source_information ($page) {
	add_meta_box(
		/*id=*/ 'feedwordpress_add_attribution_box',
		/*title=*/ __('Attribution Boilerplate'),
		/*callback=*/ 'add_source_information_box',
		/*page=*/ $page->meta_box_context(),
		/*context=*/ $page->meta_box_context()
	);
} /* add_meta_box_add_source_information() */

function add_source_information_box ($page, $box = NULL) {
	if ($page->for_feed_settings()) :
		$attrib = unserialize($page->link->settings['add attribution rules']);
		$syndicatedPosts = 'this feed\'s posts';
	else :
		$attrib = get_option('feedwordpress_add_attribution');
		$syndicatedPosts = 'syndicated posts';
	endif;
?>
	<style type="text/css">	
	.add-attribution-help-box {
		float: right;
		width: 200px;
		border: 1px dotted #777;
		margin: 3px;
		padding: 5px;
		background-color: #f7f7f7;
	}
	.add-attribution-help-box dt {
		font-weight: bold;
		font-size: 85%;
	}
	.add-attribution-help-box dd {
		font-size: 80%; line-height: 100%; font-style: italic;
		padding-left: 1.5em;
	}
	.add-attribution-help-box code {
		font-size: inherit;
		font-style: normal;
		background-color: inherit;
	}

	.add-attribution-li {
		padding-bottom: 5px;
		margin-bottom: 5px;
		border-bottom: 1px dotted black;
	}
	</style>

	<div class="add-attribution-help-box">
	<p style="border-bottom: 1px dotted #777;">To remove boilerplate, just blank out the text box and leave it empty.</p>

	<p>Use shortcodes to include information about your source:</p>
	<dl>
	<dt><code>[source]</code></dt>
	<dd>A link to the source you syndicated the post from</dd>

	<dt><code>[source-name]</code></dt>
	<dd>Name of the source you syndicated the post from</dd>

	<dt><code>[source-url]</code></dt>
	<dd>URL of the source you syndicated the post from</dd>

	<dt><code>[original-url]</code></dt>
	<dd>URL of the <em>original post</em> back on the source website</dd>

	<dt><code>[author]</code></dt>
	<dd>A link with the name of the author who wrote the post, linking to other posts by the same author</dd>

	<dt><code>[author-name]</code></dt>
	<dd>Name of the author who wrote the post</dd>
	
	<dt><code>[feed-setting key="setting-name"]</code></dt>
	<dd>Value of a custom feed setting (named <code>setting-name</code>) for the feed</dd>
	</dl>
	</div>

<?php
	print "<ul>\n";
	if (!is_array($attrib)) : $attrib = array(); endif;

	print '<input type="hidden" id="next-attribution-rule-index" name="next_attribution_rule_index" value="'.count($attrib).'" />';

	// In AJAX environment, this is a dummy rule that stays hidden. In a
	// non-AJAX environment, this provides a blank rule that we can fill in.
	$attrib['new'] = array(
		'class' => array('hide-if-js'),
		'placement' => 'before',
		'element' => 'post',
		'template' => '',
	);

	foreach ($attrib as $index => $line) :
		if (isset($line['template'])) :
			$selected['before'] = (($line['placement']=='before') ? ' selected="selected"' : '');
			$selected['after'] = (($line['placement']=='after') ? ' selected="selected"' : '');
			$selected['title'] = (($line['element']=='title') ? ' selected="selected"' : '');
			$selected['post'] = (($line['element']=='post') ? ' selected="selected"' : '');
			$selected['excerpt'] = (($line['element']=='excerpt') ? ' selected="selected"' : '');

			if (!isset($line['class'])) : $line['class'] = array(); endif;
			$line['class'][] = 'add-attribution-li';
		?>

<li id="add-attribution-<?php print $index; ?>-li" class="<?php print implode(' ', $line['class']); ?>">&raquo; <strong>Add</strong> <select id="add-attribution-<?php print $index; ?>-placement" name="add_attribution[<?php print $index; ?>][placement]" style="width: 8.0em">
<option value="before"<?php print $selected['before']; ?>>before</option>
<option value="after"<?php print $selected['after']; ?>>after</option>
</select> the <select style="width: 8.0em" id="add-attribution-<?php print $index; ?>-element" name="add_attribution[<?php print $index; ?>][element]">
<option value="title"<?php print $selected['title']; ?>>title</option>
<option value="post"<?php print $selected['post']; ?>>content</option>
<option value="excerpt"<?php print $selected['excerpt']; ?>>excerpt</option>
</select> of 
<?php print $syndicatedPosts; ?>: <textarea style="vertical-align: top" rows="2" cols="30" class="add-attribution-template" id="add-attribution-<?php print $index; ?>-template" name="add_attribution[<?php print $index; ?>][template]"><?php print htmlspecialchars($line['template']); ?></textarea></li>
	<?php
		endif;
	endforeach;
	?>
	</ul>
	<br style="clear: both" />

	<script type="text/javascript">
		jQuery(document).ready( function($) {
			$('.add-attribution-template').blur( function() {
				if (this.value.length == 0) {
					var theLi = $('li:has(#'+this.id+")");
					theLi.hide('normal')
				}
			} );

			var addRuleLi = document.createElement('li');
			addRuleLi.innerHTML = '<strong style="vertical-align: middle; font-size: 110%">[+]</strong> <a style="font-variant: small-caps" id="add-new-attribution-rule" href="#">Add new boilerplate</a> ….';
			$('#add-attribution-new-li').after(addRuleLi);

			$('#add-new-attribution-rule').click( function() {
				// Get index counter
				var nextIndex = parseInt($('#next-attribution-rule-index').val());

				var newIdPrefix = 'add-attribution-'+nextIndex;
				var newNamePrefix = 'add_attribution['+nextIndex+']';

				var dummy = {};
				dummy['li'] = {'el': $('#add-attribution-new-li') }
				dummy['placement'] = {'el': $('#add-attribution-new-placement') };
				dummy['element'] = {'el': $('#add-attribution-new-element') };
				dummy['template'] = {'el': $('#add-attribution-new-template') };

				for (var element in dummy) {
					dummy[element]['save'] = {
						'id': dummy[element]['el'].attr('id'),
						'name': dummy[element]['el'].attr('name')
					};
					dummy[element]['el'].attr('id', newIdPrefix+'-'+element);
					dummy[element]['el'].attr('name', newNamePrefix+'['+element+']');
				}
	
				var newLi = $('#'+newIdPrefix+'-li').clone(/*events=*/ true);
				//newLi.attr('id', null);
				newLi.removeClass('hide-if-js');
				newLi.addClass('add-attribution-li');
				newLi.css('display', 'none');

				// Switch back
				for (var element in dummy) {
					dummy[element]['el'].attr('id', dummy[element]['save']['id']);
					dummy[element]['el'].attr('name', dummy[element]['save']['name']);
				}

				$('#add-attribution-new-li').before(newLi);
				newLi.show('normal');

				$('#next-attribution-rule-index').val(nextIndex+1);

				return false;
			} )
		} );
	</script>
	<?php
} /* add_source_information_box () */

function add_source_information_save ($params, $page) {
	if (isset($params['add_attribution'])) :
		foreach ($params['add_attribution'] as $index => $line) :
			if (0 == strlen(trim($line['template']))) :
				unset($params['add_attribution'][$index]);
			endif;
		endforeach;

		// Convert indexes to 0..(N-1) to avoid possible collisions
		$params['add_attribution'] = array_values($params['add_attribution']);

		if ($page->for_feed_settings()) :
			$page->link->settings['add attribution rules'] = serialize($params['add_attribution']);
			$page->link->save_settings(/*reload=*/ true);
		else :
			update_option('feedwordpress_add_attribution', $params['add_attribution']);
		endif;
	endif;
}

class AddSourceInformationReformatter {
	var $id, $element;
	function AddSourceInformationReformatter ($id = NULL, $element = 'post') {
		$this->id = $id;
		$this->element = $element;
	}
	
	function source_name ($atts) {
		$param = shortcode_atts(array(
		'original' => NULL,
		), $atts);
		return get_syndication_source($param['original'], $this->id);
	}
	function source_url ($atts) {
		$param = shortcode_atts(array(
		'original' => NULL,
		), $atts);
		return get_syndication_source_link($param['original'], $this->id);
	}
	function source_link ($atts) {
		switch (strtolower($atts[0])) :
		case '-name' :
			$ret = $this->source_name($atts);
			break;
		case '-url' :
			$ret = $this->source_url($atts);
			break;
		default :
			$param = shortcode_atts(array(
			'original' => NULL,
			), $atts);
			if ('title' == $this->element) :
				$ret = $this->source_name($atts);
			else :
				$ret = '<a href="'.htmlspecialchars($this->source_url($atts)).'">'.htmlspecialchars($this->source_name($atts)).'</a>';
			endif;
		endswitch;
		return $ret;
	}
	function source_setting ($atts) {
		$param = shortcode_atts(array(
		'key' => NULL,
		), $atts);
		return get_feed_meta($param['key'], $this->id);
	}
	function original_url ($atts) {
		return get_syndication_permalink($this->id);
	}
	function source_author ($atts) {
		return get_the_author();
	}
	function source_author_link ($atts) {
		switch (strtolower($atts[0])) :
		case '-name' :
			$ret = $this->source_author($atts);
			break;
		default :
			global $authordata; // Janky.
			if ('title' == $this->element) :
				$ret = $this->source_author($atts);
			else :
				$ret = get_the_author();
				$url = get_author_posts_url((int) $authordata->ID, (int) $authordata->user_nicename);
				if ($url) :
					$ret = '<a href="'.$url.'" '
						.'title="Read other posts by '.wp_specialchars($authordata->display_name).'">'
						.$ret
						.'</a>';
				endif;			
			endif;
		endswitch;
		return $ret;
	}
}

function add_source_information_reformat ($template, $element, $id = NULL) {
	if ('post' == $element and !preg_match('/<(p|div)>/i', $template)) :
		$template = '<p class="syndicated-attribution">'.$template.'</p>';
	endif;

	// Register shortcodes. We need to use an object to preserve the value of $id
	$ref = new AddSourceInformationReformatter($id, $element);
	add_shortcode('source', array($ref, 'source_link'));
	add_shortcode('source-name', array($ref, 'source_name'));
	add_shortcode('source-url', array($ref, 'source_url'));
	add_shortcode('original-url', array($ref, 'original_url'));
	add_shortcode('author', array($ref, 'source_author_link'));
	add_shortcode('author_name', array($ref, 'source_author'));
	add_shortcode('feed-setting', array($ref, 'source_setting'));

	$template = do_shortcode($template);

	// Unregister shortcodes
	remove_shortcode('source');
	remove_shortcode('source-name');
	remove_shortcode('source-url');
	remove_shortcode('original-url');
	remove_shortcode('author');
	remove_shortcode('author_name');
	remove_shortcode('feed-setting');

	return $template;
}

function add_source_information_simple ($element, $title, $id = NULL) {
	if (is_syndicated($id)) :
		$meta = get_feed_meta('add attribution rules', $id);
		if ($meta and !is_array($meta)) : $meta = unserialize($meta); endif;

		if (!is_array($meta) or empty($meta)) :
			$meta = get_option('feedwordpress_add_attribution');
		endif;

		if (is_array($meta) and !empty($meta)) :
			foreach ($meta as $rule) :
				if ($element==$rule['element']) :
					$rule['template'] = add_source_information_reformat($rule['template'], $element, $id);

					if ('before'==$rule['placement']) :
						$title = $rule['template'] . ' ' . $title;
					else :
						$title = $title . ' ' . $rule['template'];
					endif;
				endif;
			endforeach;
		endif;
	endif;
	return $title;
}
function add_source_information_title ($title, $id = NULL) {
	return add_source_information_simple('title', $title, $id);
}
function add_source_information_excerpt ($title, $id = NULL) {
	return add_source_information_simple('excerpt', $title, $id);
}
function add_source_information_content ($content) {
	if (is_syndicated()) :
		$meta = get_feed_meta('add attribution rules');
		if ($meta and !is_array($meta)) : $meta = unserialize($meta); endif;

		if (!is_array($meta) or empty($meta)) :
			$meta = get_option('feedwordpress_add_attribution');
		endif;

		if (is_array($meta) and !empty($meta)) :
			foreach ($meta as $rule) :
				if ('post'==$rule['element']) :
					$rule['template'] = add_source_information_reformat($rule['template'], 'post');

					if ('before'==$rule['placement']) :
						$content = $rule['template'] . "\n" . $content;
					else :
						$content = $content . "\n" . $rule['template'];
					endif;
				endif;
			endforeach;
		endif;
	endif;
	return $content;	
}


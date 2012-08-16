<?php

class YARPP_Meta_Box {
	function checkbox($option,$desc,$tr="<tr valign='top'><th class='th-full' colspan='2' scope='row'>",$inputplus = '',$thplus='') {
		echo "$tr<input $inputplus type='checkbox' name='$option' value='true'";
		checked(yarpp_get_option($option) == 1);
		echo "  /> $desc</th>$thplus
			</tr>";
	}
	function template_checkbox( $rss = false, $trextra = '' ) {
		global $yarpp;
		$pre = $rss ? 'rss_' : '';
		$chosen_template = yarpp_get_option( "{$pre}template" );
		echo "<tr valign='top'{$trextra}><th colspan='2'><input type='checkbox' name='{$pre}use_template' class='{$pre}template' value='true'";
		disabled(!count($yarpp->admin->get_templates()), true);
		checked( !!$chosen_template );
		echo " /> " . __("Display using a custom template file",'yarpp')." <a href='#' class='info'>".__('more&gt;','yarpp')."<span>".__("This advanced option gives you full power to customize how your related posts are displayed. Templates (stored in your theme folder) are written in PHP.",'yarpp')."</span></a>" . "</th></tr>";
	}
	function textbox($option,$desc,$size=2,$tr="<tr valign='top'>
				<th scope='row'>", $note = '') {
		$value = esc_attr(yarpp_get_option($option));
		echo "			$tr$desc</th>
				<td><input name='$option' type='text' id='$option' value='$value' size='$size' />";
		if ( !empty($note) )
			echo " <em><small>{$note}</small></em>";
		echo "</td>
			</tr>";
	}
	function beforeafter($options,$desc,$size=10,$tr="<tr valign='top'>
				<th scope='row'>", $note = '') {
		echo "			$tr$desc</th>
				<td>";
		$value = esc_attr(yarpp_get_option($options[0]));
		echo "<input name='{$options[0]}' type='text' id='{$options[0]}' value='$value' size='$size' /> / ";
		$value = esc_attr(yarpp_get_option($options[1]));
		echo "<input name='{$options[1]}' type='text' id='{$options[1]}' value='$value' size='$size' />";
		if ( !empty($note) )
			echo " <em><small>{$note}</small></em>";
		echo "</td>
			</tr>";
	}

	function tax_weight($taxonomy) {
		$weight = (int) yarpp_get_option("weight[tax][{$taxonomy->name}]");
		$require = (int) yarpp_get_option("require_tax[{$taxonomy->name}]");
		echo "<tr valign='top'><th scope='row'>{$taxonomy->labels->name}:</th><td><select name='weight[tax][{$taxonomy->name}]'>";
		echo "<option value='no'". ((!$weight && !$require) ? ' selected="selected"': '' )."  > " . __("do not consider",'yarpp') . "</option>";
		echo "<option value='consider'". (($weight == 1 && !$require) ? ' selected="selected"': '' )."  >" . __("consider",'yarpp') . "</option>";
		echo "<option value='consider_extra'". (($weight > 1 && !$require) ? ' selected="selected"': '' )."  >" . __("consider with extra weight",'yarpp') . "</option>";
		echo "<option value='require_one'". (($require == 1) ? ' selected="selected"': '' )."  >" . sprintf(__("require at least one %s in common",'yarpp'),$taxonomy->labels->singular_name) . "</option>";
		echo "<option value='require_more'". (($require == 2) ? ' selected="selected"': '' )."  >" . sprintf(__("require more than one %s in common",'yarpp'),$taxonomy->labels->singular_name) . "</option>";
		echo "</select></td></tr>";
	}
	
	function weight($option,$desc,$tr="<tr valign='top'><th scope='row'>",$inputplus = '') {
		$weight = (int) yarpp_get_option("weight[$option]");
		echo "$tr$desc</th><td>";
		echo "<select name='weight[$option]'>";
		echo "<option $inputplus value='no'". (!$weight ? ' selected="selected"': '' )."  >".__("do not consider",'yarpp')."</option>";
		echo "<option $inputplus value='consider'". (($weight == 1) ? ' selected="selected"': '' )."  > ".__("consider",'yarpp')."</option>";
		echo "<option $inputplus value='consider_extra'". (($weight > 1) ? ' selected="selected"': '' )."  > ".__("consider with extra weight",'yarpp')."</option>";
		echo "</select></td></tr>";
	}
	
	function displayorder( $option, $class = '' ) {
	?>
			<tr<?php if (!empty($class)) echo " class='$class'"; ?> valign='top'>
				<th><?php _e("Order results:",'yarpp');?></th>
				<td><select name="<?php echo $option; ?>" id="<?php echo $option; ?>">
					<?php $order = yarpp_get_option($option); ?>
					<option value="score DESC" <?php echo ($order == 'score DESC'?' selected="selected"':'')?>><?php _e("score (high relevance to low)",'yarpp');?></option>
					<option value="score ASC" <?php echo ($order == 'score ASC'?' selected="selected"':'')?>><?php _e("score (low relevance to high)",'yarpp');?></option>
					<option value="post_date DESC" <?php echo ($order == 'post_date DESC'?' selected="selected"':'')?>><?php _e("date (new to old)",'yarpp');?></option>
					<option value="post_date ASC" <?php echo ($order == 'post_date ASC'?' selected="selected"':'')?>><?php _e("date (old to new)",'yarpp');?></option>
					<option value="post_title ASC" <?php echo ($order == 'post_title ASC'?' selected="selected"':'')?>><?php _e("title (alphabetical)",'yarpp');?></option>
					<option value="post_title DESC" <?php echo ($order == 'post_title DESC'?' selected="selected"':'')?>><?php _e("title (reverse alphabetical)",'yarpp');?></option>
				</select>
				</td>
			</tr>
	<?php
	}
}

class YARPP_Meta_Box_Pool extends YARPP_Meta_Box {
	function exclude($taxonomy, $string) {
		global $yarpp;
?>
			<tr valign='top'>
				<th scope='row'><?php echo $string; ?></th>
				<td><div class='scroll_wrapper' style="overflow:auto;max-height:100px;"><div class='exclude_terms' id='exclude_<?php echo $taxonomy; ?>'>
<?php
$exclude_tt_ids = wp_parse_id_list(yarpp_get_option('exclude'));
$exclude_term_ids = $yarpp->admin->get_term_ids_from_tt_ids( $taxonomy, $exclude_tt_ids );
if ( count($exclude_term_ids) ) {
	$terms = get_terms($taxonomy, array('include' => $exclude_term_ids));
	foreach ($terms as $term) {
		echo "<input type='checkbox' name='exclude[{$term->term_taxonomy_id}]' id='exclude_{$term->term_taxonomy_id}' value='true' checked='checked' /> <label for='exclude_{$term->term_taxonomy_id}'>" . esc_html($term->name) . "</label> ";
	}
}
?>
				</div></div></td>
			</tr>
<?php
	}

	function display() {
		global $yarpp;
?>
	<p><?php _e('"The Pool" refers to the pool of posts and pages that are candidates for display as related to the current entry.','yarpp');?></p>

	<table class="form-table" style="margin-top: 0; clear:none;">
		<tbody>
		<tr><th><?php _e('Post types considered:', 'yarpp'); ?></th><td><?php echo implode(', ', $yarpp->get_post_types( 'label' )); ?> <a href='http://wordpress.org/extend/plugins/yet-another-related-posts-plugin/other_notes'><?php _e('more&gt;','yarpp');?></a></td></tr>
<?php
	foreach ($yarpp->get_taxonomies() as $taxonomy) {
		$this->exclude($taxonomy->name, sprintf(__('Disallow by %s:','yarpp'), $taxonomy->labels->singular_name));
	}
	$this->checkbox('show_pass_post',__("Show password protected posts?",'yarpp'));

	$recent = yarpp_get_option('recent');
	if ( !!$recent ) {
		list($recent_number, $recent_units) = explode(' ', $recent);
	} else {
		$recent_number = 12;
		$recent_units = 'month';
	}
	$recent_number = "<input name=\"recent_number\" type=\"text\" id=\"recent_number\" value=\"".esc_attr($recent_number)."\" size=\"2\" />";
	$recent_units = "<select name=\"recent_units\" id=\"recent_units\">
		<option value='day'". (('day'==$recent_units)?" selected='selected'":'').">".__('day(s)','yarpp')."</option>
		<option value='week'". (('week'==$recent_units)?" selected='selected'":'').">".__('week(s)','yarpp')."</option>
		<option value='month'". (('month'==$recent_units)?" selected='selected'":'').">".__('month(s)','yarpp')."</option>
	</select>";

	echo "<tr valign='top'><th class='th-full' colspan='2' scope='row'><input type='checkbox' name='recent_only' value='true'";
	checked(!!$recent);
	echo " /> ";
	echo str_replace('NUMBER',$recent_number,str_replace('UNITS',$recent_units,__("Show only posts from the past NUMBER UNITS",'yarpp')));
	echo "</th></tr>";

?>
		</tbody>
	</table>
<?php
	}
}

add_meta_box('yarpp_pool', __('"The Pool"','yarpp'), array(new YARPP_Meta_Box_Pool, 'display'), 'settings_page_yarpp', 'normal', 'core');

class YARPP_Meta_Box_Relatedness extends YARPP_Meta_Box {
	function display() {
		global $yarpp;
?>
	<p><?php _e('YARPP limits the related posts list by (1) a maximum number and (2) a <em>match threshold</em>.','yarpp');?> <a href="#" class='info'><?php _e('more&gt;','yarpp');?><span><?php _e('The higher the match threshold, the more restrictive, and you get less related posts overall. The default match threshold is 5. If you want to find an appropriate match threshhold, take a look at some post\'s related posts display and their scores. You can see what kinds of related posts are being picked up and with what kind of match scores, and determine an appropriate threshold for your site.','yarpp');?></span></a></p>

	<table class="form-table" style="margin-top: 0; clear:none;">
		<tbody>

<?php
	$this->textbox('threshold',__('Match threshold:','yarpp'));
	$this->weight('title',__("Titles: ",'yarpp'),"<tr valign='top'>
			<th scope='row'>",( !$yarpp->myisam ? ' readonly="readonly" disabled="disabled"':'' ));
	$this->weight('body',__("Bodies: ",'yarpp'),"<tr valign='top'>
			<th scope='row'>",( !$yarpp->myisam ? ' readonly="readonly" disabled="disabled"':'' ));

	foreach ($yarpp->get_taxonomies() as $taxonomy) {
		$this->tax_weight($taxonomy);
	}

	$this->checkbox('cross_relate',__("Display results from all post types",'yarpp')." <a href='#' class='info'>".__('more&gt;','yarpp')."<span>".__("When \"display results from all post types\" is off, only posts will be displayed as related to a post, only pages will be displayed as related to a page, etc.",'yarpp')."</span></a>");
	$this->checkbox('past_only',__("Show only previous posts?",'yarpp'));
?>
			</tbody>
		</table>
<?php
	}
}

add_meta_box('yarpp_relatedness', __('"Relatedness" options','yarpp'), array(new YARPP_Meta_Box_Relatedness, 'display'), 'settings_page_yarpp', 'normal', 'core');

class YARPP_Meta_Box_Display_Web extends YARPP_Meta_Box {
	function display() {
		global $yarpp;
	?>
		<table class="form-table" style="margin-top: 0; clear:none;">
		<tbody>
<?php
		$this->checkbox('auto_display',__("Automatically display related posts?",'yarpp')." <a href='#' class='info'>".__('more&gt;','yarpp')."<span>".__("This option automatically displays related posts right after the content on single entry pages. If this option is off, you will need to manually insert <code>related_posts()</code> or variants (<code>related_pages()</code> and <code>related_entries()</code>) into your theme files.",'yarpp')."</span></a>","<tr valign='top'>
			<th class='th-full' colspan='2' scope='row' style='width:100%;'>",'','<td rowspan="3" style="border-left:8px transparent solid;"><b>'.__("Website display code example",'yarpp').'</b><br /><small>'.__("(Update options to reload.)",'yarpp').'</small><br/>'
."<div id='display_demo_web' style='overflow:auto;width:350px;max-height:500px;'></div></td>");
		$this->textbox('limit',__('Maximum number of related posts:','yarpp'));
		$this->template_checkbox( false );
		?>
		</tbody></table>
		<table class="form-table" style="clear:none;"><tbody>
			<tr valign='top' class='templated'>
				<th><?php _e("Template file:",'yarpp');?></th>
				<td>
					<select name="template_file" id="template_file">
						<?php 
						$chosen_template = yarpp_get_option('template');
						foreach ($yarpp->admin->get_templates() as $template): ?>
						<option value='<?php echo esc_attr($template)?>'<?php selected($template, $chosen_template);?>><?php echo esc_html($template)?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
	<?php
	$this->beforeafter(array('before_related', 'after_related'),__("Before / after related entries:",'yarpp'),15,"<tr class='not_templated' valign='top'>\r\t\t\t\t<th>", __("For example:",'yarpp') . ' &lt;ol&gt;&lt;/ol&gt;' . __(' or ','yarpp') . '&lt;div&gt;&lt;/div&gt;');
	$this->beforeafter(array('before_title', 'after_title'),__("Before / after each related entry:",'yarpp'),15,"<tr class='not_templated' valign='top'>\r\t\t\t\t<th>", __("For example:",'yarpp') . ' &lt;li&gt;&lt;/li&gt;' . __(' or ','yarpp') . '&lt;dl&gt;&lt;/dl&gt;');
	
	$this->checkbox('show_excerpt',__("Show excerpt?",'yarpp'),"<tr class='not_templated' valign='top'><th colspan='2'>",' class="show_excerpt"');
	$this->textbox('excerpt_length',__('Excerpt length (No. of words):','yarpp'),10,"<tr class='excerpted' valign='top'>
				<th>")?>

			<tr class="excerpted" valign='top'>
				<th><?php _e("Before / after (Excerpt):",'yarpp');?></th>
				<td><input name="before_post" type="text" id="before_post" value="<?php echo esc_attr(yarpp_get_option('before_post')); ?>" size="10" /> / <input name="after_post" type="text" id="after_post" value="<?php echo esc_attr(yarpp_get_option('after_post')); ?>" size="10" /><em><small> <?php _e("For example:",'yarpp');?> &lt;li&gt;&lt;/li&gt;<?php _e(' or ','yarpp');?>&lt;dl&gt;&lt;/dl&gt;</small></em>
				</td>
			</tr>

	<?php 
	$this->displayorder('order');
	
	$this->textbox('no_results',__('Default display if no results:','yarpp'),'40',"<tr class='not_templated' valign='top'>
				<th>");
	
	$this->checkbox('promote_yarpp',__("Help promote Yet Another Related Posts Plugin?",'yarpp')
	." <a href='#' class='info'>".__('more&gt;','yarpp')."<span>"
	.sprintf(__("This option will add the code %s. Try turning it on, updating your options, and see the code in the code example to the right. These links and donations are greatly appreciated.", 'yarpp'),"<code>".htmlspecialchars(sprintf(__("Related posts brought to you by <a href='%s'>Yet Another Related Posts Plugin</a>.",'yarpp'), 'http://yarpp.org'))."</code>")	."</span></a>"); ?>
		</tbody>
		</table>
<?php
	}
}

add_meta_box('yarpp_display_web', __('Display options <small>for your website</small>','yarpp'), array(new YARPP_Meta_Box_Display_Web, 'display'), 'settings_page_yarpp', 'normal', 'core');

class YARPP_Meta_Box_Display_Feed extends YARPP_Meta_Box {
	function display() {
		global $yarpp;
?>
		<table class="form-table" style="margin-top: 0; clear:none;"><tbody>
<?php

$this->checkbox('rss_display',__("Display related posts in feeds?",'yarpp')." <a href='#' class='info'>".__('more&gt;','yarpp')."<span>".__("This option displays related posts at the end of each item in your RSS and Atom feeds. No template changes are needed.",'yarpp')."</span></a>","<tr valign='top'><th colspan='2' style='width:100%'>",' class="rss_display"','<td class="rss_displayed" rowspan="4" style="border-left:8px transparent solid;"><b>'.__("RSS display code example",'yarpp').'</b><br /><small>'.__("(Update options to reload.)",'yarpp').'</small><br/>'
."<div id='display_demo_rss' style='overflow:auto;width:350px;max-height:500px;'></div></td>");
$this->checkbox('rss_excerpt_display',__("Display related posts in the descriptions?",'yarpp')." <a href='#' class='info'>".__('more&gt;','yarpp')."<span>".__("This option displays the related posts in the RSS description fields, not just the content. If your feeds are set up to only display excerpts, however, only the description field is used, so this option is required for any display at all.",'yarpp')."</span></a>","<tr class='rss_displayed' valign='top'>
			<th class='th-full' colspan='2' scope='row'>");

	$this->textbox('rss_limit',__('Maximum number of related posts:','yarpp'),2, "<tr valign='top' class='rss_displayed'>
				<th scope='row'>");
	$this->template_checkbox( true, " class='rss_displayed'" );
	?>
	</tbody></table>
	<table class="form-table rss_displayed" style="clear:none;">
		<tbody>
			<tr valign='top' class='rss_templated'>
				<th><?php _e("Template file:",'yarpp');?></th>
				<td>
					<select name="rss_template_file" id="rss_template_file">
						<?php
						$chosen_template = yarpp_get_option('rss_template');
						foreach ($yarpp->admin->get_templates() as $template): ?>
						<option value='<?php echo esc_attr($template);?>'<?php selected($template, $chosen_template);?>><?php echo esc_html($template);?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>

	<?php 
	$this->beforeafter(array('rss_before_related', 'rss_after_related'),__("Before / after related entries:",'yarpp'),15,"<tr class='rss_not_templated' valign='top'>\r\t\t\t\t<th>", __("For example:",'yarpp') . ' &lt;ol&gt;&lt;/ol&gt;' . __(' or ','yarpp') . '&lt;div&gt;&lt;/div&gt;');
	$this->beforeafter(array('rss_before_title', 'rss_after_title'),__("Before / after each related entry:",'yarpp'),15,"<tr class='rss_not_templated' valign='top'>\r\t\t\t\t<th>", __("For example:",'yarpp') . ' &lt;li&gt;&lt;/li&gt;' . __(' or ','yarpp') . '&lt;dl&gt;&lt;/dl&gt;');
	
	$this->checkbox('rss_show_excerpt',__("Show excerpt?",'yarpp'),"<tr class='rss_not_templated' valign='top'><th colspan='2'>",' class="rss_show_excerpt"');
	$this->textbox('rss_excerpt_length',__('Excerpt length (No. of words):','yarpp'),10,"<tr class='rss_excerpted' valign='top'>\r\t\t\t\t<th>");

	$this->beforeafter(array('rss_before_post', 'rss_after_post'),__("Before / after (excerpt):",'yarpp'),10,"<tr class='rss_excerpted' valign='top'>\r\t\t\t\t<th>", __("For example:",'yarpp') . ' &lt;li&gt;&lt;/li&gt;' . __(' or ','yarpp') . '&lt;dl&gt;&lt;/dl&gt;');

	$this->displayorder('rss_order', 'rss_displayed');
	
	$this->textbox('rss_no_results',__('Default display if no results:','yarpp'),'40',"<tr valign='top' class='rss_not_templated'>
			<th scope='row'>")?>
	<?php $this->checkbox('rss_promote_yarpp',__("Help promote Yet Another Related Posts Plugin?",'yarpp')." <a href='#' class='info'>".__('more&gt;','yarpp')."<span>"
	.sprintf(__("This option will add the code %s. Try turning it on, updating your options, and see the code in the code example to the right. These links and donations are greatly appreciated.", 'yarpp'),"<code>".htmlspecialchars(sprintf(__("Related posts brought to you by <a href='%s'>Yet Another Related Posts Plugin</a>.",'yarpp'), 'http://yarpp.org'))."</code>")	."</span></a>","<tr valign='top' class='rss_displayed'>
			<th class='th-full' colspan='2' scope='row'>"); ?>
		</tbody></table>
<?php
	}
}

add_meta_box('yarpp_display_rss', __('Display options <small>for RSS</small>','yarpp'), array(new YARPP_Meta_Box_Display_Feed, 'display'), 'settings_page_yarpp', 'normal', 'core');

class YARPP_Meta_Box_Contact extends YARPP_Meta_Box {
	function display() {
		$pluginurl = plugin_dir_url(__FILE__);
		?>
		<ul class='yarpp_contacts'>
		<li  style="background: url(<?php echo $pluginurl . 'wordpress.png'; ?>) no-repeat left bottom;"><a href="http://wordpress.org/tags/yet-another-related-posts-plugin" target="_blank"><?php _e('YARPP Forum', 'yarpp'); ?></a></li>
		<li style="background: url(<?php echo $pluginurl . 'twitter.png' ; ?>) no-repeat left bottom;"><a href="http://twitter.com/yarpp" target="_blank"><?php _e('YARPP on Twitter', 'yarpp'); ?></a></li>
		<li style="background: url(<?php echo $pluginurl . 'plugin.png'; ?>) no-repeat left bottom;"><a href="http://yarpp.org" target="_blank"><?php _e('YARPP on the Web', 'yarpp'); ?></a></li>
		<li style="background: url(<?php echo $pluginurl . 'star.png'; ?>) no-repeat 3px 2px;"><a href="http://wordpress.org/extend/plugins/yet-another-related-posts-plugin/" target="_blank"><?php _e('Rate YARPP on WordPress.org', 'yarpp'); ?></a></li>
		<li style="background: url(<?php echo $pluginurl . 'paypal-icon.png'; ?>) no-repeat left bottom;"><a href='http://tinyurl.com/donatetomitcho' target='_new'><img src="https://www.paypal.com/<?php echo $this->paypal_lang(); ?>i/btn/btn_donate_SM.gif" name="submit" alt="<?php _e('Donate to mitcho (Michael Yoshitaka Erlewine) for this plugin via PayPal');?>" title="<?php _e('Donate to mitcho (Michael Yoshitaka Erlewine) for this plugin via PayPal','yarpp');?>"/></a></li>
	 </ul>
<?php
	}
	
	function paypal_lang() {
		if ( !defined('WPLANG') )
			return 'en_US/';
		$lang = substr(WPLANG, 0, 2);
		switch ( $lang ) {
			case 'fr':
				return 'fr_FR/';
			case 'de':
				return 'de_DE/';
			case 'it':
				return 'it_IT/';
			case 'ja':
				return 'ja_JP/';
			case 'es':
				return 'es_XC/';
			case 'nl':
				return 'nl_NL/';
			case 'pl':
				return 'pl_PL/';
			case 'zh':
				if (preg_match("/^zh_(HK|TW)/i",WPLANG))
					return 'zh_HK/';
				// actually zh_CN, but interpret as default zh:
				return 'zh_XC/';
			default:
				return 'en_US/';
		}
	}
}

add_meta_box('yarpp_display_contact', __('Contact YARPP','yarpp'), array(new YARPP_Meta_Box_Contact, 'display'), 'settings_page_yarpp', 'side', 'core');

// since 3.3: hook for registering new YARPP meta boxes
do_action( 'add_meta_boxes_settings_page_yarpp' );


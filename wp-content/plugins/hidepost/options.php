<?php
if(is_plugin_page()) {
    load_plugin_textdomain('hidepost', 'wp-content/plugins/hidepost');
    $location = get_option('siteurl') . '/wp-admin/admin.php?page=hidepost/options.php';

//Update Option
if ('process' == $_POST['stage']) {
        update_option('hidepost_content_text', trim($_POST['hidepost_content_text']));
        update_option('hidepost_link_text', trim($_POST['hidepost_link_text']));
        update_option('hidepost_role_text', trim($_POST['hidepost_role_text']));
        update_option('hidepost_hide_link', $_POST['hidepost_hide_link']);
        update_option('hidepost_hide_content', $_POST['hidepost_hide_content']);
        update_option('hidepost_content_text_hide', $_POST['hidepost_content_text_hide']);
        update_option('hidepost_link_text_hide', $_POST['hidepost_link_text_hide']);
        update_option('hidepost_role_text_hide', $_POST['hidepost_role_text_hide']);
    }


if (isset($_GET['hidepost_disable_notice'])) {
	update_option('hidepost_disable_notice', $_GET['hidepost_disable_notice']);
}

if (isset($_POST['hidepost_replace_sure']))//Replace
{
  $hidepost_replace_good = 1;
  echo '<div id="message" class="updated fade"><p><strong>';
  if (!isset($_POST['hidepost_replace_tag_check'])) {
       echo 'Please check the check box next to "Yes"<br/>';
       $hidepost_replace_good = 0;
  }
  if ($_POST['hidepost_new_tag'] != '[hidepost]') {
    echo 'Please input [hidepost] in New Hidepost tag<br/>';
    $hidepost_replace_good = 0;
  }
  if ($hidepost_replace_good == 1) {
      echo hidepost_search_and_replace($_POST['hidepost_old_tag'],
                              $_POST['hidepost_old_close_tag'],
                              $_POST['hidepost_new_tag']
        );
  }
  else echo 'Replace tag Failed <br/>';
  echo '</strong></p></div>';
}
//Load the config
include("option_init.php");
//Main Option Page
?>
<form name="update_hidepost" method="post" action="<?php echo $location ?>&amp;updated=true">
<input type="hidden" name="stage" value="process" />
	<div class="wrap">
	<h2>HidePost Options</h2>
	<table class="form-table">
		<tr>
			<th scope="row" valign="top">General Options</th>
			<td>
				<label for="hidepost_hide_content"><input name="hidepost_hide_content" type="checkbox" id="hidepost_hide_content" value="1" <?php echo is_checked($hidepost_hide_content); ?>/>
				<strong>Enable Hidepost</strong> (Uncheck if you want to disable HidePost).</label>
			<br />
				<label for="hidepost_hide_link"><input name="hidepost_hide_link" type="checkbox" id="hidepost_hide_link" value="1" <?php echo is_checked($hidepost_hide_link); ?>/>
				<strong>Protect Link</strong> (Only logged in member can see link on your post).</label>
			</td>
		</tr>
		<tr>
			<th scope="row" valign="top">Protect Content:</th>
			<td>
				<label for="hidepost_content_text_hide"><input name="hidepost_content_text_hide" type="checkbox" id="hidepost_content_text_hide" value ="1" <?php echo is_checked($hidepost_content_text_hide); ?>/> Show blank spaces ( Instead of the text below )</label><br />
				<input name="hidepost_content_text" type="text" id="hidepost_content_text" value="<?php echo the_view($hidepost_content_text); ?>" size="100" />
			</td>
		</tr>
		<tr>
			<th scope="row" valign="top">Protect Content with Role:  </th>
			<td>
				<label for="hidepost_role_text_hide"><input name="hidepost_role_text_hide" type="checkbox" id="hidepost_role_text_hide" value ="1" <?php echo is_checked($hidepost_role_text_hide); ?>/> Show blank spaces</label><br />
				<input name="hidepost_role_text" type="text" id="hidepost_role_text" value="<?php echo the_view($hidepost_role_text); ?>" size="100" />
			</td>
		</tr>
		<tr>
			<th scope="row" valign="top">Protect Link:  </th>
			<td>
				<label for="hidepost_link_text_hide"><input name="hidepost_link_text_hide" type="checkbox" id="hidepost_link_text_hide" value ="1" <?php echo is_checked($hidepost_link_text_hide); ?>/> Show blank spaces</label><br />
				<input name="hidepost_link_text" type="text" id="hidepost_link_text" value="<?php echo the_view($hidepost_link_text); ?>" size="100" />
			</td>
		</tr>
		<tr>
		<th scope="row" valign="top">Note:  </th>
			<td> %login% = Link to you Login page.<br />
				 %register% = Link to you Register page.<br />
				 %role% = Display user role requirement like Administrator or Registered.<br />
				 You can only use some simple HTML code in the hide text.<br />
			</td>
		</tr>
		<tr>
		<th scope="row" valign="top">UserLevel:  </th>
			<td>
				You can use [hidepost=level][/hidepost] where level = 0->9<br />
				0 = Registered Member<br />
				....<br />
				9 = Administrator<br />
				Example [hidepost=9][/hidepost] <br/>
				More help can be found on http://codex.wordpress.org/User_Levels<br />
			</td>
		</tr>
		</table>
		<span class="submit">
			<input name="submit" value="Save Changes" type="submit" />
		</span>
	</div>
</form>

<form name="hidepost_replace_tag" method="post" action="<?php echo $location ?>">
<input type="hidden" name="hidepost_replace_sure" />
 <div class="wrap">
  <h2>Replace old Tag with [hidepost]</h2>
  <table class="form-table">
    <tr>
		<th scope="row" align="top">Your old Tag<th>
        <input type="text" id="hidepost_old_tag" name ="hidepost_old_tag" value="<?php if (get_option('open_tag')) echo get_option('open_tag');?>"/>
       	</tr><tr>
		<th scope="row" align="top">Your old close Tag<th>
        <input type="text" id="hidepost_old_close_tag" name="hidepost_old_close_tag" value="<?php if (get_option('close_tag')) echo get_option('close_tag');?>" />
    </tr><tr>
		<th scope="row" align="top">New HidePost tag<th>
        <input type="text" id="hidepost_new_tag" name ="hidepost_new_tag" value=""/>
        <label for="hidepost_new_tag"><strong> Just input [hidepost] </strong></label>
    </tr><tr>
		<th scope="row" align="top">Check if you want to Replace<th>
        <input type="checkbox" name="hidepost_replace_tag_check" value="yes" />
        <label>&nbsp;Yes I want to replace</label>
    </tr>
	</table>
		<span class="submit">
			<input type="submit" name ="hidepost_replace_submit" value="Replace"/>
		</span>
    <p style="text-align: left; color: red">
		<strong>WARNING:</strong><br />
		You should backup your Wordpress database before using this.
    </p>
  </div>
</form>
<?php
}
?>

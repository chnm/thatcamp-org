<?php
// Avoid direct access to this piece of code
if (!function_exists('is_admin') || !is_admin()){
	header('Location: /');
	exit;
}
?>
<h3><?php _e('Support the author','subscribe-reloaded') ?></h3>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
	<input type="hidden" name="cmd" value="_s-xclick">
	<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----">
	<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
	<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>

<p><?php _e('How valuable is the feature offered by this plugin to your visitors? Subscribe to Comments Reloaded is and will always be free, but consider supporting the author if this plugin made your web site better, especially if you are making money out of it. Any donation received will be reinvested in the development of Subscribe to Comments Reloaded, and to buy some food for my hungry family.','subscribe-reloaded') ?></p>

<h3><?php _e("Don't want to donate? You can still help",'subscribe-reloaded') ?></h3>
<p><?php _e("If you don't want to donate money, please consider blogging about my plugin with a link to the plugin's page. Please let your readers know what makes your blog better. You can also contribute donating your time: do not hesitate to send me bug reports, your localization files, ideas on how to improve Subscribe to Comments Reloaded and so on. Whatever you do, thanks for using my plugin!",'subscribe-reloaded') ?></p>

<h3><?php _e("Vote and show your appreciation",'subscribe-reloaded') ?></h3>
<p><?php _e('Tell other people if Subscribe to Comments Reloaded works for you and how good it is. <a href="http://wordpress.org/extend/plugins/subscribe-to-comments-reloaded/">Rate it</a> on its Plugin Directory page.','subscribe-reloaded') ?></p>

<h3><?php _e("Sponsor's Corner",'subscribe-reloaded') ?></h3>
<p style="display:block"><?php _e("If you want to sponsor this plugin, don't hesitate to <a href='http://www.duechiacchiere.it/contatto'>contact me</a>.",'subscribe-reloaded') ?></p>
<p>
	<a href="https://www.e-junkie.com/ecom/gb.php?cl=136641&c=ib&aff=152344" target="ejejcsingle" title="ThemeFuse"><img src="http://themefuse.com/banners/125x125.jpg" alt="ThemeFuse" width="85" height="85"/></a>
	<a href="https://www.e-junkie.com/ecom/gb.php?cl=136641&c=ib&aff=152344" target="ejejcsingle" title="ThemeFuse"><img src="http://themefuse.com/wp-content/themes/themefuse/images/campaigns/themefuse.jpg" alt="ThemeFuse" width="375" height="85"/></a>
</p>
<?php 
function kreative_wp_head() {
	$kt =& get_instance();
	
	$jquery = $kt->config->item('jquery_source', 'optimize');
	
	if (in_array($jquery, array('cdn-google')))
	{
		?>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>	
		<?php 
	}
	else 
	{
		?>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js"></script>
		<?php	
	}
	
	if ($kt->config->item('enable_cufon', 'general') == 'true') : ?>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/cufon-yui.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/vegur_400-vegur_700.font.js"></script>
<script type="text/javascript">
<!--
jQuery(function() {
	Cufon.replace('h1.maintitle');
	Cufon.replace('h1#blogtitle');
});
-->
</script>
		
	<?php endif;
}

add_action('wp_head', 'kreative_wp_head');
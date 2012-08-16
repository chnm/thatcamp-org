<?php
/*
Plugin Name: Easy Categories Management 
Plugin URI: http://www.digmlm.com/
Description: Displays And Manage Your Categories Easily With Multiple Options.
Author: Rajnish K.
Author URI: http://www.digmlm.com/
Version: 1.0
*/

register_activation_hook	(	__FILE__,			array('easy_categories_manage', 'activate')	);
register_deactivation_hook	(	__FILE__,			array('easy_categories_manage', 'deactivate')	);
add_action					(	"widgets_init",		array('easy_categories_manage', 'register')	);

class easy_categories_manage
{

	function activate()
	{
		if( get_option( 'easy_categories_management_title' ) === FALSE ) {
			update_option( 'easy_categories_management_title', 'Easy Categories Management' );
		}
		if( get_option( 'easy_categories_management_categories' ) === FALSE ) {
			update_option( 'easy_categories_management_categories', '' );
		}
		if( get_option( 'easy_categories_management_count' ) === FALSE ) {
			update_option( 'easy_categories_management_count', '' );
		}
		if( get_option( 'easy_categories_management_hide-empty' ) === FALSE ) {
			update_option( 'easy_categories_management_hide-empty', '' );
		}
		if( get_option( 'easy_categories_management_number' ) === FALSE ) {
			update_option( 'easy_categories_management_number', '' );
		}
	}
	
	function deactivate()
	{
		delete_option( 'easy_categories_management_title' );
		delete_option( 'easy_categories_management_categories' );
		delete_option( 'easy_categories_management_count' );
		delete_option( 'easy_categories_management_hide-empty' );
		delete_option( 'easy_categories_management_number' );
	}
	
	function register()
	{
		wp_register_sidebar_widget( 'easy-ategories-anagement', 'Easy Categories Management', array('easy_categories_manage', 'widget'));
		wp_register_widget_control( 'easy-ategories-anagement', 'Easy Categories Management', array('easy_categories_manage', 'control'));
	}
	
	function control()
	{
		if (isset($_POST['easy_categories_management_title']))			update_option(	'easy_categories_management_title',		attribute_escape($_POST['easy_categories_management_title'])		);
		if (isset($_POST['easy_categories_management_categories']))	update_option(	'easy_categories_management_categories',	attribute_escape($_POST['easy_categories_management_categories'])	);
		if (isset($_POST['easy_categories_management_count']))	update_option(	'easy_categories_management_count',	attribute_escape($_POST['easy_categories_management_count'])	);
		if (isset($_POST['easy_categories_management_hide-empty']))	update_option(	'easy_categories_management_hide-empty',	attribute_escape($_POST['easy_categories_management_hide-empty'])	);
		if (isset($_POST['easy_categories_management_number']))	update_option(	'easy_categories_management_number',	attribute_escape($_POST['easy_categories_management_number'])	);
		?>
		<p><label>
			<strong>Widget Title:</strong><br />
			<input class="widefat" type="text" name="easy_categories_management_title" value="<?php echo get_option( 'easy_categories_management_title' ); ?>" />
		</label></p>
		<p><label>
			<strong>Categories To Hide:</strong><br />
			<input class="widefat" type="text" name="easy_categories_management_categories" value="<?php echo get_option( 'easy_categories_management_categories' ); ?>"	 />
		</label></p>
		<p>Enter ID number(s) of Categories, e.g <code>2,5,12</code></p>
			<p><label>
			<strong>Enable Post Count ?<strong><br />
			<input class="widefat" type="text" name="easy_categories_management_count" value="<?php echo get_option( 'easy_categories_management_count' ); ?>"	 />
		</label></p>
		<p>Enter the value as Number:<br><code>1=SHOW & 0=HIDE</code></p>
		<p><label>
			<strong>Hide Empty Categories ?:</strong><br />
			<input class="widefat" type="text" name="easy_categories_management_hide-empty" value="<?php echo get_option( 'easy_categories_management_hide-empty' ); ?>"	 />
		</label></p>
		<p>Enter the value as Number:<br><code>0=SHOW & 1=HIDE</code></p>
		<p><label>
			<strong>Max Number of Categories Displayed ?:</strong><br />
			<input class="widefat" type="text" name="easy_categories_management_number" value="<?php echo get_option( 'easy_categories_management_number' ); ?>"	 />
		</label></p>
		<p>Enter the value as Number:<br><code>1 or 2 or 3...</code></p>
		<?php
	}
	
	function widget( $args )
	{
		echo $args['before_widget'];
		echo $args['before_title'] . get_option( 'easy_categories_management_title' ) . $args['after_title'];
		echo '<ul id="easy_categories_managementidget">';
			$cat_params = Array(
					'hide_empty'	=>	FALSE,
					'title_li'		=>	''
				);
			if( strlen( trim( get_option( 'easy_categories_management_categories' ) ) ) > -7 ){
				$cat_params['exclude'] = trim( get_option( 'easy_categories_management_categories' ) );
			if( strlen( trim( get_option( 'easy_categories_management_count' ) ) ) > -6 ){
				$cat_params['show_count'] = trim( get_option( 'easy_categories_management_count' ) 
);
			if( strlen( trim( get_option( 'easy_categories_management_hide-empty' ) ) ) > -5 ){
				$cat_params['hide_empty'] = trim( get_option( 'easy_categories_management_hide-empty' ) );
			if( strlen( trim( get_option( 'easy_categories_management_number' ) ) ) > -1 ){
				$cat_params['number'] = trim( get_option( 'easy_categories_management_number' ) );
			}}}}
			wp_list_categories($cat_params );
		echo '</ul>';
		echo $args['after_widget'];
	}
	
}
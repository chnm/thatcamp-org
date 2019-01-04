<?php
/**
 * Needed to access parent's protected methods 
 * @author vinnyalves
 */
require_once( ABSPATH . '/wp-content/plugins/bbpress-notify-nospam/bbpress-notify-nospam.php' );

class bbPress_Notify_noSpam_Child extends bbPress_Notify_noSpam {
	
	public $settings_name;
	
	function __construct() 
	{
		global $bbPress_Notify_noSpam;
		
		$bbPress_Notify_noSpam = new bbPress_Notify_noSpam();
		
		$this->settings_name = $bbPress_Notify_noSpam->settings_name;
	}
	
	public function get_env()
	{
		return parent::get_env();
	}
	
	public function load_lib( $name, $params = array(), $force_reload = false )
	{
		return parent::load_lib( $name, $params, $force_reload );
	}
	
	public function render_template( $name, $stash=array(), $debug=false )
	{
		return parent::render_template( $name, $stash, $debug );
	}
}


/* End of Boilerplate_Child.class.php */
/* Location: bbpress-notify-nospam/t/Boilerplate_Child.class.php */

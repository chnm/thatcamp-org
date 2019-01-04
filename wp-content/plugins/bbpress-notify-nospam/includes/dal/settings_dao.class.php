<?php defined( 'ABSPATH' ) or die( "No direct access allowed" );
/**
 * Datalayer to plugin settings
 * @author vinnyalves
 */
class bbPress_Notify_noSpam_DAL_Settings_Dao extends bbPress_Notify_noSpam {
	
	/**
	 * Associative array where we store our cached Settings Model Object
	 * @var object
	 */
	private $cache = array();
	

	public function __construct() 
	{
		// NOOP - we don't want PHP to call the parent automatically
	}
	
	/**
	 * Loads our data from the database and returns a settings model
	 * @return BizxpressBI_Model_Settings
	 */
	public function load()
	{
		if ( ! isset( $this->cache[$this->settings_name] ) )
		{
			$this->load_lib( 'model/settings' );
			
			$db_params = get_option( $this->settings_name, array() );
			
			// Check that WP didn't return false for settings not found
			if ( false === $db_params )
				$db_params = array();
			
			$this->cache[$this->settings_name] = new bbPress_Notify_noSpam_Model_Settings( $db_params );
		}
		
		return $this->cache[$this->settings_name];
		
	}
	
	
	/**
	 * Ensure some basic settings
	 * @param array $_post
	 * @return string
	 */
	public function validate_settings( $_post )
	{
		$settings_model = $this->load_lib( 'model/settings' );

		foreach ( $_post as $key => $value )
		{
			if ( false === ( $_post[$key] = $settings_model->is_valid( $key, $value ) ) )
				unset( $_post[$key] );
		}

		return $_post;
	}
	
	
	/**
	 * This is really only used during testing. The API is used otherwise.
	 * @param bbPress_Notify_noSpam_Model_Settings $settings_model
	 */
	public function save( bbPress_Notify_noSpam_Model_Settings $settings_model )
	{
		update_option( $this->settings_name, $settings_model->as_array());
		
		$this->cache[$this->settings_name] = $settings_model;
	}
}


/* End of file settings_dao.class.php */
/* Location: bbpress-notify-nospam/includes/dal/settings_dao.class.php */

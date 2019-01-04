<?php defined('ABSPATH') or die("No direct access allowed");
/**
 * Controls ajax requests.
 * 
 * @author vinnyalves
 */
class bbPress_Notify_noSpam_Controller_Ajax extends bbPress_Notify_noSpam {

	private $ar;
	
	public function __construct()
	{
		if ( ! parent::is_admin() )
			return;
	
		$this->load_lib('model/ajax_request');
	
		add_action('wp_ajax_bbpnns_update_db', array( $this, 'update_db' ) );
	}
	
	
	/**
	 * Update the settings to version 2
	 */
	public function update_db( $message='', $callback=null )
	{
		$params = array(
				'message'  => &$message,
		);
		
		// _init creates the model and helps with testing
		$this->_init($params, 'POST', $callback);
		
		try { 
			
			$nonce = $params['nonce'];
			
			if ( ! wp_verify_nonce( $nonce, 'bbpnns_v2_conversion_needed' ) )
			{
				throw new Exception( __( 'Invalid nonce', 'bbPress_Notify_noSpam' ) );
			}
			
			$conv = $this->load_lib( 'helper/converter', array( 'add_action' => false ) );
			if ( ! $conv->do_db_upgrade() )
			{
				throw new Exception( __( 'There was a problem updating the database.', 'bbPress_Notify_noSpam' ) );
			}
			
			
			// Set the model values
			$this->ar->is_success = true;
			$this->ar->msg = __( '<strong>Database update completed successfully!</strong>', 'bbPress_Notify_noSpam' );
// 			$data = (object) array( 'some data' => true );
// 			$this->ar->data = $data;
		}
		catch (Exception $e)
		{
			// If there was an error, set it accordingly 
			$this->ar->is_success = false;
			$this->ar->msg = $e->getMessage();
// 			$this->ar->data = null;
		}

		// And print out the response
		return $this->_done();
	}
	
	
	
	
	
	/**
	 * Wrapper to check if we're in an ajax call
	 * @return boolean
	 */
	private function _doing_ajax()
	{
		return (defined('DOING_AJAX') && DOING_AJAX);
	}
	
	/**
	 * Wrapper to fetch query params
	 * @param array $vars
	 * @param string $method
	 * @param string $callback
	 */
	private function _init( &$vars=array(), $method='POST', &$callback=null)
	{
		$this->ar = new bbPress_Notify_noSpam_Model_Ajax_Request();
		$params   = array();
		
		if ( 'GET' === $method && isset( $_GET ) ) 
		{
			$params = $_GET;
		}
		elseif( 'POST' === $method && isset($_POST) ) 
		{
			$params = $_POST;
		}

		if ( isset($params) ) 
		{
			$vars = wp_parse_args( $vars, $params );
	
			if ( isset( $params['callback'] ) ) 
			{
				$callback = trim($params['callback']);
			}
		}
		
		$this->ar->callback = $callback;
	}
	
	
	/**
	 * Output or return Ajax Request model
	 */
	private function _done()
	{
		if ( $this->_doing_ajax() )
		{
			$this->ar->output();
			wp_die();
		}
		
		ob_start();
		$this->ar->output();
		return ob_get_clean();
	}
	
}

/* End of file ajax.class.php */
/* Location: bbpress-notify-nospam/includes/controller/ajax.class.php */

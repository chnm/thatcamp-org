<?php defined('ABSPATH') or die("No direct access allowed");
/**
 * Ajax Request model
 * @author vinnyalves
*/
class bbPress_Notify_noSpam_Model_Ajax_Request {

	/**
	 * The overall status
	 * @var array
	 */
	private $is_success = false;
	
	/**
	 * A localized message to the client
	 * @var string
	 */
	private $msg;
	
	/**
	 * Action-specific data to be used by the client
	 * @var mixed
	 */
	private $data = array();
	
	/**
	 * The callback for jsonp requests
	 * @var string
	 */
	private $callback;
	
	###########################

	/**
	 * Our constructor, loads properties
	 * @param array $params
	 */
	public function __construct($params=array())
	{
		$this->set_properties($params);
	}

	
	/**
	 * Our getter. Used mainly because we have to _validate() the setter
	 * and getter/setter magic methods only get called for private properties
	 * @param string $key
	 */
	public function __get( $key )
	{
		$key = strtolower( $key );

		return $this->{$key};
	}


	/**
	 * Our setter, takes care of validating input.
	 * @param string $key
	 * @param mixed $val
	 */
	public function __set( $key, $val )
	{
		$key = strtolower( $key );

		if ( property_exists( $this, $key ) )
		{
			$val = $this->_validate( $key, $val );
			$this->{$key} = $val;
		}
	}


	/**
	 * Bulk Property setter
	 * @param array $params
	 */
	protected function set_properties( $params=array() )
	{
		foreach ($params as $key => $val)
		{
			if (! property_exists( $this, strtolower( $key ) ) )
				continue;

			list( $key, $val ) = $this->_validate( $key, $val );

			$this->{$key} = $val;
		}

	}

	
	/**
	 * Setter validation
	 * @param string $key
	 * @param mixed $val
	 * @param boolean $die_on_error - whether to throw wp_die() or return false on errors
	 * @return Ambigous <mixed, string, boolean>
	 */
	private function _validate( $key, $val, $die_on_error=true )
	{
		global $bbPress_Notify_noSpam;
		
		
		switch( $key )
		{
			case 'is_success':
				if ( ! is_bool( $val ) )
				{
					return $die_on_error ? 
						   wp_die( __( sprintf( 'Ajax Request Model Property %s must be boolean', $key), $bbPress_Notify_noSpam->domain ) ) : 
						   false;
				}
			case 'callback': // extremely basic check. a real JS identifier check would be too big
				if ( false !== ( strpos( $val, '-' ) ) )
				{
					return $die_on_error ?
						   wp_die( __( sprintf( 'Ajax Request Model Property %s must be a valid JS identifier', $key), $bbPress_Notify_noSpam->domain ) ) :
						   false;
				}
				break;
			case 'msg':
				if ( ! is_string( $val ) && ! is_null( $val ) && ! is_array( $val ) )
				{
					return $die_on_error ?
						   wp_die( __( sprintf( 'Ajax Request Model Property %s must be an array, a string or null', $key), $bbPress_Notify_noSpam->domain ) ) :
						   false;
				}
				break;
			default:
				break;
		}
		
		return $val;
	}
	
	
	/**
	 * Checks callback property to decide whether this is jsonp or json
	 * @return string
	 */
	public function content_type()
	{
		if ( isset( $this->callback ) )
		{
			return 'Content-type: text/javascript';
		}
		else
		{
			return 'Content-type: application/json; charset=UTF-8';
		}
	}
	
	
	/**
	 * Echoes the correct structure
	 * @param string $skip_headers
	 */
	public function output( $skip_headers=false )
	{
		if ( false === $skip_headers )
		{
			header( $this->content_type() );
		}
	
		$out = array(
				'success' => $this->is_success,
				'msg'     => $this->msg,
				'data'    => $this->data
		);
	
		if ( isset( $this->callback ) )
		{
			// Prepend JS comment to address XSS vulnerability
			echo sprintf( '/**/%s(%s);', $this->callback, json_encode( $out ) );
		}
		else
		{
			echo json_encode( $out );
		}
	}
	
	
	/**
	 * Wrapper to check if we're in an ajax call
	 * @return boolean
	 */
	private function _doing_ajax()
	{
		return ( defined('DOING_AJAX') && DOING_AJAX );
	}
	
	
	/**
	 * Output or return Ajax Request model
	 */
	public function done()
	{
		ob_start();
		$this->output();
		$out = ob_get_clean();

		echo $out;
		
		if ( $this->_doing_ajax() )
			die();
		else 
			return $out;
	}

}

/* End of file ajax_request.class.php */
/* Location: bbpress-notify-nospam/includes/model/ajax_request.class.php */
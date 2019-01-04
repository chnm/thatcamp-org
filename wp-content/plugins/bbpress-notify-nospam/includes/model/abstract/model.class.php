<?php defined( 'ABSPATH' ) or die( "No direct access allowed" );
/**
 * Abstract class for models
 * @author vinnyalves
 */
abstract class bbPress_Notify_noSpam_Model_Abstract_Model extends bbPress_Notify_noSpam {
	
	/**
	 * Force children to have a validation method
	 * @param unknown $key
	 * @param unknown $val
	 * @param string $die_on_error
	 */
	abstract protected function validate( $key, $val, $die_on_error=true );

	/**
	 * Holds our properties. Gets set by child via register_properties()
	 * @var array
	 */
	protected $props;
	
	####################################
	
	/**
	 * Our getter. Used mainly because we have to validate the setter
	 * and getter/setter magic methods only get called for private properties
	 * @param string $key
	 */
	public function __get( $key )
	{
		return $this->props[$key];
	}
	
	
	/**
	 * Our setter, takes care of validating input.
	 * @param string $key
	 * @param mixed $val
	 */
	public function __set( $key, $val )
	{
		$this->is_registered( $key );
		
		$val = $this->validate( $key, $val );
	
		$this->props[$key] = $val;
	}
	
	
	/**
	 * Property setter
	 * @param array $params
	 */
	protected function set_properties( $params=array() )
	{
		if ( is_object( $params ) && get_class( $params ) === get_class( $this ) )
			$params = $params->as_array();
		
		foreach ( $params as $key => $val )
		{
			$this->is_registered( $key );
			
			$val = $this->validate( $key, $val ); // Child method
				
			$this->props[$key] = $val;
		}
	}
	
	
	/**
	 * Returns properties array. To be used when saving
	 */
	public function as_array()
	{
		$props = $this->props;
		unset( $props['props'], $props['domain'] );
		
		return $props;
	}
	
	
	/**
	 * Runs validation on a single key/value pair without dying
	 * @param string $key
	 * @param mixed $val
	 * @return bool
	 */
	public function is_valid( $key, $val )
	{
		if ( $this->is_registered( $key, false ) )
			return $this->validate( $key, $val, false );
		
		return false;
	}
	
	
	/** 
	 * Sets up the available properties of the child model
	 * @param array $defaults
	 */
	public function register_properties( $defaults=array() )
	{
		$this->props = $defaults; 
	}
	
	/**
	 * Checks that the key being set has been registered
	 * @param string $key
	 */
	private function is_registered( $key, $die_on_error=true )
	{
		// Check that the property is valid
		if ( ! array_key_exists( $key, $this->props ) )
		{
			if ( true === $die_on_error )
				wp_die( __( sprintf( 'Invalid property %s for %s', $key, get_class( $this ) ), 'bbPress_Notify_noSpam' )  );
			
			return false;
		}
		
		return true;
	}
	
}
<?php defined( 'ABSPATH' ) or die( "No direct access allowed" );
/**
 * DAO for USC Bbpnns Addon products
 * 
 * @author vinnyalves
 */
class bbPress_Notify_noSpam_DAL_AddOns_DAO extends bbPress_Notify_noSpam {

	private $endpoint = 'https://usestrict.net/wp-json/wc/v2/products?category=455&status=publish';
	private $key      = 'ck_9956e8a832d177f407d3fd5eab0036c84c063a5a';
	private $secret   = 'cs_72c7a1063f3e4982a2c155ac381023a5c2fbad3c'; 
	
	private $transient = 'usc_bbpnns_addons';
	
	public function __construct()
	{

	}
	
	/**
	 * Fetch bbpnns addon info from https://usestrict.net
	 * @param string $force_reload
	 */
	public function get_products( $force_reload=false ) 
	{
		if ( true === apply_filters( 'bbpnns_force_addon_reload', $force_reload )            || 
		    ( defined( 'BBPNNS_FORCE_ADDON_RELOAD' ) && true === BBPNNS_FORCE_ADDON_RELOAD ) || 
			! get_transient( $this->transient ) ) 
		{
			$url = sprintf("%s&consumer_key=%s&consumer_secret=%s", $this->endpoint, $this->key, $this->secret);
			$response = wp_remote_get($url);
			
			$out = false;
			if ( ! is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response ))
			{
				$products = json_decode( wp_remote_retrieve_body( $response ), true );
				$keys = array( 'name'                 => 'name',              'permalink'            => 'permalink', 
						       'short_description'    => 'short_description', 'usc_plugin_signature' => 'signature', 
						       '_api_new_version'     => 'version',           '_api_tested_up_to'    => 'tested_up_to', 
						       '_api_last_updated'    => 'last_updated',      'slug'                 => 'slug',
						       'usc_recommended_for'  => 'usc_recommended_for', 
				 );
				
				$col_search = array( 'usc_recommended_for' => true, 'usc_plugin_signature' => true, '_api_new_version' => true, '_api_tested_up_to' => true, '_api_last_updated' => true );

				foreach ( $products as $p ) 
				{
					$addon = (object) array();
					foreach ( $keys as $key => $prop )
					{
						$pos   = '';
						$value = $p[$key];
						
						if ( isset( $col_search[$key] ) )
						{
							if ( $pos = array_search( $key, array_column( $p['meta_data'], 'key' )) )
							{
								$value = $p['meta_data'][$pos]['value'];
							}
							else 
							{
								$value = null;	
							}
						}
						
						$addon->{$prop} = $value;
					}
					
					if ( ! empty( $p['images'] ) ) {
						$addon->image = $p['images'][0]['src'];
					}
	
					$addon = $this->_local_plugin_meta( $addon );
					
					$out[] = $addon;
				}
				
				usort( $out, array( $this, 'sort_by_recommended' ) );
				
				set_transient( $this->transient, $out, 60 * 60 * 24 * 3 ); // 3 days
			}
			else 
			{
				$this->log_msg( 'There was a problem fetching products from USC: ' . ( is_wp_error($response) ? $response->getMessage() : print_r($response,1) ) );
			}
		}
		
		return get_transient( $this->transient );
	}
	
	/**
	 * Custom sorting function for the addons, keeping the recommended ones at the top.
	 * @param object $a
	 * @param object $b
	 * @return number
	 */
	public function sort_by_recommended($a, $b)
	{
		$ar = isset( $a->recommended ) ? $a->recommended : false;
		$br = isset( $b->recommended ) ? $b->recommended : false;
		
		if ( $ar === $br )
		{
			return 0;
		}
		
		return ( (int) $ar > (int) $br ) ? -1 : 1;
	}
	
	
	/**
	 * Load the local plugin information
	 * @param object $addon
	 */
	private function _local_plugin_meta( $addon )
	{
		static $active_plugins = null;
		static $all_plugins    = null;
		
		if ( null === $active_plugins )
		{
			if ( ! function_exists( 'get_plugins' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			
			$active_plugins = array_flip( get_option( 'active_plugins', array() ) );
			$all_plugins    = get_plugins();
		}
		
		$local  = isset( $all_plugins[ $addon->signature ] ) ? $all_plugins[ $addon->signature ] : array();
		$active = isset( $active_plugins[ $addon->signature ] );

		$addon->is_active    = $active;
		$addon->is_installed = !empty( $local );
		$addon->local        = (object) $local; 
		$addon->update_available = ( $addon->is_installed && version_compare( $addon->local->Version, $addon->version, "<" ) );

		if ( isset( $addon->usc_recommended_for ) && isset( $active_plugins[$addon->usc_recommended_for] ) )
		{
			$addon->recommended = true;
		}
		
		$license = apply_filters( $addon->local->TextDomain . '_license_instance', null );
		
		if ( $license ) 
		{
			$addon->local->license_page = admin_url( 'options-general.php?page=' . $license->ame_activation_tab_key ) ;	
		}

		return $addon;
	}
}

/* End of file addons_dao.class.php */
/* Location: bbpress-notify-nospam/includes/helper/addons_dao.class.php */

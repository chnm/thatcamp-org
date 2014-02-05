<?php
if (! defined('AVH_FRAMEWORK'))
	die('You are not allowed to call this page directly.');
if (! class_exists('AVH_DB')) {

	final class AVH_DB
	{

		/**
		 * Fetch MySQL Field Names
		 *
		 * @access	public
		 * @param	string	the table name
		 * @return	array
		 */
		public function getFieldNames ($table = '')
		{
			global $wpdb;

			$retval = wp_cache_get('field_names_' . $table, 'avhec');
			if (false === $retval) {
				$sql = $this->_getQueryShowColumns($table);

				$result = $wpdb->get_results($sql, ARRAY_A);

				$retval = array ();
				foreach ($result as $row) {
					if (isset($row['Field'])) {
						$retval[] = $row['Field'];
					}
				}
				wp_cache_set('field_names_' . $table, $retval,'avhec',3600);
			}

			return $retval;
		}

		/**
		 * Determine if a particular field exists
		 * @access	public
		 * @param	string
		 * @param	string
		 * @return	boolean
		 */
		public function field_exists ($field_name, $table_name)
		{
			return (in_array($field_name, $this->getFieldNames($table_name)));
		}

		/**
		 * Show column query
		 *
		 * Generates a platform-specific query string so that the column names can be fetched
		 *
		 * @access	public
		 * @param	string	the table name
		 * @return	string
		 */
		private function _getQueryShowColumns ($table = '')
		{
			global $wpdb;
			return 'SHOW COLUMNS FROM '.$table;
		}
	}
}


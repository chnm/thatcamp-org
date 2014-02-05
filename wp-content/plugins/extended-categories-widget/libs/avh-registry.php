<?php
if (! defined('AVH_FRAMEWORK'))
	die('You are not allowed to call this page directly.');
if (! class_exists('AVH_Settings_Registry')) {

	abstract class AVH_Settings_Registry
	{
		/**
		 * Our array of settings
		 * @access protected
		 */
		private $_settings = array ();

		/**
		 * Stores settings in the registry
		 * @param string $data
		 * @param string $key The key for the array
		 * @return void
		 */
		public function storeSetting ($key, $data)
		{
			$this->_settings[$key] = $data;
			$this->$key = $data;
		}

		/**
		 * Gets a setting from the registry
		 * @param string $key The key in the array
		 * @return mixed
		 */
		public function getSetting ($key)
		{
			return $this->_settings[$key];
		}

		/**
		 * Removes a setting from the registry
		 * @param string $key The key for the array
		 */
		public function removeSetting ($key)
		{
			unset($this->_settings[$key]);
		}
	}
}
if (! class_exists('AVH_Class_Registry')) {

	/**
	 * Class registry
	 *
	 */
	abstract class AVH_Class_Registry
	{
		/**
		 * Our array of objects
		 * @access protected
		 * @var array
		 */
		private $_objects = array ();
		private $_dir;
		private $_class_file_prefix;
		private $_class_name_prefix;

		/**
		 * Loads a class
		 *
		 * @param string $class Name of the class you want to load
		 * @param string $type What kind of class, System, Plugin
		 * @param boolean $store Store the class in the registry
		 * @return object
		 */
		public function load_class ($class, $type = 'system', $store = false)
		{
			if (isset($this->_objects[$class])) {
				return ($this->_objects[$class]);
			}
			switch ($type) {
				case 'plugin':
					$in = '/class';
					$file = $this->_class_file_prefix . $class . '.php';
					break;
				case 'system':
				default:
					$in = '/libs';
					$file = 'avh-' . $class . '.php';
			}
			require_once ($this->_dir . $in . '/' . strtolower($file));
			$name = ('system' == $type) ? 'AVH_' . $class : $this->_class_name_prefix . $class;
			$object = $this->instantiate_class(new $name());
			if ($store) {
				$this->_objects[$class] = $object;
			}
			return $object;
		}

		/**
		 * Instantiate Class
		 *
		 * Returns a new class object by reference, used by load_class() and the DB class.
		 * Required to make PHP 5.3 cry.
		 *
		 * Use: $obj =& instantiate_class(new Foo());
		 *
		 * @access	public
		 * @param	object
		 * @return	object
		 */
		protected function instantiate_class (&$class_object)
		{
			return $class_object;
		}

		/**
		 * @param $dir the $dir to set
		 */
		public function setDir ($dir)
		{
			$this->_dir = $dir;
		}

		/**
		 * @param $class Unique Identifier
		 * @param $class_prefix the $class_prefix to set
		 */
		public function setClassFilePrefix ($class_prefix)
		{
			$this->_class_file_prefix = $class_prefix;
		}

		/**
		 * @param $class Unique Identifier
		 * @param $class_name_prefix the $class_name_prefix to set
		 */
		public function setClassNamePrefix ($class_name_prefix)
		{
			$this->_class_name_prefix = $class_name_prefix;
		}

		public function setClassProperties ($properties)
		{
			$default_properties = array ( 'type' => 'system', 'store' => false );
		}
	}
}
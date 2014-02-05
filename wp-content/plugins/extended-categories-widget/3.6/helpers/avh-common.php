<?php
if ( !function_exists('avhGetArrayValue') ) {

	/**
	 * Get the value of $array[$name]
	 *
	 * @param array $array
	 * @param string $name
	 *
	 * @return mixed An empty string when the $array[$name] does not exists
	 */
	function avhGetArrayValue ($array, $name)
	{
		if ( isset($array[$name]) )
			return $array[$name];

		return '';
	}
}

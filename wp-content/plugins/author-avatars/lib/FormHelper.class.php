<?php  if(!class_exists('FormHelper')):

/*
Copyright (c) 2009 Benedikt Forchhammer

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

/**
 * This class provides a collection of functions which ease the generation of form elements.
 */
class FormHelper {

	/**
	 * Renders a set of choices/values inside a select element or a group of checkboxes/radio buttons.
	 *
	 * The $attributes parameter accepts the following values:
	 * - "multiple": If set to true the element allows the user to select multiple values (default: false)
	 * - "expanded": If set to true the choices are rendered as a set of radio buttons or checkboxes (if "multiple" is set to true). (default: false)
	 * - "wrapper_tag": A html tag which is used to wrap expanded fields into a container, the default value is "div".
	 * - "id": Set this value to override the id which by default is generated from the $name parameter.
	 * - "label": Set to display a label above the field. This is automatically wrapped into "label" tags for non-expanded fields.
	 * - "help": Set to display a (help) text underneith the field.
	 * - "inline": Set to true to display all choices "inline" without line breaks in-between. (default: false)
	 * - anything else is simply used rendered as an html attribute on the element (or wrapper span if expanded=true). You can e.g. specify "class" to add a css class name.
	 *
	 * Which combination of expanded and multiple is rendered in which field?
	 * - expanded and multiple: checkboxes (input with type checkbox)
	 * - expanded but not multiple: radio buttons (input with type radio)
	 * - not expanded but multiple: multi-select (select with multiple=true)
	 * - not expanded and not multiple: dropdown (plain normal select)
	 *
	 * @static
	 * @access public
	 * @param string $name The name of the form element.
	 * @param array $choices The array of choices. The array keys are used as form element values, the array values as labels.
	 * @param mixed $values The selected value of the element. Can be a string or an array of values.
	 * @param array $attributes Array of (html) attributes.
	 * @return string Html rendered form element.
	 */
	function choice($name, $choices, $values, $attributes=array()) {
		// make sure we have an array of choices
		if (!is_array($choices) || empty($choices)) {
			return __("Sorry, no choices available.");
		}

		// make sure we have all values in an array.
		if (!is_array($values)) $values = array($values);

		// render in expanded mode?
		$expanded = isset($attributes['expanded']) && $attributes['expanded'] == true;

		// multiple value selection allowed?
		$multiple = isset($attributes['multiple']) && $attributes['multiple'] == true;

		// render fields inline?
		$fields_inline = isset($attributes['inline']) && $attributes['inline'] == true;

		// label or help text?
		$field_label = !empty($attributes['label']) ? $attributes['label'] : false;
		$field_help = !empty($attributes['help']) ? $attributes['help'] : false;
		$wrapper_tag = !empty($attributes['wrapper_tag']) ? $attributes['wrapper_tag'] : 'div';

		// set up variables for tags and attributes.
		$wrapper_attributes = array();
		$row_attributes = array();

		// id = either from $attributes['id'] or safe version of $name
		$wrapper_attributes['id'] = !empty($attributes['id']) ? $attributes['id'] : $name;
		$wrapper_attributes['id'] = FormHelper::cleanHtmlId($wrapper_attributes['id']);

    if ($multiple) {
			$name .= '[]';
		}

		if ($expanded) {
			$row_attributes['name'] = $name;

			if ($multiple || count($choices) == 1) {
				$row_attributes['type'] = 'checkbox';
				$row_attributes['class'] = empty($row_attributes['class']) ? 'checkbox' : ' checkbox';
			}
			else {
				$row_attributes['type'] = 'radio';
				$row_attributes['class'] = empty($row_attributes['class']) ? 'radio' : ' radio';
			}
		}
		else {
			$wrapper_attributes['name'] = $name;

			if ($multiple) {
				$wrapper_attributes['multiple'] = 'multiple';
				$_size = count($choices);
				$wrapper_attributes['size'] = $_size <= 5 ? '5' : ($_size > 10 ? '10' : $_size);
				unset($_size);

				// reset height, which is set to 2em on wordpress 2.6 default admin theme.
				if (empty($wrapper_attributes['style'])) $wrapper_attributes['style'] = '';
				$wrapper_attributes['style'] .= 'height: auto;';
			}
		}

		// filter out values which we set ourselves
		unset($attributes['name'], $attributes['id'], $attributes['multiple'], $attributes['expanded'], $attributes['label'], $attributes['help'], $attributes['wrapper_tag'], $attributes['inline']);
		// add remaining $attributes values onto $wrapper_attributes
		$wrapper_attributes = array_merge($attributes, $wrapper_attributes);

		// return value
		$html = '';

		if (!$expanded && $field_label) $html .= '<label>'. $field_label;
		$html .= '<' . ($expanded ? $wrapper_tag : 'select') . FormHelper::buildHtmlAttributes($wrapper_attributes) . '>';
		if ($expanded && $field_label) $html .= $field_label;

		foreach ($choices as $value => $label) {
			$attr = FormHelper::buildHtmlAttributes($row_attributes);
			$attr .= ' value="'. $value .'"';

			$row = '';
			if ($expanded) {
				$row = '<label><input';
				if (in_array($value, $values)) $row .= ' checked="checked"';
				$row .= $attr;
				$row .= '/> ';
				$row .= $label;
				$row .= '</label>';
				if (!$fields_inline) $row .= '<br/>';
			}
			else {
				$row = '<option';
				$row .= $attr;
				if (in_array($value, $values)) $row .= ' selected="selected"';
				$row .= '>';
				$row .= $label;
				$row .= '</option>';
			}

			$html .= $row;
		}

		if ($expanded && $field_help) $html .= $field_help;

		$html .= '</' . ($expanded ? $wrapper_tag : 'select') . '>';

		if (!$expanded && $field_label) $html .= '</label>';
		if (!$expanded && $field_help) $html .= $field_help;

		return $html;
	}

	/**
	 * Renders a form input field
	 *
	 * The attributes parameter accepts any html properties plus the following:
	 * - "label": set this value to wrap the input field into a label with the given value in front of the input field.
	 * - "help": Set to display a (help) text underneith the field.
	 *
	 * @static
	 * @access public
	 * @param string $type The type of the input field. (E.g. "text" or "hidden").
	 * @param string $name The name of the input field.
	 * @param string $value The value of the input field.
	 * @param array $attributes Array of (html) attributes.
	 * @return string Html rendered form element.
	 */
	function input($type, $name, $value, $attributes=array()) {
		$valid_types = array('text', 'hidden', 'password', 'submit', 'reset');
		if (!in_array($type, $valid_types)) return '[Only types "'.join('", "', $valid_types).'" are allowed in FormHelper::input()]';

		if ($type == 'text' && isset($attributes['rows']) && intval($attributes['rows']) > 0) {
			$tag = 'textarea';
			$attributes['rows'] = intval($attributes['rows']);
		}
		else {
			$tag = 'input';
			$attributes['type'] = $type;
			$attributes['value'] = $value;
		}

		$attributes['id'] = !empty($attributes['id']) ? $attributes['id'] : $name;
		$attributes['id'] = FormHelper::cleanHtmlId($attributes['id']);
		$attributes['name'] = $name;

		$label = '';
		if (isset($attributes['label'])) {
			$label = $attributes['label'] .' ';
			unset($attributes['label']);
		}

		$help = '';
		if (isset($attributes['help'])) {
			$help = $attributes['help'] .' ';
			unset($attributes['help']);
		}

		$attr = FormHelper::buildHtmlAttributes($attributes);

		$html = '<'.$tag.$attr;
		if ($tag == 'textarea') {
			$html .= '>';
			$html .= htmlspecialchars($value);
			$html .= '</'.$tag.'>';
		}
		else {
			$html .= ' />';
		}
		if (!empty($label)) $html = '<label>'.$label.$html.'</label>';
		if (!empty($help)) $html .= $help;
		return $html;
	}

	/**
	 * Builds a string of html attributes from an associative array.
	 *
	 * Example:
	 * Array('title' => 'My title'); will be transformed into this string: [ title="My title"]
	 *
	 * All attribute values are cleaned up using the function esc_html().
	 *
	 * @static
	 * @access public
	 * @param $attributes Array of attributes
	 * @return string
	 */
	function buildHtmlAttributes($attributes) {
		if (!is_array($attributes) || empty($attributes)) return '';

		$string = '';
		foreach ($attributes as $key => $value) {
			$string .= ' '. $key . '="'. esc_html($value) .'"';
		}
		return $string;
	}

	/**
	 * Transforms any string into a valid html id by replacing non-alphanumeric characters with dashes.
	 *
	 * @static
	 * @access public
	 * @param $id string id
	 * @return string
	 */
	function cleanHtmlId($id) {
		return strtolower(trim(str_replace('--', '-', preg_replace('/[\W]/', '-', $id)), "- \t\n\r\0\x0B"));
	}
}


endif; ?>
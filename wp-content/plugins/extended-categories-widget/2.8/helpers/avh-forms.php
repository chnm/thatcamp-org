<?php
if (! function_exists('avh_doWidgetFormText')) {

	function avh_doWidgetFormText ($field_id, $field_name, $description, $value)
	{
		echo '<label for="' . $field_id . '">';
		echo $description;
		echo '<input class="widefat" id="' . $field_id . '" name="' . $field_name . '" type="text" value="' . esc_attr($value) . '" /> ';
		echo '</label>';
		echo '<br />';
	}
}

if (! function_exists('avh_doWidgetFormCheckbox')) {

	function avh_doWidgetFormCheckbox ($field_id, $field_name, $description, $is_checked = FALSE)
	{
		
		echo '<label for="' . $field_id . '">';
		echo '<input class="checkbox" type="checkbox" id="' . $field_id . '"	name="' . $field_name . '"' . ($is_checked ? ' CHECKED' : '') . ' /> ';
		echo $description;
		echo '</label>';
		echo '<br />';
	}
}

if (! function_exists('avh_doWidgetFormSelect')) {

	function avh_doWidgetFormSelect ($field_id, $field_name, $description, $options, $selected_value)
	{
		echo '<label for="' . $field_id . '">';
		echo $description . ' ';
		echo '</label>';
		
		$data = '';
		foreach ($options as $value => $text) {
			$data .= '<option value="' . $value . '" ' . ($value == $selected_value ? "SELECTED" : '') . '>' . $text . '</option>' . "/n";
		}
		echo '<select id="' . $field_id . '" name="' . $field_name . '"> ';
		echo $data;
		echo '</select>';
		echo '<br />';
	}
}

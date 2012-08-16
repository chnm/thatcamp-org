<?php

function kreative_form_submission($group = '', $options)
{
	$kt =& get_instance();
	$submit = $_POST['form_action'];
	$data = array ();
	
	if ((( !! isset($submit) && is_array($submit)) && trim($group) !== '') && is_array($options)) :
		$result = array_search($group, $submit);
		
		if ($result > -1) :
		
			foreach ($options as $value) :
				if ($value['type'] === 'checkbox') :
					$checkbox = $_POST[$group . '_' . $value['id']];
					
					$data[$value['id']] = (( !! isset($checkbox) && $checkbox === 'true') ? 'true' : 'false');
				else :
					$data[$value['id']] = $_POST[$group . '_' . $value['id']];
				endif;
			endforeach;
			
			update_option('kreativetheme_' . $group, serialize($data));
			$kt->config->set($group, $data);
			
			echo '<div id="message" class="updated fade"><p><strong>'.$kt->config->item('themename').' settings saved.</strong></p></div>';
		endif;
	endif;
	
	
}
function kreative_form_builder($group = 'defaults', $options = array ())
{
	kreative_form_submission($group, $options);
	
	$pre = $group . '_';
	
	echo '<input type="hidden" name="form_action[]" value="' . $group . '" />';
	echo '<table class="kt_table">';
	
	foreach ($options as $value) :
		
		if (in_array($value['type'], array('heading'))) 
		{
			echo '<tr class="kt_col">';
			echo '<td class="kt_row_desc" colspan="2"><h4>' . $value['name'] . '</h4></td>';
			echo '</tr>';
		}
		elseif ( ! in_array($value['type'], array('hidden'))) 
		{
			echo '<tr class="kt_col">';
			echo '<td class="kt_row_desc">' . $value['name'] . '</td>';
			echo '<td class="kt_row_field">';
		}
		
		
		
		switch ($value['type']) {
			case 'textarea' :
				echo '<textarea name="' . $pre . $value['id'] . '" id="' . $pre . $value['id'] . '" class="' . $value['class'] . '" rows="8">' . kreative_get_settings($group, $value['id'], $value['standard']) . '</textarea>';
				break;
			case 'select_wpcat' :
				$select_value = kreative_get_settings($group, $value['id'], $value['standard']);
				
				$args = array (
					'hide_empty' => 0,
					'hierarchical' => 1,
					'show_option_none' => 'No category'
				);
				
				$args = array_merge($args, $value['args']);
				
				$args['selected'] = $select_value;
				$args['class'] = $value['class'];
				$args['name'] = $pre . $value['id'];
				
				wp_dropdown_categories($args);
				break;
				
			case 'select' :
				echo '<select name="' . $pre . $value['id'] . '" id="' . $pre . $value['id'] . '" class="' . $value['class'] . '">';
				
				$select_value = kreative_get_settings($group, $value['id'], $value['standard']);
				
				foreach ($value['options'] as $key => $val) :
					echo '<option value="'. $key . '" ' . ($select_value == $key ? 'selected="selected"' : '') . '>' . $val . '</option>';
				endforeach;
				
				echo '</select>';
				break;
			
			case 'radio' :
				$radio_value = kreative_get_settings($group, $value['id'], $value['standard']);
				
				$i = 0;
				foreach ($value['options'] as $key => $val) :
					if ($i > 0) :
						echo '<br />';
					endif;
					echo '<input type="radio" name="' . $pre . $value['id'] . '" id="' . $pre . $value['id'] . '_' . $key .'" value="'. $key . '" ' . ($radio_value == $key ? 'checked="checked"' : '') . ' />';
					echo ' <label for="' . $pre . $value['id'] . '_' . $key .'">' . $val . '</label>';
					
					$i++;
				endforeach;
				break;
				
			case 'checkbox' :
				$checkbox_value = kreative_get_settings($group, $value['id'], $value['standard']);
				$checked = '';
				if ($checkbox_value == 'true') :
					$checked = 'checked="checked"';
				endif;
				
				echo '<input type="checkbox" class="checkbox" name="' . $pre . $value['id'] . '" id="' . $pre . $value['id'] . '" value="true" ' . $checked . ' />';
				echo ' <label for="' . $pre . $value['id'] . '">' . $value['desc'] . '</label>';
				break;
				
			case 'heading' :
				echo '';
				break;
				
			default :
				echo '<input name="' . $pre . $value['id'] . '" id="' .$pre . $value['id'] . '" type="' . $value['type'] .'" value="'. kreative_get_settings($group, $value['id'], $value['standard']) . '" class=" '. $value['class'] .'" />';
				break;
		}
		
		if ( ! in_array($value['type'], array('hidden', 'heading'))) 
		{
			if ( ! in_array($value['type'], array('checkbox'))) 
			{
				echo '<br />';
				echo '<em>' . $value['desc'] . '</em>';
			}
			echo '</td></tr>';
		}
		
	endforeach;
	
	echo '</table>';
}
<?php
/*
Template Name: Camper Report
*/
?>

<?php 

if($current_user->ID == 2 || $current_user->ID == 4):
?>
<style type="text/css" media="screen">
	th {text-align:left;}
	td {border-bottom:1px dotted #ccc;padding: 4px 0;}
</style>
<div id="content" class="campers">
	<h1>Campers</h1>
	<table>
		<thead>
			<tr>
				<th>No.</th>
				<th>Name</th>
				<th>T-Shirt Size</th>
				<th>Dietary Preferences</th>
			</tr>
		</thead>
		<tbody>
<?php 
$campers = cw_get_authors();
foreach($campers as $key => $camper):

	$camper_info = get_userdata($camper->ID);
	
	// Used to generate ID for vcard
	$firstname = strtolower($camper_info->first_name);
	$lastname = strtolower($camper_info->last_name);
 ?>
	<tr>
		<td width="10%"><?php echo $key + 1; ?>
		<td width="20%"><?php echo $camper_info->first_name .' '.$camper_info->last_name; ?></td>
		<td width="20%"><?php echo $camper_info->tshirt_size; ?>&nbsp;</td>
		<td width="50%"><?php echo $camper_info->dietary_preferences; ?>&nbsp;</td>
	</tr>
<?php endforeach;?>
</tbody>
</table>
<?php else: ?>
	<p>Naughty, naughty! Can't see this!</p>
<?php endif; ?>
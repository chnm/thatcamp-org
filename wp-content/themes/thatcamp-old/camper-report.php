<?php
/*
Template Name: Camper Report
*/
?>

<?php 

if($current_user->ID == 2 || $current_user->ID == 7 || $current_user->ID == 11):
?>
<style type="text/css" media="screen">
	th {text-align:left;}
	td {border-bottom:1px dotted #ccc;padding: 4px 0;text-align:top;}
</style>
<div id="content" class="campers">
	<h1>Campers</h1>
	<table>
		<thead>
			<tr>
			    <th>No.</th>
				<th>Name</th>
				<th>Email</th>
				<th>T-Shirt</th>
			</tr>
		</thead>
		<tbody>
<?php 

$campers = cw_get_authors();
$count = 1;
foreach($campers as $key => $camper):

	$camper_info = get_userdata($camper->ID);
?>
	<tr>
	    <td width="2%"><?php echo $count; ?></td>
		<td width="20%"><?php echo $camper_info->last_name .', '.$camper_info->first_name; ?></td>
		<td width="20%"><?php echo $camper_info->user_email; ?></td>
		<td width="20%"><?php echo $camper_info->tshirt_size; ?></td>
		
	</tr>
	<?php $count++; ?>
<?php endforeach;?>
</tbody>
</table>
<?php else: ?>
	<p>Naughty, naughty! Can't see this!</p>
<?php endif; ?>
<?php
/*
Template Name: Camper Badges
*/
?>

<?php 

?>
<style type="text/css" media="all">
* {margin:0; padding:0;}
body {font-family:"Whitney", sans-serif; width: 8.25in; margin: 0 auto;}
.camper {width: 3.5in; height:1.75in; float:left; position:relative; padding: 1.25in 0.25in 0;margin:0 1px 1px 0;}
.camper .logo {position:absolute; top:0; left:0;background:#38383c; width: 3.85in; height:0.85in; padding-left:0.15in;}
.camper .logo img {margin-top:1px;}
.camper .icon img {float:left; width: 50px; height:50px; margin-right: 16px; margin-bottom: 40px;}
.name {font-family:"Whitney"; font-variant:small-caps; font-weight:bold; letter-spacing:2px;
font-weight:semibold; font-size: 2em; margin-left:0.25in; margin-right:0.25in;}
.institution {color: #777; margin-left:0.25in; margin-right:0.25in; font-size:80%;}
.event {position:absolute; bottom:0; left:0; width: 3.5in;background-color: #F6DC0F !important; color: #333; padding: 0.25in;}
</style>
<?php 
$campers = cw_get_authors();
foreach($campers as $key => $camper):

	$camper_info = get_userdata($camper->ID);
	
	// Used to generate ID for vcard
	$firstname = strtolower($camper_info->first_name);
	$lastname = strtolower($camper_info->last_name);
 ?>
	<div class="camper">
        <div class="logo"><img src="/ui/i/thatcamp-logo-small.png" /></div>
		<p class="name"><?php echo $camper_info->first_name .' '.$camper_info->last_name; ?></p>
		<p class="institution"><?php echo $camper_info->institution; ?>&nbsp;</p>
		<p class="event">The Humanities and Technology Camp</p>
	</div>
<?php endforeach;?>
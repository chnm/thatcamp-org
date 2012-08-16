<?php
/*
Template Name: Posts List
*/

$the_query = new WP_Query('showposts=200&orderby=post_date&order=desc'); ?>
<table width="100%">
<?php $num=0; ?>
	<?php while ($the_query->have_posts()) : $the_query->the_post();

	$do_not_duplicate = $post->ID; ?>

    <tr>
        <td><?php echo ++$num; ?>
	<td><?php the_title(); ?></td>
    <td><?php the_author(); ?></td>
			</tr>
    		
  <!-- end latest_post -->
	
	<?php endwhile; ?>
</table>

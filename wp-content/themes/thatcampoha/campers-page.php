<?php
/**
 * Template Name: Campers
 *
 * A custom page template without sidebar.
 *
 * The "Template Name:" bit above allows this to be selectable
 * from a dropdown menu on the edit page screen.
 *
 * @package WordPress
 * @subpackage Boilerplate
 * @since Boilerplate 1.0
 */

get_header(); ?>

		<h1 class="catheader"><?php the_title(); ?></h1>


<?php

// Get the authors from the database ordered by user nicename

	$author_ids = get_users('orderby=display_name');

// Loop through each author
	foreach($author_ids as $author) :

	// Get user data
		$curauth = get_userdata($author->ID);

	// If user level is above 0 
		if ( $curauth->user_level > 0 ):

		// Get link to author page
			$user_link = get_author_posts_url($curauth->ID);

		// Set default avatar (values = default, wavatar, identicon, monsterid)
			$avatar = 'default';
?>

<div id="entry-author-info">
	
	<!-- avatar-->
	<a href="<?php echo $user_link; ?>" title="<?php echo $curauth->display_name; ?>">
		<?php echo get_avatar($curauth->user_email, '60', $avatar); ?>
	</a>


	<!-- name-->
	<h2 id="author-meta-name"><?php echo $curauth->display_name; ?></h2>
	<span id="author-meta-description">
	
	<!-- twitter -->
 	 <?php if ($curauth->user_twitter != null){
 	 	echo 'Twitter: <a href="http://twitter.com/'.$curauth->user_twitter.'">@'.$curauth->user_twitter.'</a><br>';
 	 	}
 	 ?>

	<!-- website -->
 	 <?php 
 	 $website=$curauth->user_url;
 	 $siteroot=parse_url($website);
 	 if ( $website != null){
 	 	echo 'Website: <a href="'.$website.'">'.$siteroot[host].'</a>';
 	 	}
 	 ?>	 	
 	 
 	 <!-- bio -->
 	 <?php
 	 $bio=$curauth->description;
 	 if ( $bio != null){
 	 	echo '<span class="bio">'.$bio.'</span>';
 	 }
 	 ?> 
 	 </span>
 	  	 		
	<!-- profile link -->
	<?php 
	$post_count=count_user_posts( $curauth->ID );
	if($post_count >0){?>
	<a href="<?php echo $user_link;?>" id="author-meta-link">Posts by <?php echo $curauth->display_name;?> <?php echo '('. $post_count .')';?> &rarr;</a>
	<?php }?>



</div>

		<?php endif; ?>

	<?php endforeach; ?>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
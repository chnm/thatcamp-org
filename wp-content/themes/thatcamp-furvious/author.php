<?php
if(isset($_GET['author_name'])) :
$current_camper = get_userdatabylogin($author_name);
else :
$current_camper = get_userdata(intval($author));
endif;
?>
<?php get_header(); ?>
<div id="bigg">
	<div id="contentcontainer">
		<span class="contenttop"></span>
		<div class="clear"></div>
		<div id="camper-info">
		    <h1><?php echo $current_camper->first_name; ?>&nbsp; <?php echo $current_camper->last_name; ?></h1>
		    <div class="vcard" id="hcard-<?php echo $lastname; ?>">

            <?php echo get_avatar( $current_camper->user_email, $size = '96'); ?>&nbsp;&nbsp;

	        <?php thatcamp_add_friend_button( $current_camper->ID ) ?>

        		<ul>
        		    <?php if ( $title = $current_camper->user_title ): ?>
        			<li class="title">Title / Position: <?php echo $current_camper->user_title; ?></li>
        			<?php endif; ?>
        			<?php if ($institution = $current_camper->user_organization): ?>
        			<li class="user_organization">Organization: <?php echo $current_camper->user_organization; ?></li>
        			<?php endif; ?>
        			<?php
        			// Checks to see if the user_url isn't empty and if its length is greater than 7 (so as not to write http:// only links) 
        			if(!empty($current_camper->user_url) && strlen($current_camper->user_url) > 7): ?>
        			<li>Website: <a href="<?php echo $current_camper->user_url; ?>" class="url"><?php echo str_replace("http://","",$current_camper->user_url); ?></a></li>
        			<?php endif; ?>

        			<?php if(!empty($current_camper->user_twitter)): ?>
        			<li>Twitter: <a href="http://twitter.com/<?php echo $current_camper->user_twitter; ?>/"><?php echo $current_camper->user_twitter; ?></a></li>
        			<?php endif; ?>
        		</ul>
                </div>
                <div id="camper-description">
                    <?php echo wpautop($current_camper->user_description, 0); ?>
                </div>
		</div>
			<?php if (have_posts()) : ?>
			    <ul id="content">
        		
			    <?php while (have_posts()) : the_post(); ?>
			
			<li class="entry index">
				<div class="entry_info">
					<div class="sidedateblock">
						<?php the_time('d'); ?><span><?php the_time('M'); ?></span>
					</div>	
								
					<p><?php kreative_author_avatar($post->post_author); ?></p>
					<p class="authorp">Posted by<br /><?php the_author_posts_link(); ?></p>
					<br /> 
					<p>Category</p>
					<ul>
						<li class="sidecategory"><?php the_category('</li><li class="sidecategory">') ?></li> 
					</ul>
				</div>	 
				<div class="entry_post"> 
					<div class=" pr30 pl30">
						<h1 class="maintitle">
							<a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a>
						</h1>
						<div class="main_comment">
							<a href="<?php the_permalink() ?>#comments"><?php comments_number('0','1','%'); ?></a>
						</div>
						<div class="post">
							<?php the_content('Read more') ?> 
						</div>
					</div>
				</div>
			</li>
			<?php endwhile;?>
			</ul><!--end of #content -->
    				
			<?php endif; ?>
	</div>
	<?php get_sidebar(); ?>
</div>
<span class="contentbottom"></span>
<div class="clear"></div>
<div class="paginationbar">
	<?php kreative_pagenavi(); ?>
</div>
<?php get_footer(); ?>

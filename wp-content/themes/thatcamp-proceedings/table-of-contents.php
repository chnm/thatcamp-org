<?php
/*
Template Name: Archived Table of Contents
*/
?>

<?php get_header(); ?>
    
<div class="front-page twelve columns offset-by-two omega">

<?php /* This section is the introduction for the table of contents. It pulls the_content() from the Table of Contents page. That page also has custom meta values for the PDF and EPUB links. */

if ( have_posts() ) : while ( have_posts() ) : the_post();

    the_content(); 
    
    $pdf_url = null;
    if($pdf_values = get_post_custom_values('pdf_url')) {
        $pdf_url = $pdf_values[0]; 
    }
    
    $epub_url = null;
    if($epub_values = get_post_custom_values('epub_url')) {
        $epub_url = $epub_values[0]; 
    }

    $ibook_url = null;
    if($ibook_values = get_post_custom_values('ibook_url')) {
        $ibook_url = $ibook_values[0]; 
    }


?>

    <div class="downloads">
        <p>Available for download</p>
        <?php if($pdf_url): ?>
        <a href="<?php echo $pdf_url; ?>"><img src="<?php bloginfo('template_directory'); ?>/images/pdf.png" alt="pdf download"></a>
        <?php endif; ?>
        <?php if($epub_url): ?>
        <a href="<?php echo $epub_url; ?>"><img src="<?php bloginfo('template_directory'); ?>/images/epub.png" alt="epub download"></a>
        <?php endif; ?>
        <?php if($ibook_url): ?>
        <a href="<?php echo $ibook_url; ?>"><img src="<?php bloginfo('template_directory'); ?>/images/ibook.png" alt="ibook download"></a>
        <?php endif; ?>

    </div>

<?php endwhile; else: ?>

    <p><?php _e('Sorry, no posts matched your criteria.'); ?></p>

<?php endif; ?>

<?php /*  This section checks the category for this page, which should be the issue number, and looks for all posts that fall under this issue and the "Introduction" category. */ ?>

<div class="introduction">

<?php 
    if(has_category()):
    $categories = get_the_category(); 
    $category = $categories[0]->term_id;
    endif;
    
    $intro = get_terms('category', array('name__like' => 'Introduction', 'parent' => $category, 'hide_empty' => 0));
    $introId = $intro[0]->term_id;
        if ( is_user_logged_in() ) {
	    $args = array( 'numberposts' => 2, 'cat' => $introId, 'post_status' => 'publish,private,draft,inherit' );
	} else {
		$args = array( 'numberposts' => 2, 'cat' => $introId );
	}
    $lastposts = get_posts( $args ); ?>
    
    <h2><?php echo $intro[0]->name; ?></h2>


    <?php if(count($lastposts)==1): 
        $post = $lastposts[0]; ?>
        
        <div class="solo eight columns offset-by-two">
        	<h3><a href="<?php echo get_permalink(); ?>"><?php the_title(); ?></a></h3>
            <?php if(function_exists('coauthors')): ?>
                <h4 class="author-name"><?php coauthors(',<br>'); ?></h4>
            <?php else: ?>
                <h4 class="author-name"><?php echo the_author_meta('first_name'); ?> <?php echo the_author_meta('last_name'); ?></h4>
            <?php endif; ?>
            <?php if(get_the_excerpt()): ?>
                <?php the_excerpt(); ?>
            <?php endif; ?>
        </div>
    <?php elseif(count($lastposts) == 0): ?>
            <p>No introduction posts found.</p>
    <?php else:     
        $i = 0;
        foreach($lastposts as $post) : setup_postdata($post); ?>
        	<?php if($i == 0): ?>
            	<div class="intro-post six columns alpha">
        	<?php else: ?>
            	<div class="intro-post six columns omega">
            <?php endif; ?>
            	<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                <?php if(function_exists('coauthors')): ?>
                    <h4 class="author-name"><?php coauthors(',<br>'); ?></h4>
                <?php else: ?>
                    <h4 class="author-name"><?php echo the_author_meta('first_name'); ?> <?php echo the_author_meta('last_name'); ?></h4>
                <?php endif; ?>
            <?php the_excerpt(); ?>
            </div>	
            <?php $i++; ?>
        <?php endforeach; ?>
    <?php endif; ?>

</div>


<?php /* This is where it gets ugly. This section checks for all subcategories under the parent issue category. */ 

if($introId) {
    $subcategories = get_categories( array('child_of' => $category, 'hide_empty' => 0, 'exclude' => $introId) );
} else {
    $subcategories = get_categories( array('child_of' => $category, 'hide_empty' => 0) );
}

$j = 0;
foreach($subcategories as $subcategory) :
    $j++;
    $subcategoryId = $subcategory->term_id;
    $subcategoryName = $subcategory->name; 
    $featured = get_category_by_slug('featured');
    $featuredId = $featured->term_id;
    if ( is_user_logged_in() ) {
	    $lastArgs = array('category' => $subcategoryId, 'category__not_in' => $featuredId, 'post_status' => 'publish,private,draft,inherit', 'numberposts' => -1 );
	    $featuredPosts = get_posts(array('category__and' => array($subcategoryId, $featuredId), 'post_status' => 'publish,draft,private,inherit'));
	    
	} else {
		$lastArgs = array('category' => $subcategoryId, 'category__not_in' => $featuredId, 'numberposts' => -1);
		$featuredPosts = get_posts(array('category__and' => array($subcategoryId, $featuredId), 'post_status' => 'public'));		
	}
	$lastposts = get_posts($lastArgs);
    
    /* Every other subcategory uses a layout displaying articles on the right. */  ?>

    <div class="front-page-section">

        
    <?php if( $j % 2 != 0): ?>
                
        <div class="five columns alpha">
        
            <h3><?php echo $subcategoryName; ?></h3>
            
            <?php
            
            /* We get all the posts in this subcategory unless they are in the "featured" category. */ 
            
            foreach($lastposts as $post) : setup_postdata($post); ?>
                	<p><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a><br>
                    <?php if(function_exists('coauthors')): ?>
                        <?php coauthors(',<br>'); ?><br />
		    <?php the_tags(''); ?>
                    <?php else: ?>
                        <?php echo the_author_meta('first_name'); ?> <?php echo the_author_meta('last_name'); ?><br />
                        <?php the_tags(''); ?>
                    <?php endif; ?>
                    </p>
            <?php endforeach; ?>
            
            
        </div>
        
        <div class="toc-previews six columns offset-by-one omega">
            <?php 
            /* There should only be one featured post in this subcategory, and this is where we display it. */
            if($featuredPosts): 
                echo $featuredPosts[0]->post_content;
            else:
                echo '<p>Featured content post needs to be created for this category.</p>';
            endif; ?>
        </div> 
        
    <?php else: ?>
        
        <div class="toc-previews six columns alpha">
            <?php 
            if($featuredPosts): 
                echo $featuredPosts[0]->post_content;                                    
            else:
                echo '<p>Featured content post needs to be created for this category.</p>';
            endif; ?>
        </div> 
        
        <div class="five columns offset-by-one omega">
        
            <h3><?php echo $subcategoryName; ?></h3>
            
            <?php                
            foreach($lastposts as $post) : setup_postdata($post); ?>
                <p><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a><br>
                    <?php if(function_exists('coauthors')): ?>
                        <?php coauthors(',<br>'); ?><br />
		    <?php the_tags(''); ?>
                    <?php else: ?>
                        <?php echo the_author_meta('first_name'); ?> <?php echo the_author_meta('last_name'); ?><br />
                        <?php the_tags(''); ?>
                    <?php endif; ?>
                </p>
            <?php endforeach; ?>                    
            
        </div>
        
    <?php endif; ?> 
    
    </div>                      

<?php endforeach; ?>

    <!--<p class="issn">ISSN 2165-6673</p>-->

<?php get_footer(); ?>                 
<?php get_header(); ?>

<div class="sidebar four columns alpha">
    <?php get_sidebar(); ?>
</div>
    
<div id="article" class="ten columns offset-by-two omega">

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

    <h1><?php the_title(); ?></h1>
    
    <?php if(function_exists('coauthors')): ?>
        <h4 class="author-name"><?php coauthors(',<br>'); ?></h4>
        <h5 class="date"><?php the_date(); ?></h5>
    <?php else: ?>
        <h4 class="author-name"><?php echo the_author_meta('first_name'); ?> <?php echo the_author_meta('last_name'); ?></h4>
         <h5><?php the_date(); ?></h5>
    <?php endif; ?>
    
    <?php the_content(); ?>

    <?php if(get_the_author_meta('description')): ?>
    
    <div class="author-bio">
    
        <h2>
            About 
            <?php if(function_exists('coauthors_posts_links'))
                coauthors_posts_links();
            else
                the_author_posts_link(); ?>
        </h2>
        

        <?php 
            if(function_exists('coauthors')):
                $i = new CoAuthorsIterator();
                $i->iterate();
                print '<div class="gravatar">'.get_avatar(get_the_author_meta('ID'), $size = '52', $default = 'http://www.gravatar.com/avatar/ad516503a11cd5ca435acc9bb6523536').'</div>';
                print '<p class="coauthor">'.get_the_author_meta('description').'</p>';
                while($i->iterate()){
                    print '<div class="gravatar">'.get_avatar(get_the_author_meta('ID'), $size = '52', $default = 'http://www.gravatar.com/avatar/ad516503a11cd5ca435acc9bb6523536').'</div>';
                    print '<p class="coauthor">'.get_the_author_meta('description').'</p>';
                     
                }
            else:
                print '<p>'.get_the_author_meta('description').'</p>';
            endif;
        ?>
    
    </div>

    <?php endif; ?>

    <?php endwhile; else: ?>
    <p><?php _e('Oops, no posts matched your criteria.'); ?></p>

<?php endif; ?>

<?php get_footer(); ?>
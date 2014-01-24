<?php

global $wp_query;

$currentPostId = (int) $wp_query->queried_object_id;
$categories = get_the_category($currentPostId);
if($categories[0]->category_parent == 0) {
    $parent = $categories[0];
} else {
    $parent = get_category($categories[0]->category_parent);
    
}
$parentName = $parent->name;
$parentId = $parent->term_id;
$subcategories = get_terms( 'category', array('parent' => $parentId, 'hide_empty' => 0));

?>

<h3><a href="../">Table of Contents for <?php echo $parentName; ?></a></h3>

<ul id="menu-table-of-contents" class="menu">

<?php     
    foreach($subcategories as $subcategory):
    $subcategoryId = $subcategory->term_id;
    $subcategoryName = $subcategory->name; 
    $featuredCategory = get_category_by_slug('featured');
    $featuredId = $featuredCategory->term_id;
    
    if(is_user_logged_in()) {
        $lastposts = get_posts( array('numberposts' => -1, 'category' => $subcategoryId, 'category__not_in' => $featuredId, 'post_status' => 'publish,private,draft,inherit') );
    } else {
        $lastposts = get_posts( array('numberposts' => -1, 'category' => $subcategoryId, 'category__not_in' => $featuredId) );
    }
    
?>
    <li class="parent"><a href="<?php echo get_category_link($subcategoryId); ?>"><?php echo $subcategoryName; ?></a>
        <ul class="sub-menu">
        <?php foreach($lastposts as $post) : setup_postdata($post); ?>
            <?php if($post->ID == $currentPostId): ?>
        	<li class="current-menu-item"><a href="<?php the_permalink(); ?>"><?php the_title() ?></a><br>
        	<?php else: ?>
        	<li><a href="<?php the_permalink(); ?>"><?php the_title() ?></a><br>
        	<?php endif; ?>
            <?php if(function_exists('coauthors')):
                coauthors(',<br>');
            else:
                echo the_author_meta('first_name') . ' ' . the_author_meta('last_name');
            endif; ?>
            </li>
        <?php endforeach; ?>
        </ul>
    </li>
<?php endforeach; ?>   
</ul>
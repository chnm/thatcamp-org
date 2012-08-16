<?php
function widget_sideFeature() {
	$options = get_option("widget_sideFeature");
 	$numberOf = $options['number'];
	$category = $options['category'];
	$category = "&cat=" . $category;
	$showposts = "posts_per_page=" . $numberOf . $category ;
	?>
    <?php
    $featuredPosts = new WP_Query();
    $featuredPosts->query($showposts);
	?>
	<?php $i = 1; ?>
    <?php while ($featuredPosts->have_posts()) : $featuredPosts->the_post(); ?>
    <h3 class="side"><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h3>
        <div class="meta">
            <?php _e("By", "feed-me-seymour"); echo " "; the_author_posts_link(); ?>
        </div>
        <div class="storycontent <?php if($numberOf == $i) { echo "noline"; } $i++; ?>">
            <?php theme_excerpt('25'); ?>
        </div>
	    <?php
		endwhile; 
}

function widget_myFeature($args) {
	extract($args); 
	
	$options = get_option("widget_sideFeature");
	
	echo $before_widget;
    echo $before_title;
    echo $options['title'];
    echo $after_title;
	widget_sideFeature();
	echo $after_widget; 
}

function myFeature_control()
{
  $options = get_option("widget_sideFeature");

  if (!is_array( $options ))
	{
		$options = array(
		'title' => 'Feature',
		'number' => '1',
		'category' => '0'
	    );
  }      

  if (!empty($_POST['sideFeature-Submit']))
  {
    $options['title'] = htmlspecialchars($_POST['sideFeature-WidgetTitle']);
    $options['number'] = htmlspecialchars($_POST['sideFeature-PostNumber']);
	if ( $options['number'] > 5) {  $options['number'] = 5; } 
    $options['category'] = htmlspecialchars($_POST['sideFeature-Category']);

    update_option("widget_sideFeature", $options);
  }

?>
  <p>
    <label for="sideFeature-WidgetTitle"><?php _e('Title: ', "feed-me-seymour"); ?></label><br />
    <input class="widefat" type="text" id="sideFeature-WidgetTitle" name="sideFeature-WidgetTitle" value="<?php echo $options['title'];?>" />
    <br /><br />
    <label for="sideFeature-PostNumber"><?php _e('Number of posts to show: ', "feed-me-seymour"); ?></label>
    <input type="text" id="sideFeature-PostNumber" name="sideFeature-PostNumber" style="width: 25px; text-align: center;" maxlength="1" value="<?php echo $options['number'];?>" /><br />
	<small><em><?php _e('(max 5)', "feed-me-seymour"); ?></em></small>
    <br /><br />
    <label for="sideFeature-Category"><?php _e('From which category: ', "feed-me-seymour"); ?></label>
    <?php
	$options = get_option("widget_sideFeature");
	$category = $options['category'];
    ?>
	<?php wp_dropdown_categories('name=sideFeature-Category&selected='.$category); ?>
    <input type="hidden" id="sideFeature-Submit" name="sideFeature-Submit" value="1" />
	<p><small><em><?php _e('(Note: The selected will be excluded from the main content on the index page)', "feed-me-seymour"); ?></em></small></p>
  </p>
<?php
}
wp_register_sidebar_widget( 'featured_post', __('FMS - Featured Post', "feed-me-seymour"), 'widget_myFeature', array('description' => __('Displays featured posts from one category', "feed-me-seymour")) );
wp_register_widget_control( 'featured_post', __('FMS - Featured Post', "feed-me-seymour"), 'myFeature_control');
?>
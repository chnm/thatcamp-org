<?php 
function kreative_excerpt($num) 
{  
	$limit = $num+1;  
	$excerpt = explode(' ', get_the_excerpt(), $limit);  
	array_pop($excerpt);  
	
	$excerpt = implode(" ", $excerpt) . "...";  
	
	echo $excerpt;  
}

function kreative_author_avatar($post_author)
{
	if (function_exists('get_avatar'))
	{
		echo get_avatar($post_author,'30');
	}
	else 
	{
		?><img src="#" /><?php
	}
}

add_filter('get_comments_number', 'kreative_comment_count', 0);
function kreative_comment_count( $count ) {
	if ( ! is_admin()) 
	{
		global $id;
		$comments_by_type = &separate_comments(get_comments('status=approve&post_id=' . $id));
		return count($comments_by_type['comment']);
	} 
	else 
	{
		return $count;
	}
}
 
function fpings($comment, $args, $depth) {
       $GLOBALS['comment'] = $comment;
?>
	<li id="comment-<?php comment_ID(); ?>"><?php comment_author_link(); ?>
<?php
}
?>
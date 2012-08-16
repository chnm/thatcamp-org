<?php

function kreative_post_keywords($num_to_ret = 20)
{
	global $post;
	// An array of weightings, to make adjusting them easier.
	$w = array(
			   'title' => 2,
			   'name' => 2,
			   'content' => 1,
			   'cat_name' => 3
		      );
	
	/*
	Thanks to http://www.eatdrinksleepmovabletype.com/tutorials/building_a_weighted_keyword_list/
	for the basics for this code.  It saved me much typing (or thinking) :)
	*/
	
	// This needs experimenting with.  I've given post title and url a double
	// weighting, changing this may give you better results
	$string = str_repeat($post->post_title, $w['title'].' ').
			  str_repeat(str_replace('-', ' ', $post->post_name).' ', $w['name']).
			  str_repeat($post->post_content, $w['content'].' ');
	
	// Cat names don't help with the current query: the category names of other
	// posts aren't retrieved by the query to be matched against (and can't be
	// indexed)
	// But I've left this in just in case...
	$post_categories = get_the_category();
	foreach ($post_categories as $cat) {
		$string .= str_repeat($cat->cat_name.' ', $w['cat_name']);
	}
	
	// Remove punctuation.
	$wordlist = preg_split('/\s*[\s+\.|\?|,|(|)|\-+|\'|\"|=|;|&#0215;|\$|\/|:|{|}]\s*/i', $string);
	
	// Build an array of the unique words and number of times they occur.
	$a = array_count_values($wordlist);
	
	//Remove words that don't matter--"stop words."
	$overusedwords = array( '', 'a', 'an', 'the', 'and', 'of', 'i', 'to', 'is', 'in', 'with', 'for', 'as', 'that', 'on', 'at', 'this', 'my', 'was', 'our', 'it', 'you', 'we', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '10', 'about', 'after', 'all', 'almost', 'along', 'also', 'amp', 'another', 'any', 'are', 'area', 'around', 'available', 'back', 'be', 'because', 'been', 'being', 'best', 'better', 'big', 'bit', 'both', 'but', 'by', 'c', 'came', 'can', 'capable', 'control', 'could', 'course', 'd', 'dan', 'day', 'decided', 'did', 'didn', 'different', 'div', 'do', 'doesn', 'don', 'down', 'drive', 'e', 'each', 'easily', 'easy', 'edition', 'end', 'enough', 'even', 'every', 'example', 'few', 'find', 'first', 'found', 'from', 'get', 'go', 'going', 'good', 'got', 'gt', 'had', 'hard', 'has', 'have', 'he', 'her', 'here', 'how', 'if', 'into', 'isn', 'just', 'know', 'last', 'left', 'li', 'like', 'little', 'll', 'long', 'look', 'lot', 'lt', 'm', 'made', 'make', 'many', 'mb', 'me', 'menu', 'might', 'mm', 'more', 'most', 'much', 'name', 'nbsp', 'need', 'new', 'no', 'not', 'now', 'number', 'off', 'old', 'one', 'only', 'or', 'original', 'other', 'out', 'over', 'part', 'place', 'point', 'pretty', 'probably', 'problem', 'put', 'quite', 'quot', 'r', 're', 'really', 'results', 'right', 's', 'same', 'saw', 'see', 'set', 'several', 'she', 'sherree', 'should', 'since', 'size', 'small', 'so', 'some', 'something', 'special', 'still', 'stuff', 'such', 'sure', 'system', 't', 'take', 'than', 'their', 'them', 'then', 'there', 'these', 'they', 'thing', 'things', 'think', 'those', 'though', 'through', 'time', 'today', 'together', 'too', 'took', 'two', 'up', 'us', 'use', 'used', 'using', 've', 'very', 'want', 'way', 'well', 'went', 'were', 'what', 'when', 'where', 'which', 'while', 'white', 'who', 'will', 'would', 'your');
	
	// Remove the stop words from the list.
	foreach ($overusedwords as $word) {
		 unset($a[$word]);
	}
	arsort($a, SORT_NUMERIC);
	
	$num_words = count($a);
	$num_to_ret = $num_words > $num_to_ret ? $num_to_ret : $num_words;
	
	$outwords = array_slice($a, 0, $num_to_ret);
	return implode(' ', array_keys($outwords));
	
}


function kreative_related_posts($limit=5, $len=10, $before_title = '', $after_title = '', $before_post = '', $after_post = '', $show_pass_post = FALSE, $show_excerpt = FALSE) 
{
	global $wpdb, $post;
	/*
  	$limit = get_option('limit');
  	$len = get_option('len');
  	$before_title = stripslashes(get_option('before_title'));
  	$after_title = stripslashes(get_option('after_title'));
  	$before_post = stripslashes(get_option('before_post'));
  	$after_post = stripslashes(get_option('after_post'));
  	$show_pass_post = get_option('show_pass_post');
	$show_excerpt = get_option('show_excerpt');
	*/
	
    // My new keyword way
    $terms = kreative_post_keywords();

	// Make sure the post is not from the future

	$time_difference = get_settings('gmt_offset');
	$now = gmdate("Y-m-d H:i:s",(time()+($time_difference*3600)));
	
	// Primary SQL query
	
    $sql = "SELECT ID, post_title, post_content,"
         . "MATCH (post_content) "
         . "AGAINST ('$terms') AS score "
         . "FROM $wpdb->posts WHERE "
         . "MATCH (post_content) "
         . "AGAINST ('$terms') "
		 . "AND post_date <= '$now' "
         . "AND (post_status IN ( 'publish',  'static' ) AND ID != '$post->ID') ";
    if ($show_pass_post== FALSE) 
	{ 
		$sql .= "AND post_password ='' "; 
	}
    $sql .= "ORDER BY score DESC LIMIT $limit";
    $results = $wpdb->get_results($sql);
	
    $output = '';
    if ($results) 
	{
		foreach ($results as $result) 
		{
			$title = stripslashes(apply_filters('the_title', $result->post_title));
			$permalink = get_permalink($result->ID);
        	$post_content = strip_tags($result->post_content);
			$post_content = stripslashes($post_content);
        	
			$output .= $before_title .'<a href="'. $permalink .'" rel="bookmark" title="Permanent Link: ' . $title . '">' . $title . '</a>';
        	$output .= $after_title;
			
			if ($show_excerpt=='true') 
			{
				$words=split(" ",$post_content); 
				$post_strip = join(" ", array_slice($words,0,$len));
				$output .= $before_post . $post_strip . $after_post;
	    	}
		}
		echo $output;
	} else {
        echo $before_title.'No related posts'.$after_title;
    }
}

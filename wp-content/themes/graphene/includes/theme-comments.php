<?php
if ( ! function_exists( 'graphene_comment' ) ) :
/**
 * Defines the callback function for use with wp_list_comments(). This function controls
 * how comments are displayed.
*/
function graphene_comment( $comment, $args, $depth) {
	$GLOBALS['comment'] = $comment; 
	?>
		<li id="comment-<?php comment_ID(); ?>" <?php comment_class( 'clearfix' ); ?>>
			<?php do_action( 'graphene_before_comment' ); ?>
			
			<?php /* Added support for comment numbering using Greg's Threaded Comment Numbering plugin */ ?>
			<?php if (function_exists( 'gtcn_comment_numbering' ) ) {gtcn_comment_numbering( $comment->comment_ID, $args);} ?>
			
				<div class="comment-wrap clearfix">
					
					<?php if ( $avatar = get_avatar( $comment, apply_filters( 'graphene_gravatar_size', 40) ) ) : ?>
						<div class="comment-avatar-wrap">
							<?php echo $avatar; ?>
							<?php do_action( 'graphene_comment_gravatar' ); ?>
						</div>
					<?php endif; ?>
					
					<h5 class="comment-author">
						<cite><?php echo graphene_comment_author_link( $comment->user_id ); ?></cite>
						<?php do_action( 'graphene_comment_author' ); ?>
					</h5>
					<div class="comment-meta">
						<p class="commentmetadata">
							<?php /* translators: %1$s is the comment date, %2$s is the comment time */ ?>
							<?php printf( __( '%1$s at %2$s', 'graphene' ), get_comment_date(), get_comment_time() ); ?>
							<span class="timezone"><?php echo '(UTC '.get_option( 'gmt_offset' ).')'; ?></span>
							<?php graphene_moderate_comment( get_comment_ID() ); ?>
							<?php edit_comment_link(__( 'Edit comment','graphene' ),' (',') ' ); ?>
							<span class="comment-permalink"><a href="<?php echo get_comment_link(); ?>"><?php _e( 'Link to this comment', 'graphene' ); ?></a></span>
							<?php do_action( 'graphene_comment_metadata' ); ?>    
						</p>
						<p class="comment-reply-link">
							<?php comment_reply_link(array( 'depth' => $depth, 'max_depth' => $args['max_depth'], 'reply_text' => __( 'Reply', 'graphene' ) )); ?>
						
							<?php do_action( 'graphene_comment_replylink' ); ?>
						</p>
						
						<?php do_action( 'graphene_comment_meta' ); ?>
					</div>
					<div class="comment-entry">
						<?php do_action( 'graphene_before_commententry' ); ?>
						
						<?php if ( $comment->comment_approved == '0' ) : ?>
						   <p><em><?php _e( 'Your comment is awaiting moderation.', 'graphene' ) ?></em></p>
						   <?php do_action( 'graphene_comment_moderation' ); ?>
						<?php else : ?>
							<?php comment_text(); ?>
						<?php endif; ?>
						
						<?php do_action( 'graphene_after_commententry' ); ?>
					</div>
				</div>
			
			<?php do_action( 'graphene_after_comment' ); ?>
	<?php
}
endif;


/**
 * Customise the comment form
*/
function graphene_comment_form_fields(){
	
	$req = get_option( 'require_name_email' );
	$aria_req = ( $req ? ' aria-required="true"' : '' );
	$req_mark = ( $req ? ' <span class="required">*</span>' : '' );
	$commenter = wp_get_current_commenter();
	
	$fields =  array( 
		'author' => 
					'<p class="comment-form-author clearfix">
						<label for="author" class="graphene_form_label">' . __( 'Name:', 'graphene' ) . $req_mark . '</label>
						<input id="author" name="author" type="text" class="graphene-form-field"' . $aria_req . ' value="' . esc_attr( $commenter['comment_author'] ) . '" />
					</p>',
		'email'  => 
					'<p class="comment-form-email clearfix">
						<label for="email" class="graphene_form_label">' . __( 'Email:', 'graphene' ) . $req_mark . '</label>
						<input id="email" name="email" type="text" class="graphene-form-field"' . $aria_req . ' value="' . esc_attr( $commenter['comment_author_email'] ) . '" />
					</p>',
		'url'    => 
					'<p class="comment-form-url clearfix">
						<label for="url" class="graphene_form_label">' . __( 'Website:', 'graphene' ) . ' </label>
						<input id="url" name="url" type="text" class="graphene-form-field" value="' . esc_attr( $commenter['comment_author_url'] ) . '" />
					</p>',
	);
	
	$fields = apply_filters( 'graphene_comment_form_fields', $fields );
	
	return $fields;
}


/**
 * Modify default comment parameters
 *
 * @package Graphene
 * @since Graphene 1.9
 */
function graphene_comment_args( $defaults ){
	$args = array(
				'comment_field'	=> 	'<p class="comment-form-message comment-form-comment clearfix">
										<label class="graphene_form_label" for="comment">' . __( 'Message:', 'graphene' ) . ' <span class="required">*</span></label>
										<textarea name="comment" id="comment" cols="40" rows="20" class="graphene-form-field" aria-required="true"></textarea>
									 </p>',
			);
	return array_merge( $defaults, $args );
}
 
	
// Clear
function  graphene_comment_clear(){
	echo '<div class="clear"></div>';
}

// Add all the filters we defined
add_filter( 'comment_form_default_fields', 'graphene_comment_form_fields' );
add_filter( 'comment_form_defaults', 'graphene_comment_args', 5 );
add_filter( 'comment_form', 'graphene_comment_clear', 1000 );


if ( ! function_exists( 'graphene_get_comment_count' ) ) :
/**
 * Adds the functionality to count comments by type, eg. comments, pingbacks, tracbacks.
 * Based on the code at WPCanyon (http://wpcanyon.com/tipsandtricks/get-separate-count-for-comments-trackbacks-and-pingbacks-in-wordpress/)
 * 
 * In Graphene version 1.3 the $noneText param has been removed
 *
 * @package Graphene
 * @since Graphene 1.3
*/
function graphene_comment_count( $type = 'comments', $oneText = '', $moreText = '' ){
	
	$result = graphene_get_comment_count( $type );

    if( $result == 1  )
		return str_replace( '%', $result, $oneText );
	elseif( $result > 1 )
		return str_replace( '%', $result, $moreText );
	else
		return false;
}
endif;


if ( ! function_exists( 'graphene_get_comment_count' ) ) :
/**
 * Adds the functionality to count comments by type, eg. comments, pingbacks, tracbacks. Return the number of comments, but do not print them.
 * Based on the code at WPCanyon (http://wpcanyon.com/tipsandtricks/get-separate-count-for-comments-trackbacks-and-pingbacks-in-wordpress/)
 * 
 * In Graphene version 1.3 the $noneText param has been removed
 *
 * @package Graphene
 * @since Graphene 1.3
*/
function graphene_get_comment_count( $type = 'comments', $only_approved_comments = true, $top_level = false ){
	if 		( $type == 'comments' ) 	$type_sql = 'comment_type = ""';
	elseif 	( $type == 'pings' )		$type_sql = 'comment_type != ""';
	elseif 	( $type == 'trackbacks' ) 	$type_sql = 'comment_type = "trackback"';
	elseif 	( $type == 'pingbacks' )	$type_sql = 'comment_type = "pingback"';
	
	$type_sql = apply_filters( 'graphene_comments_typesql', $type_sql, $type );
	$approved_sql = $only_approved_comments ? ' AND comment_approved="1"' : '';
	$top_level_sql = ( $top_level ) ? ' AND comment_parent="0" ' : '';
        
	global $wpdb;

    $result = $wpdb->get_var( '
        SELECT
            COUNT(comment_ID)
        FROM
            ' . $wpdb->comments . '
        WHERE
            ' . $type_sql . $approved_sql . $top_level_sql . ' AND 
            comment_post_ID= ' . get_the_ID() );
	
	return $result;
}
endif;


if ( ! function_exists( 'graphene_should_show_comments' ) ) :
/**
 * Helps to determine if the comments should be shown.
 */
function graphene_should_show_comments() {
    global $graphene_settings, $post;
    
	if ( $graphene_settings['comments_setting'] == 'disabled_completely' )
        return false;
    
	if ( $graphene_settings['comments_setting'] == 'disabled_pages' && get_post_type( $post->ID ) == 'page' )
        return false;
	
	if ( ! is_singular() && $graphene_settings['hide_post_commentcount'] )
		return false;
	
	if ( ! comments_open() && ! is_singular() && get_comments_number( $post->ID ) == 0 )
		return false;
	
    return true;
}
endif;


/**
 * Delete and mark spam link for comments. Show only if current user can moderate comments
 */
 if ( ! function_exists( 'graphene_moderate_comment' ) ) :
function graphene_moderate_comment( $id ) {
	$html = '| <a class="comment-delete-link" title="' . esc_attr__( 'Delete this comment', 'graphene' ) . '" href="' . get_admin_url() . 'comment.php?action=cdc&c=' . $id . '">' . __( 'Delete', 'graphene' ) . '</a>';
	$html .= '&nbsp;';
	$html .= '| <a class="comment-spam-link" title="' . esc_attr__( 'Mark this comment as spam', 'graphene' ) . '" href="' . get_admin_url() . 'comment.php?action=cdc&dt=spam&c=' . $id . '">' . __( 'Spam', 'graphene' ) . '</a> |';

	if ( current_user_can( 'moderate_comments' ) ) echo $html;
}
endif;


if ( ! function_exists( 'graphene_comments_nav' ) ) :
/**
 * Display comments pagination
 *
 * @package Graphene
 * @since 1.9
 */
function graphene_comments_nav(){
	global $graphene_settings, $is_paginated;
	if ( get_comment_pages_count() > 1 && $is_paginated ) : 
	?>
        <div class="comment-nav clearfix">
            <?php if ( function_exists( 'wp_commentnavi' ) && ! $graphene_settings['inf_scroll_comments'] ) : wp_commentnavi(); ?>
                <p class="commentnavi-view-all"><?php wp_commentnavi_all_comments_link(); ?></p>
            <?php else : ?> 
                <p><?php paginate_comments_links(); ?>&nbsp;</p>
            <?php endif; do_action( 'graphene_comments_pagination' ); ?>
        </div>
        
        <?php if ( $graphene_settings['inf_scroll_comments'] ) : ?>
			<p class="fetch-more-wrapper"><a href="#" class="fetch-more"><?php _e( 'Fetch more comments', 'graphene' ); ?></a></p>
		<?php endif;
	endif;
}
endif;


if ( ! function_exists( 'graphene_comment_author_link' ) ) :
/**
 * Display comment author's display name if author is registered
 *
 * @package Graphene
 * @since 1.9
 */
function graphene_comment_author_link( $user_id ){
	if ( $user_id ) {
		$author = get_userdata( $user_id );
		$author_link = comment_author_url_link( $author->display_name, '' , '' );
	} else {
		$author_link = get_comment_author_link();
	}
	
	return apply_filters( 'graphene_comment_author_link', $author_link );
}
endif;
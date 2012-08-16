<?php
/**
 * Shortcodes definition.
 */

function ms_global_search_get_the_content( $s ) {
    $content = $s->post_content;
    $content = apply_filters( 'the_content', $content );

    $output = '';
    if ( post_password_required( $s ) ) {
		$label = 'ms-global-search-'.$s->blog_id.'pwbox_'.$s->ID;
        $output = '<form action="' . get_blog_option( $s->blog_id, 'siteurl' ) . '/wp-pass.php" method="post">
        <p>' . __( 'This post is password protected. To view it please enter your password below:', 'ms-global-search' ) . '</p>
        <p><label for="' . $label . '">' . __( 'Password', 'ms-global-search' ) . ' <input name="post_password" id="' . $label . '" type="password" size="20" /></label> <input type="submit" name="Submit" value="' . __( 'Submit', 'ms-global-search' ) . '" /></p>
        </form>
        ';
        return apply_filters( 'the_password_form', $output );
	}
    return $content;
}

function ms_global_search_get_the_excerpt( $s ) {
	$output = '';
	if ( post_password_required( $s ) ) {
		$label = 'ms-global-search-'.$s->blog_id.'pwbox_'.$s->ID;
        $output = '<form action="' . get_blog_option( $s->blog_id, 'siteurl' ) . '/wp-pass.php" method="post">
        <p>' . __( 'This post is password protected. To view it please enter your password below:', 'ms-global-search' ) . '</p>
	    <p><label for="' . $label . '">' . __( 'Password', 'ms-global-search' ) . ' <input name="post_password" id="' . $label . '" type="password" size="20" /></label> <input type="submit" name="Submit" value="' . __( 'Submit', 'ms-global-search' ) . '" /></p>
	    </form>
	    ';
	    return apply_filters( 'the_password_form', $output );
	}
	
	$excerpt = $s->post_excerpt;
	
	if ( empty( $excerpt ) ) {
	    $raw_excerpt = $excerpt;
		$excerpt = $s->post_content;

		$excerpt = strip_shortcodes( $excerpt );

		$excerpt = apply_filters( 'the_content', $excerpt );
		$excerpt = str_replace( ']]>', ']]&gt;', $excerpt );
		$excerpt_length = apply_filters( 'excerpt_length', 55 );
		$excerpt_more = '... <a href="'. get_blog_permalink( $s->blog_id, $s->ID ). '">' . __( '(Read more)', 'ms-global-search' ) . '</a>';
		$words = preg_split( "/[\n\r\t ]+/", $excerpt, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY );
		if ( count($words) > $excerpt_length ) {
			array_pop( $words );
			$excerpt = implode( ' ', $words );
			$excerpt = $excerpt . $excerpt_more;
		} else {
			$excerpt = implode( ' ', $words );
		}
		
		return $excerpt;
	} else {
	    return apply_filters( 'get_the_excerpt', $excerpt );
	}
}

if( !function_exists( 'ms_global_search_get_edit_link' ) ) {
	function ms_global_search_get_edit_link( $s, $before = '', $after = '' ) {
	    if ( $s->post_type == 'page' ) {
			if ( !current_user_can( 'edit_page', $s->ID ) ) return;
		} else {
			if ( !current_user_can( 'edit_post', $s->ID ) ) return;
		}
	
	    $context = 'display';
		switch ( $s->post_type ) :
		case 'page' :
			if ( !current_user_can( 'edit_page', $s->ID ) )
				return;
			$file = 'post';
			$var  = 'post';
			break;
		case 'attachment' :
			if ( !current_user_can( 'edit_post', $s->ID ) )
				return;
			$file = 'media';
			$var  = 'attachment_id';
			break;
		case 'revision' :
			if ( !current_user_can( 'edit_post', $s->ID ) )
				return;
			$file	= 'revision';
			$var 	= 'revision';
			$action = '';
			break;
		default :
			if ( !current_user_can( 'edit_post', $s->ID ) )
				return;
			$file = 'post';
			$var  = 'post';
			break;
		endswitch;
	
		$editlink = apply_filters( 'get_edit_post_link', 'http://'.$s->domain.$s->path.'wp-admin/'.$file.'.php?action=edit&amp;'.$var.'='.$s->ID, $s->ID, $context );
	    
	    $link = '<a class="post-edit-link" href="' . $editlink . '" title="' . attribute_escape( __( 'Edit post', 'ms-global-search' ) ) . '">'. __( 'Edit' , 'ms-global-search' ) .'</a>';
		return $before . apply_filters( 'edit_post_link', $link, $s->ID ) . $after;
	}
}

if( !function_exists( 'ms_global_search_get_comments_link' ) ) {
	function ms_global_search_get_comments_link( $s, $css_class = '' ) {
	    global $wpcommentsjavascript, $wpcommentspopupfile;
	
		$number = $s->comment_count;
	
		if ( 0 == $number && 'closed' == $s->comment_status && 'closed' == $s->ping_status ) {
			echo '<span' . ( ( !empty( $css_class ) ) ? ' class="' . $css_class . '"' : '' ) . '>' . __( 'Comments off', 'ms-global-search' ) . '</span>';
			return;
		}
	
		if ( post_password_required() ) {
			echo __( 'Enter your password to view comments', 'ms-global-search' );
			return;
		}
	
		echo '<a href="';
		if ( $wpcommentsjavascript ) {
			if ( empty( $wpcommentspopupfile ) )
				$home = get_blog_option( $s->blog_id, 'home' );
			else
				$home = get_blog_option( $s->blog_id, 'siteurl' );
			echo $home . '/' . $wpcommentspopupfile . '?comments_popup=' . $s->ID;
			echo '" onclick="wpopen( this.href ); return false"';
		} else { // if comments_popup_script() is not in the template, display simple comment link
			if ( 0 == $number )
				echo get_blog_permalink( $s->blog_id, $s->ID ) . '#respond';
			else
				echo get_blog_permalink( $s->blog_id, $s->ID ) . '#comments';
			echo '"';
		}
	
		if ( !empty( $css_class ) ) {
			echo ' class="'.$css_class.'" ';
		}
		$title = attribute_escape( $s->post_title );
	
		echo apply_filters( 'comments_popup_link_attributes', '' );
	
		echo ' title="' . sprintf( __( 'Comment on %s', 'ms-global-search' ), $title ) . '">';
	    if ( $number > 1 )
			$output = str_replace( '%', number_format_i18n( $number ), __( '% Comments', 'ms-global-search' ) );
		elseif ( $number == 0 )
			$output = __( 'No Comments', 'ms-global-search' );
		else // must be one
			$output = __( '1 Comment', 'ms-global-search' );
	
		echo apply_filters( 'comments_number', $output, $number );
		echo '</a>';
	}
}

if( !function_exists( 'ms_global_search_page' ) ) {
	function ms_global_search_page( $atts ) {
		global $wp_query, $wpdb;
		
		extract( shortcode_atts( array( 'excerpt' => 'no' ), $atts ) );
		
		$term = strip_tags( apply_filters( 'get_search_query', get_query_var( 'mssearch' ) ) );
	
		if( !empty( $term ) ) {
		    // Literal keyword
            if( preg_match( '/^\"(.*?)\"$/', stripslashes($term) , $termmatch) ) {
                if( !empty( $termmatch[1] ) ) {
                    $termsearch = "( post_title LIKE '%%".$termmatch[1]."%%' OR post_content LIKE '%%".$termmatch[1]."%%' OR ".$wpdb->users.".display_name LIKE '%%".$termmatch[1]."%%' ) ";
                } else { ?>
                    <h3 class='globalpage_title center'><?php _e( "Not found", 'ms-global-search' ) ?></h3>
                    <p class='globalpage_message center'><?php _e( "Sorry, but you are looking for something that isn't here.", 'ms-global-search' ) ?></p>
                <?php
                }
            } else {
                // Multiple keywords
                $multipleterms = explode ( " ", $term );
                if( count($multipleterms) != 1 ) {
                    $termsearch = "( ";
                    $totalterms = count($multipleterms);
                    $numterms = 1;
                    foreach( $multipleterms as $aterm ) {
                        if( $numterms < $totalterms ) {
                            $termsearch .= " ( post_title LIKE '%%".$aterm."%%' OR post_content LIKE '%%".$aterm."%%' OR ".$wpdb->users.".display_name LIKE '%%".$aterm."%%' ) AND ";
                        } else {
                            $termsearch .= "( post_title LIKE '%%".$aterm."%%' OR post_content LIKE '%%".$aterm."%%' OR ".$wpdb->users.".display_name LIKE '%%".$aterm."%%' ) )";
                        }
                        $numterms++;
                    }
                } else {
                    $termsearch = "( post_title LIKE '%%".$term."%%' OR post_content LIKE '%%".$term."%%' OR ".$wpdb->users.".display_name LIKE '%%".$term."%%' ) ";
                }
            }
            
		    $wheresearch = '';
			// Search only on user blogs.
			$userid = get_current_user_id();
			if( strcmp( apply_filters ( 'get_search_query', get_query_var( 'mswhere' ) ), 'my' ) == 0 && $userid != 0 ) {
				$userblogs = get_blogs_of_user( $userid );
				
				$i=0;
				foreach( $userblogs as $ub ) {
					if( $i != 0 ) $wheresearch .= " OR ";
					else $wheresearch .= "( ";
					$i++;
					$wheresearch .= $wpdb->base_prefix."v_posts.blog_id = ".$ub->userblog_id;
					if( count( $userblogs ) == $i ) $wheresearch .= " ) AND ";
				}
			}
			
			// Search on pages.
			if(get_query_var( 'msp' )) {
                $post_type = "( post_type = 'post' OR post_type = 'page' )";
			} else {
                $post_type = "post_type = 'post'";
			}
			
			// Show private posts
			if ($userid != 0) {
    			$request = $wpdb->prepare( "SELECT ".$wpdb->base_prefix."v_posts.* from ".$wpdb->base_prefix."v_posts left join ".$wpdb->users." on ".$wpdb->users.".ID=".$wpdb->base_prefix."v_posts.post_author ".
    			"where ".$wheresearch." ".$termsearch.
    		    "AND ( post_status = 'publish' OR post_status = 'private' ) AND ".$post_type." ORDER BY ".$wpdb->base_prefix."v_posts.blog_id ASC, ".$wpdb->base_prefix."v_posts.post_date DESC, ".$wpdb->base_prefix."v_posts.comment_count DESC" );
			} else {
  		        $request = $wpdb->prepare( "SELECT ".$wpdb->base_prefix."v_posts.* from ".$wpdb->base_prefix."v_posts left join ".$wpdb->users." on ".$wpdb->users.".ID=".$wpdb->base_prefix."v_posts.post_author ".
                "where ".$wheresearch." ".$termsearch.
                "AND post_status = 'publish' AND ".$post_type." ORDER BY ".$wpdb->base_prefix."v_posts.blog_id ASC, ".$wpdb->base_prefix."v_posts.post_date DESC, ".$wpdb->base_prefix."v_posts.comment_count DESC" );
			}
			
			$search = $wpdb->get_results( $request );
	        
			// Show search results.
			if( empty( $search ) ) { ?>
				<h3 class='globalpage_title center'><?php _e( "Not found", 'ms-global-search' ) ?> <span class='ms-global-search_term'><?php echo stripslashes($term); ?></span><?php if( !empty( $wheresearch ) ) echo " ".__( 'in your blogs', 'ms-global-search' ); ?>.</h3>
				<p class='globalpage_message center'><?php _e( "Sorry, but you are looking for something that isn't here.", 'ms-global-search' ) ?></p>
			<?php
	        } else {
	        	$countResult = count( $search );
                if($countResult < 2){
                    echo '<p>'.$countResult." ".__( 'match with', 'ms-global-search' )." ";
                }else{
                    echo '<p>'.$countResult." ".__( 'matches with', 'ms-global-search' )." ";
                } ?>
                <span class='ms-global-search_term'><?php echo stripslashes($term); ?></span><?php if( !empty( $wheresearch ) ) echo " ".__( 'in your blogs', 'ms-global-search' ); ?>.</p>
                
                <?php
	            $blogid = '';
	            foreach( $search as $s ) {
	                $author = get_userdata( $s->post_author );
	                if( $blogid != $s->blog_id ) {
	                    $blogid = $s->blog_id; ?>
	                    
	                    <h2 class='globalblog_title'><?php echo get_blog_option( $blogid, 'blogname' ) ?></h2>
	                <?php
	                } ?>
	
	                <div <?php post_class( 'globalsearch_post' ) ?>>
	                	<div class="globalsearch_header">
	                    	<h2 id="post-<?php echo $s->ID.$s->blog_id; ?>" class="globalsearch_title"><a href="<?php echo get_blog_permalink( $s->blog_id, $s->ID ); ?>" rel="bookmark" title="<?php echo __( 'Permanent Link to', 'ms-global-search' ).' '.$s->post_title; ?>"><?php echo $s->post_title ?></a></h2>
	                    	<p class="globalsearch_meta">
								<span class="globalsearch_comment"><?php ms_global_search_get_comments_link( $s ); ?></span>
								<span class="globalsearch_date"><?php echo date( __( 'j/m/y, G:i', 'ms-global-search' ) ,strtotime( $s->post_date ) ); ?></span>
								<span class="globalsearch_author"><?php echo '<a href="http://' . $s->domain.$s->path.'author/'.$author->user_nicename . '" title="' . $author->user_nicename . '">' . $author->user_nicename . '</a>'; ?></span>
								<?php echo ms_global_search_get_edit_link( $s, '<span class="globalsearch_edit">', '</span>' ); ?>
							</p>
						</div>
						
						<div class="globalsearch_content">
	                    	<div class="entry">
	                    		<?php
	                    		if(strcmp($excerpt, "yes") == 0)
	                    			echo ms_global_search_get_the_excerpt( $s );
	                        	else
	                        		echo ms_global_search_get_the_content( $s ); ?>
	                    	</div>
						</div>
	                </div>
	            <?php
	            }
	        }
	    } else { ?>
		    <h3 class='globalpage_title center'><?php _e( "Not found", 'ms-global-search' ) ?></h3>
	        <p class='globalpage_message center'><?php _e( "Sorry, but you are looking for something that isn't here.", 'ms-global-search' ) ?></p>
	    <?php
	    }
	}
}

if( !function_exists( 'ms_global_search_form' ) ) {
	function ms_global_search_form( $atts ) {
		global $wp_query, $wpdb;
		
		extract( shortcode_atts( array( 'type' => 'vertical', 'page' => __( 'globalsearch', 'ms-global-search' ), 'search_on_pages' => 0, 'hide_options' => 0 ), $atts ) );
		
		$rand = rand();
		if( strcmp( $type, 'horizontal' ) == 0 ) { ?>
			<form class="ms-global-search_form" method="get" action="<?php echo get_bloginfo( 'wpurl' ).'/'.$page.'/'; ?>">
			    <div>
				    <span><?php _e( 'Search across all blogs:', 'ms-global-search' ) ?>&nbsp;</span>
				    <input class="ms-global-search_hbox" name="mssearch" type="text" value="" size="16" tabindex="1" />
				    <input type="submit" class="button" value="<?php _e( 'Search', 'ms-global-search' ) ?>" tabindex="2" />
	                
	                <?php if( $hide_options ) { ?>
                        <input title="<?php _e( 'Search on pages', 'ms-global-search' ); ?>" type="hidden" id="<?php echo $id_base.'_'.$rand2 ?>" name="msp" value="1" checked="checked" />
                        <input title="<?php _e( 'Search on all blogs', 'ms-global-search' ); ?>" type="hidden" id="<?php echo $id_base.'_'.$rand ?>" name="mswhere" value="all" checked='checked' />
                    <?php } else { ?>
    	                <span <?php if( $search_on_pages ) echo 'style="display: none"'?>>
    	                    <input title="<?php _e( 'Search on pages', 'ms-global-search' ); ?>" type="checkbox" id="<?php echo $id_base.'_'.$rand2 ?>" name="msp" value="1" <?php if( $search_on_pages ) echo 'checked="checked"'; ?> />
    	                    <?php _e( 'Search on pages', 'ms-global-search' ); ?>
    	                </span>
    	                
    	                <?php if( get_current_user_id() != 0 ) { ?>
    				    <input title="<?php _e( 'Search on all blogs', 'ms-global-search' ); ?>" type="radio" id="<?php echo "ms-global-search-form-".$rand ?>" name="mswhere" value="all" checked='checked'><?php _e( 'All', 'ms-global-search' ); ?>
    					<input title="<?php _e( 'Search only on blogs where I\'m a member', 'ms-global-search' ); ?>" type="radio" id="<?php echo "ms-global-search-form-".$rand ?>" name="mswhere" value="my"><?php _e( 'Blogs where I\'m a member', 'ms-global-search' ); ?>
    			        <?php } ?>
    			    <?php } ?>
			    </div>
		    </form>
		<?php
		} else { ?>
			<form class="ms-global-search_form" method="get" action="<?php echo get_bloginfo( 'wpurl' ).'/'.$page.'/'; ?>">
				<div>
				    <p><?php _e( 'Search across all blogs:', 'ms-global-search' ) ?></p>
				    <input class="ms-global-search_vbox" name="mssearch" type="text" value="" size="16" tabindex="1" />
				    <input type="submit" class="button" value="<?php _e( 'Search', 'ms-global-search' )?>" tabindex="2" />
				    
				    <?php if( $hide_options ) { ?>
                        <input title="<?php _e( 'Search on pages', 'ms-global-search' ); ?>" type="hidden" id="<?php echo $id_base.'_'.$rand2 ?>" name="msp" value="1" checked="checked" />
                        <input title="<?php _e( 'Search on all blogs', 'ms-global-search' ); ?>" type="hidden" id="<?php echo $id_base.'_'.$rand ?>" name="mswhere" value="all" checked='checked' />
                    <?php } else { ?>
    				    <p <?php if( $search_on_pages ) echo 'style="display: none"'?>>
    				        <input title="<?php _e( 'Search on pages', 'ms-global-search' ); ?>" type="checkbox" id="<?php echo $id_base.'_'.$rand2 ?>" name="msp" value="1" <?php if( $search_on_pages ) echo 'checked="checked"'; ?> />
    				        <?php _e( 'Search on pages', 'ms-global-search' ); ?>
    				    </p>
                        
                        <?php if( get_current_user_id() != 0 ) { ?>
    				    <p>
                            <input title="<?php _e( 'Search on all blogs', 'ms-global-search' ); ?>" type="radio" id="<?php echo "ms-global-search-form-".$rand ?>" name="mswhere" value="all" checked='checked'><?php _e( 'All', 'ms-global-search' ); ?>
    						<br />
                            <input title="<?php _e( 'Search only on blogs where I\'m a member', 'ms-global-search' ); ?>" type="radio" id="<?php echo "ms-global-search-form-".$rand ?>" name="mswhere" value="my"><?php _e( 'Blogs where I\'m a member', 'ms-global-search' ); ?>
                        </p>
    				    <?php } ?>
    				<?php } ?>
			    </div>
			</form>
		<?php
		}
	}
}

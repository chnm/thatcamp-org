<?php
/* 
 * Multisite Global Search widget class
 */

class Multisite_Global_Search extends WP_Widget {
	const horizontal = "H";
	const vertical   = "V";
		
	/**
	 * Widget actual processes.
	 */
	function Multisite_Global_Search() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'ms-global-search', 'description' => 'Adds the ability to search through blogs into your WordPress 3.0 Multisite installation. Based on my other plugin WPMU GLobal Search.' );

		/* Widget control settings. */
		$control_ops = array( 'id_base' => 'ms-global-search' );

		/* Create the widget. */
		$this->WP_Widget( 'ms-global-search', $name = __( 'Global Search', 'ms-global-search' ), $widget_ops, $control_ops );
	}
	
	/**
	 * Outputs the options form on admin.
	 */
	function form( $instance ) {
		/* Set up some default widget settings. */
		$defaults = array( 'title' => __( 'Global Search', 'ms-global-search' ), 'page' => __( 'globalsearch', 'ms-global-search' ), 'which_form' => self::vertical, 'search_pages' => 0, 'hide_options' => 0 );
		$instance = wp_parse_args( ( array ) $instance, $defaults ); ?>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'ms-global-search' ); ?>:</label><br />
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:95%;" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'page' ); ?>"><?php _e( 'Page', 'ms-global-search' ); ?>:</label><br />
			<input id="<?php echo $this->get_field_id( 'page' ); ?>" name="<?php echo $this->get_field_name( 'page' ); ?>" value="<?php echo $instance['page']; ?>" style="width:95%;" />
		</p>
		
		<p>
	 		<label for="<?php echo $this->get_field_id( 'which_form' ); ?>"><?php _e( 'Form', 'ms-global-search' ); ?>:</label><br />
	 		<input type="radio" id="<?php echo $this->get_field_id( 'which_form' ); ?>" name="<?php echo $this->get_field_name( 'which_form' ); ?>"  value="<?php echo self::horizontal ?>" <?php if( $instance['which_form']!=self::vertical ) echo "checked='checked'";?> />
	 			<?php _e( 'Horizontal', 'ms-global-search' ); ?>
			<input type="radio" id="<?php echo $this->get_field_id( 'which_form' ); ?>" name="<?php echo $this->get_field_name( 'which_form' ); ?>"  value="<?php echo self::vertical ?>" <?php if( $instance['which_form']==self::vertical ) echo "checked='checked'";?> />
				<?php _e( 'Vertical', 'ms-global-search' ); ?>
	 	</p>
		
		<p>
		    <input type="checkbox" id="<?php echo $this->get_field_id( 'search_pages' ); ?>" name="<?php echo $this->get_field_name( 'search_pages' ); ?>" value="1" <?php if ( $instance['search_pages'] ) echo "checked='checked'"; ?> />
		    <label for="<?php echo $this->get_field_id( 'search_pages' ); ?>"><?php _e( 'Searching by default on pages', 'ms-global-search' ); ?></label>
		</p>
		
		<p>
            <input type="checkbox" id="<?php echo $this->get_field_id( 'hide_options' ); ?>" name="<?php echo $this->get_field_name( 'hide_options' ); ?>" value="1" <?php if ( $instance['hide_options'] ) echo "checked='checked'"; ?> />
            <label for="<?php echo $this->get_field_id( 'hide_options' ); ?>"><?php _e( 'Disable search options', 'ms-global-search' ); ?></label>
        </p>
		
		<?php
	}

	/**
	 * Processes widget options to be saved.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags ( if needed ) and update the widget settings. */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['page'] = strip_tags( $new_instance['page'] );
		$instance['which_form'] = strip_tags ( $new_instance['which_form'] );
		$instance['search_pages'] = strip_tags ( $new_instance['search_pages'] );
        $instance['hide_options'] = strip_tags ( $new_instance['hide_options'] );
		
		return $instance;
	}
		
	/**
	 * Outputs the content of the widget.
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/* User-selected settings. */
		$title = apply_filters( 'widget_title', $instance['title'] );
		$page = $instance['page'];
		$search_pages = $instance['search_pages'];
		$hide_options = $instance['hide_options'];
		
		/* Before widget ( defined by themes ). */
		echo $before_widget;

		/* Title of widget ( before and after defined by themes ). */
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}
		
		if( $instance['which_form'] == self::horizontal ) {
			$this->ms_global_search_horizontal_form( $page, $search_pages, $hide_options );
		} else {
			$this->ms_global_search_vertical_form( $page, $search_pages, $hide_options );
		}
		
		/* After widget ( defined by themes ). */
	   echo $after_widget;
	}
	
	function ms_global_search_vertical_form( $page, $search_pages, $hide_options ) {
		if( isset( $this ) ) {
		    $id_base = $this->id_base;
		} else {
		    $id_base = 'ms-global-search';
		}
		
		$rand = rand(); $rand2 = $rand + 1; ?>
		<form class="ms-global-search_form" method="get" action="<?php echo get_bloginfo( 'wpurl' ).'/'.$page.'/'; ?>">
			<div>
			    <p><?php _e( 'Search across all blogs:', 'ms-global-search' ) ?></p>
			    <input class="ms-global-search_vbox" name="mssearch" type="text" value="" size="16" tabindex="1" />
			    <input type="submit" class="button" value="<?php _e( 'Search', 'ms-global-search' )?>" tabindex="2" />
			    
			    <?php if( $hide_options ) { ?>
			        <input title="<?php _e( 'Search on pages', 'ms-global-search' ); ?>" type="hidden" id="<?php echo $id_base.'_'.$rand2 ?>" name="msp" value="1" checked="checked" />
                    <input title="<?php _e( 'Search on all blogs', 'ms-global-search' ); ?>" type="hidden" id="<?php echo $id_base.'_'.$rand ?>" name="mswhere" value="all" checked='checked' />
                <?php } else { ?>
    			    <p <?php if( $search_pages ) echo 'style="display: none"'?>>
    			        <input title="<?php _e( 'Search on pages', 'ms-global-search' ); ?>" type="checkbox" id="<?php echo $id_base.'_'.$rand2 ?>" name="msp" value="1" <?php if( $search_pages ) echo 'checked="checked"'; ?> />
    			        <?php _e( 'Search on pages', 'ms-global-search' ); ?>
    			    </p>
    			    
    			    <?php if( get_current_user_id() != 0 ) { ?>
    			    <p>
    			    	<input title="<?php _e( 'Search on all blogs', 'ms-global-search' ); ?>" type="radio" id="<?php echo $id_base.'_'.$rand ?>" name="mswhere" value="all" checked='checked' /><?php _e( 'All', 'ms-global-search' ); ?>
    					<input title="<?php _e( 'Search only on blogs where I\'m a member', 'ms-global-search' ); ?>" type="radio" id="<?php echo $id_base.'_'.$rand ?>" name="mswhere" value="my" /><?php _e( 'Blogs where I\'m a member', 'ms-global-search' ); ?>
    			    </p>
    			    <?php } ?>
    	        <?php } ?>
		    </div>
	    </form>
	<?php
	}
		
	function ms_global_search_horizontal_form( $page, $search_pages, $hide_options ) {
		if( isset( $this ) ) {
		    $id_base = $this->id_base;
		} else {
		    $id_base = 'ms-global-search';
		}
		
		$rand = rand(); ?>
	    <form class="ms-global-search_form" method="get" action="<?php echo get_bloginfo( 'wpurl' ).'/'.$page.'/'; ?>">
		    <div>
			    <span><?php _e( 'Search across all blogs:', 'ms-global-search' ) ?>&nbsp;</span>
			    <input class="ms-global-search_hbox" name="mssearch" type="text" value="" size="16" tabindex="1" />
			    <input type="submit" class="button" value="<?php _e( 'Search', 'ms-global-search' ) ?>" tabindex="2" />
                
                <?php if( $hide_options ) { ?>
                    <input title="<?php _e( 'Search on pages', 'ms-global-search' ); ?>" type="hidden" id="<?php echo $id_base.'_'.$rand2 ?>" name="msp" value="1" checked="checked" />
                    <input title="<?php _e( 'Search on all blogs', 'ms-global-search' ); ?>" type="hidden" id="<?php echo $id_base.'_'.$rand ?>" name="mswhere" value="all" checked='checked' />
                <?php } else { ?>
                    <span <?php if( $search_pages ) echo 'style="display: none"'?>>
                        <input title="<?php _e( 'Search on pages', 'ms-global-search' ); ?>" type="checkbox" id="<?php echo $id_base.'_'.$rand2 ?>" name="msp" value="1" <?php if( $search_pages ) echo 'checked="checked"'; ?> />
                        <?php _e( 'Search on pages', 'ms-global-search' ); ?>
                    </span>
                    
    		        <?php if( get_current_user_id() != 0 ) { ?>
    			    <input title="<?php _e( 'Search on all blogs', 'ms-global-search' ); ?>" type="radio" id="<?php echo $id_base.'_'.$rand ?>" name="mswhere" value="all" checked='checked'><?php _e( 'All', 'ms-global-search' ); ?>
    				<input title="<?php _e( 'Search only on blogs where I\'m a member', 'ms-global-search' ); ?>" type="radio" id="<?php echo $id_base.'_'.$rand ?>" name="mswhere" value="my"><?php _e( 'Blogs where I\'m a member', 'ms-global-search' ); ?>
                    <?php } ?>
                <?php } ?>
		    </div>
	    </form>
	<?php
	}
}

/**
 * Register the Widget.
 */
add_action( 'widgets_init', 'ms_global_search_register' );
if( !function_exists( 'ms_global_search_register' ) ) {
	function ms_global_search_register() {
		register_widget( 'Multisite_Global_Search' );
	}
}

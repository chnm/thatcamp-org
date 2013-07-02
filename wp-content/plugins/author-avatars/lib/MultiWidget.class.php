<?php  if(!class_exists('MultiWidget')):

/*
Copyright (c) 2008 Alex Tingle.
(Based upon pattern for multi-widget, in Wordpress 2.6 wp-includes/widget.php.)

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/


/** This class wraps up lots of secret knowledge about how to make
 *  Wordpress "multi" widgets. These are widgets that allow more than one
 *  instance to be created. The standard "Text" widget is a simple example.
 *
 *  You must extend this class and over-ride three member functions.
 *  Scroll to the bottom of the file for a fully working example.
 */
class MultiWidget
{
  //
  // Interesting member variables.

  var $id_base;         ///< Root id for all widgets of this type.
  var $name;            ///< Name for this widget type.
  var $widget_options;  ///< Option array passed to wp_register_sidebar_widget()
  var $control_options; ///< Option array passed to wp_register_widget_control()

  var $number =false; ///< Unique ID number of the current instance.
  var $id =false; ///< Unique ID string of the current instance (id_base-number)


  //
  // Member functions that you must over-ride.

  /** Echo the actual widget content. Subclasses should over-ride this function
   *  to generate their widget code. */
  function widget($args,$instance)
  {
    die('function MultiWidget::widget() must be over-ridden in a sub-class.');
  }


  /** Update a particular instance.
   *  This function should check that $new_instance is set correctly.
   *  The newly calculated value of $instance should be returned. */
  function control_update($new_instance, $old_instance)
  {
    die('function MultiWidget::control_update() must be over-ridden in a sub-class.');
  }


  /** Echo a control form for the current instance. */
  function control_form($instance)
  {
    die('function MultiWidget::control_form() must be over-ridden in a sub-class.');
  }


  //
  // Functions you'll need to call.

  /** CONSTRUCTOR
   *   widget_options: passed to wp_register_sidebar_widget()
   *   - description
   *   - classname
   *   control_options: passed to wp_register_widget_control()
   *   - width
   *   - height
   */
  function MultiWidget(
      $id_base,
      $name,
      $widget_options = array(),
      $control_options = array()
    )
  {
    $this->id_base = $id_base;
    $this->name = $name;
    $this->option_name = 'multiwidget_'.$id_base;
    $this->widget_options =
      wp_parse_args( $widget_options, array('classname'=>$this->option_name) );
    $this->control_options =
      wp_parse_args( $control_options, array('id_base'=>$this->id_base) );
    // Set true when we update the data after a POST submit - makes sure we
    // don't do it twice.
    $this->updated = false;
  }


  /** Helper function to be called by control_form().
   *  Returns an HTML name for the field. */
  function get_field_name($field_name)
  {
    return 'widget-'.$this->id_base.'['.$this->number.']['.$field_name.']';
  }


  /** Helper function to be called by control_form().
   *  Returns an HTML id for the field. */
  function get_field_id($field_name)
  {
    return 'widget-'.$this->id_base.'-'.$this->number.'-'.$field_name;
  }


  /** Registers this widget-type.
   *  Must be called during the 'widget_init' action. */
  function register()
  {
    if( !$all_instances = get_option($this->option_name) )
      $all_instances = array();

    $registered = false;
    foreach( array_keys($all_instances) as $number )
    {
      // Old widgets can have null values for some reason
      if( !isset($all_instances[$number]['__multiwidget']) )
        continue;
      $this->_set($number);
      $registered = true;
      $this->_register_one($number);
    }

    // If there are none, we register the widget's existance with a
    // generic template
    if( !$registered )
    {
      $this->_set(1);
      $this->_register_one();
    }
  }


  //
  // PRIVATE FUNCTIONS. Don't worry about these.

  function _set($number)
  {
    $this->number = $number;
    $this->id = $this->id_base.'-'.$number;
  }


  function _get_widget_callback()
  {
    return array(&$this,'widget_callback');
  }


  function _get_control_callback()
  {
    return array(&$this,'control_callback');
  }


  /** Generate the actual widget content.
   *  Just finds the instance and calls widget().
   *  Do NOT over-ride this function. */
  function widget_callback($args, $widget_args = 1)
  {
    if( is_numeric($widget_args) )
      $widget_args = array( 'number' => $widget_args );
    $widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
    $this->_set( $widget_args['number'] );

    // Data is stored as array:
    //  array( number => data for that instance of the widget, ... )
    $all_instances = get_option($this->option_name);
    if( isset($all_instances[$this->number]) )
      $this->widget($args,$all_instances[$this->number]);
  }


  /** Deal with changed settings and generate the control form.
   *  Do NOT over-ride this function. */
  function control_callback($widget_args = 1)
  {
    global $wp_registered_widgets;

    if( is_numeric($widget_args) )
      $widget_args = array( 'number' => $widget_args );
    $widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );

    // Data is stored as array:
    //  array( number => data for that instance of the widget, ... )
    $all_instances = get_option($this->option_name);
    if( !is_array($all_instances) )
      $all_instances = array();

    // We need to update the data
    if( !$this->updated && !empty($_POST['sidebar']) )
    {
      // Tells us what sidebar to put the data in
      $sidebar = (string) $_POST['sidebar'];

      $sidebars_widgets = wp_get_sidebars_widgets();
      if( isset($sidebars_widgets[$sidebar]) )
        $this_sidebar =& $sidebars_widgets[$sidebar];
      else
        $this_sidebar = array();

      foreach( $this_sidebar as $_widget_id )
      {
        // Remove all widgets of this type from the sidebar.  We'll add the
        // new data in a second.  This makes sure we don't get any duplicate
        // data since widget ids aren't necessarily persistent across multiple
        // updates
        if( $this->_get_widget_callback() ==
              $wp_registered_widgets[$_widget_id]['callback'] &&
            isset($wp_registered_widgets[$_widget_id]['params'][0]['number']) )
        {
          $number = $wp_registered_widgets[$_widget_id]['params'][0]['number'];
          if( !in_array( $this->id_base.'-'.$number, (array) $_POST['widget-id'] ) )
          {
            // the widget has been removed.
            unset($all_instances[$number]);
          }
        }
      }

      foreach( (array)$_POST['widget-'.$this->id_base] as $number=>$new_instance)
      {
        $this->_set($number);
        if( isset($all_instances[$number]) )
          $instance = $this->control_update($new_instance,$all_instances[$number]);
        else
          $instance = $this->control_update($new_instance,array());
        if( !empty($instance) )
        {
          $instance['__multiwidget'] = $number;
          $all_instances[$number] = $instance;
        }
      }

      update_option($this->option_name, $all_instances);
      $this->updated = true; // So that we don't go through this more than once
    }

    // Here we echo out the form
    if( -1 == $widget_args['number'] )
    {
      // We echo out a template for a form which can be converted to a
      // specific form later via JS
      $this->_set('%i%');
      $instance = array();
    }
    else
    {
      $this->_set($widget_args['number']);
      $instance = $all_instances[ $widget_args['number'] ];
    }
    $this->control_form($instance);
  }


  /** Helper function: Registers a single instance. */
  function _register_one($number = -1)
  {
    wp_register_sidebar_widget(
        $this->id,
        $this->name,
        $this->_get_widget_callback(),
        $this->widget_options,
        array( 'number' => $number )
      );
    wp_register_widget_control(
        $this->id,
        $this->name,
        $this->_get_control_callback(),
        $this->control_options,
        array( 'number' => $number )
      );
  }

} // end class MultiWidget



/*

//
// Example MultiWidget. Use this as a template for your own.
//

class ExampleMultiWidget extends MultiWidget
{
  function ExampleMultiWidget()
  {
    $this->MultiWidget(
        'example-multi', // id_base
        'ExampleMulti', // name
        array('description'=>__('Widget which allows multiple instances', 'author-avatars'))
      );
  }


  // Echo the actual widget content. Subclasses should over-ride this function
  // to generate their widget code.
  function widget($args,$instance)
  {
    extract($args,EXTR_SKIP);
    echo $before_widget;
    echo   $before_title . $instance['title'] . $after_title;
    echo   $instance['content'];
    echo $after_widget;
  }


  // Update a particular instance.
  // This function should check that $new_instance is set correctly.
  // The newly calculated value of $instance should be returned.
  function control_update($new_instance, $old_instance)
  {
    if( !isset($new_instance['title']) ) // user clicked cancel
        return false;
    $instance = $old_instance;
    $instance['title'] = esc_html( $new_instance['title'] );
    $instance['content'] = esc_html( $new_instance['content'] );
    return $instance;
  }


  // Echo a control form for the current instance.
  // The form has inputs with names like widget-ID_BASE[$number][FIELD_NAME]
  // so that all data for that instance of the widget are stored in one
  // $_POST variable: $_POST['widget-ID_BASE'][$number]
  function control_form($instance)
  {
?>
    <p>

     <label for="<?php echo $this->get_field_id('title') ?>">
      <?php _e('Title:'); ?>
      <input class="widefat" id="<?php echo $this->get_field_id('title') ?>"
       name="<?php echo $this->get_field_name('title') ?>" type="text"
       value="<?php echo htmlspecialchars($instance['title'],ENT_QUOTES) ?>" />
     </label>

     <label for="<?php echo $this->get_field_id('content') ?>">
      <?php _e('Content:'); ?>
      <input class="widefat" id="<?php echo $this->get_field_id('content') ?>"
       name="<?php echo $this->get_field_name('content') ?>" type="text"
       value="<?php echo htmlspecialchars($instance['content'],ENT_QUOTES) ?>" />
     </label>

     <input type="hidden" id="<?php echo $this->get_field_id('submit') ?>"
      name="<?php echo $this->get_field_name('submit') ?>" value="1" />

    </p>
<?php
  }

} // end class ExampleMultiWidget


// Finally create an object for the widget-type and register it.
$example_multi = new ExampleMultiWidget();
add_action( 'widgets_init', array($example_multi,'register') );

*/
 
endif ?>

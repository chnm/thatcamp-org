<?php

/**
 * Widget Class for displaying categories.
 * Extended version of the default categories.
 */
class WP_Widget_AVH_ExtendedCategories_Normal extends WP_Widget
{

    /**
     *
     * @var AVH_EC_Core
     */
    public $core;

    /**
     * PHP 5 Constructor
     */
    public function __construct()
    {
        $this->core = & AVH_EC_Singleton::getInstance('AVH_EC_Core');

        // Convert the old option widget_extended_categories to widget_extended-categories
        $old = get_option('widget_extended_categories');
        if (!(false === $old)) {
            update_option('widget_extended-categories', $old);
            delete_option('widget_extended_categories');
        }
        $widget_ops = array('description' => __("An extended version of the default Categories widget.", 'avh-ec'));
        WP_Widget::__construct('extended-categories', 'AVH Extended Categories', $widget_ops);

        add_action('wp_print_styles', array($this, 'actionWpPrintStyles'));
    }

    public function actionWpPrintStyles()
    {
        if (!(false === is_active_widget(false, false, $this->id_base, true))) {
            wp_register_style('avhec-widget', AVHEC_PLUGIN_URL . '/css/avh-ec.widget.css', array(), $this->core->version);
            wp_enqueue_style('avhec-widget');
        }
    }

    /**
     * Display the widget
     *
     * @param unknown_type $args
     * @param unknown_type $instance
     */
    public function widget($args, $instance)
    {
        extract($args);

        $selectedonly = $instance['selectedonly'];
        $c = $instance['count'];
        $h = $instance['hierarchical'];
        $d = $instance['depth'];
        $e = $instance['hide_empty'];
        $use_desc_for_title = $instance['use_desc_for_title'];
        $s = isset($instance['sort_column']) ? $instance['sort_column'] : 'name';
        $o = isset($instance['sort_order']) ? $instance['sort_order'] : 'asc';
        $r = ($instance['rssfeed'] == true) ? 'RSS' : '';
        $i = isset($instance['rssimage']) ? $instance['rssimage'] : '';
        $invert = $instance['invert_included'];

        if (empty($r)) {
            $i = '';
        }

        if (empty($d)) {
            $d = 0;
        }

        $title = apply_filters('widget_title', empty($instance['title']) ? __('Categories', 'avh-ec') : $instance['title']);
        $style = empty($instance['style']) ? 'list' : $instance['style'];

        $included_cats = '';
        if ($instance['post_category']) {
            $post_category = unserialize($instance['post_category']);
            $children = array();
            if (!$instance['selectedonly']) {
                foreach ($post_category as $cat_id) {
                    $children = array_merge($children, get_term_children($cat_id, 'category'));
                }
            }
            $included_cats = implode(",", array_merge($post_category, $children));
        }

        if ($invert) {
            $inc_exc = 'exclude';
        } else {
            $inc_exc = 'include';
        }

        $options = $this->core->getOptions();
        $show_option_none = __('Select Category', 'avh-ec');
        if ($options['general']['alternative_name_select_category']) {
            $show_option_none = $options['general']['alternative_name_select_category'];
        }

        $cat_args = array($inc_exc => $included_cats, 'orderby' => $s, 'order' => $o, 'show_count' => $c, 'use_desc_for_title' => $use_desc_for_title, 'hide_empty' => $e, 'hierarchical' => $h, 'depth' => $d, 'title_li' => '', 'show_option_none' => $show_option_none, 'feed' => $r, 'feed_image' => $i, 'name' => 'extended-categories-select-' . $this->number);
        echo $before_widget;
        echo $this->core->comment;
        echo $before_title . $title . $after_title;

        if ($style == 'list') {
            echo '<ul>';
            $this->core->avh_wp_list_categories($cat_args);
            echo '</ul>';
        } else {
            $this->core->avh_wp_dropdown_categories($cat_args);
            echo '<script type=\'text/javascript\'>' . "\n";
            echo '/* <![CDATA[ */' . "\n";
            echo '            var ec_dropdown_' . $this->number . ' = document.getElementById("extended-categories-select-' . $this->number . '");' . "\n";
            echo '            function ec_onCatChange_' . $this->number . '() {' . "\n";
            echo '                if (ec_dropdown_' . $this->number . '.options[ec_dropdown_' . $this->number . '.selectedIndex].value > 0) {' . "\n";
            echo '                    location.href = "' . home_url() . '/?cat="+ec_dropdown_' . $this->number . '.options[ec_dropdown_' . $this->number . '.selectedIndex].value;' . "\n";
            echo '                }' . "\n";
            echo '            }' . "\n";
            echo '            ec_dropdown_' . $this->number . '.onchange = ec_onCatChange_' . $this->number . ';' . "\n";
            echo '/* ]]> */' . "\n";
            echo '</script>' . "\n";
        }
        echo $after_widget;
    }

    /**
     * When Widget Control Form Is Posted
     *
     * @param unknown_type $new_instance
     * @param unknown_type $old_instance
     * @return unknown
     */
    public function update($new_instance, $old_instance)
    {
        // update the instance's settings
        if (!isset($new_instance['submit'])) {
            return false;
        }

        $instance = $old_instance;

        $instance['title'] = strip_tags(stripslashes($new_instance['title']));
        $instance['selectedonly'] = isset($new_instance['selectedonly']);
        $instance['count'] = isset($new_instance['count']);
        $instance['hierarchical'] = isset($new_instance['hierarchical']);
        $instance['hide_empty'] = isset($new_instance['hide_empty']);
        $instance['use_desc_for_title'] = isset($new_instance['use_desc_for_title']);
        $instance['sort_column'] = strip_tags(stripslashes($new_instance['sort_column']));
        $instance['sort_order'] = strip_tags(stripslashes($new_instance['sort_order']));
        $instance['style'] = strip_tags(stripslashes($new_instance['style']));
        $instance['rssfeed'] = isset($new_instance['rssfeed']);
        $instance['rssimage'] = strip_tags(stripslashes($new_instance['rssimage']));
        if (array_key_exists('all', $new_instance['post_category'])) {
            $instance['post_category'] = false;
        } else {
            $instance['post_category'] = serialize($new_instance['post_category']);
        }
        $instance['depth'] = (int) $new_instance['depth'];
        if ($instance['depth'] < 0 || 11 < $instance['depth']) {
            $instance['depth'] = 0;
        }
        $instance['invert_included'] = isset($new_instance['invert_included']);

        return $instance;
    }

    /**
     * Display Widget Control Form
     *
     * @param unknown_type $instance
     */
    public function form($instance)
    {
        // displays the widget admin form
        $instance = wp_parse_args((array) $instance, array('title' => '', 'rssimage' => '', 'depth' => 0));

        // Prepare data for display
        $depth = (int) $instance['depth'];
        if ($depth < 0 || 11 < $depth) {
            $depth = 0;
        }
        $selected_cats = (avhGetArrayValue($instance, 'post_category') !== '') ? unserialize($instance['post_category']) : false;

        echo '<p>';
        avh_doWidgetFormText($this->get_field_id('title'), $this->get_field_name('title'), __('Title', 'avh-ec'), avhGetArrayValue($instance, 'title'));
        echo '</p>';

        echo '<p>';
        avh_doWidgetFormCheckbox($this->get_field_id('selectedonly'), $this->get_field_name('selectedonly'), __('Show selected categories only', 'avh-ec'), (bool) avhGetArrayValue($instance, 'selectedonly'));

        avh_doWidgetFormCheckbox($this->get_field_id('count'), $this->get_field_name('count'), __('Show post counts', 'avh-ec'), (bool) avhGetArrayValue($instance, 'count'));

        avh_doWidgetFormCheckbox($this->get_field_id('hierarchical'), $this->get_field_name('hierarchical'), __('Show hierarchy', 'avh-ec'), (bool) avhGetArrayValue($instance, 'hierarchical'));

        $options = array(0 => __('All Levels', 'avh-ec'), 1 => __('Toplevel only', 'avh-ec'));
        for ($i = 2; $i <= 11; $i++) {
            $options[$i] = __('Child ', 'avh-ec') . ($i - 1);
        }
        avh_doWidgetFormSelect($this->get_field_id('depth'), $this->get_field_name('depth'), __('How many levels to show', 'avh-ec'), $options, $depth);
        unset($options);

        avh_doWidgetFormCheckbox($this->get_field_id('hide_empty'), $this->get_field_name('hide_empty'), __('Hide empty categories', 'avh-ec'), (bool) avhGetArrayValue($instance, 'hide_empty'));

        avh_doWidgetFormCheckbox($this->get_field_id('use_desc_for_title'), $this->get_field_name('use_desc_for_title'), __('Use description for title', 'avh-ec'), (bool) avhGetArrayValue($instance, 'use_desc_for_title'));
        echo '</p>';

        echo '<p>';
        $options['ID'] = __('ID', 'avh-ec');
        $options['name'] = __('Name', 'avh-ec');
        $options['count'] = __('Count', 'avh-ec');
        $options['slug'] = __('Slug', 'avh-ec');
        $options['avhec_manualorder'] = 'AVH EC ' . __('Manual Order', 'avh-ec');
        if (is_plugin_active('my-category-order/mycategoryorder.php')) {
            $options['avhec_3rdparty_mycategoryorder'] = 'My Category Order';
        }

        avh_doWidgetFormSelect($this->get_field_id('sort_column'), $this->get_field_name('sort_column'), __('Sort by', 'avh-ec'), $options, avhGetArrayValue($instance, 'sort_column'));
        unset($options);

        $options['asc'] = __('Ascending', 'avh-ec');
        $options['desc'] = __('Descending', 'avh-ec');
        avh_doWidgetFormSelect($this->get_field_id('sort_order'), $this->get_field_name('sort_order'), __('Sort order', 'avh-ec'), $options, avhGetArrayValue($instance, 'sort_order'));
        unset($options);

        $options['list'] = __('List', 'avh-ec');
        $options['drop'] = __('Drop down', 'avh-ec');
        avh_doWidgetFormSelect($this->get_field_id('style'), $this->get_field_name('style'), __('Display style', 'avh-ec'), $options, avhGetArrayValue($instance, 'style'));
        unset($options);
        echo '</p>';

        echo '<p>';

        avh_doWidgetFormCheckbox($this->get_field_id('rssfeed'), $this->get_field_name('rssfeed'), __('Show RSS Feed', 'avh-ec'), (bool) avhGetArrayValue($instance, 'rssfeed'));

        avh_doWidgetFormText($this->get_field_id('rssimage'), $this->get_field_name('rssimage'), __('Path (URI) to RSS image', 'avh-ec'), avhGetArrayValue($instance, 'rssimage'));

        echo '</p>';

        echo '<p>';
        echo '<b>' . __('Select categories', 'avh-ec') . '</b><hr />';
        echo '<ul id="categorychecklist" class="list:category categorychecklist form-no-clear" style="list-style-type: none; margin-left: 5px; padding-left: 0px; margin-bottom: 20px;">';
        echo '<li id="' . $this->get_field_id('category--1') . '" class="popular-category">';
        echo '<label for="' . $this->get_field_id('post_category') . '" class="selectit">';
        echo '<input value="all" id="' . $this->get_field_id('post_category') . '" name="' . $this->get_field_name('post_category') . '[all]" type="checkbox" ' . (false === $selected_cats ? ' CHECKED' : '') . '> ';
        _e('All Categories', 'avh-ec');
        echo '</label>';
        echo '</li>';
        ob_start();
        $this->avh_wp_category_checklist($selected_cats, $this->number);
        ob_end_flush();
        echo '</ul>';
        echo '</p>';

        echo '<p>';
        avh_doWidgetFormCheckbox($this->get_field_id('invert_included'), $this->get_field_name('invert_included'), __('Exclude the selected categories', 'avh-ec'), (bool) avhGetArrayValue($instance, 'invert_included'));
        echo '</p>';

        echo '<input type="hidden" id="' . $this->get_field_id('submit') . '" name="' . $this->get_field_name('submit') . '" value="1" />';
    }

    /**
     * Creates the categories checklist
     *
     * @param int $post_id
     * @param int $descendants_and_self
     * @param array $selected_cats
     * @param array $popular_cats
     * @param int $number
     */
    public function avh_wp_category_checklist($selected_cats, $number)
    {
        $walker = new AVH_Walker_Category_Checklist();
        $walker->number = $number;
        $walker->input_id = $this->get_field_id('post_category');
        $walker->input_name = $this->get_field_name('post_category');
        $walker->li_id = $this->get_field_id('category--1');

        $args = array('taxonomy' => 'category', 'descendants_and_self' => 0, 'selected_cats' => $selected_cats, 'popular_cats' => array(), 'walker' => $walker, 'checked_ontop' => true, 'popular_cats' => array());

        if (is_array($selected_cats)) {
            $args['selected_cats'] = $selected_cats;
        } else {
            $args['selected_cats'] = array();
        }

        $categories = $this->core->getCategories();
        $_categories_id = $this->core->getCategoriesId($categories);

        // Post process $categories rather than adding an exclude to the get_terms() query to keep the query the same across all posts (for any query cache)
        $checked_categories = array();
        foreach ($args['selected_cats'] as $key => $value) {
            if (isset($_categories_id[$key])) {
                $category_key = $_categories_id[$key];
                $checked_categories[] = $categories[$category_key];
                unset($categories[$category_key]);
            }
        }

        // Put checked cats on top
        echo $walker->walk($checked_categories, 0, $args);
        // Then the rest of them
        echo $walker->walk($categories, 0, $args);
    }
}

/**
 * Widget Class for displaying the top categories
 */
class WP_Widget_AVH_ExtendedCategories_Top extends WP_Widget
{

    /**
     *
     * @var AVH_EC_Core
     */
    public $core;

    /**
     * PHP 5 Constructor
     */
    public function __construct()
    {
        $this->core = & AVH_EC_Singleton::getInstance('AVH_EC_Core');

        $widget_ops = array('description' => __("Shows the top categories.", 'avh-ec'));
        WP_Widget::__construct(false, 'AVH Extended Categories: ' . __('Top Categories'), $widget_ops);
        add_action('wp_print_styles', array($this, 'actionWpPrintStyles'));
    }

    public function actionWpPrintStyles()
    {
        if (!(false === is_active_widget(false, false, $this->id_base, true))) {
            wp_register_style('avhec-widget', AVHEC_PLUGIN_URL . '/css/avh-ec.widget.css', array(), $this->core->version);
            wp_enqueue_style('avhec-widget');
        }
    }

    /**
     * Echo the widget content.
     *
     * Subclasses should over-ride this function to generate their widget code.
     *
     * @param array $args
     *            Display arguments including before_title, after_title, before_widget, and after_widget.
     * @param array $instance
     *            The settings for the particular instance of the widget
     */
    public function widget($args, $instance)
    {
        extract($args);

        $title = apply_filters('widget_title', empty($instance['title']) ? __('Categories', 'avh-ec') : $instance['title']);
        $style = empty($instance['style']) ? 'list' : $instance['style'];
        if (!$a = (int) $instance['amount']) {
            $a = 5;
        } elseif ($a < 1) {
            $a = 1;
        }
        $c = $instance['count'];
        $use_desc_for_title = $instance['use_desc_for_title'];
        $s = isset($instance['sort_column']) ? $instance['sort_column'] : 'name';
        $o = isset($instance['sort_order']) ? $instance['sort_order'] : 'asc';
        $r = ($instance['rssfeed'] === true) ? 'RSS' : '';
        $i = isset($instance['rssimage']) ? $instance['rssimage'] : '';
        if (empty($r)) {
            $i = '';
        }
        if (!empty($i)) {
            if (!file_exists(ABSPATH . '/' . $i)) {
                $i = '';
            }
        }

        $options = $this->core->getOptions();
        $show_option_none = __('Select Category', 'avh-ec');
        if ($options['general']['alternative_name_select_category']) {
            $show_option_none = $options['general']['alternative_name_select_category'];
        }

        $top_cats = get_terms('category', array('fields' => 'ids', 'orderby' => 'count', 'order' => 'DESC', 'number' => $a, 'hierarchical' => false));
        $included_cats = implode(",", $top_cats);

        $cat_args = array('include' => $included_cats, 'orderby' => $s, 'order' => $o, 'show_count' => $c, 'use_desc_for_title' => $use_desc_for_title, 'hide_empty' => false, 'hierarchical' => false, 'depth' => -1, 'title_li' => '', 'show_option_none' => $show_option_none, 'feed' => $r, 'feed_image' => $i, 'name' => 'extended-categories-top-select-' . $this->number);
        echo $before_widget;
        echo $this->core->comment;
        echo $before_title . $title . $after_title;
        echo '<ul>';

        if ($style == 'list') {
            wp_list_categories($cat_args);
        } else {
            wp_dropdown_categories($cat_args);
            echo '<script type=\'text/javascript\'>' . "\n";
            echo '/* <![CDATA[ */' . "\n";
            echo '            var ec_dropdown_top_' . $this->number . ' = document.getElementById("extended-categories-top-select-' . $this->number . '");' . "\n";
            echo '            function ec_top_onCatChange_' . $this->number . '() {' . "\n";
            echo '                if (ec_dropdown_top_' . $this->number . '.options[ec_dropdown_top_' . $this->number . '.selectedIndex].value > 0) {' . "\n";
            echo '                    location.href = "' . get_option('home') . '/?cat="+ec_dropdown_top_' . $this->number . '.options[ec_dropdown_top_' . $this->number . '.selectedIndex].value;' . "\n";
            echo '                }' . "\n";
            echo '            }' . "\n";
            echo '            ec_dropdown_top_' . $this->number . '.onchange = ec_top_onCatChange_' . $this->number . ';' . "\n";
            echo '/* ]]> */' . "\n";
            echo '</script>' . "\n";
        }
        echo '</ul>';
        echo $after_widget;
    }

    /**
     * Update a particular instance.
     *
     * This function should check that $new_instance is set correctly.
     * The newly calculated value of $instance should be returned.
     * If "false" is returned, the instance won't be saved/updated.
     *
     * @param array $new_instance
     *            New settings for this instance as input by the user via form()
     * @param array $old_instance
     *            Old settings for this instance
     * @return array Settings to save or bool false to cancel saving
     */
    public function update($new_instance, $old_instance)
    {
        // update the instance's settings
        if (!isset($new_instance['submit'])) {
            return false;
        }

        $instance = $old_instance;

        $instance['title'] = strip_tags(stripslashes($new_instance['title']));
        $instance['amount'] = (int) $new_instance['amount'];
        $instance['count'] = isset($new_instance['count']);
        $instance['use_desc_for_title'] = isset($new_instance['use_desc_for_title']);
        $instance['sort_column'] = strip_tags(stripslashes($new_instance['sort_column']));
        $instance['sort_order'] = strip_tags(stripslashes($new_instance['sort_order']));
        $instance['style'] = strip_tags(stripslashes($new_instance['style']));
        $instance['rssfeed'] = isset($new_instance['rssfeed']);
        $instance['rssimage'] = strip_tags(stripslashes($new_instance['rssimage']));

        return $instance;
    }

    /**
     * Echo the settings update form
     *
     * @param array $instance
     *            Current settings
     */
    public function form($instance)
    {
        // displays the widget admin form
        $instance = wp_parse_args((array) $instance, array('title' => '', 'rssimage' => '', 'amount' => '5'));

        $amount = (int) avhGetArrayValue($instance, 'amount');
        if ($amount < 1) {
            $amount = 1;
        }
        echo '<p>';
        avh_doWidgetFormText($this->get_field_id('title'), $this->get_field_name('title'), __('Title', 'avh-ec'), avhGetArrayValue($instance, 'title'));
        echo '</p>';

        echo '<p>';
        avh_doWidgetFormText($this->get_field_id('amount'), $this->get_field_name('amount'), __('How many categories to show', 'avh-ec'), $amount);
        echo '</p>';

        echo '<p>';
        avh_doWidgetFormCheckbox($this->get_field_id('count'), $this->get_field_name('count'), __('Show post counts', 'avh-ec'), (bool) avhGetArrayValue($instance, 'count'));
        echo '<br />';

        avh_doWidgetFormCheckbox($this->get_field_id('use_desc_for_title'), $this->get_field_name('use_desc_for_title'), __('Use description for title', 'avh-ec'), (bool) avhGetArrayValue($instance, 'use_desc_for_title'));
        echo '</p>';

        echo '<p>';
        $options['ID'] = __('ID', 'avh-ec');
        $options['name'] = __('Name', 'avh-ec');
        $options['count'] = __('Count', 'avh-ec');
        $options['slug'] = __('Slug', 'avh-ec');
        avh_doWidgetFormSelect($this->get_field_id('sort_column'), $this->get_field_name('sort_column'), __('Sort by', 'avh-ec'), $options, avhGetArrayValue($instance, 'sort_column'));
        unset($options);

        $options['asc'] = __('Ascending', 'avh-ec');
        $options['desc'] = __('Descending', 'avh-ec');
        avh_doWidgetFormSelect($this->get_field_id('sort_order'), $this->get_field_name('sort_order'), __('Sort order', 'avh-ec'), $options, avhGetArrayValue($instance, 'sort_order'));
        unset($options);

        $options['list'] = __('List', 'avh-ec');
        $options['drop'] = __('Drop down', 'avh-ec');
        avh_doWidgetFormSelect($this->get_field_id('style'), $this->get_field_name('style'), __('Display style', 'avh-ec'), $options, avhGetArrayValue($instance, 'style'));
        unset($options);
        echo '</p>';

        echo '<p>';

        avh_doWidgetFormCheckbox($this->get_field_id('rssfeed'), $this->get_field_name('rssfeed'), __('Show RSS Feed', 'avh-ec'), (bool) avhGetArrayValue($instance, 'rssfeed'));

        avh_doWidgetFormText($this->get_field_id('rssimage'), $this->get_field_name('rssimage'), __('Path (URI) to RSS image', 'avh-ec'), avhGetArrayValue($instance, 'rssimage'));

        echo '</p>';

        echo '<input type="hidden" id="' . $this->get_field_id('submit') . '" name="' . $this->get_field_name('submit') . '" value="1" />';
    }
}

/**
 * Widget Class for displaying the grouped categories
 */
class WP_Widget_AVH_ExtendedCategories_Category_Group extends WP_Widget
{

    /**
     *
     * @var AVH_EC_Core
     */
    public $core;

    /**
     *
     * @var AVH_EC_Category_Group
     */
    public $catgrp;

    /**
     * PHP 5 Constructor
     */
    public function __construct()
    {
        $this->core = & AVH_EC_Singleton::getInstance('AVH_EC_Core');
        $this->catgrp = & AVH_EC_Singleton::getInstance('AVH_EC_Category_Group');

        $widget_ops = array('description' => __("Shows grouped categories.", 'avh-ec'));
        WP_Widget::__construct(false, 'AVH Extended Categories: ' . __('Category Group'), $widget_ops);
        add_action('wp_print_styles', array($this, 'actionWpPrintStyles'));
    }

    public function actionWpPrintStyles()
    {
        if (!(false === is_active_widget(false, false, $this->id_base, true))) {
            wp_register_style('avhec-widget', AVHEC_PLUGIN_URL . '/css/avh-ec.widget.css', array(), $this->core->version);
            wp_enqueue_style('avhec-widget');
        }
    }

    /**
     * Display the widget
     *
     * @param unknown_type $args
     * @param unknown_type $instance
     */
    public function widget($args, $instance)
    {
        global $post, $wp_query;

        $catgrp = & AVH_EC_Singleton::getInstance('AVH_EC_Category_Group');
        $options = $this->core->getOptions();

        $row = array();

        if (is_home()) {
            $special_page = 'home_group';
        } elseif (is_category()) {
            $special_page = 'category_group';
        } elseif (is_day()) {
            $special_page = 'day_group';
        } elseif (is_month()) {
            $special_page = 'month_group';
        } elseif (is_year()) {
            $special_page = 'year_group';
        } elseif (is_author()) {
            $special_page = 'author_group';
        } elseif (is_search()) {
            $special_page = 'search_group';
        } else {
            $special_page = 'none';
        }

        $toDisplay = false;
        if ('none' == $special_page) {
            $terms = wp_get_object_terms($post->ID, $catgrp->taxonomy_name);
            if (!empty($terms)) {
                $selected_catgroups = unserialize($instance['post_group_category']);
                foreach ($terms as $key => $value) {
                    if ($selected_catgroups === false || array_key_exists($value->term_id, $selected_catgroups)) {
                        if (!($this->getWidgetDoneCatGroup($value->term_id))) {
                            $row = $value;
                            $group_found = true;
                            break;
                        }
                    }
                }
            } else {
                $options = $this->core->options;
                $no_cat_group = $options['cat_group']['no_group'];
                $row = get_term_by('id', $no_cat_group, $catgrp->taxonomy_name);
                $group_found = true;
            }
        } else {
            if ('category_group' == $special_page) {
                $tax_meta = get_option($this->core->db_options_tax_meta);
                $term = $wp_query->get_queried_object();
                if (isset($tax_meta[$term->taxonomy][$term->term_id]['category_group_term_id'])) {
                    $sp_category_group_id = $tax_meta[$term->taxonomy][$term->term_id]['category_group_term_id'];
                } else {
                    $sp_category_group = $this->catgrp->getGroupByCategoryID($term->term_id);
                    $sp_category_group_id = $sp_category_group->term_id;
                }
            } else {
                $sp_category_group_id = $options['sp_cat_group'][$special_page];
            }
            $row = get_term_by('id', $sp_category_group_id, $catgrp->taxonomy_name); // Returns false when non-existance. (empty(false)=true)
            $group_found = true;
        }

        if ($group_found) {
            $toDisplay = true;
            $category_group_id_none = $this->catgrp->getTermIDBy('slug', 'none');
            $selected_catgroups = unserialize($instance['post_group_category']);

            if ($category_group_id_none == $row->term_id) {
                $toDisplay = false;
            } elseif (!(false == $selected_catgroups || array_key_exists($row->term_id, $selected_catgroups))) {
                $toDisplay = false;
            } elseif ($special_page != 'none' && $this->getWidgetDoneCatGroup($sp_category_group_id)) {
                $toDisplay = false;
            }
        }

        if ($toDisplay) {
            extract($args);

            $c = $instance['count'];
            $e = $instance['hide_empty'];
            $h = $instance['hierarchical'];
            $use_desc_for_title = $instance['use_desc_for_title'];
            $s = isset($instance['sort_column']) ? $instance['sort_column'] : 'name';
            $o = isset($instance['sort_order']) ? $instance['sort_order'] : 'asc';
            $r = $instance['rssfeed'] ? 'RSS' : '';
            $i = $instance['rssimage'] ? $instance['rssimage'] : '';

            if (empty($r)) {
                $i = '';
            }

            $style = empty($instance['style']) ? 'list' : $instance['style'];
            $group_id = $row->term_id;
            $cats = $catgrp->getCategoriesFromGroup($group_id);
            if (empty($instance['title'])) {
                $title = $catgrp->getWidgetTitleForGroup($group_id);
                if (!$title) {
                    $title = __('Categories', 'avh-ec');
                }
            } else {
                $title = $instance['title'];
            }
            $title = apply_filters('widget_title', $title);

            $included_cats = implode(',', $cats);

            $show_option_none = __('Select Category', 'avh-ec');
            if ($options['general']['alternative_name_select_category']) {
                $show_option_none = $options['general']['alternative_name_select_category'];
            }

            $cat_args = array('include' => $included_cats, 'orderby' => $s, 'order' => $o, 'show_count' => $c, 'use_desc_for_title' => $use_desc_for_title, 'hide_empty' => $e, 'hierarchical' => $h, 'title_li' => '', 'show_option_none' => $show_option_none, 'feed' => $r, 'feed_image' => $i, 'name' => 'extended-categories-select-group-' . $this->number);
            echo $before_widget;
            echo $this->core->comment;
            echo $before_title . $title . $after_title;

            if ($style == 'list') {
                echo '<ul>';
                $this->core->avh_wp_list_categories($cat_args, true);
                echo '</ul>';
            } else {
                $this->core->avh_wp_dropdown_categories($cat_args, true);
                echo '<script type=\'text/javascript\'>' . "\n";
                echo '/* <![CDATA[ */' . "\n";
                echo '            var ec_dropdown_' . $this->number . ' = document.getElementById("extended-categories-select-group-' . $this->number . '");' . "\n";
                echo '            function ec_onCatChange_' . $this->number . '() {' . "\n";
                echo '                if (ec_dropdown_' . $this->number . '.options[ec_dropdown_' . $this->number . '.selectedIndex].value > 0) {' . "\n";
                echo '                    location.href = "' . get_option('home') . '/?cat="+ec_dropdown_' . $this->number . '.options[ec_dropdown_' . $this->number . '.selectedIndex].value;' . "\n";
                echo '                }' . "\n";
                echo '            }' . "\n";
                echo '            ec_dropdown_' . $this->number . '.onchange = ec_onCatChange_' . $this->number . ';' . "\n";
                echo '/* ]]> */' . "\n";
                echo '</script>' . "\n";
            }
            echo $after_widget;
        }
    }

    /**
     * When Widget Control Form Is Posted
     *
     * @param unknown_type $new_instance
     * @param unknown_type $old_instance
     * @return unknown
     */
    public function update($new_instance, $old_instance)
    {
        // update the instance's settings
        if (!isset($new_instance['submit'])) {
            return false;
        }

        $instance = $old_instance;

        $instance['title'] = strip_tags(stripslashes($new_instance['title']));
        $instance['count'] = isset($new_instance['count']);
        $instance['hierarchical'] = isset($new_instance['hierarchical']);
        $instance['hide_empty'] = isset($new_instance['hide_empty']);
        $instance['use_desc_for_title'] = isset($new_instance['use_desc_for_title']);
        $instance['sort_column'] = strip_tags(stripslashes($new_instance['sort_column']));
        $instance['sort_order'] = strip_tags(stripslashes($new_instance['sort_order']));
        $instance['style'] = strip_tags(stripslashes($new_instance['style']));
        $instance['rssfeed'] = isset($new_instance['rssfeed']);
        $instance['rssimage'] = strip_tags(stripslashes($new_instance['rssimage']));
        if (array_key_exists('all', $new_instance['post_group_category'])) {
            $instance['post_group_category'] = false;
        } else {
            $instance['post_group_category'] = serialize($new_instance['post_group_category']);
        }

        return $instance;
    }

    /**
     * Display Widget Control Form
     *
     * @param unknown_type $instance
     */
    public function form($instance)
    {
        // displays the widget admin form
        $instance = wp_parse_args((array) $instance, array('title' => '', 'rssimage' => ''));

        $selected_cats = (avhGetArrayValue($instance, 'post_group_category') !== '') ? unserialize($instance['post_group_category']) : false;
        ob_start();
        echo '<p>';
        avh_doWidgetFormText($this->get_field_id('title'), $this->get_field_name('title'), __('Title', 'avh-ec'), $instance['title']);
        echo '</p>';

        echo '<p>';

        avh_doWidgetFormCheckbox($this->get_field_id('count'), $this->get_field_name('count'), __('Show post counts', 'avh-ec'), (bool) avhGetArrayValue($instance, 'count'));

        avh_doWidgetFormCheckbox($this->get_field_id('hierarchical'), $this->get_field_name('hierarchical'), __('Show hierarchy', 'avh-ec'), (bool) avhGetArrayValue($instance, 'hierarchical'));

        avh_doWidgetFormCheckbox($this->get_field_id('hide_empty'), $this->get_field_name('hide_empty'), __('Hide empty categories', 'avh-ec'), (bool) avhGetArrayValue($instance, 'hide_empty'));

        avh_doWidgetFormCheckbox($this->get_field_id('use_desc_for_title'), $this->get_field_name('use_desc_for_title'), __('Use description for title', 'avh-ec'), (bool) avhGetArrayValue($instance, 'use_desc_for_title'));
        echo '</p>';

        echo '<p>';
        $options['ID'] = __('ID', 'avh-ec');
        $options['name'] = __('Name', 'avh-ec');
        $options['count'] = __('Count', 'avh-ec');
        $options['slug'] = __('Slug', 'avh-ec');
        avh_doWidgetFormSelect($this->get_field_id('sort_column'), $this->get_field_name('sort_column'), __('Sort by', 'avh-ec'), $options, avhGetArrayValue($instance, 'sort_column'));
        unset($options);

        $options['asc'] = __('Ascending', 'avh-ec');
        $options['desc'] = __('Descending', 'avh-ec');
        avh_doWidgetFormSelect($this->get_field_id('sort_order'), $this->get_field_name('sort_order'), __('Sort order', 'avh-ec'), $options, avhGetArrayValue($instance, 'sort_order'));
        unset($options);

        $options['list'] = __('List', 'avh-ec');
        $options['drop'] = __('Drop down', 'avh-ec');
        avh_doWidgetFormSelect($this->get_field_id('style'), $this->get_field_name('style'), __('Display style', 'avh-ec'), $options, avhGetArrayValue($instance, 'style'));
        unset($options);
        echo '</p>';

        echo '<p>';

        avh_doWidgetFormCheckbox($this->get_field_id('rssfeed'), $this->get_field_name('rssfeed'), __('Show RSS Feed', 'avh-ec'), (bool) avhGetArrayValue($instance, 'rssfeed'));

        avh_doWidgetFormText($this->get_field_id('rssimage'), $this->get_field_name('rssimage'), __('Path (URI) to RSS image', 'avh-ec'), avhGetArrayValue($instance, 'rssimage'));
        echo '</p>';

        echo '<p>';
        echo '<b>' . __('Select Groups', 'avh-ec') . '</b><hr />';
        echo '<ul id="categorychecklist" class="list:category categorychecklist form-no-clear" style="list-style-type: none; margin-left: 5px; padding-left: 0px; margin-bottom: 20px;">';
        echo '<li id="' . $this->get_field_id('group_category--1') . '" class="popular-group_category">';
        echo '<label for="' . $this->get_field_id('group_post_category') . '" class="selectit">';
        echo '<input value="all" id="' . $this->get_field_id('group_post_category') . '" name="' . $this->get_field_name('post_group_category') . '[all]" type="checkbox" ' . (false === $selected_cats ? ' CHECKED' : '') . '> ';
        _e('Any Group', 'avh-ec');
        echo '</label>';
        echo '</li>';

        $this->avh_wp_group_category_checklist($selected_cats, $this->number);

        echo '</ul>';
        echo '</p>';

        echo '<input type="hidden" id="' . $this->get_field_id('submit') . '" name="' . $this->get_field_name('submit') . '" value="1" />';
        ob_end_flush();
    }

    public function avh_wp_group_category_checklist($selected_cats, $number)
    {
        $walker = new AVH_Walker_Category_Checklist();
        $walker->number = $number;
        $walker->input_id = $this->get_field_id('post_group_category');
        $walker->input_name = $this->get_field_name('post_group_category');
        $walker->li_id = $this->get_field_id('group_category--1');

        $args = array('taxonomy' => 'avhec_catgroup', 'descendants_and_self' => 0, 'selected_cats' => array(), 'popular_cats' => array(), 'walker' => $walker, 'checked_ontop' => true);

        if (is_array($selected_cats)) {
            $args['selected_cats'] = $selected_cats;
        } else {
            $args['selected_cats'] = array();
        }

        $categories = (array) get_terms($args['taxonomy'], array('get' => 'all'));

        // Post process $categories rather than adding an exclude to the get_terms() query to keep the query the same across all posts (for any query cache)
        $checked_categories = array();
        $keys = array_keys($categories);

        foreach ($keys as $k) {
            if (in_array($categories[$k]->term_id, $args['selected_cats'])) {
                $checked_categories[] = $categories[$k];
                unset($categories[$k]);
            }
        }

        // Put checked cats on top
        echo $walker->walk($checked_categories, 0, $args);
        // Then the rest of them
        echo $walker->walk($categories, 0, $args);
    }

    public function getWidgetDoneCatGroup($id)
    {
        $catgrp = & AVH_EC_Singleton::getInstance('AVH_EC_Category_Group');
        if (is_array($catgrp->widget_done_catgroup) && array_key_exists($id, $catgrp->widget_done_catgroup)) {
            return true;
        }
        $catgrp->widget_done_catgroup[$id] = true;

        return false;
    }
}

/**
 * Class that will display the categories
 */
class AVH_Walker_Category_Checklist extends Walker
{

    public $tree_type = 'category';

    public $db_fields = array('parent' => 'parent', 'id' => 'term_id'); // TODO: decouple this
    public $number;

    public $input_id;

    public $input_name;

    public $li_id;

    /**
     * Display array of elements hierarchically.
     *
     * It is a generic function which does not assume any existing order of
     * elements. max_depth = -1 means flatly display every element. max_depth =
     * 0 means display all levels. max_depth > 0 specifies the number of
     * display levels.
     *
     * @since 2.1.0
     *
     * @param array $elements
     * @param int $max_depth
     * @param array $args;
     * @return string
     */
    public function walk($elements, $max_depth)
    {
        $args = array_slice(func_get_args(), 2);
        $output = '';

        if ($max_depth < -1) {
            return $output;
        }

        if (empty($elements)) { // nothing to walk
            return $output;
        }

        $id_field = $this->db_fields['id'];
        $parent_field = $this->db_fields['parent'];

        // flat display
        if (-1 == $max_depth) {
            $empty_array = array();
            foreach ($elements as $e) {
                $this->display_element($e, $empty_array, 1, 0, $args, $output);
            }

            return $output;
        }

        /*
         * need to display in hierarchical order separate elements into two buckets: top level and children elements children_elements is two dimensional array, eg. children_elements[10][] contains all sub-elements whose parent is 10.
         */
        $top_level_elements = array();
        $children_elements = array();
        foreach ($elements as $e) {
            if (0 == $e->$parent_field) {
                $top_level_elements[] = $e;
            } else {
                $children_elements[$e->$parent_field][] = $e;
            }
        }

        /*
         * when none of the elements is top level assume the first one must be root of the sub elements
         */
        if (empty($top_level_elements)) {

            $first = array_slice($elements, 0, 1);
            $root = $first[0];

            $top_level_elements = array();
            $children_elements = array();
            foreach ($elements as $e) {
                if ($root->$parent_field == $e->$parent_field) {
                    $top_level_elements[] = $e;
                } else {
                    $children_elements[$e->$parent_field][] = $e;
                }
            }
        }

        foreach ($top_level_elements as $e) {
            $this->display_element($e, $children_elements, $max_depth, 0, $args, $output);
        }

        /*
         * if we are displaying all levels, and remaining children_elements is not empty, then we got orphans, which should be displayed regardless
         */
        if (($max_depth == 0) && count($children_elements) > 0) {
            $empty_array = array();
            foreach ($children_elements as $orphans) {
                foreach ($orphans as $op) {
                    $this->display_element($op, $empty_array, 1, 0, $args, $output);
                }
            }
        }

        return $output;
    }

    public function start_lvl(&$output, $depth = 0, $args = array())
    {
        $indent = str_repeat("\t", $depth);
        $output .= $indent . '<ul class="children">' . "\n";
    }

    public function end_lvl(&$output, $depth = 0, $args = array())
    {
        $indent = str_repeat("\t", $depth);
        $output .= $indent . '</ul>' . "\n";
    }

    public function start_el(&$output, $object, $depth = 0, $args = array(), $current_object_id = 0)
    {
        extract($args);
        if (!isset($selected_cats)) {
            $selected_cats = array();
        }
        $input_id = $this->input_id . '-' . $object->term_id;
        $output .= "\n" . '<li id="' . $this->li_id . '">';
        $output .= '<label for="' . $input_id . '" class="selectit">';
        $output .= '<input value="' . $object->term_id . '" type="checkbox" name="' . $this->input_name . '[' . $object->term_id . ']" id="' . $input_id . '"' . (in_array($object->term_id, $selected_cats) ? ' checked="checked"' : "") . '/> ' . esc_html(apply_filters('the_category', $object->name)) . '</label>';
    }

    public function end_el(&$output, $object, $depth = 0, $args = array())
    {
        $output .= "</li>\n";
    }
}

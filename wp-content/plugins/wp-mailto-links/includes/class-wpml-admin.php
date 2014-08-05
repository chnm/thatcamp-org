<?php defined('ABSPATH') OR die('No direct access.');

/**
 * Class WPML_Admin
 * @package WP_Mailto_Links
 * @category WordPress Plugins
 */
if (!class_exists('WPML_Admin')):

class WPML_Admin {

    /**
     * Options to be saved
     * @var array
     */
    protected $default_options = array(
        'convert_emails' => 1,
        'protect' => 1,
        'filter_body' => 1,
        'filter_posts' => 1,
        'filter_comments' => 1,
        'filter_widgets' => 1,
        'filter_rss' => 1,
        'filter_head' => 1,
        'protection_text' => '*protected email*',
        'icon' => 0,
        'image_no_icon' => 0,
        'no_icon_class' => 'no-mail-icon',
        'class_name' => 'mail-link',
        'widget_logic_filter' => 0,
        'own_admin_menu' => 0,
    );

    /**
     * @var array
     */
    protected $options = array();

    /**
     * Constructor
     */
    public function __construct() {
        // load text domain for translations
        load_plugin_textdomain(WP_MAILTO_LINKS_DOMAIN, FALSE, dirname(plugin_basename(WP_MAILTO_LINKS_FILE)) . '/languages/');

        // set option values
        $this->set_options();

        // set uninstall hook
        register_uninstall_hook(WP_MAILTO_LINKS_FILE, array('WPML_Admin', 'uninstall'));

        // add actions
        add_action('admin_init', array($this, 'admin_init'));
        add_action('admin_menu', array($this, 'admin_menu'));
    }

    /**
     * Set options from save values or defaults
     */
    private function set_options($saved_options = null) {
        $previous_version = get_option('wpml_version');

        // first set defaults
        $this->options = $this->default_options;

        if ($saved_options === null) {
            // set options
            $saved_options = get_option(WP_MAILTO_LINKS_OPTIONS_NAME);

            // backwards compatible (old values)
            if (empty($saved_options)) {
                $saved_options = get_option('WP_Mailto_Linksoptions');
            }
        }

        // set all options
        if (!empty($saved_options)) {
            foreach ($saved_options AS $key => $value) {
                $this->options[$key] = $value;
            }
        }

        // check upgrade
        if ($previous_version != WP_MAILTO_LINKS_VERSION) {
            // update version
            update_option('wpml_version', WP_MAILTO_LINKS_VERSION);
        }

        // set widget_content filter of Widget Logic plugin
        $widget_logic_opts = get_option('widget_logic');
        if (is_array($widget_logic_opts) && key_exists('widget_logic-options-filter', $widget_logic_opts)) {
            $this->options['widget_logic_filter'] = ($widget_logic_opts['widget_logic-options-filter'] == 'checked') ? 1 : 0;
        }
    }

    /**
     * Method for test purpuses
     */
    public function __options($values = null) {
        if (class_exists('Test_WP_Mailto_Links')) {
            if ($values !== null) {
                $this->set_options($values);
            }

            return $this->options;
        }
    }

    /**
     * Uninstall Callback
     */
    public static function uninstall() {
        delete_option(WP_MAILTO_LINKS_OPTIONS_NAME);
        unregister_setting(WP_MAILTO_LINKS_KEY, WP_MAILTO_LINKS_OPTIONS_NAME);
    }

    /**
     * admin_init action
     */
    public function admin_init() {
        // register settings
        register_setting(WP_MAILTO_LINKS_KEY, WP_MAILTO_LINKS_OPTIONS_NAME);

        // actions and filters
        add_action('admin_notices', array($this, 'show_notices'));
        add_filter('plugin_action_links', array($this, 'plugin_action_links'), 10, 2);
    }

    /**
     * Callback add links on plugin page
     * @param array $links
     * @param string $file
     * @return array
     */
    public function plugin_action_links($links, $file) {
        if ($file == plugin_basename(WP_MAILTO_LINKS_FILE)) {
            $page = ($this->options['own_admin_menu']) ? 'admin.php' : 'options-general.php';
            $settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/' . $page . '?page=' . WP_MAILTO_LINKS_ADMIN_PAGE . '">' . __('Settings', WP_MAILTO_LINKS_DOMAIN) . '</a>';
            array_unshift($links, $settings_link);
        }

        return $links;
    }

    /**
     * admin_menu action
     */
    public function admin_menu() {
        // add page and menu item
        if ($this->options['own_admin_menu']) {
        // create main menu item
            $page_hook = add_menu_page(__('WP Mailto Links', WP_MAILTO_LINKS_DOMAIN), __('WP Mailto Links', WP_MAILTO_LINKS_DOMAIN),
                                'manage_options', WP_MAILTO_LINKS_ADMIN_PAGE, array($this, 'show_options_page'),
                                plugins_url('images/icon-wp-mailto-links-16.png', WP_MAILTO_LINKS_FILE));
        } else {
        // create submenu item under "Settings"
            $page_hook = add_submenu_page('options-general.php', __('WP Mailto Links', WP_MAILTO_LINKS_DOMAIN), __('WP Mailto Links', WP_MAILTO_LINKS_DOMAIN),
                                'manage_options', WP_MAILTO_LINKS_ADMIN_PAGE, array($this, 'show_options_page'));
        }

        // load plugin page
        add_action('load-' . $page_hook, array($this, 'load_options_page'));
    }

    /* -------------------------------------------------------------------------
     *  Admin Options Page
     * ------------------------------------------------------------------------*/

    /**
     * show notices
     */
    public function show_notices() {
        if (isset($_GET['page']) && $_GET['page'] == WP_MAILTO_LINKS_ADMIN_PAGE && is_plugin_active('email-encoder-bundle/email-encoder-bundle.php')) {
            echo '<div class="error fade"><p>';
            _e('<strong>Warning:</strong> "Email Encoder Bundle"-plugin is also activated, which could cause conflicts on encoding email addresses and mailto links.', WP_MAILTO_LINKS_DOMAIN);
            echo '</p></div>';
        }
    }

    /**
     * Load admin options page
     */
    public function load_options_page() {
        // set dashboard postbox
        wp_enqueue_script('dashboard');

        // add plugin script
        wp_enqueue_script('wp_mailto_links_admin', plugins_url('js/wp-mailto-links-admin.js', WP_MAILTO_LINKS_FILE), array('jquery'), WP_MAILTO_LINKS_VERSION);

        // add help tabs
        $this->add_help_tabs();

        // screen settings
        if (function_exists('add_screen_option')) {
            add_screen_option('layout_columns', array(
                'max' => 2,
                'default' => 2
            ));
        }

        // add meta boxes
        add_meta_box('general_settings', __('General Settings', WP_MAILTO_LINKS_DOMAIN), array($this, 'show_meta_box_content'), null, 'normal', 'core', array('general_settings'));
        add_meta_box('style_settings', __('Style Settings', WP_MAILTO_LINKS_DOMAIN), array($this, 'show_meta_box_content'), null, 'normal', 'core', array('style_settings'));
        add_meta_box('admin_settings', __('Admin Settings', WP_MAILTO_LINKS_DOMAIN), array($this, 'show_meta_box_content'), null, 'normal', 'core', array('admin_settings'));
        add_meta_box('this_plugin', __('Support', WP_MAILTO_LINKS_DOMAIN), array($this, 'show_meta_box_content'), NULL, 'side', 'core', array('this_plugin'));
        add_meta_box('other_plugins', __('Other Plugins', WP_MAILTO_LINKS_DOMAIN), array($this, 'show_meta_box_content'), null, 'side', 'core', array('other_plugins'));
    }

    /**
     * Show admin options page
     */
    public function show_options_page() {
        $this->set_options();
?>
        <div class="wrap">
            <div class="icon32" id="icon-options-custom" style="background:url(<?php echo plugins_url('images/icon-wp-mailto-links.png', WP_MAILTO_LINKS_FILE) ?>) no-repeat 50% 50%"><br></div>
            <h2><?php echo get_admin_page_title() ?> - <em><small><?php _e('Manage Email Links', WP_MAILTO_LINKS_DOMAIN) ?></small></em></h2>

            <?php if (isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true' && $this->options['own_admin_menu']): ?>
            <div class="updated settings-error" id="setting-error-settings_updated">
                <p><strong><?php _e('Settings saved.') ?></strong></p>
            </div>
            <?php endif; ?>

            <form method="post" action="options.php">
                <?php settings_fields(WP_MAILTO_LINKS_KEY); ?>

                <input type="hidden" name="<?php echo WP_MAILTO_LINKS_KEY ?>_nonce" value="<?php echo wp_create_nonce(WP_MAILTO_LINKS_KEY) ?>" />
                <?php wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false); ?>
                <?php wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false); ?>

                <div id="poststuff">
                    <div id="post-body" class="metabox-holder columns-<?php echo 1 == get_current_screen()->get_columns() ? '1' : '2'; ?>">
                        <!--<div id="post-body-content"></div>-->

                        <div id="postbox-container-1" class="postbox-container">
                            <?php do_meta_boxes('', 'side', ''); ?>
                        </div>

                        <div id="postbox-container-2" class="postbox-container">
                            <?php do_meta_boxes('', 'normal', ''); ?>
                            <?php do_meta_boxes('', 'advanced', ''); ?>
                        </div>
                    </div> <!-- #post-body -->
                </div> <!-- #poststuff -->
            </form>
        </div>
<?php
    }

    /**
     * Show content of metabox (callback)
     * @param array $post
     * @param array $meta_box
     */
    public function show_meta_box_content($post, $meta_box) {
        $key = $meta_box['args'][0];
        $options = $this->options;

        if ($key === 'general_settings') {
?>
            <fieldset class="options">
                <table class="form-table">
                <tr>
                    <th><?php _e('Protect mailto links', WP_MAILTO_LINKS_DOMAIN) ?></th>
                    <td><label><input type="checkbox" id="<?php echo WP_MAILTO_LINKS_OPTIONS_NAME ?>[protect]" name="<?php echo WP_MAILTO_LINKS_OPTIONS_NAME ?>[protect]" value="1" <?php checked('1', (int) $options['protect']); ?> />
                        <span><?php _e('Protect mailto links against spambots', WP_MAILTO_LINKS_DOMAIN) ?></span></label>
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Protect plain emails', WP_MAILTO_LINKS_DOMAIN) ?></th>
                    <td><label><input type="radio" id="<?php echo WP_MAILTO_LINKS_OPTIONS_NAME ?>[convert_emails]" name="<?php echo WP_MAILTO_LINKS_OPTIONS_NAME ?>[convert_emails]" value="0" <?php checked('0', (int) $options['convert_emails']); ?> />
                        <span><?php _e('No, keep plain emails as they are', WP_MAILTO_LINKS_DOMAIN) ?></span></label>
                        <br/><label><input type="radio" id="<?php echo WP_MAILTO_LINKS_OPTIONS_NAME ?>[convert_emails]" name="<?php echo WP_MAILTO_LINKS_OPTIONS_NAME ?>[convert_emails]" value="1" <?php checked('1', (int) $options['convert_emails']); ?> />
                        <span><?php _e('Yes, protect plain emails with protection text *', WP_MAILTO_LINKS_DOMAIN) ?></span> <span class="description"><?php _e('(recommended)', WP_MAILTO_LINKS_DOMAIN) ?></span></label>
                        <br/><label><input type="radio" id="<?php echo WP_MAILTO_LINKS_OPTIONS_NAME ?>[convert_emails]" name="<?php echo WP_MAILTO_LINKS_OPTIONS_NAME ?>[convert_emails]" value="2" <?php checked('2', (int) $options['convert_emails']); ?> />
                        <span><?php _e('Yes, convert plain emails to mailto links', WP_MAILTO_LINKS_DOMAIN) ?></span></label>
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Options have effect on', WP_MAILTO_LINKS_DOMAIN) ?></th>
                    <td>
                        <label><input type="checkbox" name="<?php echo WP_MAILTO_LINKS_OPTIONS_NAME ?>[filter_body]" id="filter_body" value="1" <?php checked('1', (int) $options['filter_body']); ?> />
                        <span><?php _e('All contents', WP_MAILTO_LINKS_DOMAIN) ?></span> <span class="description"><?php _e('(the whole <code>&lt;body&gt;</code>)', WP_MAILTO_LINKS_DOMAIN) ?></span></label>
                        <br/>&nbsp;&nbsp;<label><input type="checkbox" name="<?php echo WP_MAILTO_LINKS_OPTIONS_NAME ?>[filter_posts]" id="filter_posts" value="1" <?php checked('1', (int) $options['filter_posts']); ?> />
                                <span><?php _e('Post contents', WP_MAILTO_LINKS_DOMAIN) ?></span></label>
                        <br/>&nbsp;&nbsp;<label><input type="checkbox" name="<?php echo WP_MAILTO_LINKS_OPTIONS_NAME ?>[filter_comments]" id="filter_comments" value="1" <?php checked('1', (int) $options['filter_comments']); ?> />
                                <span><?php _e('Comments', WP_MAILTO_LINKS_DOMAIN) ?></span></label>
                        <br/>&nbsp;&nbsp;<label><input type="checkbox" name="<?php echo WP_MAILTO_LINKS_OPTIONS_NAME ?>[filter_widgets]" id="filter_widgets" value="1" <?php checked('1', (int) $options['filter_widgets']); ?> />
                                <span><?php if ($this->options['widget_logic_filter']) { _e('All widgets (uses the <code>widget_content</code> filter of the Widget Logic plugin)', WP_MAILTO_LINKS_DOMAIN); } else { _e('All text widgets', WP_MAILTO_LINKS_DOMAIN); } ?></span></label>
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Also protect...', WP_MAILTO_LINKS_DOMAIN) ?></th>
                    <td><label><input type="checkbox" name="<?php echo WP_MAILTO_LINKS_OPTIONS_NAME ?>[filter_head]" value="1" <?php checked('1', (int) $options['filter_head']); ?> />
                            <span><?php _e('<code>&lt;head&gt;</code>-section by replacing emails with protection text *', WP_MAILTO_LINKS_DOMAIN) ?></span></label>
                        <br/><label><input type="checkbox" name="<?php echo WP_MAILTO_LINKS_OPTIONS_NAME ?>[filter_rss]" value="1" <?php checked('1', (int) $options['filter_rss']); ?> />
                            <span><?php _e('RSS feed by replacing emails with protection text *', WP_MAILTO_LINKS_DOMAIN) ?></span></label>
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Set protection text *', WP_MAILTO_LINKS_DOMAIN) ?></th>
                    <td><label><input type="text" id="protection_text" class="regular-text" name="<?php echo WP_MAILTO_LINKS_OPTIONS_NAME ?>[protection_text]" value="<?php echo $options['protection_text']; ?>" />
                            <br/><span class="description"><?php _e('This text will be shown for protected emailaddresses.', WP_MAILTO_LINKS_DOMAIN) ?></span>
                        </label>
                    </td>
                </tr>
                </table>
            </fieldset>
            <p class="submit">
                <input class="button-primary" type="submit" disabled="disabled" value="<?php _e('Save Changes' ) ?>" />
            </p>
            <br class="clear" />
<?php
        } elseif ($key === 'style_settings') {
?>
            <fieldset class="options">
                <table class="form-table">
                <tr>
                    <th><?php _e('Show icon', WP_MAILTO_LINKS_DOMAIN) ?>
                    </th>
                    <td>
                        <div>
                            <div style="width:15%;float:left">
                                <label><input type="radio" name="<?php echo WP_MAILTO_LINKS_OPTIONS_NAME ?>[icon]" value="0" <?php checked('0', (int) $options['icon']); ?> />
                                <span><?php _e('No icon', WP_MAILTO_LINKS_DOMAIN) ?></span></label>
                            <?php for ($x = 1; $x <= 25; $x++): ?>
                                <br/>
                                <label title="<?php echo sprintf(__( 'Icon %1$s: choose this icon to show for all mailto links or add the class \'mail-icon-%1$s\' to a specific link.' ), $x ) ?>"><input type="radio" name="<?php echo WP_MAILTO_LINKS_OPTIONS_NAME ?>[icon]" value="<?php echo $x ?>" <?php checked($x, (int) $options['icon']); ?> />
                                <img src="<?php echo plugins_url('images/mail-icon-'. $x .'.png', WP_MAILTO_LINKS_FILE)  ?>" /></label>
                                <?php if ($x % 5 == 0): ?>
                            </div>
                            <div style="width:12%;float:left">
                                <?php endif; ?>
                            <?php endfor; ?>
                            </div>
                            <div style="width:29%;float:left;"><span class="description"><?php _e('Example:', WP_MAILTO_LINKS_DOMAIN) ?></span>
                                <br/><img src="<?php echo plugins_url('images/link-icon-example.png', WP_MAILTO_LINKS_FILE) ?>"    />
                            </div>
                            <br style="clear:both" />
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Skip images', WP_MAILTO_LINKS_DOMAIN) ?></th>
                    <td><label><input type="checkbox" name="<?php echo WP_MAILTO_LINKS_OPTIONS_NAME ?>[image_no_icon]" value="1" <?php checked('1', (int) $options['image_no_icon']); ?> />
                        <span><?php _e('Do not show icon for mailto links containing an image', WP_MAILTO_LINKS_DOMAIN) ?></span></label>
                    </td>
                </tr>
                <tr>
                    <th><?php _e('No-icon Class', WP_MAILTO_LINKS_DOMAIN) ?></th>
                    <td><label><input type="text" class="regular-text" id="<?php echo WP_MAILTO_LINKS_OPTIONS_NAME ?>[no_icon_class]" name="<?php echo WP_MAILTO_LINKS_OPTIONS_NAME ?>[no_icon_class]" value="<?php echo $options['no_icon_class']; ?>" />
                        <br/><span class="description"><?php _e('Use this class when a mailto link should not show an icon.', WP_MAILTO_LINKS_DOMAIN) ?></span></label>
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Additional Classes (optional)', WP_MAILTO_LINKS_DOMAIN) ?></th>
                    <td><label><input type="text" class="regular-text" id="<?php echo WP_MAILTO_LINKS_OPTIONS_NAME ?>[class_name]" name="<?php echo WP_MAILTO_LINKS_OPTIONS_NAME ?>[class_name]" value="<?php echo $options['class_name']; ?>" />
                        <br/><span class="description"><?php _e('Add extra classes to mailto links (or leave blank).', WP_MAILTO_LINKS_DOMAIN) ?></span></label></td>
                </tr>
                </table>
            </fieldset>
            <p class="submit">
                <input class="button-primary" type="submit" disabled="disabled" value="<?php _e('Save Changes' ) ?>" />
            </p>
            <br class="clear" />
<?php
        } elseif ($key === 'admin_settings') {
?>
            <fieldset class="options">
                <table class="form-table">
                <tr>
                    <th><?php _e('Admin menu position', WP_MAILTO_LINKS_DOMAIN) ?></th>
                    <td><label><input type="checkbox" id="<?php echo WP_MAILTO_LINKS_OPTIONS_NAME ?>[own_admin_menu]" name="<?php echo WP_MAILTO_LINKS_OPTIONS_NAME ?>[own_admin_menu]" value="1" <?php checked('1', (int) $options['own_admin_menu']); ?> />
                            <span><?php _e('Show as main menu item', WP_MAILTO_LINKS_DOMAIN) ?></span>
                            <br/><span class="description"><?php _e('When disabled item will be shown under "General settings".', WP_MAILTO_LINKS_DOMAIN) ?></span></label></td>
                </tr>
                </table>
            </fieldset>
            <p class="submit">
                <input class="button-primary" type="submit" disabled="disabled" value="<?php _e('Save Changes') ?>" />
            </p>
            <br class="clear" />
<?php
        } else if ($key === 'this_plugin') {
?>
            <ul>
                <li><a href="#" class="wpml-help-link"><?php _e('Documentation', WP_MAILTO_LINKS_DOMAIN) ?></a></li>
                <li><a href="http://wordpress.org/support/plugin/wp-mailto-links#postform" target="_blank"><?php _e('Report a problem', WP_MAILTO_LINKS_DOMAIN) ?></a></li>
            </ul>

            <p><strong><a href="http://wordpress.org/support/view/plugin-reviews/wp-mailto-links" target="_blank"><?php _e('Please rate this plugin!', WP_MAILTO_LINKS_DOMAIN) ?></a></strong></p>
<?php
        } elseif ($key === 'other_plugins') {
?>
            <h4><img src="<?php echo plugins_url('images/icon-wp-external-links.png', WP_MAILTO_LINKS_FILE) ?>" width="16" height="16" /> <?php _e('WP External Links', WP_MAILTO_LINKS_DOMAIN) ?> -
                <?php if (is_plugin_active('wp-external-links/wp-external-links.php')): ?>
                    <a href="<?php echo get_bloginfo('url') ?>/wp-admin/admin.php?page=wp_external_links"><?php _e('Settings') ?></a>
                <?php elseif( file_exists( WP_PLUGIN_DIR . '/wp-external-links/wp-external-links.php')): ?>
                    <a href="<?php echo get_bloginfo('url') ?>/wp-admin/plugins.php?plugin_status=inactive"><?php _e('Activate', WP_MAILTO_LINKS_DOMAIN) ?></a>
                <?php else: ?>
                    <a href="<?php echo get_bloginfo('url') ?>/wp-admin/plugin-install.php?tab=search&type=term&s=WP+External+Links+freelancephp&plugin-search-input=Search+Plugins"><?php _e('Get this plugin', WP_MAILTO_LINKS_DOMAIN) ?></a>
                <?php endif; ?>
            </h4>
            <p><?php _e('Open external links in a new window or tab, adding "nofollow", set link icon, styling, SEO friendly options and more. Easy install and go.', WP_MAILTO_LINKS_DOMAIN) ?>
                <br /><a href="http://wordpress.org/extend/plugins/wp-external-links/" target="_blank">WordPress.org</a> | <a href="http://www.freelancephp.net/wp-external-links-plugin/" target="_blank">FreelancePHP.net</a>
            </p>

            <h4><img src="<?php echo plugins_url('images/icon-email-encoder-bundle.png', WP_MAILTO_LINKS_FILE) ?>" width="16" height="16" /> <?php _e('Email Encoder Bundle', WP_MAILTO_LINKS_DOMAIN) ?> -
                <?php if (is_plugin_active('email-encoder-bundle/email-encoder-bundle.php')): ?>
                    <a href="<?php echo get_bloginfo('url') ?>/wp-admin/admin.php?page=email-encoder-bundle/email-encoder-bundle.php"><?php _e('Settings') ?></a>
                <?php elseif( file_exists( WP_PLUGIN_DIR . '/email-encoder-bundle/email-encoder-bundle.php')): ?>
                    <a href="<?php echo get_bloginfo('url') ?>/wp-admin/plugins.php?plugin_status=inactive"><?php _e('Activate', WP_MAILTO_LINKS_DOMAIN) ?></a>
                <?php else: ?>
                    <a href="<?php echo get_bloginfo('url') ?>/wp-admin/plugin-install.php?tab=search&type=term&s=WP+Mailto+Links+freelancephp&plugin-search-input=Search+Plugins"><?php _e('Get this plugin', WP_MAILTO_LINKS_DOMAIN) ?></a>
                <?php endif; ?>
            </h4>
            <p><?php _e('Encode mailto links, email addresses or any text and hide them from spambots. Easy to use, plugin works directly when activated.', WP_MAILTO_LINKS_DOMAIN) ?>
                <br /><a href="http://wordpress.org/extend/plugins/email-encoder-bundle/" target="_blank">WordPress.org</a> | <a href="http://www.freelancephp.net/wp-email-encoder-bundle-plugin-3/" target="_blank">FreelancePHP.net</a>
            </p>
<?php
        }
    }

    /* -------------------------------------------------------------------------
     *  Help Tabs
     * ------------------------------------------------------------------------*/

    /**
     * Add help tabs
     */
    public function add_help_tabs() {
        if (!function_exists('get_current_screen')) {
            return;
        }

        $screen = get_current_screen();

        $screen->set_help_sidebar($this->get_help_text('sidebar'));

        $screen->add_help_tab(array(
            'id' => 'general',
            'title'    => __('General', WP_MAILTO_LINKS_DOMAIN),
            'content' => $this->get_help_text('general'),
        ));
        $screen->add_help_tab(array(
            'id' => 'shortcodes',
            'title'    => __('Shortcodes', WP_MAILTO_LINKS_DOMAIN),
            'content' => $this->get_help_text('shortcodes'),
        ));
        $screen->add_help_tab(array(
            'id' => 'templatefunctions',
            'title'    => __('Template functions', WP_MAILTO_LINKS_DOMAIN),
            'content' => $this->get_help_text('templatefunctions'),
        ));
        $screen->add_help_tab(array(
            'id' => 'actionhooks',
            'title'    => __('Action Hooks', WP_MAILTO_LINKS_DOMAIN),
            'content' => $this->get_help_text('actionhooks'),
        ));
        $screen->add_help_tab(array(
            'id' => 'filterhooks',
            'title'    => __('Filter Hooks', WP_MAILTO_LINKS_DOMAIN),
            'content' => $this->get_help_text('filterhooks'),
        ));
        $screen->add_help_tab(array(
            'id' => 'faq',
            'title'    => __('FAQ', WP_MAILTO_LINKS_DOMAIN),
            'content' => $this->get_help_text('faq'),
        ));
    }

    /**
     * Get text for given help tab
     * @param string $key
     * @return string
     */
    private function get_help_text($key) {
        if ($key === 'general') {
            $plugin_title = get_admin_page_title();
            $icon_url = plugins_url('images/icon-wp-mailto-links.png', WP_MAILTO_LINKS_FILE);
            $version = WP_MAILTO_LINKS_VERSION;
            $content = sprintf(__('<h3><img src="%s" width="16" height="16" /> %s - version %s</h3>'
                     . '<p>Protect your email addresses and manage mailto links on your site, set mail icon, styling and more.</p>'
                     . '<h4>Features</h4>'
                     . '<ul>'
                     . '<li>Protect mailto links (automatically or shortcode)</li>'
                     . '<li>Protect plain email addresses or convert them to mailto links</li>'
                     . '<li>Set mail icon</li>'
                     . '<li>RSS feed protection</li>'
                     . '<li>And more...</li>'
                     . '</ul>'
                     , WP_MAILTO_LINKS_DOMAIN), $icon_url, $plugin_title, $version);
        } elseif ($key === 'shortcodes') {
            $content = <<<SHORTCODES
<h3>Shortcodes</h3>
<h4>[wpml_mailto]</h4>
<p>Create a protected mailto link in your posts:</p>
<p><code>[wpml_mailto email="info@myemail.com"]My Email[/wpml_mailto]</code>
</p>
<p>It's also possible to add attributes to the mailto link, like a target:</p>
<p><code>[wpml_mailto email="info@myemail.com" target="_blank"]My Email[/wpml_mailto]</code>
</p>
SHORTCODES;
        } elseif ($key === 'templatefunctions') {
            $content = <<<TEMPLATEFUNCTIONS
<h3>Template functions</h3>

<h4>wpml_mailto()</h4>
<p>Create a protected mailto link:</p>
<pre><code><&#63;php
if (function_exists('wpml_mailto')) {
    echo wpml_mailto('info@somedomain.com');
}
&#63;></code></pre>
<p>You can pass a few extra optional params (in this order): <code>display</code>, <code>attrs</code></p>

<h4>wpml_filter()</h4>
<p>Filter given content to protect mailto links, shortcodes and plain emails (according to the settings in admin):</p>
<pre><code><&#63;php
if (function_exists('wpml_filter')) {
    echo wpml_filter('Filter some content to protect an emailaddress like info@somedomein.com.');
}
&#63;></code></pre>

TEMPLATEFUNCTIONS;
        } elseif ($key === 'actionhooks') {
            $content = <<<ACTIONHOOKS
<h3>Action Hooks</h3>

<h4>wpml_ready</h4>
<p>Add extra code after plugin is ready on the site, f.e. to add extra filters:</p>
<pre><code><&#63;php
add_action('wpml_ready', 'extra_filters');

function extra_filters(\$filter_callback, \$object) {
    add_filter('some_filter', \$filter_callback);
}
&#63;></code></pre>
ACTIONHOOKS;
        } elseif ($key === 'filterhooks') {
            $content = <<<FILTERHOOKS
<h3>Filter Hooks</h3>

<h4>wpml_mailto</h4>
<p>The wpml_mailto filter gives you the possibility to manipulate output of the mailto created by the plugin. F.e. make all mailto links bold:</p>
<pre><code><&#63;php
add_filter('wpml_mailto', 'special_mailto', 10, 4);

function special_mailto(\$link, \$display, \$email, \$attrs) {
    return '&lt;b&gt;'. \$link .'&lt;/b&gt;';
}
&#63;></code></pre>
<p>Now all mailto links will be wrapped around a &lt;b&gt;-tag.</p>
FILTERHOOKS;
        } else if ($key === 'faq') {
            $content = __('<h3>FAQ</h3>'
                     . '<p>Please check the <a href="http://wordpress.org/extend/plugins/wp-mailto-links/faq/" target="_blank">FAQ on the Plugin site</a>.'
                     , WP_MAILTO_LINKS_DOMAIN);
        } else if ($key === 'sidebar') {
            $content = __('<h4>About the author</h4>'
                     . '<ul>'
                     . '<li><a href="http://www.freelancephp.net/" target="_blank">FreelancePHP.net</a></li>'
                     . '<li><a href="http://www.freelancephp.net/contact/" target="_blank">Contact</a></li>'
                     . '</ul>'
                     , WP_MAILTO_LINKS_DOMAIN);
        }

        return ((empty($content)) ? '' : __($content, WP_MAILTO_LINKS_DOMAIN));
    }


} // end class WPML_Admin

endif;

/*?> // ommit closing tag, to prevent unwanted whitespace at the end of the parts generated by the included files */
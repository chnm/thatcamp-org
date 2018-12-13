<?php
/**
 * Multi-level Navigation plugin core
 * 
 * @package    WordPress
 * @subpackage Multi-level Navigation
 */


/**
 * Multi-level Navigation core
 * 
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.0
 */
class MultiLevelNavigationAdmin {

	/**
	 * Class constructor
	 * 
	 * Adds methods to appropriate hooks
	 * 
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @since Multi-level Navigation Plugin 2.3
	 * @access public
	 */
	public function __construct() {

		// Load upgrade script (needs to be loaded before anything else in admin panel)
		require( 'upgrader.php' );

		// Default settings
		$array = array(
			'css'               => '#suckerfishnav {background:#1F3E9F url("../multi-level-navigation-plugin/images/suckerfish_blue.png") repeat-x;font-size:18px;font-family:verdana,sans-serif;font-weight:bold;	width:100%;}#suckerfishnav, #suckerfishnav ul {float:left;list-style:none;line-height:40px;padding:0;border:1px solid #aaa;margin:0;	width:100%;}#suckerfishnav a {display:block;color:#dddddd;text-decoration:none;padding:0px 10px;}#suckerfishnav li {float:left;padding:0;}#suckerfishnav ul {position:absolute;left:-999em;height:auto;	width:151px;font-weight:normal;margin:0;line-height:1;	border:0;border-top:1px solid #666666;	}#suckerfishnav li li {	width:149px;border-bottom:1px solid #666666;border-left:1px solid #666666;border-right:1px solid #666666;font-weight:bold;font-family:verdana,sans-serif;}#suckerfishnav li li a {padding:4px 10px;	width:130px;font-size:12px;color:#dddddd;}#suckerfishnav li ul ul {margin:-21px 0 0 150px;}#suckerfishnav li li:hover {background:#1F3E9F;}#suckerfishnav li ul li:hover a, #suckerfishnav li ul li li:hover a, #suckerfishnav li ul li li li:hover a, #suckerfishnav li ul li li li:hover a  {color:#dddddd;}#suckerfishnav li:hover a, #suckerfishnav li.sfhover a {color:#dddddd;}#suckerfishnav li:hover li a, #suckerfishnav li li:hover li a, #suckerfishnav li li li:hover li a, #suckerfishnav li li li li:hover li a {color:#dddddd;}#suckerfishnav li:hover ul ul, #suckerfishnav li:hover ul ul ul, #suckerfishnav li:hover ul ul ul ul, #suckerfishnav li.sfhover ul ul, #suckerfishnav li.sfhover ul ul ul, #suckerfishnav li.sfhover ul ul ul ul  {left:-999em;}#suckerfishnav li:hover ul, #suckerfishnav li li:hover ul, #suckerfishnav li li li:hover ul, #suckerfishnav li li li li:hover ul, #suckerfishnav li.sfhover ul, #suckerfishnav li li.sfhover ul, #suckerfishnav li li li.sfhover ul, #suckerfishnav li li li li.sfhover ul  {left:auto;background:#444444;}#suckerfishnav li:hover, #suckerfishnav li.sfhover {background:#5E7AD3;}' ,
			'superfish'         => '' ,
			'superfish_speed'   => 'normal' ,
			'superfish_time'    => '800' ,
			'superfish_timeout' => '100' ,
			'menuitem1'         => 'Home' ,
			'menuitem2'         => 'Pages' ,
			'menuitem3'         => 'Categories (single dropdown)' ,
			'menuitem4'         => 'Archives - months (single dropdown)' ,
			'menuitem5'         => 'Links - no categories (single dropdown)' ,
			'menuitem6'         => 'None' ,
			'menuitem7'         => 'None' ,
			'menuitem8'         => 'None' ,
			'menuitem9'         => 'None' ,
			'menuitem10'        => 'None' ,
			'hometitle'         => 'Home' ,
			'pagestitle'        => 'Pages' ,
			'categoriestitle'   => 'Categories' ,
			'archivestitle'     => 'Archives' ,
			'blogrolltitle'     => 'Links' ,
			'recentcommentstitle'=> 'Recent Comments' ,
			'recentpoststitle'  => 'Recent Posts' ,
			'keyboard'          => '' ,
			'disablecss'        => '' ,
			'inlinecss'         => '' ,
			'superfish_delaymouseover'  => '200' ,
			'superfish_sensitivity'  => 'high' ,
			'maintenance'       => '' ,
			'2_css'             => '',
			'2_menuitem1'       => 'Home' ,
			'2_menuitem2'       => 'Pages',
			'2_menuitem3'       => 'Categories (single dropdown)',
			'2_menuitem4'       => 'Archives - months (single dropdown)',
			'2_menuitem5'       => 'Links (single dropdown)',
			'2_menuitem6'       => 'None',
			'2_menuitem7'       => 'None',
			'2_menuitem8'       => 'None',
			'2_menuitem9'       => 'None',
			'2_menuitem10'      => 'None',
			'categoryorder'     => 'Ascending Name',
			'categorycount'     => '' ,
			'titletags'         => '' ,
			'recentpostsnumber' => '10' ,
			'recentcommentsnumber' => '10' ,
			'includeexcludecategories' => 'Exclude',
			'delay'             => '0',
			'displaycss'        => 'Normal',
		);
		add_option( 'pixopoint-menu', $array );

		// Add actions
		add_action( 'admin_menu', array( $this, 'add_admin_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );

	}

	/**
	 * Allowed HTML
	 * 
	 * @since 0.1
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @access private
	 */
	private $allowed_html = array(
		'a'       => array(
			'href'       => array(),
			'title'      => array(),
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'div'     => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'form'     => array(
			'role'       => array(),
			'method'     => array(),
			'action'     => array(),
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'input'     => array(
			'placeholder'=> array(),
			'type'       => array(),
			'value'      => array(),
			'name'       => array(),
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'span'     => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'p'     => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'h1'     => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'h2'     => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'h3'     => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'h4'     => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'h5'     => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'h6'     => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'table'      => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'blockquote'      => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'small'      => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'code'      => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'pre'       => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'tr'        => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
			'td'     => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'th'        => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'thead'     => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'tfoot'     => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'style'     => array(
			'type'       => array(),
			'id'         => array(),
			'rel'        => array(),
			'media'      => array(),
			'href'       => array()
		),
		'ul'        => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'li'        => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'ol'         => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'img'         => array(
			'src'        => array(),
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'article'     => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'aside'       => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'header'      => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'nav'        => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'footer'     => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'section'    => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'br'     => array(),
		'em'     => array(),
		'i'      => array(),
		'strong' => array(),
		'b'      => array(),
		'u'      => array(),
		'font'   => array()
	);

	/**
	 * Add scripts
	 * 
	 * @since 0.1
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 */
	public function admin_scripts() {

		// Adds support for tabber menus
		wp_enqueue_script( 
			'tabber-init', 
			MULTILEVELNAVIGATION_URL . '/scripts/tabber-init.js',
			'', 
			'1.0'
		);

		// Creates tabber menu
		wp_enqueue_script( 
			'tabber',
			MULTILEVELNAVIGATION_URL . '/scripts/tabber-minimized.js', 
			array( 'tabber-init' ),
			'1.9' 
		);
	}

	/**
	 * Add styles
	 * 
	 * @since 0.1
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 */
	public function admin_styles() {
		wp_enqueue_style( 'mln-admin', MULTILEVELNAVIGATION_URL . '/admin.css', false, '', 'screen' );
	}

	/**
	 * Load up the menu pages
	 * 
	 * @since 0.1
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 */
	public function add_admin_page() {

		// Edit template admin page
		$page = add_options_page(
			__( 'Multi-level Navigation Plugin Options', 'pixopoint_mln' ),
			__( 'Multi-level Navigation', 'pixopoint_mln' ),
			'edit_theme_options',
			'multileveloptions',
			array( $this, 'do_page' )
		);
		add_action( 'admin_print_styles-' . $page, array( $this, 'admin_styles' ) ); // Add styles (only for this admin page)
		add_action( 'admin_print_styles-' . $page, array( $this, 'admin_scripts' ) );
		
	}

	/**
	 * Init options to white list our options
	 * 
	 * @since 0.1
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 */
	function register_settings(){
		register_setting( 'mln-group', 'pixopoint-menu', array( $this, 'settings_sanitize' ) );
	}

	/**
	 * Sanitize settings
	 * 
	 * @since 0.1
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @todo Need to be more aggressive with sanitization. In particular, CSS should be sanitized with CSS Tidy. The lack of sanitization should not be a security risk per se, but extra sanitization is always advisable. The extra sanitization was not completed due to time contrains during the rapid upgrade of the plugin during version 2.4. Previously, little or no santiziation was performed and what is below is a significant upgrade on the previous system.
	 */
	function settings_sanitize( $input ){

		$output = array();

		// Options which need sanitized as CSS
		$css = array(
			'2_css',
			'css',
		);

		// Options which need sanitized as numbers
		$numbers = array(
			'superfish',
			'superfish_time',
			'superfish_timeout',
			'superfish_delaymouseover',
			'recentpostsnumber',
			'recentcommentsnumber',
		);

		// Options which need sanitized as HTML
		$html = array(
			'superfish_speed',
			'menuitem1',
			'menuitem2',
			'menuitem3',
			'menuitem4',
			'menuitem5',
			'menuitem6',
			'menuitem7',
			'menuitem8',
			'menuitem9',
			'menuitem10',
			'hometitle',
			'pagestitle',
			'categoriestitle',
			'archivestitle',
			'blogrolltitle',
			'recentcommentstitle',
			'recentpoststitle',
			'keyboard',
			'disablecss',
			'inlinecss',
			'superfish_sensitivity',
			'2_menuitem1',
			'2_menuitem2',
			'2_menuitem3',
			'2_menuitem4',
			'2_menuitem5',
			'2_menuitem6',
			'2_menuitem7',
			'2_menuitem8',
			'2_menuitem9',
			'2_menuitem10',
			'categoryorder',
			'categorycount',
			'titletags',
			'delay',
			'displaycss',
			'includeexcludepages',
			'excludepages',
			'includeexcludecategories',
			'excludecategories',
		);

		// Raw HTML (used for custom code boxes)
		$raw_html = array(
			'custommenu',
			'custommenu2',
			'custommenu3',
			'custommenu4',
		);

		// CSS sanitization
		foreach( $css as $item ) {
			if ( isset( $input[$item] ) )
				$output[$item] = wp_kses( $input[$item], '', '' );
			else
				$output[$item] = '';
		}

		// Sanitization of HTML
		foreach( $html as $item ) {
			if ( isset( $input[$item] ) )
				$output[$item] = wp_kses( $input[$item], '', '' );
			else
				$output[$item] = '';
		}

		// Sanitization of numbers
		foreach( $numbers as $item ) {
			if ( isset( $input[$item] ) )
				$output[$item] = (int) $input[$item];
			else
				$output[$item] = 0;
		}

		// Sanitization of raw HTML
		foreach( $raw_html as $item ) {
			if ( isset( $input[$item] ) )
				$output[$item] = wp_kses( $input[$item], $this->allowed_html, '' );
			else
				$output[$item] = '';
		}

		return $output;
	}

	/**
	 * Create the options page
	 * 
	 * @since 0.1
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @todo Upgrade old legacy code - some oddball name spacing of variables and strange methods of outputting form fields should be fixed - doesn't cause any problems but would be nice to clean it up in future
	 * echo string
	 */
	public function do_page() {
		?>
<div class="wrap"><?php
	
	// "Options Saved" message as displayed at top of page on clicking "Save"
	if ( isset( $_REQUEST['updated'] ) )
		echo '<div class="updated fade"><p><strong>' . __( 'Options saved' ) . '</strong></p></div>';

	?>
	<form method="post" action="options.php">
		<?php settings_fields( 'mln-group' ); ?>
<h2><?php _e( 'PixoPoint Multi-level Navigation Plugin', 'pixopoint_mln' ); ?></h2>
<div style="clear:both;padding-top:5px;"></div>
<div class="tabber" id="mytabber1">


<?php /* Home tab */ ?>
<div class="tabbertab">
<h2><?php _e( 'Home', 'pixopoint_mln' ); ?></h2>

<div class="clear"></div>
<table class="widefat" cellspacing="0" id="active-plugins-table">
	<thead>
	<tr>
		<th scope="col"><?php _e( 'Introduction', 'pixopoint_mln' ); ?></th>
	</tr>
	</thead>
	<tfoot>
	<tr>
		<th scope="col"><?php _e( 'Thanks for using our plugin :)', 'pixopoint_mln' ); ?></th>
	</tr>
	</tfoot>
	<tbody class="plugins">
		<tr class="inactive">
			<td class="desc">
				<p><?php _e( 'The Multi-level Navigation Plugin creates a dropdown, flyout or slider menu for your WordPress site based on the <a href="http://www.htmldog.com/articles/suckerfish/ target="_blank">Son of Suckerfish technique</a>. If you have any comments, questions or suggestions about this plugin, please visit the <a href="https://geek.hellyer.kiwi/forum/index.php?board=4.0">PixoPoint multi-level navigation forum</a>.', 'pixopoint_mln' ); ?></p>
				<h4><?php _e( 'Installation', 'pixopoint_mln' ); ?></h4>
				<p><?php _e( 'Add the following code wherever you want the dropdown to appear in your theme (usually header.php)', 'pixopoint_mln' ); ?></p>
				<p><code><?php _e( '&lt;?php if (function_exists(\'pixopoint_menu\')) {pixopoint_menu();} ?&gt;', 'pixopoint_mln' ); ?></code></p>
				<p><?php _e( 'To style your menu, please visit the <a href="https://geek.hellyer.kiwi/suckerfish_css/">Multi-level Navigation CSS Generator</a> page to obtain your CSS and enter it into the "Appearance" tab.', 'pixopoint_mln' ); ?></p>
			</td>
		</tr>
	</tbody>
</table>

<div class="clear"></div>
<table class="widefat" cellspacing="0" id="active-plugins-table">
	<thead>
	<tr>
		<th scope="col"><?php _e( 'Help', 'pixopoint_mln' ); ?></th>
	</tr>
	</thead>
	<tfoot>
	<tr>
		<th scope="col"><?php _e( 'Please do not email us for support. We do not have time to reply.', 'pixopoint_mln' ); ?></th>
	</tr>
	</tfoot>
	<tbody class="plugins">
		<tr class="inactive">
			<td class="desc">
				<?php
				// Only added if main menu isn't already specified in theme - no point telling them to do something they're already doing
				?>
				<h4><?php _e( 'Support', 'pixopoint_mln' ); ?></h4>
				<p><?php _e( 'We no longer provide paid or free support sorry. The shear volume of support requests was too high and we could not keep up with demand.', 'pixopoint_mln' ); ?></p>

				<h4><?php _e( 'FAQ', 'pixopoint_mln' ); ?></h4>
				<p><?php _e( '<strong>Q:</strong><em> Your plugin doesn\'t work in IE, why don\'t you fix it?</em> <br /><strong>A:</strong> The plugin does work with IE, you just haven\'t integrated it correctly. See \'Free support\' below for some tips on how to get it working with IE.', 'pixopoint_mln' ); ?></p>
				<p><?php _e( '<strong>Q:</strong><em> How do I change the menu contents?</em> <br /><strong>A:</strong> See the big tab at the top of the screen right now which says "Menu Contents"? Click that ...', 'pixopoint_mln' ); ?></p>
				<p><?php _e( '<strong>Q:</strong><em> How do I change the colour/font/whatever in my menu?</em> <br /><strong>A:</strong> Visit the <a href="https://geek.hellyer.kiwi/suckerfish_css/">CSS generator</a>.', 'pixopoint_mln' ); ?></p>
				<p><?php _e( '<strong>Q:</strong><em> How do I get a fully customised version?</em> <br /><strong>A:</strong> Leave a message on the PixoPoint <a href="https://geek.hellyer.kiwi/contact/">Contact Page</a> with your requirements and we will get back to you ASAP with pricing information. Alternatively you can sign up for our <a href="https://geek.hellyer.kiwi/premium-support/">Premium Support</a> option which gives you access to our new dropdown, flyout and slider menu CSS generator, plus access to our premium support forum.', 'pixopoint_mln' ); ?></p>
				<p><?php _e( '<strong>Q:</strong><em> Why can\'t the plugin do X, Y or Z?</em> <br /><strong>A:</strong> It probably can, we just haven\'t supplied instructions on how to do it. If you have any requests, then please leave them in the <a href="https://geek.hellyer.kiwi/forum/index.php?board=4.0">PixoPoint dropdown menu support board</a>. We often update the plugin with new functionality and we\'re far more likely to include the functionality you want if we know there is a demand for it already.', 'pixopoint_mln' ); ?></p>

				<p><?php _e( 'If you follow all of the instructions here, activate the plugin and find the menu is appearing on your site but looks all messed up, then the problem is probably caused by a clash between your themes CSS and plugins CSS. These problems can usually be remedied by removing the wrapper tags which surround the menu in your theme. For example, most themes will have some HTML such as <code>&lt;div id="nav"&gt;&lt;?php wp_list_pages(); ?&gt;&lt;/div&gt;</code> which contains the existing themes menu. By placing the <code>pixopoint_menu()</code> function between those DIV tags, the menu will often interact with that DIV tag. The solution is to either remove the DIV tag or to alter it\'s CSS so that it doesn\'t interact with the menu.', 'pixopoint_mln' ); ?></p>
			</td>
		</tr>
	</tbody>
</table>
<div style="clear:both"></div>
</div>


<?php /* Appearance tab */ ?>
<div class="tabbertab">
<h2><?php _e( 'Appearance', 'pixopoint_mln' ); ?></h2>
<div class="clear"></div>
<p><?php _e( 'To change the appearance of your menu, please visit the <a href="https://geek.hellyer.kiwi/suckerfish_css/">PixoPoint Multi-level CSS Generator</a> to obtain CSS. Paste your new CSS into the main menu box below.', 'pixopoint_mln' ); ?></p>
<div class="clear"></div>
<table class="widefat" cellspacing="0" id="active-plugins-table">
	<thead>
	<tr>
		<th scope="col"><?php _e( 'Enter the CSS for your main menu here', 'pixopoint_mln' ); ?></th>
	</tr>
	</thead>
	<tfoot>
	<tr>
		<th scope="col"><?php _e( 'To obtain new CSS, please visit the <a href="https://geek.hellyer.kiwi/suckerfish_css/">PixoPoint CSS generator</a>', 'pixopoint_mln' ); ?></th>
	</tr>
	</tfoot>
	<tbody class="plugins">
		<tr class="inactive">
			<td class="desc"><p><textarea name="pixopoint-menu[css]" style="width:100%;border:none" value="" rows="10"><?php echo get_mlnmenu_option( 'css' ); ?></textarea></p></td>
		</tr>
	</tbody>
</table>

<?php if (get_mlnmenu_option( 'secondmenu') == 'on') {?>
<div class="clear"></div>
<table class="widefat" cellspacing="0" id="active-plugins-table">
	<thead>
	<tr>
		<th scope="col"><?php _e( 'Enter the CSS for your second menu here. Note: the ID of these menu items must be suckerfishnav_2', 'pixopoint_mln' ); ?></th>
	</tr>
	</thead>
	<tfoot>
	<tr>
		<th scope="col"><?php _e( 'As this is the second menu, the CSS ID\'s must be different from the first. The ID of these menu items must be suckerfishnav_2 which is not the default format from the <a href="https://geek.hellyer.kiwi/suckerfish_css/">CSS generator</a> so if you want to use the <a href="https://geek.hellyer.kiwi/suckerfish_css/">CSS generator</a> for this option you will need to \'search and replace\' (in a text editor) <strong>suckerfishnav</strong> to <strong>suckerfishnav_2</strong>.', 'pixopoint_mln' ); ?></th>
	</tr>
	</tfoot>
	<tbody class="plugins">
		<tr class="inactive">
			<td class="desc">
				<p><textarea name="pixopoint-menu[2_css]" style="width:100%;border:none" value="" rows="10"><?php echo get_mlnmenu_option( '2_css' ); ?></textarea></p>
			</td>
		</tr>
	</tbody>
</table>
<?php } ?>
</div>

<?php /* Menu contents */ ?>
<div class="tabbertab">
  <h2><?php _e( 'Menu contents', 'pixopoint_mln' ); ?></h2>
<div class="clear"></div>
<table class="widefat" cellspacing="0" id="active-plugins-table">
	<thead>
	<tr>
		<th scope="col"><?php _e( 'Main menu contents', 'pixopoint_mln' ); ?></th>
	</tr>
	</thead>
	<tfoot>
	<tr>
		<th scope="col"><?php _e( 'Modify the contents of your main menu via the options above.', 'pixopoint_mln' ); ?></th>
	</tr>
	</tfoot>
	<tbody class="plugins">
		<tr class="inactive">
			<td class="desc">
				<p>
				<?php
					// Legacy function - kept to avoid too much code churn during upgrade
					function mln_contents($name) {
						$options = array('None', 'Home', 'Pages', 'Pages (single dropdown)', 'Categories', 'Categories (single dropdown)', 'Archives - months', 'Archives - months (single dropdown)', 'Archives - years', 'Archives - years (single dropdown)', 'Links - no categories', 'Links - no categories (single dropdown)', 'Links - with categories', 'Links - with categories (single dropdown)', 'Recent Comments (single dropdown)', 'Recent Posts (single dropdown)', 'Custom 1', 'Custom 2', 'Custom 3', 'Custom 4');
						$ret = '<option>'.$name.'</option>';
						foreach($options as $option) {if($name != $option) {$ret .= '<option>'.$option.'</option>';}}
						return $ret;
					}

					$menuitem = array(
						get_mlnmenu_option( 'menuitem1' ),
						get_mlnmenu_option( 'menuitem2' ),
						get_mlnmenu_option( 'menuitem3' ),
						get_mlnmenu_option( 'menuitem4' ),
						get_mlnmenu_option( 'menuitem5' ),
						get_mlnmenu_option( 'menuitem6' ),
						get_mlnmenu_option( 'menuitem7' ),
						get_mlnmenu_option( 'menuitem8' ),
						get_mlnmenu_option( 'menuitem9' ),
						get_mlnmenu_option( 'menuitem10' )
					);
					foreach( $menuitem as $key => $menuitem ) {
						echo '
						<div class="menuitems">
							<label>Menu Item #' . ( $key + 1 ) . '</label>
							<select name="pixopoint-menu[menuitem' . ( $key + 1 ).']">
								' . mln_contents($menuitem) . '
							</select>
						</div>';
					}
				?>
				</p>
			</td>
		</tr>
	</tbody>
</table>

<div class="clear"></div>
<table class="widefat" cellspacing="0" id="active-plugins-table">
	<thead>
	<tr>
		<th scope="col"><?php _e( 'Second menu contents', 'pixopoint_mln' ); ?></th>
	</tr>
	</thead>
	<tfoot>
	<tr>
		<th scope="col"><?php _e( 'Modify the contents of your second menu via the options above.', 'pixopoint_mln' ); ?></th>
	</tr>
	</tfoot>
	<tbody class="plugins">
		<tr class="inactive">
			<td class="desc">
				<p>
				<?php
					// Legacy function left to avoid code churn during upgrade
					function mln_contents_2($name) {
						$options = array('None', 'Home', 'Pages', 'Pages (single dropdown)', 'Categories', 'Categories (single dropdown)', 'Archives - months', 'Archives - months (single dropdown)', 'Archives - years', 'Archives - years (single dropdown)', 'Links - no categories', 'Links - no categories (single dropdown)', 'Links - with categories', 'Links - with categories (single dropdown)', 'Recent Comments (single dropdown)', 'Recent Posts (single dropdown)', 'Custom 1', 'Custom 2', 'Custom 3', 'Custom 4');
						$ret = '<option>'.$name.'</option>';
						foreach($options as $option) {if($name != $option) {$ret .= '<option>'.$option.'</option>';}}
						return $ret;
					}

					$menuitem2 = array(
						get_mlnmenu_option( '2_menuitem1' ),
						get_mlnmenu_option( '2_menuitem2' ),
						get_mlnmenu_option( '2_menuitem3' ),
						get_mlnmenu_option( '2_menuitem4' ),
						get_mlnmenu_option( '2_menuitem5' ),
						get_mlnmenu_option( '2_menuitem6' ),
						get_mlnmenu_option( '2_menuitem7' ),
						get_mlnmenu_option( '2_menuitem8' ),
						get_mlnmenu_option( '2_menuitem9' ),
						get_mlnmenu_option( '2_menuitem10' )
					);
					foreach( $menuitem2 as $key => $menuitem ) {
						echo '
						<div class="menuitems">
							<label>Menu Item #' . ( $key + 1 ) . '</label>
							<select name="pixopoint-menu[2_menuitem' . ( $key + 1 ) . ']">
								' . mln_contents_2 ( $menuitem ) . '
							</select>
						</div>';
					}
				?>
			</p>
		</td>
	</tr>	</tbody>
</table>

<table class="widefat" cellspacing="0" id="active-plugins-table">
	<thead>
	<tr>
		<th scope="col"><?php _e( 'Modifications', 'pixopoint_mln' ); ?></th>
	</tr>
	</thead>
	<tfoot>
	<tr>
		<th scope="col"><?php _e( 'To change the text displayed in the top level menu items for Pages, Categories etc. or to exclude or include specific Pages or Categories modify the above options.', 'pixopoint_mln' ); ?></th>
	</tr>
	</tfoot>
	<tbody class="plugins">
		<tr class="inactive">
			<td class="desc">
				<h4><?php _e( 'Titles', 'pixopoint_mln' ); ?></h4>
 			 	<div class="menuitems2">
					<p>
						<label><?php _e( 'Home', 'pixopoint_mln' ); ?></label>
							<input type="text" name="pixopoint-menu[hometitle]" value="<?php echo get_mlnmenu_option( 'hometitle' ); ?>" />
					</p>
				</div>
		  	<div class="menuitems2">
					<p>
						<label><?php _e( 'Pages', 'pixopoint_mln' ); ?></label>
						<input type="text" name="pixopoint-menu[pagestitle]" value="<?php echo get_mlnmenu_option( 'pagestitle' ); ?>" />
					</p>
				</div>
		  	<div class="menuitems2">
					<p>
						<label><?php _e( 'Categories', 'pixopoint_mln' ); ?></label>
						<input type="text" name="pixopoint-menu[categoriestitle]" value="<?php echo get_mlnmenu_option( 'categoriestitle' ); ?>" />
					</p>
				</div>
		  	<div class="menuitems2">
					<p>
						<label><?php _e( 'Archives', 'pixopoint_mln' ); ?></label>
						<input type="text" name="pixopoint-menu[archivestitle]" value="<?php echo get_mlnmenu_option( 'archivestitle' ); ?>" />
					</p>
				</div>
		  	<div class="menuitems2">
					<p>
						<label><?php _e( 'Links', 'pixopoint_mln' ); ?></label>
						<input type="text" name="pixopoint-menu[blogrolltitle]" value="<?php echo get_mlnmenu_option( 'blogrolltitle' ); ?>" />
					</p>
				</div>
		  	<div class="menuitems2">
					<p>
						<label><?php _e( 'Recent Comments', 'pixopoint_mln' ); ?></label>
						<input type="text" name="pixopoint-menu[recentcommentstitle]" value="<?php echo get_mlnmenu_option( 'recentcommentstitle' ); ?>" />
					</p>
				</div>
		  	<div class="menuitems2">
					<p>
						<label><?php _e( 'Recent Posts', 'pixopoint_mln' ); ?></label>
						<input type="text" name="pixopoint-menu[recentpoststitle]" value="<?php echo get_mlnmenu_option( 'recentpoststitle' ); ?>" />
					</p>
				</div>
				<div class="clear"></div>
				<h4><?php _e( 'Title URL\'s', 'pixopoint_mln' ); ?></h4>
				<p><?php _e( 'If a URL is not specified, then a default option will be used.', 'pixopoint_mln' ); ?></p>
 			 	<div class="menuitems2">
					<p>
						<label><?php _e( 'Home', 'pixopoint_mln' ); ?></label>
							<input type="text" name="pixopoint-menu[homeurl]" value="<?php echo get_mlnmenu_option( 'homeurl' ); ?>" />
					</p>
				</div>
		  	<div class="menuitems2">
					<p>
						<label><?php _e( 'Pages', 'pixopoint_mln' ); ?></label>
						<input type="text" name="pixopoint-menu[pagesurl]" value="<?php echo get_mlnmenu_option( 'pagesurl' ); ?>" />
					</p>
				</div>
		  	<div class="menuitems2">
					<p>
						<label><?php _e( 'Categories', 'pixopoint_mln' ); ?></label>
						<input type="text" name="pixopoint-menu[categoriesurl]" value="<?php echo get_mlnmenu_option( 'categoriesurl' ); ?>" />
					</p>
				</div>
		  	<div class="menuitems2">
					<p>
						<label><?php _e( 'Archives', 'pixopoint_mln' ); ?></label>
						<input type="text" name="pixopoint-menu[archivesurl]" value="<?php echo get_mlnmenu_option( 'archivesurl' ); ?>" />
					</p>
				</div>
		  	<div class="menuitems2">
					<p>
						<label><?php _e( 'Links', 'pixopoint_mln' ); ?></label>
						<input type="text" name="pixopoint-menu[blogrollurl]" value="<?php echo get_mlnmenu_option( 'blogrollurl' ); ?>" />
					</p>
				</div>
		  	<div class="menuitems2">
					<p>
						<label><?php _e( 'Recent Comments', 'pixopoint_mln' ); ?></label>
						<input type="text" name="pixopoint-menu[recentcommentsurl]" value="<?php echo get_mlnmenu_option( 'recentcommentsurl' ); ?>" />
					</p>
				</div>
		  	<div class="menuitems2">
					<p>
						<label><?php _e( 'Recent Posts', 'pixopoint_mln' ); ?></label>
						<input type="text" name="pixopoint-menu[recentpostsurl]" value="<?php echo get_mlnmenu_option( 'recentpostsurl' ); ?>" />
					</p>
				</div>
				<div class="clear"></div>
				<h4><?php _e( 'Pages/categories to exclude', 'pixopoint_mln' ); ?></h4>
				<p><?php _e( 'If no pages or categories are specified then all of them will be included', 'pixopoint_mln' ); ?></p>
			  <div class="includeexclude">
					<p>
						<label><?php _e( 'Pages to include or exclude in the main menu', 'pixopoint_mln' ); ?></label>
						<input type="text" name="pixopoint-menu[excludepages]" value="<?php echo get_mlnmenu_option( 'excludepages' ); ?>" />
					</p>
					<select name="pixopoint-menu[includeexcludepages]">
						<?php
						$suckerfish_includeexcludepages = get_mlnmenu_option( 'includeexcludepages');
						switch ($suckerfish_includeexcludepages){
							case "include":echo '<option>include</option><option>exclude</option>';break;
							case "exclude":echo '<option>exclude</option><option>include</option>';break;
							case "":echo '<option>include</option><option>exclude</option>';break;
						}
						?>
					</select>
				</div>
		 	 	<div class="includeexclude">
					<p>
						<label><?php _e( 'Categories to include or exclude', 'pixopoint_mln' ); ?></label>
						<input type="text" name="pixopoint-menu[excludecategories]" value="<?php echo get_mlnmenu_option('excludecategories' ); ?>" />
					</p>
					<select name="pixopoint-menu[includeexcludecategories]">
					<?php
						$suckerfish_includeexcludecategories = get_mlnmenu_option( 'includeexcludecategories');
						switch ($suckerfish_includeexcludecategories){
							case "include":echo '<option>include</option><option>exclude</option>';break;
							case "exclude":echo '<option>exclude</option><option>include</option>';break;
							case "":echo '<option>include</option><option>exclude</option>';break;
							}
					?>
					</select>
				</div>
				<div class="clear"></div>
				<h4><?php _e( 'Pages/categories depth', 'pixopoint_mln' ); ?></h4>
				<p><?php _e( 'Controls the depth of the menu. \'No nesting\' means that all the available menu items will be displayed in a flat list with no children.', 'pixopoint_mln' ); ?></p>
			  <div class="includeexclude">
					<p>
						<label><?php _e( 'Pages depth', 'pixopoint_mln' ); ?></label>
					</p>
					<select name="pixopoint-menu[depthpages]">
						<?php
						$suckerfish_depthpages = get_mlnmenu_option( 'depthpages');
						switch ($suckerfish_depthpages){
							case "Top level only":echo '<option>Top level only</option><option>No nesting</option><option>1 level of children</option><option>2 levels of children</option><option>Infinite</option>';break;
							case "No nesting":echo '<option>No nesting</option><option>Top level only</option><option>1 level of children</option><option>2 levels of children</option><option>Infinite</option>';break;
							case "1 level of children":echo '<option>1 level of children</option><option>Top level only</option><option>No nesting</option><option>2 levels of children</option><option>Infinite</option>';break;
							case "2 levels of children":echo '<option>2 levels of children</option><option>Top level only</option><option>No nesting</option><option>1 level of children</option><option>Infinite</option>';break;
							case "Infinite":echo '<option>Infinite</option><option>Top level only</option><option>No nesting</option><option>1 level of children</option><option>2 levels of children</option>';break;
							case "":echo '<option>Infinite</option><option>Top level only</option><option>No nesting</option><option>1 level of children</option><option>2 levels of children</option>';break;
						}
						?>
					</select>
				</div>
		 	 	<div class="includeexclude">
					<p>
						<label><?php _e( 'Categories depth', 'pixopoint_mln' ); ?></label>
					</p>
					<select name="pixopoint-menu[depthcategories]">
					<?php
						$suckerfish_depthecategories = get_mlnmenu_option( 'depthcategories');
						switch ($suckerfish_depthecategories){
							case "Top level only":echo '<option>Top level only</option><option>No nesting</option><option>1 level of children</option><option>2 levels of children</option><option>Infinite</option>';break;
							case "No nesting":echo '<option>No nesting</option><option>Top level only</option><option>1 level of children</option><option>2 levels of children</option><option>Infinite</option>';break;
							case "1 level of children":echo '<option>1 level of children</option><option>Top level only</option><option>No nesting</option><option>2 levels of children</option><option>Infinite</option>';break;
							case "2 levels of children":echo '<option>2 levels of children</option><option>Top level only</option><option>No nesting</option><option>1 level of children</option><option>Infinite</option>';break;
							case "Infinite":echo '<option>Infinite</option><option>Top level only</option><option>No nesting</option><option>1 level of children</option><option>2 levels of children</option>';break;
							case "":echo '<option>Infinite</option><option>Top level only</option><option>No nesting</option><option>1 level of children</option><option>2 levels of children</option>';break;
							}
					?>
					</select>
				</div>
				<div class="clear"></div>
				<h4><?php _e( 'Categories settings', 'pixopoint_mln' ); ?></h4>
				<p><?php _e( 'You may order your categories by Ascending ID #, Descending ID # or alphabetically, Ascending Name or Descending Name.', 'pixopoint_mln' ); ?></p>
			  <div class="includeexclude">
					<p>
						<label><?php _e( 'Category order', 'pixopoint_mln' ); ?></label>
					</p>
					<select name="pixopoint-menu[categoryorder]">
						<?php
						$suckerfish_categoryorder = get_mlnmenu_option( 'categoryorder');
						switch ($suckerfish_categoryorder){
							case "Ascending ID #":echo '<option>Ascending ID #</option><option>Descending ID #</option><option>Ascending Name</option><option>Descending Name</option>';break;
							case "Descending ID #":echo '<option>Descending ID #</option><option>Ascending ID #</option><option>Ascending Name</option><option>Descending Name</option>';break;
							case "Ascending Name":echo '<option>Ascending Name</option><option>Descending Name</option><option>Descending ID #</option><option>Ascending ID #</option>';break;
							case "Descending Name":echo '<option>Descending Name</option><option>Ascending Name</option><option>Descending ID #</option><option>Ascending ID #</option>';break;
							case "":echo '<option>Ascending Name</option><option>Descending Name</option><option>Descending ID #</option><option>Ascending ID #</option>';break;
						}
						?>
					</select>
				</div>
			  <div class="includeexclude">
					<p style="margin-top:10px">
						<label><?php _e( 'Show empty categories', 'pixopoint_mln' ); ?></label>
					</p>
					<?php
						if (get_mlnmenu_option( 'categoryshowempty') == 'on') {echo '<input type="checkbox" name="pixopoint-menu[categoryshowempty]" checked="yes" />';}
						else {echo '<input type="checkbox" name="pixopoint-menu[categoryshowempty]" />';}
						?>
				</div>
				<div class="clear"></div>
				<h4><?php _e( 'Number of recent posts and comments', 'pixopoint_mln' ); ?></h4>
				<p><?php _e( 'Controls the number of recent posts and comments shown when using the \'Recent Posts\' or \'Recent Comments\' menu option.', 'pixopoint_mln' ); ?></p>
			  <div class="includeexclude">
					<p>
						<label><?php _e( 'Number of recent posts', 'pixopoint_mln' ); ?></label>
						<input type="text" name="pixopoint-menu[recentpostsnumber]" value="<?php echo get_mlnmenu_option( 'recentpostsnumber' ); ?>" />
					</p>
				</div>
			  <div class="includeexclude">
					<p>
						<label><?php _e( 'Number of recent comments', 'pixopoint_mln' ); ?></label>
						<input type="text" name="pixopoint-menu[recentcommentsnumber]" value="<?php echo get_mlnmenu_option( 'recentcommentsnumber' ); ?>" />
					</p>
				</div>
			</td>
		</tr>
	</tbody>
</table>

<div class="clear"></div>
<table class="widefat" cellspacing="0" id="active-plugins-table">
	<thead>
		<tr>
			<th scope="col">
				<?php _e( 'Custom HTML code', 'pixopoint_mln' ); ?> (<div class="csstooltip"><?php _e( 'example', 'pixopoint_mln' ); ?><div>
				<?php _e( 'Note: You can have multiple top level menu items in one custom code entry. The following example will display a menu with links to \'Home\', \'Categories\' and \'Pages\', the \'Categories\' and \'Pages\' links would have dropdowns and the \'Page 1\' link in the \'Pages\' dropdown would contain another further level.', 'pixopoint_mln' ); ?>
				<br />
				<code><br />
&lt;li&gt;&lt;a href="https://geek.hellyer.kiwi/">Home&lt;/a&gt;&lt;/li&gt;<br />
&lt;li&gt;&lt;a href=""&gt;Categories&lt;/a&gt;<br />
&nbsp;&nbsp;&lt;ul&gt;<br />
&nbsp;&nbsp;&nbsp;&nbsp;&lt;li&gt;&lt;a href="https://geek.hellyer.kiwi/categories/templates/"&gt;Templates&lt;/a&gt;&lt;/li&gt;<br />
&nbsp;&nbsp;&nbsp;&nbsp;&lt;li&gt;&lt;a href="https://geek.hellyer.kiwi/categories/plugins/"&gt;Plugins&lt;/a&gt;&lt;/li&gt;<br />
&nbsp;&nbsp;&nbsp;&nbsp;&lt;li&gt;&lt;a href="https://geek.hellyer.kiwi/categories/plugins/"&gt;WordPress&lt;/a&gt;&lt;/li&gt;<br />
&nbsp;&nbsp;&lt;/ul&gt;<br />
&lt;/li&gt;<br />
&lt;li&gt;&lt;a href=""&gt;Pages&lt;/a&gt;<br />
&nbsp;&nbsp;&lt;ul&gt;<br />
&nbsp;&nbsp;&nbsp;&nbsp;&lt;li&gt;&lt;a href="https://geek.hellyer.kiwi/page1/">Page 1&lt;/a&gt;<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;ul&gt;<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;li&gt;&lt;a href="https://geek.hellyer.kiwi/page1/flyout/">Flyout&lt;/a&gt;<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;ul&gt;<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;li&gt;&lt;a href="https://geek.hellyer.kiwi/page1/flyout/test1/">Test 1&lt;/a&gt;&lt;/li&gt;<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;li&gt;&lt;a href="https://geek.hellyer.kiwi/page1/flyout/test2/">Test 2&lt;/a&gt;&lt;/li&gt;<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;li&gt;&lt;a href="https://geek.hellyer.kiwi/page1/flyout/test3/">Test 3&lt;/a&gt;&lt;/li&gt;<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/ul&gt;<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/li&gt;<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;li&gt;&lt;a href="https://geek.hellyer.kiwi/page1/nested1/">Nested 1&lt;/a&gt;&lt;/li&gt;<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;li&gt;&lt;a href="https://geek.hellyer.kiwi/page1/nested2/">Nested 2&lt;/a&gt;&lt;/li&gt;<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/ul&gt;<br />
&nbsp;&nbsp;&nbsp;&nbsp;&lt;/li&gt;<br />
&nbsp;&nbsp;&nbsp;&nbsp;&lt;li&gt;&lt;a href="https://geek.hellyer.kiwi/page2/">Page 2&lt;/a&gt;&lt;/li&gt;<br />
&nbsp;&nbsp;&nbsp;&nbsp;&lt;li&gt;&lt;a href="https://geek.hellyer.kiwi/page3/">Page 3&lt;/a&gt;&lt;/li&gt;<br />
&nbsp;&nbsp;&lt;/ul&gt;<br />
&lt;/li&gt;
				</code></div></div>)
			</th>
		</tr>
	</thead>
	<tfoot>
	<tr>
		<th scope="col"><?php _e( 'Enter the HTML for the \'Custom code 1\' and \'Custom code 2\' menu options above, add your code to the appropriate box above. <strong>Note:</strong> The menu uses an unordered list to format the menu. You will need to know some HTML to use this option. The menu is already wrapped in UL tags.', 'pixopoint_mln' ); ?></th>
	</tr>
	</tfoot>
	<tbody class="plugins">
		<tr class="inactive">
			<td class="desc">
				<div style="float:left;width:48%">
					<h4><?php _e( 'Custom code 1', 'pixopoint_mln' ); ?></h4>
					<p><textarea name="pixopoint-menu[custommenu]" style="height:200px;width:100%;border:1px solid #ddd" value=""><?php echo get_mlnmenu_option( 'custommenu' ); ?></textarea></p>
				</div>
				<div style="float:right;width:48%">
					<h4><?php _e( 'Custom code 2', 'pixopoint_mln' ); ?></h4>
					<p><textarea name="pixopoint-menu[custommenu2]" style="height:200px;width:100%;border:1px solid #ddd" value=""><?php echo get_mlnmenu_option( 'custommenu2' ); ?></textarea></p>
				</div>
				<div style="float:left;width:48%">
					<h4><?php _e( 'Custom code 3', 'pixopoint_mln' ); ?></h4>
					<p><textarea name="pixopoint-menu[custommenu3]" style="height:200px;width:100%;border:1px solid #ddd" value=""><?php echo get_mlnmenu_option( 'custommenu3' ); ?></textarea></p>
				</div>
				<div style="float:right;width:48%">
					<h4><?php _e( 'Custom code 4', 'pixopoint_mln' ); ?></h4>
					<p><textarea name="pixopoint-menu[custommenu4]" style="height:200px;width:100%;border:1px solid #ddd" value=""><?php echo get_mlnmenu_option( 'custommenu4' ); ?></textarea></p>
				</div>
			</td>
		</tr>
	</tbody>
</table>

<div style="clear:both"></div>
</div>

<?php /* Settings tab */ ?>
<div class="tabbertab">
<h2><?php _e( 'Settings', 'pixopoint_mln' ); ?></h2>
<div class="clear"></div>

<table class="widefat" cellspacing="0" id="inactive-plugins-table">
	<thead>
		<tr>
			<th scope="col" colspan="2"><?php _e( 'Setting', 'pixopoint_mln' ); ?></th>
			<th scope="col"><?php _e( 'Description', 'pixopoint_mln' ); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th scope="col" colspan="3"><?php _e( 'Use the various options above to control some of the advanced settings of the plugin', 'pixopoint_mln' ); ?></th>
		</tr>
	</tfoot>
	<tbody class="plugins">
		<tr class='inactive'>
			<th scope='row' class='check-column'>
				<select name="pixopoint-menu[superfish_speed]">
				<?php
					$suckerfish_superfish_speed = get_mlnmenu_option( 'superfish_speed');
					switch ($suckerfish_superfish_speed){
						case "slow":echo '<option>slow</option><option>normal</option><option>fast</option><option>instant</option>';break;
						case "normal":echo '<option>normal</option><option>slow</option><option>fast</option><option>instant</option>';break;
						case "fast":echo '<option>fast</option><option>slow</option><option>normal</option><option>instant</option>';break;
						case "instant":echo '<option>instant</option><option>normal</option><option>slow</option><option>fast</option>';break;
						case "":echo '<option>instant</option><option>normal</option><option>slow</option><option>fast</option>';break;
						}
				?>
				</select>
			</th>
			<td class='name'><?php _e( 'Speed of fade-in effect', 'pixopoint_mln' ); ?></td>
			<td class='desc'>
				<p><?php _e( 'This option enhances the behaviour of the dropdown by creating an animated fade-in effect. The script which powers this part of the plugin is called <a href="http://users.tpg.com.au/j_birch/plugins/superfish/">\'Superfish\'</a> and was created by Joel Birch. This option utilizes <a href="http://jquery.com/">jQuery</a>.', 'pixopoint_mln' ); ?></p>
			</td>
		</tr>
		<tr class='inactive'>
			<th scope='row' class='check-column'>
				<input style="width:60px" name="pixopoint-menu[superfish_delaymouseover]" type="text" value="<?php echo get_mlnmenu_option( 'superfish_delaymouseover' ); ?>" />
			</th>
			<td class='name'><?php _e( 'Mouseover delay (milliseconds)', 'pixopoint_mln' ); ?></td>
			<td class='desc'>
				<p><?php _e( 'This option adds a delay time before the dropdown/flyout appears. This option is controlled by the <a href="http://users.tpg.com.au/j_birch/plugins/superfish/">\'Superfish plugin\'</a> for jQuery.', 'pixopoint_mln' ); ?></p>
			</td>
		</tr>
		<tr class='inactive'>
			<th scope='row' class='check-column'>
				<input style="width:60px" name="pixopoint-menu[delay]" type="text" value="<?php echo get_mlnmenu_option( 'delay' ); ?>" />
			</th>
			<td class='name'><?php _e( 'Hide delay time (milliseconds)', 'pixopoint_mln' ); ?></td>
			<td class='desc'>
				<p><?php _e( 'This option adds a delay before the dropdown disappears. This option is particularly suitable for small menus where users may accidentally hover off of the menu. The script is powered by the <a href="http://users.tpg.com.au/j_birch/plugins/superfish/">\'Superfish plugin\'</a> for <a href="http://jquery.com/">jQuery</a>', 'pixopoint_mln' ); ?></p>
			</td>
		</tr>
		<tr class='inactive'>
			<th scope='row' class='check-column'>
				<select name="pixopoint-menu[superfish_sensitivity]">
				<?php
					$suckerfish_superfish_sensitivity = get_mlnmenu_option( 'superfish_sensitivity');
					switch ($suckerfish_superfish_sensitivity){
						case "high":echo '<option>high</option><option>average</option><option>low</option>';break;
						case "average":echo '<option>average</option><option>high</option><option>low</option>';break;
						case "low":echo '<option>low</option><option>high</option><option>average</option>';break;
						case "":echo '<option>high</option><option>average</option><option>low</option>';break;
						}
				?>
				</select>
			</th>
			<td class='name'><?php _e( 'Sensitivity', 'pixopoint_mln' ); ?></td>
			<td class='desc'>
				<p><?php _e( 'When this option is enabled, the menu will attempt to determine the user\'s intent. On low sensitivity, instead of immediately displaying the dropdown/flyout menu on mouseover, the menu will wait until the user\'s mouse slows down before displaying it.', 'pixopoint_mln' ); ?></p>
			</td>
		</tr>
		<tr class='inactive'>
			<th scope='row' class='check-column'>
				<?php
					if (get_mlnmenu_option( 'keyboard') == 'on') {echo '<input type="checkbox" name="pixopoint-menu[keyboard]" checked="yes" />';}
					else {echo '<input type="checkbox" name="pixopoint-menu[keyboard]" />';}
				?>
			</th>
			<td class='name'><?php _e( 'Enable keyboard accessible menu?', 'pixopoint_mln' ); ?></td>
			<td class='desc'>
				<p><em><?php _e( 'This option may not work correctly as it contains bugs.', 'pixopoint_mln' ); ?></em></p>
				<p><?php _e( 'This option enables users to access your menu via the tab key on their keyboard rather than the mouse. Thanks to <a href="http://www.transientmonkey.com/">malcalevak</a> for writing the script. This option utilizes <a href="http://jquery.com/">jQuery</a>.', 'pixopoint_mln' ); ?></p>
			</td>
		</tr>
		<tr class='inactive'>
			<th scope='row' class='check-column'>
					<?php
					if (get_mlnmenu_option( 'superfish_arrows') == 'on') {echo '<input type="checkbox" name="pixopoint-menu[superfish_arrows]" checked="yes" />';}
					else {echo '<input type="checkbox" name="pixopoint-menu[superfish_arrows]" />';}
				?>
			</th>
			<td class='name'><?php _e( 'Enable arrow mark-up?', 'pixopoint_mln' ); ?></td>
			<td class='desc'>
				<p><?php _e( 'This option adds a small arrow to any menu option which contains children. Thanks to <a href="http://transientmonkey.com/">malcalevak</a> for help with implementing this feature. This option utilizes <a href="http://jquery.com/">jQuery</a>.', 'pixopoint_mln' ); ?></p>
			</td>
		</tr>
		<tr class="<?php if (!function_exists('pixopoint_secondmenu')) {echo 'in';} ?>active">
			<th scope='row' class='check-column'>
				<?php
					// Only added if second menu isn't already specified in theme
					if (!function_exists('pixopoint_secondmenu')) {
						if (get_mlnmenu_option( 'secondmenu') == 'on') {echo '<input type="checkbox" name="pixopoint-menu[secondmenu]" checked="yes" />';}
						else {echo '<input type="checkbox" name="pixopoint-menu[secondmenu]" />';}
					}
					else {echo '<label style="width:15px">&nbsp;&nbsp;X</label>';}
				?>
			</th>
			<td class='name'><?php _e( 'Add a second menu?', 'pixopoint_mln' ); ?></td>
			<td class='desc'>
				<?php
				// Only added if second menu isn't already specified in theme
				if (function_exists('pixopoint_secondmenu')) {?>
				<p><strong><?php _e( 'Note: You can not turn this option off as your theme has been indicated that it has a second menu.', 'pixopoint_mln' ); ?></strong></p>
				<?php } ?>
				<p><?php _e( 'You may add a second menu to your site. This is particularly common with magazine style layouts. For a second menu, add the following code to your theme <code>&lt;?php if (function_exists(\'pixopoint_menu\')) {pixopoint_menu(2);} ?&gt;</code>. The <a href="https://geek.hellyer.kiwi/suckerfish_css/">PixoPoint CSS generator</a> does not currently support a second menu by default, but if you do a search and replace (in a text editor) for <code>suckerfishnav</code> to <code>suckerfishnav_2</code> in the CSS you will be able to adapt the standard CSS for the second menu.', 'pixopoint_mln' ); ?></p>
			</td>
		</tr>
		<tr class="inactive">
			<th scope='row' class='check-column'>
				<?php
					if (get_mlnmenu_option( 'titletags') == 'on') {echo '<input type="checkbox" name="pixopoint-menu[titletags]" checked="yes" />';}
					else {echo '<input type="checkbox" name="pixopoint-menu[titletags]" />';}
				?>
			</th>
			<td class='name'><?php _e( 'Remove title attribute?', 'pixopoint_mln' ); ?></td>
			<td class='desc'>
				<p><?php _e( 'This removes the title attributes from the links in the menu. The title attributes display in most browsers as a small tool tip on hover over the links.', 'pixopoint_mln' ); ?></p>
			</td>
		</tr>
		<tr class='inactive'>
			<th scope='row' class='check-column'>
				<select name="pixopoint-menu[displaycss]">
				<?php
					$suckerfish_displaycss = get_mlnmenu_option( 'displaycss');
					switch ($suckerfish_displaycss){
						case "Inline":echo '<option>Inline</option><option>Disable</option><option>Normal</option>';break;
						case "Disable":echo '<option>Disable</option><option>Inline</option><option>Normal</option>';break;
						case "Normal":echo '<option>Normal</option><option>Inline</option><option>Disable</option>';break;
						case "":echo '<option>Normal</option><option>Inline</option><option>Disable</option>';break;
						}
				?>
				</select>
			</th>
			<td class='name'><?php _e( 'Style sheet', 'pixopoint_mln' ); ?></td>
			<td class='desc'>
				<p><?php _e( 'The plugin includes it\'s own built in stylesheet. However many site owners wish to use their themes built in stylesheet (good idea if you want to reduce the HTML in your page) or wish to specify their CSS inline between their head tags (not recommended).', 'pixopoint_mln' ); ?></p>
			</td>
		</tr>
	</tbody>
</table>

</div>
</div>
<div style="clear:both;padding-top:20px;"></div>
	<p class="submit"><input type="submit" name="Submit" value="<?php _e('Update Options','pixopoint_mln') ?>" /></p>
<div style="clear:both;padding-top:20px;"></div>
</form>
</div><?php
	}	
}

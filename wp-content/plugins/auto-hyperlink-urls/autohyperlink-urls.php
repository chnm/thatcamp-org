<?php
/**
 * Plugin Name: Auto-hyperlink URLs
 * Version:     5.4.1
 * Plugin URI:  http://coffee2code.com/wp-plugins/auto-hyperlink-urls/
 * Author:      Scott Reilly
 * Author URI:  http://coffee2code.com/
 * Text Domain: auto-hyperlink-urls
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Description: Automatically turns plaintext URLs and email addresses into links.
 *
 * Compatible with WordPress 4.7 through 5.3+.
 *
 * =>> Read the accompanying readme.txt file for instructions and documentation.
 * =>> Also, visit the plugin's homepage for additional information and updates.
 * =>> Or visit: https://wordpress.org/plugins/auto-hyperlink-urls/
 *
 * @package Auto_Hyperlink_URLs
 * @author  Scott Reilly
 * @version 5.4.1
 */

/*
 * TODO:
 * - Test against oembeds (and Viper's Video Quicktags). Run at 11+ priority?
 * - More tests (incl. testing filters)
 * - Ability to truncate middle of link http://domain.com/som...file.php (config options for
 *   # of chars for first part, # of chars for ending, and truncation string?)
 * - Ability to link plain-text phone numbers.
 *   See https://wordpress.org/support/topic/telephone-numbers-to-urls/
 * - Ability to disable linking for non-protocoled links.
 *   See https://wordpress.org/support/topic/more-option-please/
 * - Ability to output icon denoting external links
 *   See https://wordpress.org/support/topic/add-an-icon-for-external-links/
 * - Ability to disable linking for a specific link. Could prefix link with special char,
 *   e.g. !example.com or !http://example.com. A meta box could allow for explicit listing.
 * - Consider using https://github.com/iamcal/lib_autolink to handle auto-linking.
 * - Add support for Event Manager plugin
 *   See https://wordpress.org/support/topic/support-events-managers-custom-post-types-events-and-locations/
 *   See https://wordpress.org/support/topic/events-manager-23/
 * - Add setting to disable ACF support? (Can already be done via filters.)
 * - Add setting to specify additional filters to be handled by the plugin
 *   Re: https://wordpress.org/support/topic/it-doesnt-work-226/
 * - Default protocol-less URLs to 'https' instead of 'http' (possible controlled by setting)
 */

/*
	Copyright (c) 2004-2020 by Scott Reilly (aka coffee2code)

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

defined( 'ABSPATH' ) or die();

if ( ! class_exists( 'c2c_AutoHyperlinkURLs' ) ) :

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'c2c-plugin.php' );

final class c2c_AutoHyperlinkURLs extends c2c_AutoHyperlinkURLs_Plugin_049 {

	/**
	 * Name of plugin's setting.
	 *
	 * @var string
	 */
	const SETTING_NAME = 'c2c_autohyperlink_urls';

	/**
	 * The one true instance.
	 *
	 * @var c2c_AutoHyperlinkURLs
	 */
	public static $instance;

	/**
	 * Memoized array of TLDs.
	 *
	 * @since 5.0
	 * @var array
	 */
	public static $tlds = array();

	/**
	 * Returns singleton instance.
	 *
	 * @since 5.0
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	protected function __construct() {
		parent::__construct( '5.4.1', 'autohyperlink-urls', 'c2c', __FILE__, array() );
		// TODO: Temporary fix. The slug specified for the parent constructor
		// should actually be this value, but at the very least it affects the
		// plugin's setting name, so changing it requires a migration.
		$this->id_base = 'auto-hyperlink-urls';
		register_activation_hook( __FILE__, array( __CLASS__, 'activation' ) );

		return self::$instance = $this;
	}

	/**
	 * Handles activation tasks, such as registering the uninstall hook.
	 *
	 * @since 4.0
	 */
	public static function activation() {
		register_uninstall_hook( __FILE__, array( __CLASS__, 'uninstall' ) );
	}

	/**
	 * Handles uninstallation tasks, such as deleting plugin options.
	 *
	 * @since 4.0
	 */
	public static function uninstall() {
		delete_option( self::SETTING_NAME );
	}

	/**
	 * Resets plugin options.
	 *
	 * @since 5.0
	 */
	public function reset_options() {
		parent::reset_options();
		self::$tlds = array();
	}

	/**
	 * Initializes the plugin's config data array.
	 */
	public function load_config() {
		$this->name      = __( 'Auto-hyperlink URLs', 'auto-hyperlink-urls' );
		$this->menu_name = __( 'Auto-hyperlink', 'auto-hyperlink-urls' );

		$this->config = array(
			'hyperlink_comments' => array(
				'input'    => 'checkbox',
				'default'  => true,
				'label'    => __( 'Auto-hyperlink comments?', 'auto-hyperlink-urls' ),
				'help'     => __( 'Note that if disabled WordPress&#8217;s built-in hyperlinking function will still be performed, which links email addresses and text URLs with explicit URI schemes.', 'auto-hyperlink-urls' ),
			),
			'hyperlink_emails' => array(
				'input'    => 'checkbox',
				'default'  => true,
				'label'    => __( 'Hyperlink email addresses?', 'auto-hyperlink-urls' ),
			),
			'strip_protocol' => array(
				'input'    => 'checkbox',
				'default'  => true,
				'label'    => __( 'Strip URI scheme?', 'auto-hyperlink-urls' ),
				'help'     => sprintf(
					__( 'Remove the <a href="%s">URI scheme</a> (i.e. "http://") from the displayed auto-hyperlinked link?', 'auto-hyperlink-urls' ),
					'https://en.wikipedia.org/wiki/Uniform_Resource_Identifier#Conceptual_distinctions'
				),
			),
			'open_in_new_window' => array(
				'input'    => 'checkbox',
				'default'  => false,
				'label'    => __( 'Open auto-hyperlinked links in new window?', 'auto-hyperlink-urls' ),
			),
			'nofollow' => array(
				'input'    => 'checkbox',
				'default'  => false,
				'label'    => sprintf( __( 'Enable <a href="%s">nofollow</a>?', 'auto-hyperlink-urls' ), 'http://en.wikipedia.org/wiki/Nofollow' ),
			),
			'require_scheme' => array(
				'input'    => 'checkbox',
				'default'  => false,
				'label'    => __( 'Require explicit URI scheme?', 'auto-hyperlink-urls' ),
				'help'     => __( 'Only links with an explicit URI scheme (e.g. "http://", "https://") will be auto-hyperlinked.', 'auto-hyperlink-urls' ),
			),
			'hyperlink_mode' => array(
				'input'    => 'shorttext',
				'default'  => 0,
				'label'    => __( 'Hyperlink Mode/Truncation', 'auto-hyperlink-urls' ),
				'help'     => __( 'This determines what text should appear as the link.  Use <code>0</code> to show the full URL, use <code>1</code> to show just the hostname, or use a value greater than <code>10</code> to indicate how many characters of the URL you want shown before it gets truncated.  <em>If</em> text gets truncated, the truncation before/after text values below will be used.', 'auto-hyperlink-urls' ),
			),
			'truncation_before_text' => array(
				'input'    => 'text',
				'default'  => '',
				'label'    => __( 'Text to show before link truncation', 'auto-hyperlink-urls' ),
			),
			'truncation_after_text' => array(
				'input'    => 'text',
				'default'  => '...',
				'label'    => __( 'Text to show after link truncation', 'auto-hyperlink-urls' ),
			),
			'more_extensions' => array( 'input' => 'text',
				'default'  => '',
				'label'    => __( 'Extra domain extensions', 'auto-hyperlink-urls' ),
				'help'     => __( 'Space and/or comma-separated list of extensions/<acronym title="Top-Level Domains">TLDs</acronym>.<br />These are already built-in: com, org, net, gov, edu, mil, us, info, biz, ws, name, mobi, cc, tv', 'auto-hyperlink-urls' ),
			),
			'exclude_domains' => array(
				'input'    => 'inline_textarea',
				'datatype' => 'array',
				'no_wrap'  => true,
				'input_attributes' => 'rows="6"',
				'label'    => __( 'Exclude domains', 'auto-hyperlink-urls' ),
				'help'     => __( 'List domains that should NOT get automatically hyperlinked. One domain per line. Do not include URI scheme (e.g. "http://") or trailing slash.', 'auto-hyperlink-urls' ),
			),
			'enable_3p_advanced_custom_fields' => array(
				'input'    => 'checkbox',
				'default'  => false,
				'label'    => __( 'Enable support for plugin: Advanced Custom Fields?', 'auto-hyperlink-urls' ),
			),
		);
	}

	/**
	 * Overrides the plugin framework's register_filters() to actually actions
	 * against filters.
	 */
	public function register_filters() {
		$options = $this->get_options();
		$filters = array();

		if ( $options[ 'enable_3p_advanced_custom_fields' ] ) {
			/**
			 * Filters Advanced Custom Field hooks that get processed for auto-
			 * linkification.
			 *
			 * Supported hooks:
			 *    'acf/format_value/type=text',
			 *    'acf/format_value/type=textarea',
			 *    'acf/format_value/type=url',
			 *    'acf_the_content',
			 *
			 * @since 3.9
			 *
			 * @param array $filters The ACF filters that get processed for auto-
			 *                       linkification. See filter inline docs for defaults.
			 */
			$filters = (array) apply_filters( 'c2c_autohyperlink_urls_acf_filters', array(
				'acf/format_value/type=text',
				'acf/format_value/type=textarea',
				'acf/format_value/type=url',
				//'acf/format_value/type=wysiwyg',
				'acf_the_content',
			) );
		}

		// Add in relevant stock WP filters.
		$filters = array_merge( $filters, array( 'the_content', 'the_excerpt', 'widget_text' ) );

		/**
		 * Filters the list of filters that get processed for auto-hyperlinking.
		 *
		 * @param array $filters The list of filters. Default ['the_content', 'the_excerpt', 'widget_text'].
		 */
		$filters = (array) apply_filters( 'c2c_autohyperlink_urls_filters', $filters );

		foreach( $filters as $filter ) {
			add_filter( $filter, array( $this, 'hyperlink_urls' ), 9 );
		}

		if ( $options['hyperlink_comments'] ) {
			remove_filter( 'comment_text', 'make_clickable', 9 );
			add_filter( 'comment_text', array( $this, 'hyperlink_urls' ), 9 );
		}
	}

	/**
	 * Outputs the text above the setting form.
	 *
	 * @param string $localized_heading_text (optional) Localized page heading text.
	 */
	public function options_page_description( $localized_heading_text = '' ) {
		parent::options_page_description( __( 'Auto-hyperlink URLs', 'auto-hyperlink-urls' ) );

		echo '<p>' . __( 'Automatically hyperlink text URLs and email addresses originally written only as plaintext.', 'auto-hyperlink-urls' ) . '</p>';
	}

	/**
	 * Returns the class name(s) to be used for links created by Auto-hyperlinks.
	 *
	 * Default value is 'autohyperlink'. Can be filtered via the
	 * 'autohyperlink_urls_class' filter.
	 *
	 * @return string Class to assign to link.
	 */
	public function get_class() {
		/**
		 * Filters the class name used for links created by Auto-hyperlinks.
		 *
		 * @since 3.5
		 *
		 * @param string $class The class name. Default 'autohyperlink'.
		 */
		return apply_filters( 'autohyperlink_urls_class', 'autohyperlink' );
	}

	/**
	 * Returns the link attributes to be used for links created by Auto-hyperlinks.
	 *
	 * Utilizes plugin options to determine if attributes such as 'target' and
	 * 'nofollow' should be used. Calls get_class() to determine the
	 * appropriate class name(s).
	 *
	 * Can be filtered via 'autohyperlink_urls_link_attributes' filter.
	 *
	 * @param  string $title   Optional. The text for the link's title attribute.
	 * @param  string $context Optional. The context for the link attributes. Either 'url' or 'email'. Default 'url'.
	 * @return string The entire HTML attributes string to be used for link.
	 */
	public function get_link_attributes( $title = '', $context = 'url' ) {
		$options = $this->get_options();

		$context = 'email' === $context ? 'email' : 'url';

		$link_attributes['class'] = $this->get_class();

		// URL specific attributes.
		if ( 'url' === $context ) {
			if ( $options['open_in_new_window'] ) {
				$link_attributes['target'] = '_blank';
			}

			if ( $options['nofollow'] ) {
				$link_attributes['rel'] = 'nofollow';
			}
		}

		/**
		 * Filters the attributes used for links created by Auto-hyperlinks.
		 *
		 * @since 3.5
		 *
		 * @param array  $attributes The link attributes.
		 * @param string $context    The context for the link. Either 'url' or 'email'. Default 'url'.
		 * @param string $title      The text for the link's title attribute.
		 */
		$link_attributes = (array) apply_filters( 'autohyperlink_urls_link_attributes', $link_attributes, $context, $title );

		// Assemble the attributes into a string.
		$output_attributes = '';
		foreach ( $link_attributes as $key => $val ) {
			$output_attributes .= $key . '="' . esc_attr( $val ) . '" ';
		}

		return trim( $output_attributes );
	}

	/**
	 * Returns the TLDs recognized by the plugin.
	 *
	 * Returns a '|'-separated string of TLDs recognized by the plugin to be
	 * used in searches for text links without URI scheme.
	 *
	 * By default this is:
	 * 'com|org|net|gov|edu|mil|us|info|biz|ws|name|mobi|cc|tv'.  More
	 * extensions can be added via the plugin's settings page.
	 *
	 * @return string The '|'-separated string of TLDs.
	 */
	public function get_tlds() {
		if ( ! self::$tlds ) {
			$options = $this->get_options();

			// The default TLDs.
			self::$tlds = 'com|org|net|gov|edu|mil|us|info|biz|ws|name|mobi|cc|tv';

			// Add TLDs defined via options.
			if ( $options['more_extensions'] ) {
				self::$tlds .= '|' . implode( '|', array_map( 'trim', explode( '|', str_replace( array( ', ', ' ', ',' ), '|', $options['more_extensions'] ) ) ) );
			}
		}

		/**
		 * Filters the list of recognized TLDs for auto-linking.
		 *
		 * @since 3.5
		 *
		 * @param string $tlds The '|'-separated string of TLDs. Default
		 *                     'com|org|net|gov|edu|mil|us|info|biz|ws|name|mobi|cc|tv'`.
		 */
		$tlds = apply_filters( 'autohyperlink_urls_tlds', self::$tlds );

		// Sanitize TLDs for use in regex.
		$safe_tlds = array();
		foreach ( explode( '|', $tlds ) as $tld ) {
			if ( $tld ) {
				$safe_tlds[] = preg_quote( $tld, '#' );
			}
		}

		return implode( '|', $safe_tlds );
	}

	/**
	 * Truncates a URL according to plugin settings.
	 *
	 * Based on various plugin settings, this function will potentially
	 * truncate the supplied URL, optionally adding text before and/or
	 * after the URL if truncated.
	 *
	 * @param string $url     The URL to potentially truncate
	 * @param string $context Optional. The context for the link. Either 'url' or 'email'. Default 'email'.
	 * @return string the potentially truncated version of the URL
	 */
	public function truncate_link( $url, $context = 'url' ) {
		$options         = $this->get_options();
		$mode            = intval( $options['hyperlink_mode'] );
		$more_extensions = $options['more_extensions'];
		$trunc_before    = $options['truncation_before_text'];
		$trunc_after     = $options['truncation_after_text'];
		$original_url    = $url;

		if ( 1 === $mode ) {
			$url = preg_replace( "#(([a-z]+?):\\/\\/[a-z0-9\-\:@]+).*#i", "$1", $url );
			$extensions = $this->get_tlds();
			$url = $trunc_before . preg_replace( "/([a-z0-9\-\:@]+\.($extensions)).*/i", "$1", $url ) . $trunc_after;
		} elseif ( ( $mode > 10 ) && ( strlen( $url ) > $mode ) ) {
			$url = $trunc_before . substr( $url, 0, $mode ) . $trunc_after;
		}

		if ( 'email' === $context ) {
			$url = esc_attr( $url );
		} elseif ( preg_match( "~^[a-z]+://~i", $url ) ) {
			$url = esc_url( $url );
		} else {
			$ourl = 'http://' . $url;
			$url = substr( esc_url( $ourl ), 7 );
		}

		/**
		 * Filters link truncation.
		 *
		 * @since 3.5
		 *
		 * @param string $url          The potentially truncated URL.
		 * @param string $original_url The full, original URL.
		 * @param string $context      The context for the link. Either 'url' or 'email'. Default 'url'.
		 */
		return apply_filters( 'autohyperlink_urls_truncate_link', $url, $original_url, $context );
	}

	/**
	 * Hyperlinks plaintext links within text.
	 *
	 * @see make_clickable() in core, parts of which were adapted here.
	 *
	 * @param  string $text The text to have its plaintext links hyperlinked.
	 * @param  array  $args An array of configuration options, each element of which will override the plugin's corresponding default setting.
	 * @return string The hyperlinked version of the text.
	 */
	public function hyperlink_urls( $text, $args = array() ) {
		$r               = '';
		$textarr         = preg_split( '/(<[^<>]+>)/', $text, -1, PREG_SPLIT_DELIM_CAPTURE ); // split out HTML tags
		$nested_code_pre = 0; // Keep track of how many levels link is nested inside tags
		foreach ( $textarr as $piece ) {
			/**
			 * Filters the HTML tags the contents of which will be excluded from
			 * auto-linkification.
			 *
			 * @since 5.4
			 *
			 * @param $tags array Array of HTML tags. Default ['code', 'pre',
			 *                    'script', 'style'].
			 */
			$no_content_autolink = (array) apply_filters( 'autohyperlink_no_autolink_content_tags', array( 'code', 'pre', 'script', 'style' ) );

			if ( preg_match( '#^<(' . implode( '|', array_map( 'preg_quote', $no_content_autolink ) )  . ')[\s>]#i', $piece ) ) {
				$nested_code_pre++;
			} elseif ( $nested_code_pre && in_array( substr( strtolower( $piece ), 2, -1 ), $no_content_autolink ) ) {
				$nested_code_pre--;
			}

			if ( $nested_code_pre || empty( $piece ) || ( $piece[0] === '<' && ! preg_match( '|^<\s*[\w]{1,20}+://|', $piece ) ) ) {
				$r .= $piece;
				continue;
			}

			// Long strings might contain expensive edge cases ...
			if ( 10000 < strlen( $piece ) ) {
				// ... break it up
				foreach ( _split_str_by_whitespace( $piece, 2100 ) as $chunk ) { // 2100: Extra room for scheme and leading and trailing paretheses
					if ( 2101 < strlen( $chunk ) ) {
						$r .= $chunk; // Too big, no whitespace: bail.
					} else {
						$r .= $this->hyperlink_urls( $chunk, $args );
					}
				}
			} else {
				$options = $this->get_options();

				if ( $args ) {
					$options = $this->options = wp_parse_args( $args, $options );
				}

				// Temporarily introduce a leading and trailing single space to the text to simplify regex handling.
				$ret = " $piece ";

				// Get the regex-style list of domain extensions that are acceptable for links without URI scheme.
				$extensions = $this->get_tlds();

				// Link email addresses, if enabled to do so.
				if ( $options['hyperlink_emails'] ) {
					$ret = preg_replace_callback(
						'#(?!<.*?)([.0-9a-z_+-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})(?![^<>]*?>)#i',
						array( $this, 'do_hyperlink_email' ),
						$ret
					);
				}

				// Link links that don't have a URI scheme.
				if ( ! $options['require_scheme'] ) {
					$ret = preg_replace_callback(
						"#(?!<.*?)([\s{}\(\)\[\]>,\'\";:])([a-z0-9]+[a-z0-9\-\.]*)\.($extensions)((?:[/\#?][^\s<{}\(\)\[\]]*[^\.,\s<{}\(\)\[\]]?)?)(?![^<>]*?>)#is",
						array( $this, 'do_hyperlink_url_no_uri_scheme' ),
						$ret
					);
				}

				// Link links that have an explicit URI scheme.
				$scheme_regex =  '~
					(?!<.*?)                                           # Non-capturing check to ensure not matching what looks like the inside of an HTML tag.
					(?<=[\s>.,:;!?])                                   # Leading whitespace or character.
					(\(?)                                              # 1: Maybe an open parenthesis?
					(                                                  # 2: Full URL
						([\w]{1,20}?://)                               # 3: Scheme and hier-part prefix
						(                                              # 4: URL minus URI scheme
							(?=\S{1,2000}\s)                           # Limit to URLs less than about 2000 characters long
							[\\w\\x80-\\xff#%\\~/@\\[\\]*(+=&$-]*+     # Non-punctuation URL character
							(?:                                        # Unroll the Loop: Only allow puctuation URL character if followed by a non-punctuation URL character
								[\'.,;:!?)]                            # Punctuation URL character
								[\\w\\x80-\\xff#%\\~/@\\[\\]*()+=&$-]++ # Non-punctuation URL character
							)*
						)
					)
					(\)?)                                              # 5: Trailing closing parenthesis (for parethesis balancing post processing)
					(?![^<>]*?>)                                       # Check to ensure not within what looks like an HTML tag.
				~ixS';

				$ret = preg_replace_callback(
					$scheme_regex,
					array( $this, 'do_hyperlink_url' ),
					$ret
				);

				// Remove temporarily added leading and trailing single spaces.
				$ret = substr( $ret, 1, -1 );

				$r .= $ret;

			} // else

		} //foreach

		// Remove links within links
		return preg_replace(
			'#'
				. '(<a\s+[^>]+>)'    // 1: Opening link tag with any number of attributes
				. '('                // 2: Contents of the link tag
				.     '(?:'          // Non-capturing group
				.         '(?!</a>)' // Not followed by closing link tag
				.         '.'        // Any character
				.     ')'            // End of non-capturing group
				.     '*'            // 0 or more characters
				. ')'                // End of 2:
				. '<a\s[^>]+>'       // Embedded opening link tag with any number of attributes
				. '([^<]*)'          // 3: Contents of the embedded link tag
				. '</a>'             // Closing embedded link tag
			. '#iU',
			'$1$2$3',
			$r
		);
	}

	/**
	 * Should the hyperlinking be performed?
	 *
	 * At the point before the plugin constructs the actual markup for the link,
	 * should the text link actually get linked?
	 *
	 * @since 5.0
	 *
	 * @param  string $url    The URL to hyperlink.
	 * @param  string $domain Optional. The domain part of the URL, if known.
	 * @return bool   True if the URL can be hyperlinked, false if not.
	 */
	protected function can_do_hyperlink( $url, $domain = '' ) {
		$options = $this->get_options();

		// If domain wasn't provided, figure it out.
		if ( ! $domain ) {
			$parts = parse_url( $url );
			if ( ! $parts || empty( $parts['host'] ) ) {
				return false;
			}
			$domain = $parts['host'];
		}

		/**
		 * Filters Allow custom exclusions from hyperlinking.
		 *
		 * @since 5.0
		 *
		 * @param bool   $autolink Should the link be hyperlinked? Default true.
		 * @param string $url      The URL to be hyperlinked.
		 * @param string $domain   The domain/host part of the URL.
		 */
		if ( ! (bool) apply_filters( 'autohyperlink_urls_custom_exclusions', true, $url, $domain ) ) {
			return false;
		}

		/**
		 * Filters domains that are explicitly excluded from getting auto-linked.
		 *
		 * @since 5.0
		 *
		 * @param array $excluded_domains The excluded domains.
		 */
		$exclude_domains = (array) apply_filters( 'autohyperlink_urls_exclude_domains', $options['exclude_domains'] );

		foreach ( $exclude_domains as $exclude ) {
			if ( strcasecmp( $domain, $exclude ) == 0 ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Callback to create the replacement text for hyperlinks.
	 *
	 * @param  array  $matches Matches as generated by a `preg_replace_callback()`.
	 * @return string Replacement string.
	 */
	public function do_hyperlink_url( $matches ) {
		$options = $this->get_options();

		$url = $matches[2];

		// Check to see if the link should actually be hyperlinked.
		if ( ! $this->can_do_hyperlink( $url ) ) {
			return $matches[0];
		}

		// If an opening parenthesis was captured, but not a closing one, then check
		// if the closing parenthesis is included as part of URL. If so, it and
		// anything after should not be part of the URL.
		if ( '(' === $matches[1] && empty( $matches[5] ) ) {
			if ( false !== ( $pos = strrpos( $url, ')' ) ) ) {
				$matches[5] = substr( $url, $pos );
				$url = substr( $url, 0, $pos );
			}
		}

		// If the URL has more closing parentheses than opening, then an extra
		// parenthesis got errantly included as part of the URL, so exclude it.
		// Note: This is most likely case, though edge cases definitely exist.
		if ( substr_count( $url, '(' ) < substr_count( $url, ')' ) ) {
			$pos = strrpos( $url, ')' );
			$matches[5] = substr( $url, $pos ) . $matches[5];
			$url = substr( $url, 0, $pos );
		}

		// If the link ends with punctuation, assume it wasn't meant to be part of
		// the URL.
		$last_char = substr( $url, -1 );
		if ( in_array( $last_char, array( "'", '.', ',', ';', ':', '!', '?' ) ) ) {
			$matches[5] = $last_char . $matches[5];
			$url = substr( $url, 0, -1 );
		}

		$url = esc_url( $url );

		// Check whether URI scheme should be retained for link text.
		$link_text = $url;
		if ( $options['strip_protocol'] ) {
			$n = strpos( $url, '://' );
			if ( false !== $n ) {
				$link_text = substr( $url, $n+3 );
			}
		}

		return $matches[1]
			. sprintf( '<a href="%s"%s>%s</a>', esc_url( $url ), rtrim( ' ' . $this->get_link_attributes( $url ) ), $this->truncate_link( $link_text ) )
			. $matches[5];
	}

	/**
	 * Callback to create the replacement text for hyperlinks without
	 * URI scheme.
	 *
	 * @param  array  $matches Matches as generated by a `reg_replace_callback()`.
	 * @return string Replacement string
	 */
	public function do_hyperlink_url_no_uri_scheme( $matches ) {
		$dest = $matches[2] . '.' . $matches[3] . $matches[4];

		// Check to see if the link should actually be hyperlinked.
		if ( ! $this->can_do_hyperlink( $matches[0], $dest ) ) {
			return $matches[0];
		}

		// If the link ends in a question mark, pull the question mark out of the URL
		// and append to link text.
		if ( '?' === substr( $dest, -1 ) ) {
			$dest  = substr( $dest, 0, -1 );
			$after = '?';
		} else {
			$after = '';
		}
		return $matches[1]
			. sprintf( '<a href="%s"%s>%s</a>', esc_url( "http://$dest" ), rtrim( ' ' . $this->get_link_attributes( "http://$dest" ) ), $this->truncate_link( $dest ) )
			. $after;
	}

	/**
	 * Callback to create the replacement text for emails.
	 *
	 * @param  array  $matches Matches as generated by a `preg_replace_callback()`.
	 * @return string Replacement string.
	 */
	public function do_hyperlink_email( $matches ) {
		$email = $matches[1] . '@' . $matches[2];

		return sprintf(
			'<a href="mailto:%s"%s>%s</a>',
			esc_attr( $email ),
			rtrim( ' ' . $this->get_link_attributes( $email, 'email' ) ),
			$this->truncate_link( $email, 'email' )
		);
	}
} // end c2c_AutoHyperlinkURLs

add_action( 'plugins_loaded', array( 'c2c_AutoHyperlinkURLs', 'get_instance' ) );

endif; // end if !class_exists()

/*
 * TEMPLATE TAGS
 */
if ( ! function_exists( 'c2c_autohyperlink_truncate_link' ) ) :
	/**
	 * Truncates a URL according to plugin settings.
	 *
	 * Based on various plugin settings, this function will potentially
	 * truncate the supplied URL, optionally adding text before and/or
	 * after the URL if truncated.
	 *
	 * @param  string $url The URL to potentially truncate.
	 * @return string The potentially truncated version of the URL.
	 */
	function c2c_autohyperlink_truncate_link( $url ) {
		return c2c_AutoHyperlinkURLs::get_instance()->truncate_link( $url );
	}
endif;

if ( ! function_exists( 'c2c_autohyperlink_link_urls' ) ) :
	/**
	 * Hyperlinks plaintext links within text.
	 *
	 * @param  string $text The text to have its plaintext links hyperlinked.
	 * @param  array  $args An array of configuration options, each element of which will override the plugin's corresponding default setting.
	 * @return The hyperlinked version of the text.
	 */
	function c2c_autohyperlink_link_urls( $text, $args = array() ) {
		return c2c_AutoHyperlinkURLs::get_instance()->hyperlink_urls( $text, $args );
	}
endif;


/**
 * DEPRECATED
 */

if ( ! function_exists( 'autohyperlink_truncate_link' ) ) :
	/**
	 * @deprecated since 4.0 Use c2c_autohyperlink_truncate_link()
	 */
	function autohyperlink_truncate_link( $url ) {
		_deprecated_function( __FUNCTION__, '4.0', 'c2c_autohyperlink_truncate_link()' );
		return c2c_autohyperlink_truncate_link( $url );
	}
endif;

if ( ! function_exists( 'autohyperlink_link_urls' ) ) :
	/**
	 * @deprecated since 4.0 Use c2c_autohyperlink_link_urls()
	 */
	function autohyperlink_link_urls( $text ) {
		_deprecated_function( __FUNCTION__, '4.0', 'c2c_autohyperlink_link_urls()' );
		return c2c_autohyperlink_link_urls( $text );
	}
endif;

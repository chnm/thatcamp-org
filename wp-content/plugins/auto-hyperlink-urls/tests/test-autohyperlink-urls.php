<?php

defined( 'ABSPATH' ) or die();

class Autohyperlink_URLs_Test extends WP_UnitTestCase {

	public static function setUpBeforeClass() {
		c2c_AutoHyperlinkURLs::get_instance()->install();
	}

	public function setUp() {
		parent::setUp();
	}

	public function tearDown() {
		parent::tearDown();

		// Reset options.
		c2c_AutoHyperlinkURLs::get_instance()->reset_options();

		// Remove hooks.
		remove_filter( 'autohyperlink_urls_class',             array( $this, 'autohyperlink_urls_class' ) );
		remove_filter( 'autohyperlink_urls_link_attributes',   array( $this, 'autohyperlink_urls_link_attributes' ), 10, 3 );
		remove_filter( 'autohyperlink_urls_tlds',              array( $this, 'autohyperlink_urls_tlds' ) );
		remove_filter( 'autohyperlink_urls_exclude_domains',   array( $this, 'autohyperlink_urls_exclude_domains' ) );
		remove_filter( 'autohyperlink_urls_custom_exclusions', array( $this, 'autohyperlink_urls_custom_exclusions' ), 10, 3 );
	}


	//
	//
	// DATA PROVIDERS
	//
	//


	public static function get_default_filters() {
		return array(
			array( 'the_content' ),
			array( 'the_excerpt' ),
			array( 'widget_text' ),
		);
	}

	public static function get_comment_filters() {
		return array(
			array( 'get_comment_text' ),
			array( 'get_comment_excerpt' ),
		);
	}

	public static function get_protocols() {
		return array(
			array( 'http' ),
			array( 'https' ),
			array( 'ftp' ),
		);
	}

	public static function get_tlds() {
		return array_map( function($v) { return array( $v ); }, explode( '|', c2c_AutoHyperlinkURLs::get_instance()->get_tlds() ) );
	}

	public static function get_ending_punctuation() {
		return array(
			array( '.' ),
			array( ',' ),
			array( '!' ),
			array( '?' ),
			array( ';' ),
			array( ':' ),
		);
	}

	public static function get_punctuation_bookends() {
		return array(
			array( '(', ')' ),
			array( '[', ']' ),
			array( '{', '}' ),
			array( '>', '<' ),
//			array( '.', '.' ),
			array( ',', ',' ),
			array( "'", "'" ),
			array( '"', '"' ),
			array( ':', ':' ),
			array( ';', ';' ),
			array( ';', '&' ),
			array( "\n", "\n"),
		);
	}

	public function data_script_and_style_tags_with_email() {
		return array(
			array(
				'<code>test@example.com</code>',
			),
			array(
				'<pre>test@example.com</pre>',
			),
			array(
				'<code>aaa test@example.com bbb</code>',
			),
			array(
				'<pre>aaa test@example.com bbb</pre>',
			),
			array(
				'<script>test@example.com</script>',
			),
			array(
				'<style>test@example.com</style>',
			),
			array(
				'<script type="text/javascript">test@example.com/script>',
			),
			array(
				'<style type="text/css">test@example.com</style>',
			),
		);
	}

	public function data_script_and_style_tags_with_non_scheme_url() {
		return array(
			array(
				'<code>example.com</code>',
			),
			array(
				'<code>aaa example.com bbb</code>',
			),
			array(
				'<pre>example.com</pre>',
			),
			array(
				'<script>example.com</script>',
			),
			array(
				'<style>example.com</style>',
			),
			array(
				'<script type="text/javascript">example.com/script>',
			),
			array(
				'<style type="text/css">example.com</style>',
			),
		);
	}


	//
	//
	// HELPER FUNCTIONS
	//
	//


	public function set_option( $settings = array() ) {
		$obj = c2c_AutoHyperlinkURLs::get_instance();
		$defaults = $obj->get_options();
		$settings = wp_parse_args( (array) $settings, $defaults );
		$obj->update_option( $settings, true );
	}

	public function autolink_text( $text, $args = array() ) {
		return c2c_AutoHyperlinkURLs::get_instance()->hyperlink_urls( $text, $args );
	}

	public function autohyperlink_urls_class( $class ) {
		return 'customclass';
	}

	public function autohyperlink_urls_link_attributes( $attributes, $context = 'url', $title = '' ) {
		if ( $title ) {
			$attributes['title'] = $title;
		}
		return $attributes;
	}

	public function autohyperlink_urls_tlds( $tlds ) {
		if ( $tlds ) {
			$tlds .= '|';
		}
		return $tlds . 'dev|co|io';
	}

	public function autohyperlink_urls_exclude_domains( $exclusions ) {
		$exclusions[] = 'example.com';
		return $exclusions;
	}

	public function autohyperlink_urls_custom_exclusions( $allow, $url, $domain ) {
		return ! ( 'e' ===  $domain[0] );
	}


	//
	//
	// TESTS
	//
	//


	public function test_class_exists() {
		$this->assertTrue( class_exists( 'c2c_AutoHyperlinkURLs' ) );
	}

	public function test_plugin_framework_class_name() {
		$this->assertTrue( class_exists( 'c2c_AutoHyperlinkURLs_Plugin_047' ) );
	}

	public function test_plugin_framework_version() {
		$this->assertEquals( '047', c2c_AutoHyperlinkURLs::get_instance()->c2c_plugin_version() );
	}

	public function test_get_version() {
		$this->assertEquals( '5.2', c2c_AutoHyperlinkURLs::get_instance()->version() );
	}

	public function test_instance_object_is_returned() {
		$this->assertTrue( is_a( c2c_AutoHyperlinkURLs::get_instance(), 'c2c_AutoHyperlinkURLs' ) );
	}

	/*
	 * Setting defaults.
	 */

	public function test_default_value_of_hyperlink_comments() {
		$options = c2c_AutoHyperlinkURLs::get_instance()->get_options();
		$this->assertTrue( $options['hyperlink_comments'] );
	}

	public function test_default_value_of_hyperlink_emails() {
		$options = c2c_AutoHyperlinkURLs::get_instance()->get_options();
		$this->assertTrue( $options['hyperlink_emails'] );
	}

	public function test_default_value_of_strip_protocol() {
		$options = c2c_AutoHyperlinkURLs::get_instance()->get_options();
		$this->assertTrue( $options['strip_protocol'] );
	}

	public function test_default_value_of_open_in_new_window() {
		$options = c2c_AutoHyperlinkURLs::get_instance()->get_options();
		$this->assertFalse( $options['open_in_new_window'] );
	}

	public function test_default_value_of_nofollow() {
		$options = c2c_AutoHyperlinkURLs::get_instance()->get_options();
		$this->assertFalse( $options['nofollow'] );
	}

	public function test_default_value_of_require_scheme() {
		$options = c2c_AutoHyperlinkURLs::get_instance()->get_options();
		$this->assertFalse( $options['require_scheme'] );
	}

	public function test_default_value_of_hyperlink_mode() {
		$options = c2c_AutoHyperlinkURLs::get_instance()->get_options();
		$this->assertEquals( 0, $options['hyperlink_mode'] );
	}

	public function test_default_value_of_truncation_before_text() {
		$options = c2c_AutoHyperlinkURLs::get_instance()->get_options();
		$this->assertEmpty( $options['truncation_before_text'] );
	}

	public function test_default_value_of_truncation_after_text() {
		$options = c2c_AutoHyperlinkURLs::get_instance()->get_options();
		$this->assertEquals( '...', $options['truncation_after_text'] );
	}

	public function test_default_value_of_more_extensions() {
		$options = c2c_AutoHyperlinkURLs::get_instance()->get_options();
		$this->assertEmpty( $options['more_extensions'] );
	}

	public function test_default_value_of_exclude_domains() {
		$options = c2c_AutoHyperlinkURLs::get_instance()->get_options();
		$this->assertEmpty( $options['exclude_domains'] );
		$this->assertTrue( is_array( $options['exclude_domains'] ) );
	}

	/*
	 * Linking.
	 */

	public function test_basic_autolinking( $url = 'http://coffee2code.com', $text = '', $before = '', $after = '', $strip = true ) {
		$url = esc_url( $url );
		$out_text = $strip ? preg_replace( '~^.+://(.+)$~U', '$1', $url ) : $url;

		if ( empty( $text ) ) {
			$text = $out_text; //preg_replace( '~^.+://(.+)$~', '$1', $url );
		}


		$expected =  $before . '<a href="' . esc_url( $url ) . '" class="autohyperlink">' . $out_text . '</a>' . $after;

		$this->assertEquals(
			$expected,
			c2c_autohyperlink_link_urls( $text )
		);
	}

	public function test_linking_domain_with_trailing_slash() {
		$this->test_basic_autolinking( 'http://coffee2code.com/', 'coffee2code.com/' );
	}

	public function test_linking_URL_with_trailing_slash() {
		$this->test_basic_autolinking( 'http://coffee2code.com/' );
	}

	public function test_linking_domain_with_directories() {
		$this->test_basic_autolinking( 'http://coffee2code.com/wp-plugins/autohyperlink-urls/', 'coffee2code.com/wp-plugins/autohyperlink-urls/' );
	}

	public function test_linking_URL_with_directories() {
		$this->test_basic_autolinking( 'http://coffee2code.com/wp-plugins/autohyperlink-urls/' );
	}

	public function test_linking_domain_with_hashbang_in_path() {
		$this->test_basic_autolinking( "http://twitter.com/#!/coffee2code/" );
	}

	public function test_linking_domain_with_query_args() {
		$this->test_basic_autolinking( 'http://coffee2code.com?emu=1&dog=rocky', 'coffee2code.com?emu=1&dog=rocky' );
	}

	public function test_URI_with_query_args() {
		$this->test_basic_autolinking( 'http://coffee2code.com?emu=1&dog=rocky' );
	}

	public function test_domain_with_encoded_query_args() {
		$this->test_basic_autolinking( 'http://coffee2code.com?emu=1&amp;dog=rocky', 'coffee2code.com?emu=1&amp;dog=rocky' );
	}

	public function test_URI_with_encoded_query_args() {
		$this->test_basic_autolinking( 'http://coffee2code.com?emu=1&amp;dog=rocky' );
	}

	public function test_linking_single_letter_domain() {
		$this->test_basic_autolinking( 'http://w.org', 'w.org' );
	}

	public function test_linking_single_letter_domain_in_URL() {
		$this->test_basic_autolinking( 'https://w.org', 'https://w.org' );
	}

	public function test_linking_two_letter_domain() {
		$this->test_basic_autolinking( 'http://wp.com', 'wp.com' );
	}

	public function test_linking_domain_with_hyphens() {
		$this->test_basic_autolinking( 'http://example-w.org', 'example-w.org' );
	}

	public function test_linking_domain_with_repeating_components() {
		$this->test_basic_autolinking( 'http://org.org.org', 'org.org.org' );
	}

	/**
	 * @dataProvider get_tlds
	 */
	public function test_linking_tlds_with_protocol( $tld ) {
		$this->test_basic_autolinking( "http://coffee2code.{$tld}", "http://coffee2code.{$tld}" );
	}

	/**
	 * @dataProvider get_tlds
	 */
	public function test_linking_tlds_with_no_protocol( $tld ) {
		$this->test_basic_autolinking( "http://coffee2code.{$tld}", "coffee2code.{$tld}" );
	}

	public function test_does_not_autolink_unknown_tlds() {
		$text = 'coffee2code.zzz';

		$this->assertEquals(
			$text,
			c2c_autohyperlink_link_urls( $text )
		);
	}

	/**
	 * @dataProvider get_protocols
	 */
	public function test_linking_protocols( $protocol ) {
		$this->test_basic_autolinking( "{$protocol}://coffee2code.com", "{$protocol}://coffee2code.com" );
	}

	/**
	 * @dataProvider get_ending_punctuation
	 */
	public function test_linking_when_appended_with_punctuation( $punctuation ) {
		$this->test_basic_autolinking( "http://coffee2code.com", "coffee2code.com{$punctuation}", '', $punctuation );
	}

	/**
	 * @dataProvider get_punctuation_bookends
	 */
	public function test_linking_when_bookended_with_punctuation( $before, $after ) {
		$this->test_basic_autolinking( "http://coffee2code.com", "{$before}coffee2code.com{$after}", $before , $after );
	}

	public function test_does_not_autolink_already_linked_URL() {
		$text = '<a href="http://example.com">example.com</a>';
		$this->assertEquals(
			$text,
			c2c_autohyperlink_link_urls( $text )
		);
	}

	public function test_does_not_autolink_already_linked_URL_with_subdomain() {
		$text = 'check out <a href="https://make.wordpress.org/">Make WordPress</a> and ';

		$this->assertEquals(
			$text,
			c2c_autohyperlink_link_urls( $text )
		);
	}

	public function test_does_not_autolink_domain_in_query_arg_of_already_linked_URL() {
		$text = 'check out <a href="https://make.wordpress.org/?url=http://example.com">Make WordPress</a> and ';

		$this->assertEquals(
			$text,
			c2c_autohyperlink_link_urls( $text )
		);
	}

	public function test_does_not_autolink_domain_in_query_arg_of_domain_being_autolinked() {
		$this->test_basic_autolinking( "http://coffee2code.com?url=http://example.com", "http://coffee2code.com?url=http://example.com" );
	}

	public function test_does_not_autolink_a_URL_within_linked_sentence() {
		$text = '<a href="http://example.com">Go to the link at example.com if you can</a>';

		$this->assertEquals(
			$text,
			c2c_autohyperlink_link_urls( $text )
		);
	}

	public function test_does_not_autolink_domain_in_tag_attribute() {
		$text = '<a href="http://example.com" title="Or at example.net ok">visit me</a>';

		$this->assertEquals(
			$text,
			c2c_autohyperlink_link_urls( $text )
		);
	}

	public function test_does_not_autolink_domain_immediately_bookended_with_tag_brackets() {
		$text = 'Visit me at <coffee2code.com> if you wish.';

		$this->assertEquals(
			$text,
			c2c_autohyperlink_link_urls( $text )
		);
	}

	public function test_does_not_autolink_URL_in_tag_attribute() {
		$text = '<a href="http://example.com" title="Or at http://example.net ok">visit me</a>';

		$this->assertEquals(
			$text,
			c2c_autohyperlink_link_urls( $text )
		);
	}

	public function test_does_not_autolink_URL_immediately_bookended_with_tag_brackets() {
		$text = 'Visit me at <http://coffee2code.com> if you wish.';

		$this->assertEquals(
			$text,
			c2c_autohyperlink_link_urls( $text )
		);
	}

	public function test_does_not_autolink_URL_already_autolinked() {
		$this->set_option( array( 'strip_protocol' => false ) );

		$text = 'Visit me at <a href="http://coffee2code.com">http://coffee2code.com</a> if you wish.';

		$this->assertEquals(
			$text,
			c2c_autohyperlink_link_urls( $text )
		);
	}

	public function test_url_without_uri_scheme_except_as_query_arg() {
		$this->set_option( array( 'strip_protocol' => false ) );

		$text = 'Visit me at example.com?ref=http://example.net if you wish.';
		$expected = 'Visit me at <a href="http://example.com?ref=http://example.net" class="autohyperlink">example.com?ref=http://example.net</a> if you wish.';

		$this->assertEquals(
			$expected,
			c2c_autohyperlink_link_urls( $text )
		);
	}

	/**
	 * @dataProvider data_script_and_style_tags_with_non_scheme_url
	 */
	public function test_url_without_uri_scheme_in_code_tags( $tag ) {
		$this->assertEquals( $tag, c2c_autohyperlink_link_urls( $tag ) );
	}

	/*
	 * Email.
	 */

	public function test_basic_email_autolinking( $email = 'user@example.com', $text = '', $before = '', $after = '' ) {
		if ( empty( $text ) ) {
			$text = $email;
		}


		$expected =  $before . '<a href="mailto:' . esc_attr( $email ) . '" class="autohyperlink">' . $text . '</a>' . $after;

		$this->assertEquals(
			$expected,
			c2c_autohyperlink_link_urls( $text )
		);
	}

	/**
	 * @dataProvider get_tlds
	 */
	public function test_autolink_email_with_tlds( $tld ) {
		$this->test_basic_email_autolinking( "user@example.{$tld}" );
	}

	public function test_does_not_autolink_already_linked_email() {
		$text = '<a href="mailto:test@example.com">test@example.com</a>';

		$this->assertEquals(
			$text,
			c2c_autohyperlink_link_urls( $text )
		);
	}

	public function test_does_not_autolink_email_within_linked_sentence() {
		$text = '<a href="mailto:test@example.com">Email me at test@example.com if you can</a>';

		$this->assertEquals(
			$text,
			c2c_autohyperlink_link_urls( $text )
		);
	}

	public function test_does_not_autolink_email_immediately_bookended_with_tag_brackets() {
		$text = 'Write me at <test@example.com> if you wish.';

		$this->assertEquals(
			$text,
			c2c_autohyperlink_link_urls( $text )
		);
	}

	public function test_does_not_autolink_email_in_tag_attribute() {
		$text = '<a href="http://example.com" title="Or email me at test@example.com ok">visit me</a>';

		$this->assertEquals(
			$text,
			c2c_autohyperlink_link_urls( $text )
		);
	}

	/**
	 * @dataProvider data_script_and_style_tags_with_email
	 */
	public function test_dont_link_script_and_style_tags_with_email( $tag ) {
		$this->assertEquals( $tag, c2c_autohyperlink_link_urls( $tag ) );
	}


	/*
	 * Setting: strip_protocol
	 */

	public function test_strip_protocol_false() {
		$this->set_option( array( 'strip_protocol' => false ) );

		$expected = '<a href="http://coffee2code.com/" class="autohyperlink">http://coffee2code.com/</a>';

		$this->assertEquals(
			$expected,
			c2c_autohyperlink_link_urls( 'http://coffee2code.com/' )
		);
	}

	public function test_strip_protocol_false_via_args() {
		$expected = '<a href="http://coffee2code.com/" class="autohyperlink">http://coffee2code.com/</a>';

		$this->assertEquals(
			$expected,
			c2c_autohyperlink_link_urls( 'http://coffee2code.com/', array( 'strip_protocol' => false ) )
		);
	}

	public function test_strip_protocol_true() {
		$this->set_option( array( 'strip_protocol' => true ) );

		$expected = '<a href="http://coffee2code.com/" class="autohyperlink">coffee2code.com/</a>';

		$this->assertEquals(
			$expected,
			c2c_autohyperlink_link_urls( 'http://coffee2code.com/' )
		);
	}

	public function test_strip_protocol_true_via_args() {
		$expected = '<a href="http://coffee2code.com/" class="autohyperlink">coffee2code.com/</a>';

		$this->assertEquals(
			$expected,
			c2c_autohyperlink_link_urls( 'http://coffee2code.com/', array( 'strip_protocol' => true ) )
		);
	}

	/*
	 * Setting: open_in_new_window
	 */

	public function test_open_in_new_window_true() {
		$this->set_option( array( 'open_in_new_window' => true ) );

		$expected = '<a href="http://coffee2code.com/" class="autohyperlink" target="_blank">coffee2code.com/</a>';

		$this->assertEquals(
			$expected,
			c2c_autohyperlink_link_urls( 'http://coffee2code.com/' )
		);
	}

	public function test_open_in_new_window_true_via_args() {
		$expected = '<a href="http://coffee2code.com/" class="autohyperlink" target="_blank">coffee2code.com/</a>';

		$this->assertEquals(
			$expected,
			c2c_autohyperlink_link_urls( 'http://coffee2code.com/', array( 'open_in_new_window' => true ) )
		);
	}

	public function test_open_in_new_window_false() {
		$this->set_option( array( 'open_in_new_window' => false ) );

		$expected = '<a href="http://coffee2code.com/" class="autohyperlink">coffee2code.com/</a>';

		$this->assertEquals(
			$expected,
			c2c_autohyperlink_link_urls( 'http://coffee2code.com/' )
		);
	}

	public function test_open_in_new_window_false_via_args() {
		$expected = '<a href="http://coffee2code.com/" class="autohyperlink">coffee2code.com/</a>';

		$this->assertEquals(
			$expected,
			c2c_autohyperlink_link_urls( 'http://coffee2code.com/', array( 'open_in_new_window' => false ) )
		);
	}

	/*
	 * Setting: nofollow
	 */

	public function test_nofollow_true() {
		$this->set_option( array( 'nofollow' => true ) );

		$expected = '<a href="http://coffee2code.com/" class="autohyperlink" rel="nofollow">coffee2code.com/</a>';

		$this->assertEquals(
			$expected,
			c2c_autohyperlink_link_urls( 'http://coffee2code.com/' )
		);
	}

	public function test_nofollow_true_via_args() {
		$expected = '<a href="http://coffee2code.com/" class="autohyperlink" rel="nofollow">coffee2code.com/</a>';

		$this->assertEquals(
			$expected,
			c2c_autohyperlink_link_urls( 'http://coffee2code.com/', array( 'nofollow' => true ) )
		);
	}

	/*
	 * Setting: require_scheme
	 */

	public function test_require_scheme_true() {
		$this->set_option( array( 'require_scheme' => true ) );

		$string = 'Visit coffee2code.com now!';

		$this->assertEquals( $string, c2c_autohyperlink_link_urls( $string ) );
	}

	public function test_require_scheme_true_via_args() {
		$string = 'Visit coffee2code.com now!';

		$this->assertEquals( $string, c2c_autohyperlink_link_urls( $string, array( 'require_scheme' => true ) ) );
	}

	/*
	 * Setting: more_extensions
	 */

	public function test_space_separated_more_extensions() {
		$link = 'coffee2code.io';

		$this->assertEquals(
			$link,
			c2c_autohyperlink_link_urls( $link )
		);

		$this->set_option( array( 'more_extensions' => 'co io' ) );

		$this->assertEquals(
			'<a href="http://coffee2code.io" class="autohyperlink">coffee2code.io</a>',
			c2c_autohyperlink_link_urls( $link )
		);

	}

	public function test_comma_separated_more_extensions() {
		$link = 'coffee2code.io';

		$this->assertEquals(
			$link,
			c2c_autohyperlink_link_urls( $link )
		);

		$this->set_option( array( 'more_extensions' => 'co,io' ) );

		$this->assertEquals(
			'<a href="http://coffee2code.io" class="autohyperlink">coffee2code.io</a>',
			c2c_autohyperlink_link_urls( $link )
		);

	}

	public function test_comma_and_space_separated_more_extensions() {
		$link = 'coffee2code.io';

		$this->assertEquals(
			$link,
			c2c_autohyperlink_link_urls( $link )
		);

		$this->set_option( array( 'more_extensions' => 'co, io' ) );

		$this->assertEquals(
			'<a href="http://coffee2code.io" class="autohyperlink">coffee2code.io</a>',
			c2c_autohyperlink_link_urls( $link )
		);

	}

	/*
	 * Setting: hyperlink_commentss
	*/

	public function test_hyperlink_comments() {
		// Since the setting is only consulted on plugin load, reset related filters
		// to default and rerun filter registration.
		add_filter( 'comment_text', 'make_clickable', 9 );
		remove_filter( 'comment_text', array( c2c_AutoHyperlinkURLs::get_instance(), 'hyperlink_urls' ), 9 );

		$this->set_option( array( 'hyperlink_comments' => false ) );
		c2c_AutoHyperlinkURLs::get_instance()->register_filters();

		$text = 'Comment containing example.com and user@example.com that shoud not get linked.';
		// Note: If hyperlink_comments is false, then make_clickable() is still run,
		// which would hyperlink the email address.
		$expected = 'Comment containing example.com and <a href="mailto:user@example.com">user@example.com</a> that shoud not get linked.';

		$this->assertEquals(
			wpautop( $expected ),
			apply_filters( 'comment_text', $text )
		);
	}

	/*
	 * Setting: hyperlink_emails
	 */

	public function test_hyperlink_emails() {
		$this->set_option( array( 'hyperlink_emails' => false ) );

		$text = 'Contact me at user@example.com at once.';

		$this->assertEquals(
			$text,
			c2c_autohyperlink_link_urls( $text )
		);
	}

	/*
	 * Setting: hyperlink_mode
	 */

	public function test_hyperlink_mode_0() {
		$this->set_option( array( 'hyperlink_mode' => 0 ) );

		$expected = '<a href="http://coffee2code0123456789.com/page" class="autohyperlink">coffee2code0123456789.com/page</a>';

		$this->assertEquals(
			$expected,
			c2c_autohyperlink_link_urls( 'http://coffee2code0123456789.com/page' )
		);
	}

	public function test_hyperlink_mode_11() {
		$this->set_option( array( 'hyperlink_mode' => 11 ) );

		$expected = '<a href="http://coffee2code0123456789.com/page" class="autohyperlink">coffee2code...</a>';

		$this->assertEquals(
			$expected,
			c2c_autohyperlink_link_urls( 'http://coffee2code0123456789.com/page' )
		);
	}

	public function test_hyperlink_mode_truncation( $mode = 11 ) {
		$this->set_option( array( 'hyperlink_mode' => $mode ) );

		$expected = '<a href="http://coffee2code0123456789.com/page" class="autohyperlink">'
			. substr( 'coffee2code0123456789.com', 0, $mode )
			. '...</a>';

		$this->assertEquals(
			$expected,
			c2c_autohyperlink_link_urls( 'http://coffee2code0123456789.com/page' )
		);
	}

	public function test_hyperlink_mode_longer_truncation() {
		$this->test_hyperlink_mode_truncation( 14 );
	}

	/*
	 * Setting: truncation_before_text
	 */

	public function test_truncation_before_text_not_used_by_default() {
		$this->set_option( array( 'truncation_before_text' => '!!!', 'hyperlink_mode' => 5 ) );

		$expected = '<a href="http://coffee2code0123456789.com/" class="autohyperlink">coffee2code0123456789.com/</a>';

		$this->assertEquals(
			$expected,
			c2c_autohyperlink_link_urls( 'http://coffee2code0123456789.com/' )
		);
	}

	public function test_truncation_before_text_applies_with_proper_hyperlink_mode() {
		$this->set_option( array( 'truncation_before_text' => '!!!', 'hyperlink_mode' => 11 ) );

		$expected = '<a href="http://coffee2code0123456789.com/" class="autohyperlink">!!!coffee2code...</a>';

		$this->assertEquals(
			$expected,
			c2c_autohyperlink_link_urls( 'http://coffee2code0123456789.com/' )
		);
	}

	/*
	 * Setting: truncation_after_text
	 */

	public function test_truncation_after_text_not_used_by_default() {
		$this->set_option( array( 'truncation_after_text' => '!!!', 'hyperlink_mode' => 5 ) );

		$expected = '<a href="http://coffee2code0123456789.com/" class="autohyperlink">coffee2code0123456789.com/</a>';

		$this->assertEquals(
			$expected,
			c2c_autohyperlink_link_urls( 'http://coffee2code0123456789.com/' )
		);
	}

	public function test_truncation_after_text_applies_with_proper_hyperlink_mode() {
		$this->set_option( array( 'truncation_after_text' => '!!!', 'hyperlink_mode' => 11 ) );

		$expected = '<a href="http://coffee2code0123456789.com/" class="autohyperlink">coffee2code!!!</a>';

		$this->assertEquals(
			$expected,
			c2c_autohyperlink_link_urls( 'http://coffee2code0123456789.com/' )
		);
	}

	public function test_truncation_before_text_and_truncation_after_text_apply_with_proper_hyperlink_mode() {
		$this->set_option( array( 'truncation_before_text' => '(', 'truncation_after_text' => '...)', 'hyperlink_mode' => 11 ) );

		$expected = '<a href="http://coffee2code0123456789.com/" class="autohyperlink">(coffee2code...)</a>';

		$this->assertEquals(
			$expected,
			c2c_autohyperlink_link_urls( 'http://coffee2code0123456789.com/' )
		);
	}

	public function test_truncation_before_text_and_truncation_after_text_dont_apply_with_untruncated_link() {
		$this->set_option( array( 'truncation_before_text' => '(', 'truncation_after_text' => '...)', 'hyperlink_mode' => 11 ) );

		$expected = '<a href="http://example.com" class="autohyperlink">example.com</a>';

		$this->assertEquals(
			$expected,
			c2c_autohyperlink_link_urls( 'example.com' )
		);
	}

	/*
	 * Setting: exclude_domains
	 */

	public function test_exclude_domains() {
		$this->set_option( array( 'exclude_domains' => array( 'example.com') ) );

		$texts = array(
			'example.com',
			'Visit example.com soon.',
			'Visit Example.com soon.',
			'Visit exAMPle.com soon.',
		);

		foreach ( $texts as $text ) {
			$this->assertEquals(
				$text,
				c2c_autohyperlink_link_urls( $text )
			);
		}
	}

	/*
	 * Filters
	 */

	public function test_filter_autohyperlink_urls_tlds() {
		$link = 'coffee2code.io';

		$this->assertEquals(
			$link,
			c2c_autohyperlink_link_urls( $link )
		);
		add_filter( 'autohyperlink_urls_tlds', array( $this, 'autohyperlink_urls_tlds' ) );

		$this->assertEquals(
			'<a href="http://coffee2code.io" class="autohyperlink">coffee2code.io</a>',
			c2c_autohyperlink_link_urls( $link )
		);
	}

	public function test_filter_autohyperlink_urls_class() {
		add_filter( 'autohyperlink_urls_class', array( $this, 'autohyperlink_urls_class' ) );

		$expected = '<a href="http://coffee2code.com/" class="customclass">coffee2code.com/</a>';

		$this->assertEquals(
			$expected,
			c2c_autohyperlink_link_urls( 'http://coffee2code.com/' )
		);
	}

	public function test_filter_autohyperlink_urls_link_attributes() {
		add_filter( 'autohyperlink_urls_link_attributes', array( $this, 'autohyperlink_urls_link_attributes' ), 10, 3 );

		$expected = '<a href="http://coffee2code.com/" class="autohyperlink" title="http://coffee2code.com/">coffee2code.com/</a>';

		$this->assertEquals(
			$expected,
			c2c_autohyperlink_link_urls( 'http://coffee2code.com/' )
		);
	}

	public function test_filter_autohyperlink_urls_exclude_domains() {
		add_filter( 'autohyperlink_urls_exclude_domains', array( $this, 'autohyperlink_urls_exclude_domains' ) );

		$texts = array(
			'Visit example.com soon.',
			'Visit Example.com soon.',
			'Visit exAMPle.com soon.',
		);

		foreach ( $texts as $text ) {
			$this->assertEquals(
				$text,
				c2c_autohyperlink_link_urls( $text )
			);
		}
	}

	public function test_filter_autohyperlink_urls_custom_exclusions_recognizes_false() {
		add_filter( 'autohyperlink_urls_custom_exclusions', array( $this, 'autohyperlink_urls_custom_exclusions' ), 10, 3 );

		$texts = array(
			'Visit example.com soon.',
			'Visit http://example.com soon.',
			'Visit exAMPle.com soon.',
		);

		foreach ( $texts as $text ) {
			$this->assertEquals(
				$text,
				c2c_autohyperlink_link_urls( $text )
			);
		}
	}

	public function test_filter_autohyperlink_urls_custom_exclusions_recognizes_true() {
		add_filter( 'autohyperlink_urls_custom_exclusions', array( $this, 'autohyperlink_urls_custom_exclusions' ), 10, 3 );

		$before = array(
			'Visit coffee2code.com soon.',
			'Visit http://coffee2code.com soon.',
			'Visit http://Coffee2Code.com soon.',
		);

		$expected = array(
			'Visit <a href="http://coffee2code.com" class="autohyperlink">coffee2code.com</a> soon.',
			'Visit <a href="http://coffee2code.com" class="autohyperlink">coffee2code.com</a> soon.',
			'Visit <a href="http://Coffee2Code.com" class="autohyperlink">Coffee2Code.com</a> soon.',
		);

		foreach ( $before as $key => $text ) {
			$this->assertEquals(
				$expected[ $key ],
				c2c_autohyperlink_link_urls( $text )
			);
		}
	}

	/*
	 * Setting handling
	 */

	public function test_does_not_immediately_store_default_settings_in_db() {
		$option_name = c2c_AutoHyperlinkURLs::SETTING_NAME;
		// Get the options just to see if they may get saved.
		$options     = c2c_AutoHyperlinkURLs::get_instance()->get_options();

		$this->assertFalse( get_option( $option_name ) );
	}

	public function test_uninstall_deletes_option() {
		$option_name = c2c_AutoHyperlinkURLs::SETTING_NAME;
		$options     = c2c_AutoHyperlinkURLs::get_instance()->get_options();

		// Explicitly set an option to ensure options get saved to the database.
		$this->set_option( array( 'hyperlink_comments' => true ) );

		$this->assertNotEmpty( $options );
		$this->assertNotFalse( get_option( $option_name ) );

		c2c_AutoHyperlinkURLs::uninstall();

		$this->assertFalse( get_option( $option_name ) );
	}

}

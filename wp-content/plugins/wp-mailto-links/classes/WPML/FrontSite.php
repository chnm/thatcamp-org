<?php
/**
 * Class WPML_Front
 *
 * @todo Refactor and cleanup
 *
 * @package  WPML
 * @category WordPress Plugins
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @license  MIT license
 */
final class WPML_FrontSite
{
    /**
     * Regular expressions
     * @var array
     */
    protected $regexps = array();

    /**
     * @var \WPDev_Option
     */
    protected $option = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->option = WPML::glob('option');

        // @link http://www.mkyong.com/regular-expressions/how-to-validate-email-address-with-regular-expression/
        $regexpEmail = '([_A-Za-z0-9-]+(\\.[_A-Za-z0-9-]+)*@[A-Za-z0-9-]+(\\.[A-Za-z0-9-]+)*(\\.[A-Za-z]{2,}))';

        $this->regexps = array(
            'emailPlain' => '/'.$regexpEmail.'/i',
            'emailMailto' => '/mailto\:[\s+]*'.$regexpEmail.'/i',
            'input' => '/<input([^>]*)value=["\'][\s+]*'.$regexpEmail.'[\s+]*["\']([^>]*)>/is',
            'mailtoLink' => '/<a[\s+]*(([^>]*)href=["\']mailto\:([^>]*)["\'])>(.*?)<\/a[\s+]*>/is',
            'image' => '/<img([^>]*)>/is',
            'body' => '/(<body(([^>]*)>))/is',
        );

        $this->createCustomFilterHooks();
        $this->createTemplateFunctions();

        // add actions
        add_action('wp', array($this, 'actionWpSite'), 10);
        add_filter('wp_head', array($this, 'filterWpHead'), 10);
    }

    protected function createCustomFilterHooks()
    {
        if ($this->option->getValue('filter_body') || $this->option->getValue('filter_head')) {
            // final_output filter
            WPDev_Filter_FinalOutput::create();
        }

        if (!$this->option->getValue('filter_body') && $this->option->getValue('filter_widgets')) {
            // widget_output filter
            global $wp_registered_widgets; // not very nice but need to get global WP var by reference
            WPDev_Filter_WidgetOutput::create($wp_registered_widgets);
        }
    }

    /**
     * WP action callback
     */
    public function actionWpSite()
    {
        if (is_feed()) {
            // rss feed
            if ($this->option->getValue('filter_rss')) {
                $rss_filters = array('the_title', 'the_content', 'the_excerpt', 'the_title_rss',
                    'the_content_rss', 'the_excerpt_rss',
                    'comment_text_rss', 'comment_author_rss', 'the_category_rss',
                    'the_content_feed', 'author_feed_link', 'feed_link');

                foreach ($rss_filters as $filter) {
                    add_filter($filter, array($this, 'filterRss'), 100);
                }
            }
        } else {
            // site
            // set js file
            if ($this->option->getValue('protect')) {
                wp_enqueue_script('wp-mailto-links',
                    WPML::glob('URL') . '/js/wp-mailto-links.js',
                    array('jquery'));
            }

            // add css font icons
            if ($this->option->getValue('mail_icon') === 'dashicons') {
                wp_enqueue_style('dashicons');
            } elseif ($this->option->getValue('mail_icon') === 'fontawesome') {
                wp_enqueue_style(
                    'font-awesome'
                    , 'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css'
                    , array()
                    , null // use caching CDN file
                );
            }

            if ($this->option->getValue('filter_body') || $this->option->getValue('filter_head')) {
                add_filter('final_output', array($this, 'filterPage'), 10, 1);
            }

            if (!$this->option->getValue('filter_body')) {
                $filters = array();

                // post content
                if ($this->option->getValue('filter_posts')) {
                    array_push($filters, 'the_title', 'the_content',
                        'the_excerpt', 'get_the_excerpt');
                }

                // comments
                if ($this->option->getValue('filter_comments')) {
                    array_push($filters, 'comment_text', 'comment_excerpt',
                        'comment_url', 'get_comment_author_url',
                        'get_comment_author_link', 'get_comment_author_url_link');
                }

                // widgets ( only text widgets )
                if ($this->option->getValue('filter_widgets')) {
                    array_push($filters, 'widget_output');
                }

                foreach ($filters as $filter) {
                    add_filter($filter, array($this, 'filterContent'), 100);
                }
            }
        }

        // shortcodes
        add_shortcode('wpml_mailto', array($this, 'shortcodeProtectedMailto'));

        // hook
        do_action('wpml_ready', array($this, 'filterContent'), $this);
    }

    /**
     * WP filter callbacks
     */
    public function filterWpHead()
    {
        $icon = $this->option->getValue('image');
        $className = $this->option->getValue('class_name');
        $showBefore = $this->option->getValue('show_icon_before');

        // add style to <head>
        echo '<style type="text/css" media="all">'."\n";
        echo '/* WP Mailto Links Plugin */'."\n";
        echo '.wpml-nodis { display:none; }';
        echo '.wpml-rtl { unicode-bidi:bidi-override; direction:rtl; }';
        echo '.wpml-encoded { position:absolute; margin-top:-0.3em; z-index:1000; color:green; }';

        // add nowrap style
        if ($className) {
            echo '.' . $className . ' { white-space:nowrap; }';
        }

        // add icon styling
        if ($icon) {
            echo '.mail-icon-' . $icon . ' {';
            echo 'background:url(' . WPML::glob('URL') . '/images/mail-icon-' . $icon . '.png) no-repeat ';
            if ($showBefore) {
                echo '0% 50%; padding-left:18px;';
            } else {
                echo '100% 50%; padding-right:18px;';
            }
            echo '}';
        }

        echo '</style>'."\n";
    }

    /**
     * Filter complete <html>
     * @param string $content
     * @return string
     */
    public function filterPage($content)
    {
        $filtered = $content;

        $html_split = preg_split($this->regexps['body'], $filtered, null, PREG_SPLIT_DELIM_CAPTURE);

        if (count($html_split) >= 4) {
            // protect emails in <head> section
            if ($this->option->getValue('filter_head')) {
                $headFiltered = $this->filterHead(array($html_split[0]));
            } else {
                $headFiltered = $html_split[0];
            }

            // only replace links in <body> part
            if ($this->option->getValue('filter_body')) {
                $bodyFiltered = $this->filterBody(array($html_split[4]));
            } else {
                $bodyFiltered = $html_split[4];
            }

            $filtered = $headFiltered . $html_split[1] . $bodyFiltered;
        }

        // when no filtered content
        if (!$filtered || strlen(trim($filtered)) === 0) {
            return $content;
        }

        return $filtered;
    }

    /**
     * Filter <head>
     * @param array $match
     * @return string
     */
    protected function filterHead($match)
    {
        if (count($match) == 0) {
            return '';
        }

        return $this->replacePlainEmails($match[0]);
    }

    /**
     * Filter <body>
     * @param array $match
     * @return string
     */
    protected function filterBody($match)
    {
        if (count($match) == 0) {
            return '';
        }

        return $this->filterContent($match[0]);
    }

    /**
     * Filter content
     * @param string $content
     * @return string
     */
    public function filterContent($content)
    {
        $filtered = $content;

        // first encode email address in input values
        $filtered = preg_replace_callback($this->regexps['input'], array($this, 'pregReplaceInputValues'), $filtered);

        // get mailto links
        $filtered = preg_replace_callback($this->regexps['mailtoLink'], array($this, 'pregReplaceMailto'), $filtered);

        // convert plain emails (only when mailto links protection on)
        if ($this->option->getValue('convert_emails') == 1) {
            // protect plain emails
            $filtered = $this->replacePlainEmails($filtered);
        } elseif ($this->option->getValue('convert_emails') == 2) {
            // make mailto links from plain emails
            $filtered = preg_replace_callback($this->regexps['emailPlain'], array($this, 'pregReplacePlainEmail'), $filtered);
        }
 
        // when no filtered content
        if (!$filtered || strlen(trim($filtered)) === 0) {
            return $content;
        }

        return $filtered;
    }

    /**
     * Encode emails in input values
     * @param array $match
     * @return string
     */
    public function pregReplaceInputValues($match)
    {
        $input = $match[0];
        $email = $match[2];

        $encodedEmail = $this->getEncEmail($email);

        if ($this->option->getValue('input_strong_protection') == 1) {
            // add data-enc-email after "<input"
            $encodedInput = substr($input, 0, 6);
            $encodedInput .= ' data-enc-email="' . $encodedEmail . '"';
            $encodedInput .= substr($input, 6);

            // remove email from value attribute
            $encodedInput = str_replace($email, '', $encodedInput);
        } else {
            // replace email in value attribute
            $encodedInput = str_replace($email, antispambot($email), $input);
        }

        return $encodedInput;
    }

    /**
     * Convert plain email to protected mailto link
     * @param array $match
     * @return string
     */
    public function pregReplacePlainEmail($match)
    {
        $content = $this->protectedMailto($match[0], array('href' => 'mailto:'.$match[0]));
        return $content;
    }

    /**
     * Make a clean protected mailto link
     * @param array $match Result of a preg call in filterContent()
     * @return string Protected mailto link
     */
    public function pregReplaceMailto($match)
    {
        $attrs = shortcode_parse_atts($match[1]);
        $link  = $this->protectedMailto($match[4], $attrs);

        return $link;
    }

    /**
     * Emails will be replaced by '*protected email*'
     * @param string $content
     * @return string
     */
    public function filterRss($content)
    {
        $content = $this->replacePlainEmails($content);
        $content = preg_replace($this->regexps['emailMailto'], 'mailto:' . __($this->option->getValue('protection_text'), 'wp-mailto-links'), $content);
        return $content;
    }

    /**
     * Emails will be replaced by '*protected email*'
     * @param string $content
     * @param string $email_replacement  Optional
     * @return string
     */
    public function replacePlainEmails($content, $emailReplacement = null)
    {
        $emailReplacement = ($emailReplacement === null) ? __($this->option->getValue('protection_text'), 'wp-mailto-links') : $emailReplacement;
        return preg_replace($this->regexps['emailPlain'], $emailReplacement, $content);
    }

    /**
     * Shortcode protected mailto link
     * @param array $attrs
     * @param string $content Optional
     * @return string
     */
    public function shortcodeProtectedMailto($attrs, $content = '')
    {
        if ($this->option->getValue('protect') && preg_match($this->regexps['emailPlain'], $content) > 0) {
            $content = $this->getProtectedDisplay($content);
        }

        // set "email" to "href"
        if (isset($attrs['email'])) {
            $attrs['href'] = 'mailto:'.$attrs['email'];
            unset($attrs['email']);
        }

        $content = $this->protectedMailto($content, $attrs);

        return $content;
    }

    /**
     * Create a protected mailto link
     * @param string $display
     * @param array $attrs Optional
     * @return string
     */
    public function protectedMailto($display, $attrs = array())
    {
        $email     = '';
        $class_ori = (empty($attrs['class'])) ? '' : $attrs['class'];


        // does not contain no-icon class and no icon when contains <img>
        if ((!$this->option->getValue('no_icon_class') || strpos($class_ori, $this->option->getValue('no_icon_class')) === FALSE)
                    && !($this->option->getValue('image_no_icon') == 1 && (bool) preg_match($this->regexps['image'], $display))) {
            if ($this->option->getValue('mail_icon') === 'image') {
            // image
                if ($this->option->getValue('image') > 0 && strpos($class_ori, 'mail-icon-') === FALSE) {
                    $icon_class = 'mail-icon-' . $this->option->getValue('image');

                    $attrs['class'] = (empty($attrs['class'])) ? $icon_class : $attrs['class'] .' '.$icon_class;
                }
            } elseif ($this->option->getValue('mail_icon') === 'dashicons') {
            // dashicons
                $fontIcon = '<i class="dashicons-before ' . $this->option->getValue('dashicons') . '"></i>';
            } elseif ($this->option->getValue('mail_icon') === 'fontawesome') {
            // fontawesome
                $fontIcon = '<i class="fa ' . $this->option->getValue('fontawesome') . '"></i>';
            }
        }

        // set user-defined class
        if ($this->option->getValue('class_name') && strpos($class_ori, $this->option->getValue('class_name')) === FALSE) {
            $attrs['class'] = (empty($attrs['class'])) ? $this->option->getValue('class_name') : $attrs['class'].' '.$this->option->getValue('class_name');
        }

        // check title for email address
        if (!empty($attrs['title'])) {
            $attrs['title'] = $this->replacePlainEmails($attrs['title'], '{{email}}'); // {{email}} will be replaced in javascript
        }

        // create element code
        $link = '<a ';

        foreach ($attrs AS $key => $value) {
            if (strtolower($key) == 'href' && $this->option->getValue('protect')) {
                // get email from href
                $email = substr($value, 7);

                $encoded_email = $this->getEncEmail($email);

                // set attrs
                $link .= 'href="javascript:;" ';
                $link .= 'data-enc-email="'.$encoded_email.'" ';
            } else {
                $link .= $key.'="'.$value.'" ';
            }
        }

        // remove last space
        $link = substr($link, 0, -1);

        $link .= '>';

        if (!empty($fontIcon) && $this->option->getValue('show_icon_before')) {
            $link .= $fontIcon . ' ';
        }

        $link .= ($this->option->getValue('protect') && preg_match($this->regexps['emailPlain'], $display) > 0) ? $this->getProtectedDisplay($display) : $display;

        if (!empty($fontIcon) && !$this->option->getValue('show_icon_before')) {
            $link .= ' ' . $fontIcon;
        }

        $link .= '</a>';

        // filter
        $link = apply_filters('wpml_mailto', $link, $display, $email, $attrs);

        // just in case there are still email addresses f.e. within title-tag
        $link = $this->replacePlainEmails($link);

        // mark link as successfullly encoded (for admin users)
        if (current_user_can('manage_options') && $this->option->getValue('security_check')) {
            $link .= '<i class="wpml-encoded dashicons-before dashicons-lock" title="' . __('Email encoded successfully!', 'wp-mailto-links') . '"></i>';
        }


        return $link;
    }

    /**
     * Get encoded email, used for data-attribute (translate by javascript)
     * @param string $email
     * @return string
     */
    protected function getEncEmail($email)
    {
        $encEmail = $email;

        // decode entities
        $encEmail = html_entity_decode($encEmail);

        // rot13 encoding
        $encEmail = str_rot13($encEmail);

        // replace @
        $encEmail = str_replace('@', '[at]', $encEmail);

        return $encEmail;
    }

    /**
     * Create protected display combining these 3 methods:
     * - reversing string
     * - adding no-display spans with dummy values
     * - using the wp antispambot function
     *
     * Source:
     * - http://perishablepress.com/press/2010/08/01/best-method-for-email-obfuscation/
     * - http://techblog.tilllate.com/2008/07/20/ten-methods-to-obfuscate-e-mail-addresses-compared/
     *
     * @param string|array $display
     * @return string Protected display
     */
    public function getProtectedDisplay($display)
    {
        // get display outof array (result of preg callback)
        if (is_array($display)) {
            $display = $display[0];
        }

        // first strip html tags
        $stripped_display = strip_tags($display);
        // decode entities
        $stripped_display = html_entity_decode($stripped_display);

        $length = strlen($stripped_display);
        $interval = ceil(min(5, $length / 2));
        $offset = 0;
        $dummy_content = time();
        $protected = '';

        // reverse string ( will be corrected with CSS )
        $rev = strrev($stripped_display);

        while ($offset < $length) {
            // set html entities
            $protected .= antispambot(substr($rev, $offset, $interval));

            // set some dummy value, will be hidden with CSS
            $protected .= '<span class="wpml-nodis">'.$dummy_content.'</span>';
            $offset += $interval;
        }

        $protected = '<span class="wpml-rtl">'.$protected.'</span>';

        return $protected;
    }

    /**
     * Create the global template functions
     */
    protected function createTemplateFunctions()
    {
        // set this object as "global" to use in template functions
        WPML::plugin()->setGlobal('frontObject', $this);

        if (!function_exists('wpml_mailto')):
            function wpml_mailto($email, $display = null, $attrs = array())
            {
                if (is_array($display)) {
                   // backwards compatibility (old params: $display, $attrs = array())
                   $attrs   = $display;
                   $display = $email;
               } else {
                   $attrs['href'] = 'mailto:'.$email;
               }

               return WPML::glob('frontObject')->protectedMailto($display, $attrs);
            }
        endif;

        if (!function_exists('wpml_filter')):
            function wpml_filter($content)
            {
                return WPML::glob('frontObject')->filterContent($content);
            }
        endif;
    }

}

/*?>*/

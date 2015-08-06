<?php
/**
 * Class WPML_Front
 *
 * @package  WPML
 * @category WordPress Plugins
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @license  MIT license
 */
class WPML_Front
{
    /**
     * Regular expressions
     * @var array
     */
    protected $regexps = array();

    /**
     * @var array
     */
    protected $optionValues = array();

    /**
     * Constructor
     */
    public function __construct()
    {
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

        // set values
        $this->optionValues = WPML::get('optionValues')->get();

        // create template function
        $this->createTemplateFunctions();

        // add actions
        add_action('wp', array($this, 'actionWpSite'), 10);
        add_filter('wp_head', array($this, 'filterWpHead'), 10);
    }

    /**
     * WP action callback
     */
    public function actionWpSite()
    {
        if (is_feed()) {
            // rss feed
            if ($this->optionValues['filter_rss']) {
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
            if ($this->optionValues['protect']) {
                wp_enqueue_script('wp-mailto-links',
                    WPML::url('js/wp-mailto-links.js'), array('jquery'),
                    WPML::get('version'));
            }

            if ($this->optionValues['filter_body'] || $this->optionValues['filter_head']) {
                ob_start(array($this, 'filterPage'));

                // set ob flush
                add_action('wp_footer', array($this, 'actionWpFooter'), 10000);
            }

            if (!$this->optionValues['filter_body']) {
                $filters = array();

                // post content
                if ($this->optionValues['filter_posts']) {
                    array_push($filters, 'the_title', 'the_content',
                        'the_excerpt', 'get_the_excerpt');
                }

                // comments
                if ($this->optionValues['filter_comments']) {
                    array_push($filters, 'comment_text', 'comment_excerpt',
                        'comment_url', 'get_comment_author_url',
                        'get_comment_author_link', 'get_comment_author_url_link');
                }

                // widgets ( only text widgets )
                if ($this->optionValues['filter_widgets']) {
                    array_push($filters, 'widget_title', 'widget_text',
                        'widget_content'); // widget_content id filter of Widget Logic plugin
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
        $icon       = $this->optionValues['icon'];
        $class_name = $this->optionValues['class_name'];

        // add style to <head>
        echo '<style type="text/css" media="all">'."\n";
        echo '/* WP Mailto Links Plugin */'."\n";
        echo '.wpml-nodis { display:none; }';
        echo '.wpml-rtl { unicode-bidi:bidi-override; direction:rtl; }';

        // add nowrap style
        if ($class_name) {
            echo '.' . $class_name . ' { white-space:nowrap; }';
        }

        // add icon styling
        if ($icon) {
            $padding = ($icon < 19) ? 15 : 17;
            echo '.mail-icon-' . $icon . ' { background:url(' . WPML::url('/images/mail-icon-' . $icon . '.png') . ') no-repeat 100% 75%; padding-right:' . $padding . 'px; }';
        }

        echo '</style>'."\n";
    }

    /**
     * WP action callback
     */
    public function actionWpFooter()
    {
        ob_end_flush();
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
            if ($this->optionValues['filter_head']) {
                $headFiltered = $this->filterHead(array($html_split[0]));
            } else {
                $headFiltered = $html_split[0];
            }

            // only replace links in <body> part
            if ($this->optionValues['filter_body']) {
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

        // convert plain emails
        if ($this->optionValues['convert_emails'] == 1) {
            // protect plain emails
            $filtered = $this->replacePlainEmails($filtered);
        } elseif ($this->optionValues['convert_emails'] == 2) {
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

        if ($this->optionValues['input_strong_protection'] == 1) {
            // add data-enc-email after "<input"
            $encodedInput .= substr($input, 0, 6);
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
        $content = preg_replace($this->regexps['emailMailto'], 'mailto:'.WPML::__($this->optionValues['protection_text']), $content);
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
        $emailReplacement = ($emailReplacement === null) ? WPML::__($this->optionValues['protection_text']) : $emailReplacement;
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
        if ($this->optionValues['protect'] && preg_match($this->regexps['emailPlain'], $content) > 0) {
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

        // set icon class, unless no-icon class isset or another icon class ('mail-icon-...') is found and display does not contain image
        if ($this->optionValues['icon'] > 0 && (empty($this->optionValues['no_icon_class'])
                || strpos($class_ori, $this->optionValues['no_icon_class']) === FALSE) && strpos($class_ori, 'mail-icon-') === FALSE
                && !($this->optionValues['image_no_icon'] == 1
                && (bool) preg_match($this->regexps['image'], $display))) {
            $icon_class = 'mail-icon-' . $this->optionValues['icon'];

            $attrs['class'] = (empty($attrs['class'])) ? $icon_class : $attrs['class'] .' '.$icon_class;
        }

        // set user-defined class
        if (!empty($this->optionValues['class_name']) && strpos($class_ori, $this->optionValues['class_name']) === FALSE) {
            $attrs['class'] = (empty($attrs['class'])) ? $this->optionValues['class_name'] : $attrs['class'].' '.$this->optionValues['class_name'];
        }

        // check title for email address
        if (!empty($attrs['title'])) {
            $attrs['title'] = $this->replacePlainEmails($attrs['title'], '{{email}}'); // {{email}} will be replaced in javascript
        }

        // create element code
        $link = '<a ';

        foreach ($attrs AS $key => $value) {
            if (strtolower($key) == 'href' && $this->optionValues['protect']) {
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
        $link .= ($this->optionValues['protect'] && preg_match($this->regexps['emailPlain'], $display) > 0) ? $this->getProtectedDisplay($display) : $display;
        $link .= '</a>';

        // filter
        $link = apply_filters('wpml_mailto', $link, $display, $email, $attrs);

        // just in case there are still email addresses f.e. within title-tag
        $link = $this->replacePlainEmails($link);

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

               return WPML::get('front')->protectedMailto($display, $attrs);
            }
        endif;

        if (!function_exists('wpml_filter')):
            function wpml_filter($content)
            {
                return WPML::get('front')->filterContent($content);
            }
        endif;
    }
}
// End Class WPML_Front

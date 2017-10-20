<?php
/**
 * Class WPML_Front_Email
 *
 * @todo Refactor and cleanup
 *
 * @package  WPML
 * @category WordPress Plugins
 * @version  2.1.6
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @link     https://github.com/freelancephp/WP-Mailto-Links
 * @link     https://wordpress.org/plugins/wp-mailto-links/
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
final class WPML_Front_Email extends WPRun_BaseAbstract_0x5x0
{
//    private $option = null;
//    public function __construct($option)
//    {
//        $this->option = $option;
//    }

    private function opt($key)
    {
        return $this->getArgument(0)->getValue($key);
    }

    public function pageFilter($content, $filterHead, $filterBody, $convertPlainEmails)
    {
        $htmlSplit = preg_split('/(<body(([^>]*)>))/is', $content, null, PREG_SPLIT_DELIM_CAPTURE);

        if (count($htmlSplit) < 4) {
            return $content;
        }

        if ($filterHead === true) {
            $filteredHead = $this->filterPlainEmails($htmlSplit[0]);
        } else {
            $filteredHead = $htmlSplit[0];
        }

        if ($filterBody === true) {
            $filteredBody = $this->contentFilter($htmlSplit[4], $convertPlainEmails);
        } else {
            $filteredBody = $htmlSplit[4];
        }

        $filteredContent = $filteredHead . $htmlSplit[1] . $filteredBody;
        return $filteredContent;
    }

    /**
     * Filter content
     * @param string  $content
     * @param integer $convertPlainEmails
     * @return string
     */
    public function contentFilter($content, $convertPlainEmails)
    {
        $filtered = $content;

        $filtered = $this->filterInputFields($filtered);

        $filtered = $this->filterMailtoLinks($filtered);

        if ($convertPlainEmails == 1) {
            $filtered = $this->filterPlainEmails($filtered);

        } elseif ($convertPlainEmails == 2) {
            $self = $this;

            $filtered = $this->filterPlainEmails($filtered, function ($match) use ($self) {
                return $self->protectedMailto($match[0], array('href' => 'mailto:' . $match[0]));
            });
        }

        return $filtered;
    }

    /**
     * Emails will be replaced by '*protected email*'
     * @param string $content
     * @return string
     */
    public function rssFilter($content)
    {
        $regexpHrefMailto = '/mailto\:[\s+]*' . $this->getEmailRegExp(true) . '/i';

        $filtered = $this->filterPlainEmails($content);
        // @todo Check removing explicit mailto check
        $filtered = preg_replace($regexpHrefMailto, 'mailto:' . $this->getProtectionText(), $filtered);
        return $filtered;
    }

    /**
     * @link http://www.mkyong.com/regular-expressions/how-to-validate-email-address-with-regular-expression/
     * @param boolean $include
     * @return string
     */
    public function getEmailRegExp($include = false)
    {
        $baseEmailRegexp = '([_A-Za-z0-9-]+(\\.[_A-Za-z0-9-]+)*@[A-Za-z0-9-]+(\\.[A-Za-z0-9-]+)*(\\.[A-Za-z]{2,}))';

        if ($include === true) {
            return $baseEmailRegexp;
        }

        return '/' . $baseEmailRegexp . '/i';
    }

    /**
     * @param string $content
     * @return string
     */
    private function filterMailtoLinks($content)
    {
        $self = $this;

        $callbackEncodeMailtoLinks = function ($match) use ($self) {
            $attrs = shortcode_parse_atts($match[1]);
            return $self->protectedMailto($match[4], $attrs);
        };

        $regexpMailtoLink = '/<a[\s+]*(([^>]*)href=["\']mailto\:([^>]*)["\'])>(.*?)<\/a[\s+]*>/is';

        return preg_replace_callback($regexpMailtoLink, $callbackEncodeMailtoLinks, $content);
    }

    /**
     * @param string $content
     * @return string
     */
    private function filterInputFields($content)
    {
        $self = $this;

        $callbackEncodeInputFields = function ($match) use ($self) {
            $input = $match[0];
            $email = $match[2];
            $strongEncoding = (bool) $self->opt('input_strong_protection');

            return $self->encodeInputField($input, $email, $strongEncoding);
        };

        $regexpInputField = '/<input([^>]*)value=["\'][\s+]*' . $this->getEmailRegExp(true) . '[\s+]*["\']([^>]*)>/is';

        return preg_replace_callback($regexpInputField, $callbackEncodeInputFields, $content);
    }

    /**
     * @return string
     */
    private function getProtectionText()
    {
        return __($this->opt('protection_text'), 'wp-mailto-links');
    }

    /**
     * Emails will be replaced by '*protected email*'
     * @param string           $content
     * @param string|callable  $replaceBy  Optional
     * @return string
     */
    public function filterPlainEmails($content, $replaceBy = null)
    {
        if ($replaceBy === null) {
            $replaceBy = $this->getProtectionText();
        }

        return preg_replace_callback($this->getEmailRegExp(), function ($matches) use ($replaceBy) {
            // workaround to skip responsive image names containing @
            $extention = strtolower($matches[4]);
            $excludedList = array('.jpg', '.jpeg', '.png', '.gif');

            if (in_array($extention, $excludedList)) {
                return $matches[0];
            }

            if (is_callable($replaceBy)) {
                return call_user_func($replaceBy, $matches);
            }

            return $replaceBy;
        }, $content);
    }

    /**
     * Encode email in input field
     * @param string $input
     * @param string $email
     * @return string
     */
    public function encodeInputField($input, $email, $strongEncoding = true)
    {
        if ($strongEncoding === false) {
            // encode email with entities (default wp method)
            return str_replace($email, antispambot($email), $input);
        }

        // add data-enc-email after "<input"
        $inputWithDataAttr = substr($input, 0, 6);
        $inputWithDataAttr .= ' data-enc-email="' . $this->getEncEmail($email) . '"';
        $inputWithDataAttr .= substr($input, 6);

        // remove email from value attribute
        $encInput = str_replace($email, '', $inputWithDataAttr);

        return $encInput;
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
        if ((!$this->opt('no_icon_class') || strpos($class_ori, $this->opt('no_icon_class')) === FALSE)
                    && !($this->opt('image_no_icon') == 1 && $this->hasImageTag($display))) {
            if ($this->opt('mail_icon') === 'image') {
            // image
                if ($this->opt('image') > 0 && strpos($class_ori, 'mail-icon-') === FALSE) {
                    $icon_class = 'mail-icon-' . $this->opt('image');

                    $attrs['class'] = (empty($attrs['class'])) ? $icon_class : $attrs['class'] .' '.$icon_class;
                }
            } elseif ($this->opt('mail_icon') === 'dashicons') {
            // dashicons
                $fontIcon = '<i class="dashicons-before ' . $this->opt('dashicons') . '"></i>';
            } elseif ($this->opt('mail_icon') === 'fontawesome') {
            // fontawesome
                $fontIcon = '<i class="fa ' . $this->opt('fontawesome') . '"></i>';
            }
        }

        // set user-defined class
        if ($this->opt('class_name') && strpos($class_ori, $this->opt('class_name')) === FALSE) {
            $attrs['class'] = (empty($attrs['class'])) ? $this->opt('class_name') : $attrs['class'].' '.$this->opt('class_name');
        }

        // check title for email address
        if (!empty($attrs['title'])) {
            $attrs['title'] = $this->filterPlainEmails($attrs['title'], '{{email}}'); // {{email}} will be replaced in javascript
        }

        // set ignore to data-attribute to prevent being processed by WPEL plugin
        $attrs['data-wpel-link'] = 'ignore';

        // create element code
        $link = '<a ';

        foreach ($attrs AS $key => $value) {
            if (strtolower($key) == 'href' && $this->opt('protect')) {
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

        if (!empty($fontIcon) && $this->opt('show_icon_before')) {
            $link .= $fontIcon . ' ';
        }

        $link .= ($this->opt('protect') && preg_match($this->getEmailRegExp(), $display) > 0) ? $this->getProtectedDisplay($display) : $display;

        if (!empty($fontIcon) && !$this->opt('show_icon_before')) {
            $link .= ' ' . $fontIcon;
        }

        $link .= '</a>';

        // filter
        $link = apply_filters('wpml_mailto', $link, $display, $email, $attrs);

        // just in case there are still email addresses f.e. within title-tag
        $link = $this->filterPlainEmails($link);

        // mark link as successfullly encoded (for admin users)
        if (current_user_can('manage_options') && $this->opt('security_check')) {
            $link .= '<i class="wpml-encoded dashicons-before dashicons-lock" title="' . __('Email encoded successfully!', 'wp-mailto-links') . '"></i>';
        }


        return $link;
    }

    /**
     * @param string $content
     * @return string
     */
    private function hasImageTag($content)
    {
        return (bool) preg_match('/<img([^>]*)>/is', $content);
    }

    /**
     * Get encoded email, used for data-attribute (translate by javascript)
     * @param string $email
     * @return string
     */
    private function getEncEmail($email)
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

}

/*?>*/

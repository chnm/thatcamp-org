<?php
/**
 * Class WPML_Shortcode_Mailto
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
final class WPML_Shortcode_Mailto extends WPRun_BaseAbstract_0x5x0
{

    /**
     * Add shortcode "[wpml_mailto]"
     */
    protected function init()
    {
        add_shortcode('wpml_mailto', $this->getCallback('mailto'));
    }

    /**
     * Handle shortcode
     * @param array   $atts
     * @param string  $content
     */
    protected function mailto($atts, $content = null)
    {
        $option = $this->getArgument(0);
        $email = $this->getArgument(1);

        if ($option->getValue('protect') && preg_match($email->getEmailRegExp(), $content) > 0) {
            $content = $email->getProtectedDisplay($content);
        }

        // set "email" to "href"
        if (isset($atts['email'])) {
            $atts['href'] = 'mailto:' . $atts['email'];
            unset($atts['email']);
        }

        $content = $email->protectedMailto($content, $atts);

        return $content;
    }

}

/*?>*/

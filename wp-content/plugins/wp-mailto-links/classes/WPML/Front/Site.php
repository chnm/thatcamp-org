<?php
/**
 * Class WPML_Front_Site
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
final class WPML_Front_Site extends WPRun_BaseAbstract_0x5x0
{

    private $option = null;
    private $emailEncoder = null;

    /**
     * Initialize
     */
    protected function init($option, $emailEncoder)
    {
        $this->option = $option;
        $this->emailEncoder = $emailEncoder;
    }

    /**
     * Get option value
     * @param string $key
     * @return string
     */
    private function opt($key)
    {
        return $this->option->getValue($key);
    }

    /**
     * Action for "wp"
     */
    protected function actionWp()
    {
        if (is_feed()) {
            if ($this->opt('filter_rss')) {
                add_filter('final_output', $this->getCallback('rssFilter'), 100);
            }
            
        } else {
            if (!$this->opt('filter_body')) {
                $filterHooks = array();

                if ($this->opt('filter_posts')) {
                    array_push($filterHooks, 'the_title', 'the_content', 'the_excerpt', 'get_the_excerpt');
                }

                if ($this->opt('filter_comments')) {
                    array_push($filterHooks, 'comment_text', 'comment_excerpt');
                }

                if ($this->opt('filter_widgets')) {
                    array_push($filterHooks, 'widget_output');
                }

                foreach ($filterHooks as $hook) {
                   add_filter($hook, $this->getCallback('contentFilter'), 100);
                }
            }

            if ($this->opt('filter_head') || $this->opt('filter_body')) {
                add_filter('final_output', $this->getCallback('pageFilter'), 100);
            }
        }
    }

    /**
     * Action for "wp_head"
     */
    protected function actionWpHead()
    {
        $headTemplateFile = WP_MAILTO_LINKS_DIR . '/templates/site/head.php';

        $this->showTemplate($headTemplateFile, array(
            'icon' => $this->opt('image'),
            'className' => $this->opt('class_name'),
            'showBefore' => $this->opt('show_icon_before'),
        ));
    }

    /**
     * Action for "wp_enqueue_scripts"
     */
    protected function actionWpEnqueueScripts()
    {
        if ($this->opt('protect')) {
            wp_enqueue_script(
                'wp-mailto-links'
                , plugins_url('/public/js/wp-mailto-links.js', WP_MAILTO_LINKS_FILE)
                , array('jquery')
            );
        }

        // add css font icons
        if ($this->opt('mail_icon') === 'dashicons') {
            wp_enqueue_style('dashicons');

        } elseif ($this->opt('mail_icon') === 'fontawesome') {
            wp_enqueue_style(
                'font-awesome'
                , 'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css'
                , array()
                , null // use caching CDN file
            );
        }
    }

    /**
     * @param string $content
     * @return string
     */
    protected function pageFilter($content)
    {
        $filterHead = (bool) $this->opt('filter_head');
        $filterBody = (bool) $this->opt('filter_body');

        return $this->emailEncoder->pageFilter($content, $filterHead, $filterBody, $this->opt('convert_emails'));
    }

    /**
     * @param string $content
     * @return string
     */
    protected function contentFilter($content)
    {
        return $this->emailEncoder->contentFilter($content, $this->opt('convert_emails'));
    }

    /**
     * @param string $content
     * @return string
     */
    protected function rssFilter($content)
    {
        return $this->emailEncoder->rssFilter($content, $this->opt('convert_emails'));
    }

}

/*?>*/

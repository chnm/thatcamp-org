<?php
/**
 * Class WPML_RegisterHook_Activate
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
final class WPML_RegisterHook_Activate extends WPRun_BaseAbstract_0x5x0
{

    /**
     * Initialize
     * @param string $pluginFile
     */
    protected function init($pluginFile)
    {
        register_activation_hook($pluginFile, $this->getCallback('activate'));
    }

    /**
     * Plugin activation procedure
     */
    protected function activate()
    {
        $option = $this->getArgument(1);

        // get old option name "WP_Mailto_Links_options"
        $oldOption = WPML_Option_OldSettings::create();
        $oldValues = $oldOption->getValues();

        if (!empty($oldValues)) {
            foreach ($oldValues as $key => $oldValue) {
                // take old value
                if ($key === 'icon') {
                    // old 'icon' contained the image number
                    // new 'mail_icon' contains type (image, dashicons, fontawesome)
                    $newValue = empty($oldValue) ? '' : 'image';
                    $option->setValue('mail_icon', $newValue, false);

                    // mail_icon === 'image' ---> 'image' contains number
                    if (!empty($oldValue)) {
                        $option->setValue('image', $oldValue, false);
                    }
                } else {
                    $option->setValue($key, $oldValue, false);
                }
            }

            $option->update();
            $oldOption->delete();
        }
    }

}

/*?>*/

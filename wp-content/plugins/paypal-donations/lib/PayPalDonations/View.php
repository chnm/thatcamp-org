<?php
/**
 * Class for MVC like View Handling in WordPress.
 *
 * @package  PayPal Donations
 * @author   Johan Steen <artstorm at gmail dot com>
 */
class PayPalDonations_View
{
    /**
     * Render a View.
     * 
     * @param  string  $filePath  Include path to the template.
     * @param  array   $data      Data to be used within the template.
     * @return string  Returns the completed view.
     */
    public static function render($filePath, $data = null)
    {
        // Check for data
        ($data) ? extract($data) : null;
 
        ob_start();
        include ($filePath);
        $template = ob_get_contents();
        ob_end_clean();

        return $template;
    }
}

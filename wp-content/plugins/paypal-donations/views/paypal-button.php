<!-- Begin PayPal Donations by http://johansteen.se/ -->
<form action="<?php echo apply_filters( 'paypal_donations_url', 'https://www.paypal.com/cgi-bin/webscr'); ?>" method="post">
    <div class="paypal-donations">
        <input type="hidden" name="cmd" value="_donations" />
        <input type="hidden" name="business" value="<?php echo $pd_options['paypal_account']; ?>" />
<?php
        # Build the button
        $paypal_btn = '';

        // Optional Settings
        if ($pd_options['page_style'])
            $paypal_btn .=  '<input type="hidden" name="page_style" value="' .$pd_options['page_style']. '" />';
        if ($return_page)
            $paypal_btn .=  '<input type="hidden" name="return" value="' .$return_page. '" />'; // Return Page
        if ($purpose)
            $paypal_btn .=  apply_filters('paypal_donations_purpose_html', '<input type="hidden" name="item_name" value="' .$purpose. '" />');  // Purpose
        if ($reference)
            $paypal_btn .=  '<input type="hidden" name="item_number" value="' .$reference. '" />';  // LightWave Plugin
        if ($amount)
            $paypal_btn .=  '<input type="hidden" name="amount" value="' . apply_filters( 'paypal_donations_amount', $amount ) . '" />';

        // More Settings
        if (isset($pd_options['return_method']))
            $paypal_btn .= '<input type="hidden" name="rm" value="' .$pd_options['return_method']. '" />';
        if (isset($pd_options['currency_code']))
            $paypal_btn .= '<input type="hidden" name="currency_code" value="' .$pd_options['currency_code']. '" />';
        if (isset($pd_options['button_localized']))
            { $button_localized = $pd_options['button_localized']; } else { $button_localized = 'en_US'; }
        if (isset($pd_options['set_checkout_language']) and $pd_options['set_checkout_language'] == true)
            $paypal_btn .= '<input type="hidden" name="lc" value="' .$pd_options['checkout_language']. '" />';

        // Settings not implemented yet
        //      $paypal_btn .=     '<input type="hidden" name="amount" value="20" />';

        // Get the button URL
        if ( $pd_options['button'] != "custom" && !$button_url)
            $button_url = str_replace('en_US', $button_localized, $donate_buttons[$pd_options['button']]);
        $paypal_btn .=  '<input type="image" src="' .$button_url. '" name="submit" alt="PayPal - The safer, easier way to pay online." />';

        // PayPal stats tracking
        if (!isset($pd_options['disable_stats']) or $pd_options['disable_stats'] != true)
            $paypal_btn .=  '<img alt="" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" />';
        echo $paypal_btn;
?>
    </div>
</form>
<!-- End PayPal Donations -->

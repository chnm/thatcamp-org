<?php
/**
 * PayPal Donations Settings.
 *
 * Class that renders out the HTML for the settings screen and contains helpful
 * methods to simply the maintainance of the admin screen.
 *
 * @package		PayPal Donations
 * @author		Johan Steen <artstorm at gmail dot com>
 * @since		Post Snippets 1.5
 */
class Paypal_Donations_Settings
{
	private $plugin_options;
	private $currency_codes;
	private $donate_buttons;
	private $localized_buttons;
	private $checkout_languages;

	public function set_options( $options, $code, $buttons, $loc_buttons, $checkout_lng )
	{
		$this->plugin_options = $options;
		$this->currency_codes = $code;
		$this->donate_buttons = $buttons;
		$this->localized_buttons = $loc_buttons;
		$this->checkout_languages = $checkout_lng;
	}

	public function render()
	{
?>
<div class=wrap>
    <h2>PayPal Donations</h2>

	<form method="post" action="">
	<?php wp_nonce_field('update-options'); ?>
	<?php // $pd_options = get_option($this->plugin_options); 
	$pd_options = $this->plugin_options;
	?>
    <table class="form-table">
    <tr valign="top">
    <th scope="row"><label for="paypal_account"><?php _e( 'PayPal Account', 'paypal-donations' ) ?></label></th>
    <td><input name="paypal_account" type="text" id="paypal_account" value="<?php echo $pd_options['paypal_account']; ?>" class="regular-text" /><span class="setting-description"><br/><?php _e( 'Your PayPal email address or your PayPal secure merchant account ID.', 'paypal-donations' ) ?></span></td>
    </tr>
    <tr valign="top">
    <th scope="row"><label for="currency_code"><?php _e( 'Currency', 'paypal-donations' ) ?></label></th>
    <td><select name="currency_code" id="currency_code">
<?php   if (isset($pd_options['currency_code'])) { $current_currency = $pd_options['currency_code']; } else { $current_currency = 'USD'; }
		foreach ( $this->currency_codes as $key => $code ) {
	        echo '<option value="'.$key.'"';
			if ($current_currency == $key) { echo ' selected="selected"'; }
			echo '>'.$code.'</option>';
		}?></select>
        <span class="setting-description"><br/><?php _e( 'The currency to use for the donations.', 'paypal-donations' ) ?></span></td>
    </tr>
    </table>

	<h3><?php _e( 'Optional Settings', 'paypal-donations' ) ?></h3>
    <table class="form-table">
    <tr valign="top">
    <th scope="row"><label for="page_style"><?php _e( 'Page Style', 'paypal-donations' ) ?></label></th>
    <td><input name="page_style" type="text" id="page_style" value="<?php echo $pd_options['page_style']; ?>" class="regular-text" /><span class="setting-description"><br/><?php _e( 'Specify the name of a custom payment page style from your PayPal account profile.', 'paypal-donations' ) ?></span></td>
    </tr>
    <tr valign="top">
    <th scope="row"><label for="return_page"><?php _e( 'Return Page', 'paypal-donations' ) ?></label></th>
    <td><input name="return_page" type="text" id="return_page" value="<?php echo $pd_options['return_page']; ?>" class="regular-text" /><span class="setting-description"><br/><?php _e( 'URL to which the donator comes to after completing the donation; for example, a URL on your site that displays a "Thank you for your donation".', 'paypal-donations' ) ?></span></td>
    </tr>    
    </table>

	<h3><?php _e( 'Defaults', 'paypal-donations' ) ?></h3>
    <table class="form-table">
    <tr valign="top">
    <th scope="row"><label for="amount"><?php _e( 'Amount', 'paypal-donations' ) ?></label></th>
    <td><input name="amount" type="text" id="amount" value="<?php echo $pd_options['amount']; ?>" class="regular-text" /><span class="setting-description"><br/><?php _e( 'The default amount for a donation (Optional).', 'paypal-donations' ) ?></span></td>
    </tr>
    <tr valign="top">
    <th scope="row"><label for="purpose"><?php _e( 'Purpose', 'paypal-donations' ) ?></label></th>
    <td><input name="purpose" type="text" id="purpose" value="<?php echo $pd_options['purpose']; ?>" class="regular-text" /><span class="setting-description"><br/><?php _e( 'The default purpose of a donation (Optional).', 'paypal-donations' ) ?></span></td>
    </tr>
    <tr valign="top">
    <th scope="row"><label for="reference"><?php _e( 'Reference', 'paypal-donations' ) ?></label></th>
    <td><input name="reference" type="text" id="reference" value="<?php echo $pd_options['reference']; ?>" class="regular-text" /><span class="setting-description"><br/><?php _e( 'Default reference for the donation (Optional).', 'paypal-donations' ) ?></span></td>
    </tr>    
    </table>

	<h3><?php _e( 'Donation Button', 'paypal-donations' ) ?></h3>
    <table class="form-table">
    <tr>
	<th scope="row"><?php _e( 'Select Button', 'paypal-donations' ) ?></th>
	<td>
	<fieldset><legend class="hidden">PayPal Button</legend>
<?php
	$custom = TRUE;
	if (isset($pd_options['button_localized'])) { $button_localized = $pd_options['button_localized']; } else { $button_localized = 'en_US'; }
	if (isset($pd_options['button'])) { $current_button = $pd_options['button']; } else { $current_button = 'large'; }
	foreach ( $this->donate_buttons as $key => $button ) {
		echo "\t<label title='" . esc_attr($key) . "'><input style='padding: 10px 0 10px 0;' type='radio' name='button' value='" . esc_attr($key) . "'";
		if ( $current_button === $key ) { // checked() uses "==" rather than "==="
			echo " checked='checked'";
			$custom = FALSE;
		}
		echo " /> <img src='" . str_replace('en_US', $button_localized, $button) . "' alt='" . $key  . "' style='vertical-align: middle;' /></label><br /><br />\n";
	}

	echo '	<label><input type="radio" name="button" value="custom"';
	checked( $custom, TRUE );
	echo '/> ' . __('Custom Button:', 'paypal-donations') . ' </label>';
?>
	<input type="text" name="button_url" value="<?php echo $pd_options['button_url']; ?>" class="regular-text" /><br/>
	<span class="setting-description"><?php _e( 'Enter a URL to a custom donation button.', 'paypal-donations' ) ?></span>
	</fieldset>
	</td>
	</tr>
    <tr valign="top">
    <th scope="row"><label for="button_localized"><?php _e( 'Country and Language', 'paypal-donations' ) ?></label></th>
    <td><select name="button_localized" id="button_localized">
<?php   foreach ( $this->localized_buttons as $key => $localize ) {
	        echo '<option value="'.$key.'"';
			if ($button_localized == $key) { echo ' selected="selected"'; }
			echo '>'.$localize.'</option>';
		}?></select>
        <span class="setting-description"><br/><?php _e( 'Localize the language and the country for the button (Updated after saving the settings).', 'paypal-donations' ) ?></span></td>
    </tr>    
    </table>

	<?php
	// Extras
	?>
	<h3><?php _e( 'Extras', 'paypal-donations' ) ?></h3>
	<p>Optional extra settings to fine tune the setup in certain scenarios.</p>
	<?php
	$this->checkbox(
		__('Disable PayPal Statistics', 'paypal-donations'),
		'disable_stats',
		$pd_options['disable_stats']);
	echo '<br/>';

	$this->checkbox(
		__('Theme CSS Override: Center Button', 'paypal-donations'),
		'center_button',
		$pd_options['center_button']);
	echo '<br/>';

	$this->checkbox(
		__('Set Checkout Language:', 'paypal-donations'),
		'set_checkout_language',
		isset($pd_options['set_checkout_language']) ? $pd_options['set_checkout_language'] : false);
	?>

	<?php
	if (isset($pd_options['checkout_language'])) { $checkout_language = $pd_options['checkout_language']; } else { $checkout_language = ''; }
	?>
	<select name="checkout_language" id="checkout_language">
		<option value="">None</option>
		<?php
	   foreach ( $this->checkout_languages as $key => $language ) {
	        echo '<option value="'.$key.'"';
			if ($checkout_language == $key) { echo ' selected="selected"'; }
			echo '>'.$language.'</option>';
		}?>
	</select>
	<br/>


    <p class="submit">
    <input type="submit" name="Submit" class="button-primary" value="<?php _e( 'Save Changes', 'paypal-donations' ) ?>" />
    </p>
</div>


<?php
	}

	// -------------------------------------------------------------------------
	// HTML and Form element methods
	// -------------------------------------------------------------------------
	
	/**
	 * Checkbox.
	 * Renders the HTML for an input checkbox.
	 *
	 * @param	string	$label		The label rendered to screen
	 * @param	string	$name		The unique name to identify the input
	 * @param	boolean	$checked	If the input is checked or not
	 */
	private function checkbox( $label, $name, $checked )
	{
		printf( '<input type="checkbox" name="%s" value="true"', $name );
		if ($checked)
			echo ' checked';
		echo ' />';
		echo ' '.$label;
	}

}

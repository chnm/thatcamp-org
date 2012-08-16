<?php
/*
Plugin Name: Countdown Timer
Plugin URI: http://www.andrewferguson.net/wordpress-plugins/countdown-timer/
Description: Add template tags and widget to count down or up to the years, months, weeks, days, hours, minutes, and/or seconds to a particular event.
Version: 2.4.3
Author: Andrew Ferguson
Author URI: http://www.andrewferguson.net

Countdown Timer - Add template tags and widget to count down the years, months, weeks, days, hours, and minutes to a particular event
Copyright (c) 2005-2012 Andrew Ferguson

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
*/
		
	$currentLocale = get_locale();
	if(!empty($currentLocale)) {
		$moFile = dirname(__FILE__) . "/lang/fergcorp_countdownTimer-" . $currentLocale . ".mo";
		if(@file_exists($moFile) && is_readable($moFile)) load_textdomain('fergcorp_countdownTimer', $moFile);
	}

	/**
	 * Displays the option page
	 *
	 * @since 2.1
	 * @access public
	 * @author Andrew Ferguson
	 */
	function fergcorp_countdownTimer_myOptionsSubpanel(){
			?>
			<script type="text/javascript">			
			jQuery(document).ready( function($) {
				// close postboxes that should be closed
				jQuery('.if-js-closed').removeClass('if-js-closed').addClass('closed');
				
				// postboxes setup
				postboxes.add_postbox_toggles('fergcorp-countdown-timer'); //For WP2.7 and above
	
			});

			function clearField(eventType, fieldNum){ //For deleting events without reloading
				var agree=confirm('<?php _e('Are you sure you wish to delete', 'fergcorp_countdownTimer'); ?> '+document.getElementsByName(eventType+'[text'+fieldNum+']').item(0).value+'?');
				if(agree){
					var inputID = eventType + '_table' + fieldNum;
					document.getElementById(inputID).style.display = 'none';
					document.getElementsByName(eventType+'[date'+fieldNum+']').item(0).value = '';
					document.getElementsByName(eventType+'[text'+fieldNum+']').item(0).value = '';
					document.getElementsByName(eventType+'[link'+fieldNum+']').item(0).value = '';
					document.getElementsByName(eventType+'[timeSince'+fieldNum+']').item(0).value = '';
					}
				else
					return false;
			}

			function showHideContent(id, show){ //For hiding sections
				var elem = document.getElementById(id);
				if (elem){
					if (show){
						elem.style.display = 'block';
						elem.style.visibility = 'visible';
					}
					else{
						elem.style.display = 'none';
						elem.style.visibility = 'hidden';
					}
				}
			}

			</script>

			<div class="wrap" id="fergcorp_countdownTimer_div">

				<h2>Countdown Timer</h2>
            
				<div id="poststuff">        
                    
				<?php
                		function fergcorp_countdownTimer_resources_meta_box(){
							?>
                            <table width="90%" border="0" cellspacing="0" cellpadding="0">
                                  <tr>
                                    <td><ul><li><a href="http://andrewferguson.net/wordpress-plugins/countdown-timer/" target="_blank"><?php _e('Plugin Homepage','fergcorp_countdownTimer'); ?></a></li></ul></td>
                                    <td><ul><li><a href="http://wordpress.org/tags/countdown-timer" target="_blank"><?php _e('Support Forum','fergcorp_countdownTimer'); ?></a></li></ul></td>
                                    <td><ul><li><a href="http://www.amazon.com/gp/registry/registry.html?ie=UTF8&amp;type=wishlist&amp;id=E7Q6VO0I8XI4" target="_blank"><?php _e('Amazon Wishlist','fergcorp_countdownTimer'); ?></a></li></ul></td>
                                    <td><ul><li><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=38923"><img src="https://www.paypal.com/en_US/i/btn/btn_donate_SM.gif" alt="Donate"/></a></li></ul></td>
                                  </tr>
                                </table>
								<p><?php _e("I've coded and supported this plugin for several years now, however I am a full-time engineer with a real, full-time job and really only do this programming thing on the side for the love of it. If you would like to continue to see updates, please consider donating above.", 'fergcorp_countdownTimer'); ?></p>                            
							<?php
						}
						add_meta_box("fergcorp_countdownTimer_resources", __('Resources'), "fergcorp_countdownTimer_resources_meta_box", "fergcorp-countdown-timer");
                        ?>
   
                        <form method="post" action="options.php">
                        
							<?php echo '<input type="hidden" name="fergcorp_countdownTimer_noncename" id="fergcorp_countdownTimer_noncename" value="'.wp_create_nonce( plugin_basename(__FILE__) ) . '" />'; ?>
                            <?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
                            <?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>
							<?php settings_fields('fergcorp_countdownTimer_options'); ?>

                            <?php
							function fergcorp_countdownTimer_installation_meta_box(){
							?>
                            <p><?php printf(__("Countdown timer uses <a %s>PHP's strtotime function</a> and will parse about any English textual datetime description.", 'fergcorp_countdownTimer'), "href='http://us2.php.net/strtotime' target='_blank'"); ?></p>
										<p><?php _e('Examples of some (but not all) valid dates', 'fergcorp_countdownTimer'); ?>:</p>
										<ul style="list-style:inside circle; font-size:11px; margin-left: 20px;">
													<li>now</li>
													<li>31 january 1986</li>
													<li>+1 day</li>
													<li>next thursday</li>
													<li>last monday</li>
										</ul>
                          
                            <p><?php printf(__("To insert the Countdown Timer into your sidebar, you can use the <a %s>Countdown Timer Widget</a>.", 'fergcorp_countdownTimer'), "href='".admin_url('widgets.php')."'"); ?></p>
										<p><?php _e("Alternatively, you can also use this code in your sidebar.php file:", 'fergcorp_countdownTimer'); ?></p>
										<p>
											<code>&lt;li id='countdown'&gt;&lt;h2&gt;Countdown:&lt;/h2&gt;<br />
												&lt;ul&gt;<br />
												&lt;?php function_exists('fergcorp_countdownTimer')?fergcorp_countdownTimer():NULL; ?&gt;<br />
												&lt;/ul&gt;<br />
												&lt;/li&gt;
											</code>
										</p>
                                        
                                        <p><?php printf(__("If you want to insert the Countdown Timer into a page or post, you can use the following <abbr %s %s>shortcodes</abbr> to return all or a limited number of Countdown Timers, respectively:", 'fergcorp_countdownTimer'), "title='".__('A shortcode is a WordPress-specific code that lets you do nifty things with very little effort. Shortcodes can embed files or create objects that would normally require lots of complicated, ugly code in just one line. Shortcode = shortcut.', 'fergcorp_countdownTimer')."'", "style='cursor:pointer; border-bottom:1px black dashed'" ); ?></p>
										<p>
                                   			<code>
													[fergcorp_cdt]<br /><br />
                                                    [fergcorp_cdt max=##]
											</code>
                                        </p>
                                        <p><?php _e("Where <em>##</em> is maximum number of results to be displayed - ordered by date.", 'fergcorp_countdownTimer'); ?></p>   
										<p><?php _e("If you want to insert individual countdown timers, such as in posts or on pages, you can use the following shortcode:", 'fergcorp_countdownTimer'); ?></p>
										<p>
											<code><?php _e("Time until my birthday:", 'fergcorp_countdownTimer'); ?><br />
													[fergcorp_cdt_single date="<em>ENTER_DATE_HERE</em>"]
											</code>
										</p>
										<p><?php printf(__("Where <em>ENTER_DATE_HERE</em> uses <a %s>PHP's strtotime function</a> and will parse about any English textual datetime description.", 'fergcorp_countdownTimer'), "href='http://us2.php.net/strtotime' target='_blank'"); ?></p>                     
                            <?php		
							}
                        	add_meta_box('fergcorp_countdownTimer_installation', __('Installation and Usage Notes'), 'fergcorp_countdownTimer_installation_meta_box', 'fergcorp-countdown-timer', 'advanced', 'default');
										
							function fergcorp_countdownTimer_events_meta_box(){

								$fergcorp_countdownTimer_oneTimeEvent = get_option("fergcorp_countdownTimer_oneTimeEvent"); //Get the events from the WPDB to make sure a fresh copy is being used
								/*If the user wants, cycle through the array to find out if they have already occured, if so: set them to NULL*/
								if(get_option('fergcorp_countdownTimer_deleteOneTimeEvents') && (count($fergcorp_countdownTimer_oneTimeEvent[0])!=0) ){
									foreach($fergcorp_countdownTimer_oneTimeEvent as $key => $value){
										if(($value["date"]<=time())&&($value["timeSince"]=="")){
										$fergcorp_countdownTimer_oneTimeEvent[$key]["date"]=NULL;
										}
									}
								}
							?>
										<table border="0" cellspacing="0" cellpadding="2">
										<tr align="center">
											<td><strong><?php _e('Delete', 'fergcorp_countdownTimer'); ?></strong></td>
											<td><?php _e('Event Date', 'fergcorp_countdownTimer'); ?></td>
											<td><?php _e('Event Title', 'fergcorp_countdownTimer'); ?></td>
											<td><?php _e('Link', 'fergcorp_countdownTimer'); ?></td>
											<td><?php _e('Display "Time since"', 'fergcorp_countdownTimer'); ?></td>
										</tr>
											<?php
												$oneTimeEvent_count = 0;
												$oneTimeEvent_entriesCount = count($fergcorp_countdownTimer_oneTimeEvent);
												if($fergcorp_countdownTimer_oneTimeEvent != ""){
													for($i=0; $i < $oneTimeEvent_entriesCount+1; $i++){
														if($fergcorp_countdownTimer_oneTimeEvent[$i]["date"]!=''){ //If the text is NULL, skip over it?>
														<tr id="fergcorp_countdownTimer_oneTimeEvent_table<?php echo $oneTimeEvent_count; ?>" align="center">
														<td><a href="javascript:void(0);" onclick="javascript:clearField('fergcorp_countdownTimer_oneTimeEvent','<?php echo $oneTimeEvent_count; ?>');">X</a></td>
														<td><input type="text" size="30" name="fergcorp_countdownTimer_oneTimeEvent[date<?php echo $oneTimeEvent_count; ?>]" value="<?php if($fergcorp_countdownTimer_oneTimeEvent[$i]["date"] != "")echo gmdate("D, d M Y H:i:s", $fergcorp_countdownTimer_oneTimeEvent[$i]["date"] + (get_option('gmt_offset') * 3600))." ".(get_option('gmt_offset')>="0"?"+":NULL).(get_option('gmt_offset')=="0"?"00":NULL).(get_option('gmt_offset')*100); ?>" /></td>
														<td><input type="text" size="20" name="fergcorp_countdownTimer_oneTimeEvent[text<?php echo $oneTimeEvent_count; ?>]" value="<?php echo htmlspecialchars(stripslashes($fergcorp_countdownTimer_oneTimeEvent[$i]["text"])); ?>" /></td>
														<td><input type="text" size="15" name="fergcorp_countdownTimer_oneTimeEvent[link<?php echo $oneTimeEvent_count; ?>]" value="<?php echo $fergcorp_countdownTimer_oneTimeEvent[$i]["link"]; ?>" /></td>

														<td><input type="checkbox" name="fergcorp_countdownTimer_oneTimeEvent[timeSince<?php echo $oneTimeEvent_count; ?>]" value="1" <?php checked("1", $fergcorp_countdownTimer_oneTimeEvent[$i]["timeSince"]); ?>/></td>
														</tr>
														<?php
														$oneTimeEvent_count++;
														 }
														@next($fergcorp_countdownTimer_oneTimeEvent);
														}
													}
													?><tr align="center">
													<td></td>
													<td><input type="text" size="30" name="fergcorp_countdownTimer_oneTimeEvent[date<?php echo $oneTimeEvent_count; ?>]" /></td>
													<td><input type="text" size="20" name="fergcorp_countdownTimer_oneTimeEvent[text<?php echo $oneTimeEvent_count; ?>]" /></td>
													<td><input type="text" size="15" name="fergcorp_countdownTimer_oneTimeEvent[link<?php echo $oneTimeEvent_count; ?>]" /></td>
													<td><input type="checkbox" name="fergcorp_countdownTimer_oneTimeEvent[timeSince<?php echo $oneTimeEvent_count; ?>]" value="1" /></td>
													</tr>
										</table>
										<?php echo '<input type="hidden" name="oneTimeEvent_count" value="'.($oneTimeEvent_count+1).'" />'; ?>

										<p><?php _e("Automatically delete 'One Time Events' after they have occured?", 'fergcorp_countdownTimer'); ?> <input name="fergcorp_countdownTimer_deleteOneTimeEvents" type="radio" value="1" <?php print(get_option('fergcorp_countdownTimer_deleteOneTimeEvents')==1?"checked='checked'":NULL)?> /><?php _e('Yes', 'fergcorp_countdownTimer'); ?> :: <input name="fergcorp_countdownTimer_deleteOneTimeEvents" type="radio" value="0" <?php print(get_option('fergcorp_countdownTimer_deleteOneTimeEvents')==0?"checked='checked'":NULL)?>/><?php _e('No', 'fergcorp_countdownTimer'); ?></p>
                               <?php
                            }
						   	add_meta_box("fergcorp_countdownTimer_events", __('One Time Events'), "fergcorp_countdownTimer_events_meta_box", "fergcorp-countdown-timer");

							function fergcorp_countdownTimer_management_meta_box(){
								?>
								<p><?php _e("How long the timer remain visable if \"Display 'Time Since'\" is ticked:", 'fergcorp_countdownTimer'); ?><br />
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php _e("Seconds: ", 'fergcorp_countdownTimer'); ?><input type="text" value="<?php echo get_option('fergcorp_countdownTimer_timeSinceTime'); ?>" name="fergcorp_countdownTimer_timeSinceTime" size="10" /> <?php _e("(0 = infinite; 86400 seconds = 1 day; 604800 seconds = 1 week)", "fergcorp_countdownTimer"); ?></p>
								<p><?php _e('Enable JavaScript countdown:', 'fergcorp_countdownTimer'); ?> <input name="fergcorp_countdownTimer_enableJS" type="radio" value="1" <?php checked('1', get_option('fergcorp_countdownTimer_enableJS')); ?> /><?php _e('Yes', 'fergcorp_countdownTimer'); ?> :: <input name="fergcorp_countdownTimer_enableJS" type="radio" value="0" <?php checked('0', get_option('fergcorp_countdownTimer_enableJS'));?>/><?php _e('No', 'fergcorp_countdownTimer'); ?></p>
                                <p><?php _e('By default, WordPress does not parse shortcodes that are in excerpts. If you want to enable this functionality, you can do so here. Note that this will enable the parsing of <em>all</em> shortcodes in the excerpt, not just the ones associated with Countdown Timer.', 'fergcorp_countdownTimer'); ?></p>
                                <p><?php _e('Enable shortcodes in the_excerpt:', 'fergcorp_countdownTimer'); ?> <input name="fergcorp_countdownTimer_enableShortcodeExcerpt" type="radio" value="1" <?php checked('1', get_option('fergcorp_countdownTimer_enableShortcodeExcerpt')); ?> /><?php _e('Yes', 'fergcorp_countdownTimer'); ?> :: <input name="fergcorp_countdownTimer_enableShortcodeExcerpt" type="radio" value="0" <?php checked('0', get_option('fergcorp_countdownTimer_enableShortcodeExcerpt'));?>/><?php _e('No', 'fergcorp_countdownTimer'); ?></p>
                                <?php /*<p><?php //_e('Countdown Timer exports your events so they can be used by other applications, such as Facebook. The location of your file is:', 'fergcorp_countdownTimer'); ?></p>
								<ul>
                                	<li><input name="serialDataFilename" type="hidden" value="<?php print(get_option('fergcorp_countdownTimer_serialDataFilename')); ?>" size="50"/> <a href="<?php print(plugins_url(dirname(plugin_basename(__FILE__)) . "/" . get_option('fergcorp_countdownTimer_serialDataFilename'))); ?>" target="_blank"><?php //print(plugins_url(dirname(plugin_basename(__FILE__)) . "/". get_option('fergcorp_countdownTimer_serialDataFilename'))); ?></a></li>
		                        </ul>
								*/ ?>
								<?php
							}
							add_meta_box("fergcorp_countdownTimer_management", __('Management', 'fergcorp_countdownTimer'), "fergcorp_countdownTimer_management_meta_box", "fergcorp-countdown-timer");



							function fergcorp_countdownTimer_display_options_meta_box(){
								?>
								<p><?php _e('This setting controls what units of time are displayed.', 'fergcorp_countdownTimer'); ?></p>
								<ul>
									<li><?php _e('Years:', 'fergcorp_countdownTimer'); ?> <input name="fergcorp_countdownTimer_showYear" type = "radio" value = "1" <?php checked('1', get_option('fergcorp_countdownTimer_showYear')); ?> /> <?php _e('Yes', 'fergcorp_countdownTimer'); ?> :: <input name="fergcorp_countdownTimer_showYear" type = "radio" value = "0" <?php checked('0', get_option('fergcorp_countdownTimer_showYear')); ?> /> <?php _e('No', 'fergcorp_countdownTimer'); ?></li>
								  <li><?php _e('Months:', 'fergcorp_countdownTimer'); ?> <input name="fergcorp_countdownTimer_showMonth" type = "radio" value = "1" <?php checked('1', get_option('fergcorp_countdownTimer_showMonth')); ?> /> <?php _e('Yes', 'fergcorp_countdownTimer'); ?> :: <input name="fergcorp_countdownTimer_showMonth" type = "radio" value = "0" <?php checked('0', get_option('fergcorp_countdownTimer_showMonth')); ?> /> <?php _e('No', 'fergcorp_countdownTimer'); ?></li>
									<li><?php _e('Weeks:', 'fergcorp_countdownTimer'); ?> <input name="fergcorp_countdownTimer_showWeek" type = "radio" value = "1" <?php checked('1', get_option('fergcorp_countdownTimer_showWeek')); ?> /> <?php _e('Yes', 'fergcorp_countdownTimer'); ?> :: <input name="fergcorp_countdownTimer_showWeek" type = "radio" value = "0" <?php checked('0', get_option('fergcorp_countdownTimer_showWeek')); ?> /> <?php _e('No', 'fergcorp_countdownTimer'); ?></li>
									<li><?php _e('Days:', 'fergcorp_countdownTimer'); ?> <input name="fergcorp_countdownTimer_showDay" type = "radio" value = "1" <?php checked('1', get_option('fergcorp_countdownTimer_showDay')); ?> /> <?php _e('Yes', 'fergcorp_countdownTimer'); ?> :: <input name="fergcorp_countdownTimer_showDay" type = "radio" value = "0" <?php checked('0', get_option('fergcorp_countdownTimer_showDay')); ?> /> <?php _e('No', 'fergcorp_countdownTimer'); ?></li>
									<li><?php _e('Hours:', 'fergcorp_countdownTimer'); ?> <input name="fergcorp_countdownTimer_showHour" type = "radio" value = "1" <?php checked('1', get_option('fergcorp_countdownTimer_showHour')); ?> /> <?php _e('Yes', 'fergcorp_countdownTimer'); ?> :: <input name="fergcorp_countdownTimer_showHour" type = "radio" value = "0" <?php checked('0', get_option('fergcorp_countdownTimer_showHour')); ?> /> <?php _e('No', 'fergcorp_countdownTimer'); ?></li>
									<li><?php _e('Minutes:', 'fergcorp_countdownTimer'); ?> <input name="fergcorp_countdownTimer_showMinute" type = "radio" value = "1" <?php checked('1', get_option('fergcorp_countdownTimer_showMinute')); ?> /> <?php _e('Yes', 'fergcorp_countdownTimer'); ?> :: <input name="fergcorp_countdownTimer_showMinute" type = "radio" value = "0" <?php checked('0', get_option('fergcorp_countdownTimer_showMinute')); ?> /> <?php _e('No', 'fergcorp_countdownTimer'); ?></li>
									<li><?php _e('Seconds:', 'fergcorp_countdownTimer'); ?> <input name="fergcorp_countdownTimer_showSecond" type = "radio" value = "1" <?php checked('1', get_option('fergcorp_countdownTimer_showSecond')); ?> /> <?php _e('Yes', 'fergcorp_countdownTimer'); ?> :: <input name="fergcorp_countdownTimer_showSecond" type = "radio" value = "0" <?php checked('0', get_option('fergcorp_countdownTimer_showSecond')); ?> /> <?php _e('No', 'fergcorp_countdownTimer'); ?></li>
									<li><?php _e('Strip non-significant zeros:', 'fergcorp_countdownTimer'); ?> <input name="fergcorp_countdownTimer_stripZero" type = "radio" value = "1" <?php checked('1', get_option('fergcorp_countdownTimer_stripZero')); ?> /> <?php _e('Yes', 'fergcorp_countdownTimer'); ?> :: <input name="fergcorp_countdownTimer_stripZero" type = "radio" value = "0" <?php checked('0', get_option('fergcorp_countdownTimer_stripZero')); ?> /> <?php _e('No', 'fergcorp_countdownTimer'); ?></li>
								</ul>
								<?php
							}
							add_meta_box("fergcorp_countdownTimer_display_options", __('Countdown Time Display'), "fergcorp_countdownTimer_display_options_meta_box", "fergcorp-countdown-timer");
							
							function fergcorp_countdownTimer_onHover_time_format_meta_box(){
								?>
								<p><?php printf(__("If you set 'onHover Time Format', hovering over the time left will show the user what the date of the event is. onHover Time Format uses <a %s>PHP's Date() function</a>.", 'fergcorp_countdownTimer'), "href='http://us2.php.net/date' target='_blank'"); ?></p>
								<p><?php _e('Examples', 'fergcorp_countdownTimer'); ?>:</p>
								<ul>
									<li>"<em>j M Y, G:i:s</em>" <?php _e('goes to', 'fergcorp_countdownTimer'); ?> "<strong>17 Mar 2008, 14:50:00</strong>"</li>
									<li>"<em>F jS, Y, g:i a</em>" <?php _e('goes to', 'fergcorp_countdownTimer'); ?> "<strong>March 17th, 2008, 2:50 pm</strong>"</li>
								</ul>
								<p><?php _e('onHover Time Format', 'fergcorp_countdownTimer'); ?> <input type="text" value="<?php echo get_option('fergcorp_countdownTimer_timeOffset'); ?>" name="fergcorp_countdownTimer_timeOffset" /></p>
								<?php
							}
							add_meta_box("fergcorp_countdownTimer_onHover_time_format", __('onHover Time Format'), "fergcorp_countdownTimer_onHover_time_format_meta_box", "fergcorp-countdown-timer");
							
							function fergcorp_countdownTimer_display_format_options_meta_box(){
								?>
								<p><?php _e('This setting allows you to customize how each event is styled and wrapped.', 'fergcorp_countdownTimer'); ?></p>
								<p><?php _e('<strong>Title Suffix</strong> sets the content that appears immediately after title and before the timer.', 'fergcorp_countdownTimer'); ?></p>
								<p><?php _e('Examples/Defaults', 'fergcorp_countdownTimer'); ?>:</p>
								<ul>
                                       <li><em><?php _e('Title Suffix', 'fergcorp_countdownTimer'); ?>:</em> <code>:&lt;br /&gt;</code></li>
                                   </ul>
                                   <p><?php _e('Title Suffix', 'fergcorp_countdownTimer'); ?> <input type="text" value="<?php echo htmlspecialchars(stripslashes(get_option('fergcorp_countdownTimer_titleSuffix'))); ?>" name="fergcorp_countdownTimer_titleSuffix" /></p>
								<?php
							}
							add_meta_box("fergcorp_countdownTimer_display_format_options", __('Display Format Options'), "fergcorp_countdownTimer_display_format_options_meta_box", "fergcorp-countdown-timer");

							function fergcorp_countdownTimer_example_display_meta_box(){
								echo "<ul>";
								fergcorp_countdownTimer();
								echo "</ul>";
								if(get_option('fergcorp_countdownTimer_enableJS')) {
	                                fergcorp_countdownTimer_js();
								}
							}
							add_meta_box("fergcorp_countdownTimer_example_display", __('Example Display'), "fergcorp_countdownTimer_example_display_meta_box", "fergcorp-countdown-timer");
							do_meta_boxes('fergcorp-countdown-timer','advanced',null);
							   
						?>

						<div>
							<p class="submit">
								<input type="submit" class="button-primary" value="<?php _e('Save Changes', 'fergcorp_countdownTimer'); ?>&raquo;" />
							</p>
						</div>
						</form>
				</div>
       
            </div>
	<?php
	}

	/**
	 * Returns/echos the formated output for the countdown
	 *
	 * @param $eventLimit int The maximum number of events to echo or return, sorted by date
	 * @param $output string If set to 'echo', will echo the results with no return; If set to 'return', will return the results with no echo.
	 * @since 2.1
	 * @access public
	 * @author Andrew Ferguson
	 * @return string If set, will return the formated output ready for display
	*/
	function fergcorp_countdownTimer($eventLimit = -1, $output = "echo"){ //'echo' will print the results, 'return' will just return them
		global $fergcorp_countdownTimer_noEventsPresent;
		$fergcorp_countdownTimer_noEventsPresent = FALSE;

		$fergcorp_countdownTimer_oneTimeEvent = get_option("fergcorp_countdownTimer_oneTimeEvent"); //Get the events from the WPDB to make sure a fresh copy is being used
		if($fergcorp_countdownTimer_oneTimeEvent!=''){
		/*If the user wants, cycle through the array to find out if they have already occured, if so: set them to NULL*/
			if(count($fergcorp_countdownTimer_oneTimeEvent[0])!=0){
				foreach($fergcorp_countdownTimer_oneTimeEvent as $key => $value){
					if(($value["date"]<=time())&&($value["timeSince"]=="")){
					$fergcorp_countdownTimer_oneTimeEvent[$key]["date"]=NULL;
					}
				}
			}
			else{
				$fergcorp_countdownTimer_noEventsPresent = TRUE;//because there are no dates at all!
			}
		}
		else{
				$fergcorp_countdownTimer_noEventsPresent = TRUE;//because there are no dates at all!
		}
		
		/*Now that all the events are in the same array, we need to sort them by date. This is actually the same code used above for the admin page.
		At some point, I plan to make this into a function; but for, this will do...

		And what it does is this:
		The number of elements in the array are counted. Then for array is gone through x^(x-1) times. This allows for all posible date permuations to be sorted out and ordered correctly.
		Genious, yes? No*/
		$eventCount = count($fergcorp_countdownTimer_oneTimeEvent);
		for($x=0; $x<$eventCount; $x++){
			for($z=0; $z<$eventCount-1; $z++){
				if(($fergcorp_countdownTimer_oneTimeEvent[$z+1]["date"] < $fergcorp_countdownTimer_oneTimeEvent[$z]["date"]) && (array_key_exists($z+1, $fergcorp_countdownTimer_oneTimeEvent))){
					$temp = $fergcorp_countdownTimer_oneTimeEvent[$z];
					$fergcorp_countdownTimer_oneTimeEvent[$z] = $fergcorp_countdownTimer_oneTimeEvent[$z+1];
					$fergcorp_countdownTimer_oneTimeEvent[$z+1] = $temp;
				}
			}
		}
		if($eventLimit != -1)	//If the eventLimit is set
			$eventCount = $eventLimit;

		//This is the part that does the actual outputting. If you want to preface data, this an excellent spot to do it in.
		if($fergcorp_countdownTimer_noEventsPresent == FALSE){
			$fergcorp_countdownTimer_noEventsPresent = TRUE; //Reset the test
			for($i = 0; $i < $eventCount; $i++){
					$thisEvent = fergcorp_countdownTimer_format(stripslashes($fergcorp_countdownTimer_oneTimeEvent[$i]["text"]), $fergcorp_countdownTimer_oneTimeEvent[$i]["date"], 0, $fergcorp_countdownTimer_oneTimeEvent[$i]["timeSince"], get_option('fergcorp_countdownTimer_timeSinceTime'), stripslashes($fergcorp_countdownTimer_oneTimeEvent[$i]["link"]), get_option('fergcorp_countdownTimer_timeOffset'), false);			
				if($output == "echo")
					echo $thisEvent;
				elseif($output == "return"){
					$toReturn .= $thisEvent;
				}
				if(($fergcorp_countdownTimer_oneTimeEvent[$i]["date"]==NULL) && (isset($fergcorp_countdownTimer_oneTimeEvent[$i]))){
					$eventCount++;
				}
			}
		}
		if($fergcorp_countdownTimer_noEventsPresent){
			if($output == "echo"){

				_e('No dates present', 'fergcorp_countdownTimer');
			}
			elseif($output == "return"){
				$toReturn .= __('No dates present', 'fergcorp_countdownTimer');
			}
		}

		if($output == "return")
				return $toReturn;

	}

	/**
	 * Returns an individual countdown element
	 *
	 * @param $text string Text associated with the countdown event
	 * @param $time int Unix time of the event
	 * @param $offset int Server offset of the time
	 * @param $timeSince int If the event should be displayed if it has already passed
	 * @param $timeSinceTime int If $timeSince is set, how long should it be displayed for in seconds
	 * @param $link string Link associated with the countdown event
	 * @param $timeFormat string Forming of the onHover time display
	 * @param $standalone If true, don't output the li-element
	 * @since 2.1
	 * @access private
	 * @author Andrew Ferguson
	 * @return string The content of the post with the appropriate dates inserted (if any)
	*/
	
	function fergcorp_countdownTimer_format($eventText, $time, $offset, $timeSince=0, $timeSinceTime=0, $link=NULL, $timeFormat = "j M Y, G:i:s", $standalone =  FALSE){
		global $fergcorp_countdownTimer_noEventsPresent, $fergcorp_countdownTimer_jsUID;
		if(!isset($fergcorp_countdownTimer_jsUID)){
			$fergcorp_countdownTimer_jsUID = array();
		}
		$time_left = $time - time() + $offset;
		if(!$standalone)
			$content = "<li class = 'fergcorp_countdownTimer_event_li'>";
		$nonceTracker = "x".md5(rand()); //XHTML prevents IDs from starting with a number, so append a 'x' on the front just to make sure it dosn't start with numeric	
		$eventTitle = "<span class = 'fergcorp_countdownTimer_event_title'>".($link==""?$eventText:"<a href=\"$link\" class = 'fergcorp_countdownTimer_event_linkTitle'>".$eventText."</a>").'</span>'.get_option('fergcorp_countdownTimer_titleSuffix')."\n";
		$timePrefix = "<abbr title = \"".gmdate($timeFormat, $time + (get_option('gmt_offset') * 3600))."\" id = '$nonceTracker' class = 'fergcorp_countdownTimer_event_time'>";
		
		if(($time_left < 0)&&($timeSince==1)&&((($time_left + $timeSinceTime) > 0)||($timeSinceTime == 0))){ //If the event has already passed and we still want to display the event
			$fergcorp_countdownTimer_noEventsPresent = FALSE; //Set to FALSE so we know there's an event to display
			$fergcorp_countdownTimer_jsUID[count($fergcorp_countdownTimer_jsUID)] = array("id"			=> $nonceTracker,
																										"targetDate"	=> $time,
																										);	//Don't want to actually keep track of it until now
			if($eventText){
				$content .= $eventTitle;
			}
			$content .= $timePrefix.sprintf(__("%s ago", 'fergcorp_countdownTimer'), fergcorp_countdownTimer_fuzzyDate((time() + $offset), $time, $time))."</abbr>";
			if(!$standalone)
				$content .= "</li>\r\n";
			return $content;
		}
		elseif($time_left > 0){ //If the event has not yet happened yet
			$fergcorp_countdownTimer_noEventsPresent = FALSE; //Set to FALSE so we know there's an event to display
			$fergcorp_countdownTimer_jsUID[count($fergcorp_countdownTimer_jsUID)] = array("id"			=> $nonceTracker,
																										"targetDate"	=> $time,
																										);	//Don't want to actually keep track of it until now
			if($eventText){
				$content .= $eventTitle;
			}
			$content .= $timePrefix.sprintf(__("in %s", 'fergcorp_countdownTimer'), fergcorp_countdownTimer_fuzzyDate($time, (time() + $offset), $time))."</abbr>";
			if(!$standalone)
				$content .= "</li>\r\n";
			return $content;
		}
		else{
			return NULL;
		}
	}
	
	if(!function_exists("cal_days_in_month")){
		/**
		 * Returns the number of days in a given month and year, taking into account leap years.
		 * The is a replacement function should cal_days_in_month not be availible
		 *
		 * @param $calendar int ignored
	 	 * @param $month int month (integers 1-12) 
		 * @param $year int year (any integer)
		 * @since 2.3
		 * @access private
		 * @author David Bindel (dbindel at austin dot rr dot com) (http://us.php.net/manual/en/function.cal-days-in-month.php#38666)
		 * @author ben at sparkyb dot net
		 * @return int The content of the post with the appropriate dates inserted (if any)
		*/
		function cal_days_in_month($calendar, $month, $year){
			// calculate number of days in a month 
			return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
		}
	}

	/**
	 * Returns the numerical part of a single countdown element
	 *
	 * @param $targetTime
	 * @param $nowTime
	 * @param $realTargetTime
	 * @since 2.1
	 * @access private
	 * @author Andrew Ferguson
	 * @return string The content of the post with the appropriate dates inserted (if any)
	*/
	function fergcorp_countdownTimer_fuzzyDate($targetTime, $nowTime, $realTargetTime){

		$rollover = 0;
		$s = '';
		$sigNumHit = false;

		$nowYear = date("Y", $nowTime);
		$nowMonth = date("m", $nowTime);
		$nowDay = date("d", $nowTime);
		$nowHour = date("H", $nowTime);
		$nowMinute = date("i", $nowTime);
		$nowSecond = date("s", $nowTime);

		$targetYear = date("Y", $targetTime);
		$targetMonth = date("m", $targetTime);
		$targetDay = date("d", $targetTime);
		$targetHour = date("H", $targetTime);
		$targetMinute = date("i", $targetTime);
		$targetSecond = date("s", $targetTime);

		$resultantYear = $targetYear - $nowYear;
		$resultantMonth = $targetMonth - $nowMonth;
		$resultantDay = $targetDay - $nowDay;
		$resultantHour = $targetHour - $nowHour;
		$resultantMinute = $targetMinute - $nowMinute;
		$resultantSecond = $targetSecond - $nowSecond;
		
		if($resultantSecond < 0){
			$resultantMinute--;
			$resultantSecond = 60 + $resultantSecond;
		}

		if($resultantMinute < 0){
			$resultantHour--;
			$resultantMinute = 60 + $resultantMinute;
		}

		if($resultantHour < 0){

			$resultantDay--;
			$resultantHour = 24 + $resultantHour;
		}

		if($resultantDay < 0){
			$resultantMonth--;
			$resultantDay = $resultantDay + cal_days_in_month(CAL_GREGORIAN, $nowMonth, $nowYear); //Holy crap! When did they introduce this function and why haven't I heard about it??
		}

		if($resultantMonth < 0){
			$resultantYear--;
			$resultantMonth = $resultantMonth + 12;
		}

		//Year
		if(get_option('fergcorp_countdownTimer_showYear')){
			if($sigNumHit || !get_option('fergcorp_countdownTimer_stripZero') || $resultantYear){
				$s = '<span class="fergcorp_countdownTimer_year fergcorp_countdownTimer_timeUnit">' . sprintf(_n("%d year,", "%d years,", $resultantYear, "fergcorp_countdownTimer"), $resultantYear)."</span> ";
				$sigNumHit = true;
			}
		}
		else{
			$rollover = $resultantYear*31536000;
		}

		//Month
		if(get_option('fergcorp_countdownTimer_showMonth')){
			if($sigNumHit || !get_option('fergcorp_countdownTimer_stripZero') || intval($resultantMonth + ($rollover/2628000)) ){
				$resultantMonth = intval($resultantMonth + ($rollover/2628000));
				$s .= '<span class="fergcorp_countdownTimer_month fergcorp_countdownTimer_timeUnit">' . sprintf(_n("%d month,", "%d months,", $resultantMonth, "fergcorp_countdownTimer"), $resultantMonth)."</span> ";
				$rollover = $rollover - intval($rollover/2628000)*2628000; //(12/31536000)
				$sigNumHit = true;
			}
		}
		else{
			//If we don't want to show months, let's just calculate the exact number of seconds left since all other units of time are fixed (i.e. months are not a fixed unit of time)
			$rollover = $rollover + $resultantMonth*2592000;
			$totalTime = $targetTime - $nowTime;
			
			//If we showed years, but not months, we need to account for those.
			if(get_option('fergcorp_countdownTimer_showYear')){
				$totalTime = $totalTime - $resultantYear*31536000;
			}
			
			//Re calculate the resultant times
			$resultantWeek = intval( $totalTime/(86400*7) ); 
			$resultantDay = intval( $totalTime/86400 );
			$resultantHour = intval( ($totalTime - $resultantDay*86400)/3600 );
			$resultantMinute = intval( ($totalTime - $resultantDay*86400 - $resultantHour*3600)/60 );
			$resultantSecond = intval( ($totalTime - $resultantDay*86400 - $resultantHour*3600 - $resultantMinute*60) );
			
			//and clear any rollover time
			$rollover = 0;
		}

		//Week (weeks are counted differently becuase we can just take 7 days and call it a week...so we do that)
		if(get_option('fergcorp_countdownTimer_showWeek')){
			if($sigNumHit || !get_option('fergcorp_countdownTimer_stripZero') || ( ($resultantDay + intval($rollover/86400) )/7)){
				$resultantWeek = $resultantWeek + intval($rollover/86400)/7;
				$s .= '<span class="fergcorp_countdownTimer_week fergcorp_countdownTimer_timeUnit">' . sprintf(_n("%d week,", "%d weeks,", (intval( ($resultantDay + intval($rollover/86400) )/7)), "fergcorp_countdownTimer"), (intval( ($resultantDay + intval($rollover/86400) )/7)))."</span> ";		
				$rollover = $rollover - intval($rollover/86400)*86400;
				$resultantDay = $resultantDay - intval( ($resultantDay + intval($rollover/86400) )/7 )*7;
				$sigNumHit = true;
			}
		}

		//Day
		if(get_option('fergcorp_countdownTimer_showDay')){
			if($sigNumHit || !get_option('fergcorp_countdownTimer_stripZero') || ($resultantDay + intval($rollover/86400)) ){
				$resultantDay = $resultantDay + intval($rollover/86400);
				$s .= '<span class="fergcorp_countdownTimer_day fergcorp_countdownTimer_timeUnit">' . sprintf(_n("%d day,", "%d days,",  $resultantDay, "fergcorp_countdownTimer"), $resultantDay)."</span> ";
				$rollover = $rollover - intval($rollover/86400)*86400;
				$sigNumHit = true;
			}
		}
		else{
			$rollover = $rollover + $resultantDay*86400;
		}

		//Hour
		if(get_option('fergcorp_countdownTimer_showHour')){
			if($sigNumHit || !get_option('fergcorp_countdownTimer_stripZero') || ($resultantHour + intval($rollover/3600)) ){
				$resultantHour = $resultantHour + intval($rollover/3600);
				$s .= '<span class="fergcorp_countdownTimer_hour fergcorp_countdownTimer_timeUnit">' . sprintf(_n("%d hour,", "%d hours,", $resultantHour, "fergcorp_countdownTimer"), $resultantHour)."</span> ";
				$rollover = $rollover - intval($rollover/3600)*3600;
				$sigNumHit = true;
			}
		}
		else{
			$rollover = $rollover + $resultantHour*3600;
		}

		//Minute
		if(get_option('fergcorp_countdownTimer_showMinute')){
			if($sigNumHit || !get_option('fergcorp_countdownTimer_stripZero') || ($resultantMinute + intval($rollover/60)) ){
				$resultantMinute = $resultantMinute + intval($rollover/60);
				$s .= '<span class="fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit">' . sprintf(_n("%d minute,", "%d minutes,", $resultantMinute, "fergcorp_countdownTimer"), $resultantMinute)."</span> ";
				$rollover = $rollover - intval($rollover/60)*60;
				$sigNumHit = true;
			}
		}
		else{
			$rollover = $rollover + $resultantMinute*60;
		}

		//Second
		if(get_option('fergcorp_countdownTimer_showSecond')){
			$resultantSecond = $resultantSecond + $rollover;
			$s .= '<span class="fergcorp_countdownTimer_second fergcorp_countdownTimer_timeUnit">' . sprintf(_n("%d second,", "%d seconds,", $resultantSecond, "fergcorp_countdownTimer"), $resultantSecond)."</span> ";
		}
		
		//Catch blank statements
		if($s==""){
			if(get_option('fergcorp_countdownTimer_showSecond')){
				$s = sprintf(__("%d seconds, ", "fergcorp_countdownTimer"), "0");
			}
			elseif(get_option('fergcorp_countdownTimer_showMinute')){
				$s = sprintf(__("%d minutes, ", "fergcorp_countdownTimer"), "0");
			}
			elseif(get_option('fergcorp_countdownTimer_showHour')){
				$s = sprintf(__("%d hours, ", "fergcorp_countdownTimer"), "0");
			}	
			elseif(get_option('fergcorp_countdownTimer_showDay')){
				$s = sprintf(__("%d days, ", "fergcorp_countdownTimer"), "0");
			}	
			elseif(get_option('fergcorp_countdownTimer_showWeek')){
				$s = sprintf(__("%d weeks, ", "fergcorp_countdownTimer"), "0");
			}
			elseif(get_option('fergcorp_countdownTimer_showMonth')){
				$s = sprintf(__("%d months, ", "fergcorp_countdownTimer"), "0");
			}
			else{
				$s = sprintf(__("%d years, ", "fergcorp_countdownTimer"), "0");
			}
		}
		
		return preg_replace("/(, ?<\/span> *)$/is", "</span>", $s);
		

	}

	/**
	 * Returns the content of the post with dates inserted (if any)
	 *
	 * @param $theContent string The content of the post
	 * @since 2.1
	 * @access public
	 * @author Andrew Ferguson
	 * @return string The content of the post with the appropriate dates inserted (if any)
	*/
	function fergcorp_countdownTimer_loop($theContent){
																						//Filter function for including the countdown with The Loop
		if(preg_match("<!--fergcorp_countdownTimer(\([0-9]+\))-->", $theContent)){																//If the string is found within the loop, replace it
			$theContent = preg_replace("/<!--fergcorp_countdownTimer(\(([0-9]+)\))?-->/e", "fergcorp_countdownTimer($2, 'return')", $theContent);	//The actual replacement of the string with the timer
		}
		elseif(preg_match("<!--fergcorp_countdownTimer-->", $theContent)){																		//If the string is found within the loop, replace it
			$theContent = preg_replace("/<!--fergcorp_countdownTimer-->/e", "fergcorp_countdownTimer('-1', 'return')", $theContent);				//The actual replacement of the string with the timer
		}



		if(preg_match("<!--fergcorp_countdownTimer_single\((.*?)\)-->", $theContent)){
				$theContent = preg_replace("/<!--fergcorp_countdownTimer_single\(('|\")(.*?)('|\")\)-->/e", "fergcorp_countdownTimer_format('', strtotime('$2'), ".( date('Z') - (get_option('gmt_offset') * 3600) ).", true, '0', '', '".get_option('fergcorp_countdownTimer_timeOffset')."', true)", $theContent);
		}

		return $theContent;																													//Return theContent
	}
	add_filter('the_content', 'fergcorp_countdownTimer_loop', 1);
	add_filter('the_excerpt', 'fergcorp_countdownTimer_loop', 1);
	
	/**
	 * Processes shortcodes
	 *
	 * @param $atts array Attributes of the shortcode
	 * @since 2.3
	 * @access public
	 * @author Andrew Ferguson
	 * @return string countdown timer(s)
	*/	
	// [fergcorp_cdt max=##]
	function fergcorp_cdt_function($atts) {
		extract(shortcode_atts(array(
			'max' => '-1',
		), $atts));
	
		return fergcorp_countdownTimer($max, 'return');
	}
	add_shortcode('fergcorp_cdt', 'fergcorp_cdt_function');

	
	/**
	 * Processes shortcodes
	 *
	 * @param $atts array Attributes of the shortcode
	 * @since 2.3
	 * @access public
	 * @author Andrew Ferguson
	 * @return string countdown timer
	*/	
	// [fergcorp_cdt max=##]
	function fergcorp_cdt_single_function($atts) {
		extract(shortcode_atts(array(
			'date' => '-1',
		), $atts));
	
		return fergcorp_countdownTimer_format('', strtotime($date), ( date('Z') - (get_option('gmt_offset') * 3600) ), true, '0', '', get_option('fergcorp_countdownTimer_timeOffset'), true);
	}
	add_shortcode('fergcorp_cdt_single', 'fergcorp_cdt_single_function');

	/**
	 * Creates a PHP-based one-off time for use outside the loop
	 *
	 * @param $date string Any string parsable by PHP's strtotime function
	 * @since 2.2
	 * @access public
	 * @author Andrew Ferguson
	*/
	function fergcorp_countdownTimer_single($date){
		return fergcorp_countdownTimer_format('', strtotime($date), ( date('Z') - (get_option('gmt_offset') * 3600) ), true, '0', '', get_option('fergcorp_countdownTimer_timeOffset'), true);
	}

	/**
	 * Sets the defaults for the timer
	 *
	 * @since 2.1
	 * @access public
	 * @author Andrew Ferguson
	*/
	function fergcorp_countdownTimer_install(){
		$plugin_data = get_plugin_data(__FILE__);
		$theOptions = get_option("afdn_countdownOptions");
		$theTracker = get_option("afdn_countdowntracker");
		$theWidget = get_option("widget_fergcorp_countdown");
		
		if(!empty($theTracker)){ //Convert the old format of oneTimeEvent storage to the new format in 2.4
			update_option('fergcorp_countdownTimer_oneTimeEvent', $theTracker['oneTime']);
			delete_option('afdn_countdowntracker');
		}

		if(empty($theWidget)){	//Create default details for the widget if needed
			update_option("widget_fergcorp_countdown", array("title"=>"Countdown Timer", "count"=>"-1"));
		}

		/**
		 * Checks to see if an option exists in either the old or new database location and then sets the value to a default if it doesn't exist
		 *
		 * @param $prefix string Prefix for the option i.e. fergcorp_countdownTimer_
		 * @param $option string Actual option
		 * @param $default string What the default value should be if it doesn't exist
		 * @since 2.4
		 * @access public
		 * @author Andrew Ferguson
		 * @return string The content of the post with the appropriate dates inserted (if any)
		*/
		function install_option($prefix, $option, $theOptions, $default){
			if(get_option($prefix.$option) != NULL){
				return false;
			}
			elseif(array_key_exists($option, $theOptions)){
				update_option($prefix.$option, $theOptions[$option]);
				return true;
			}
			else{
				update_option($prefix.$option, $default);
				return true;
			}
		}

		install_option('fergcorp_countdownTimer_', 'deleteOneTimeEvents', $theOptions, '0');
		install_option('fergcorp_countdownTimer_', 'timeOffset', $theOptions, 'F jS, Y, g:i a');
		install_option('fergcorp_countdownTimer_', 'showYear', $theOptions, '1');
		install_option('fergcorp_countdownTimer_', 'showMonth', $theOptions, '1');
		install_option('fergcorp_countdownTimer_', 'showWeek', $theOptions, '0');
		install_option('fergcorp_countdownTimer_', 'showDay', $theOptions, '1');
		install_option('fergcorp_countdownTimer_', 'showHour', $theOptions, '1');
		install_option('fergcorp_countdownTimer_', 'showMinute', $theOptions, '1');
		install_option('fergcorp_countdownTimer_', 'showSecond', $theOptions, '0');
		install_option('fergcorp_countdownTimer_', 'stripZero', $theOptions, '1');
		install_option('fergcorp_countdownTimer_', 'enableJS', $theOptions, '1');
		install_option('fergcorp_countdownTimer_', 'timeSinceTime', $theOptions, '0');
		install_option('fergcorp_countdownTimer_', 'titleSuffix', $theOptions, ':<br />');
		//install_option('fergcorp_countdownTimer_', 'serialDataFilename', $theOptions, 'fergcorp_countdownTimer_serialData_'.wp_generate_password(8,false).'.txt'); //For 2.5 release
		install_option('fergcorp_countdownTimer_', 'enableShortcodeExcerpt', $theOptions, '0');

		delete_option('afdn_countdownOptions');
		
		update_option("fergcorp_countdownTimer_version", $plugin_data["Version"]);
	}

	if(!function_exists('widget_fergcorp_countdown_init')){

		/**
		 * Initialize the widget
		 *
		 * @since 2.1
		 * @access public
		 * @author Andrew Ferguson
		*/
		function widget_fergcorp_countdown_init() {

			// Check for the required plugin functions. This will prevent fatal
			// errors occurring when you deactivate the dynamic-sidebar plugin.
			if ( !function_exists('wp_register_sidebar_widget') || !function_exists('wp_register_widget_control') )
				return;

			/**
			 * Saves options and prints the widget's config form.
			 *
			 * @since 2.1
			 * @access private
			 * @author Andrew Ferguson
			*/
			function widget_fergcorp_countdown_control() {
				$options = $newoptions = get_option('widget_fergcorp_countdown');
				if ( $_POST['countdown-submit'] ) {
					$newoptions['title'] = strip_tags(stripslashes($_POST['countdown-title']));
					$newoptions['count'] = (int) $_POST['countdown-count'];
				}
				if ( $options != $newoptions ) {
					$options = $newoptions;
					update_option('widget_fergcorp_countdown', $options);
				}
			?>
						<div style="text-align:left">
						<label for="countdown-title" style="line-height:35px;display:block;"><?php _e('Widget title:', 'fergcorp_countdownTimer'); ?> <input type="text" id="countdown-title" name="countdown-title" value="<?php echo wp_specialchars($options['title'], true); ?>" /></label>
						<label for="countdown-count" style="line-height:35px;display:block;"><?php _e('Maximum # of events to show:', 'fergcorp_countdownTimer'); ?> <input type="text" id="countdown-count" name="countdown-count" value="<?php echo $options['count']; ?>" size="5"/></label>
						<input type="hidden" name="countdown-submit" id="countdown-submit" value="1" />
						<small><strong><?php _e('Notes:', 'widget_fergcorp_countdown'); ?></strong> <?php _e("Set 'Maximum # of events' to '-1' if you want no limit.", 'fergcorp_countdownTimer'); ?></small>
						</div>
			<?php
			}

			/**
			 * Outputs the widget version of the countdown timer
			 *
			 * @since 2.1
			 * @access private
			 * @author Andrew Ferguson
			*/
			function widget_fergcorp_countdown($args) {

				$options = get_option('widget_fergcorp_countdown');

				// $args is an array of strings that help widgets to conform to the active theme: before_widget, before_title, after_widget, and after_title are the array keys. Default tags: li and h2.
				extract($args);

				$title = $options['title'];

				// These lines generate our output. Widgets can be very complex but as you can see here, they can also be very, very simple.
				echo $before_widget . $before_title . "<span class = 'fergcorp_countdownTimer_widgetTitle' >". $title . "</span>" . $after_title;

				?>
					<ul>
						<?php fergcorp_countdownTimer($options['count'], "echo"); ?>
					</ul>
				<?php
				echo $after_widget;
			}

			// This registers our widget so it appears with the other available widgets and can be dragged and dropped into any active sidebars.
			$widget_ops = array('description' => __('Adds the Countdown Timer', 'fergcorp_countdownTimer'));
			wp_register_sidebar_widget('fergcorp_countdownTimer', 'Countdown Timer', 'widget_fergcorp_countdown', $widget_ops);
			wp_register_widget_control('fergcorp_countdownTimer', 'Countdown Timer', 'widget_fergcorp_countdown_control');
		}

	// Run our code later in case this loads prior to any required plugins.
	add_action('widgets_init', 'widget_fergcorp_countdown_init');
}


	/**
	 * Echos the JavaScript for the timer
	 *
	 * @since 2.1
	 * @access public
	 * @author Andrew Ferguson
	*/
	function fergcorp_countdownTimer_js(){
		global $fergcorp_countdownTimer_jsUID;

		echo "<script type=\"text/javascript\">\n";
		echo "<!--\n";

		//Pass on what units of time should be used
		echo "var getOptions = new Array();\n";
		echo "getOptions['showYear'] = ".get_option('fergcorp_countdownTimer_showYear').";\n";
		echo "getOptions['showMonth'] = ".get_option('fergcorp_countdownTimer_showMonth').";\n";
		echo "getOptions['showWeek'] = ".get_option('fergcorp_countdownTimer_showWeek').";\n";
		echo "getOptions['showDay'] = ".get_option('fergcorp_countdownTimer_showDay').";\n";
		echo "getOptions['showHour'] = ".get_option('fergcorp_countdownTimer_showHour').";\n";
		echo "getOptions['showMinute'] = ".get_option('fergcorp_countdownTimer_showMinute').";\n";
		echo "getOptions['showSecond'] = ".get_option('fergcorp_countdownTimer_showSecond').";\n";
		echo "getOptions['stripZero'] = ".get_option('fergcorp_countdownTimer_stripZero').";\n";

		//Pass on language variables
		echo "var fergcorp_countdownTimer_js_language = new Array();\n";
		echo "fergcorp_countdownTimer_js_language['year'] = '".addslashes(__('%d year, ', 'fergcorp_countdownTimer'))."';\n";
		echo "fergcorp_countdownTimer_js_language['years'] = '".addslashes(__('%d years, ', 'fergcorp_countdownTimer'))."';\n";
		echo "fergcorp_countdownTimer_js_language['month'] = '".addslashes(__('%d month, ', 'fergcorp_countdownTimer'))."';\n";
		echo "fergcorp_countdownTimer_js_language['months'] = '".addslashes(__('%d months, ', 'fergcorp_countdownTimer'))."';\n";
		echo "fergcorp_countdownTimer_js_language['week'] = '".addslashes(__('%d week, ', 'fergcorp_countdownTimer'))."';\n";
		echo "fergcorp_countdownTimer_js_language['weeks'] = '".addslashes(__('%d weeks, ', 'fergcorp_countdownTimer'))."';\n";
		echo "fergcorp_countdownTimer_js_language['day'] = '".addslashes(__('%d day, ', 'fergcorp_countdownTimer'))."';\n";
		echo "fergcorp_countdownTimer_js_language['days'] = '".addslashes(__('%d days, ', 'fergcorp_countdownTimer'))."';\n";
		echo "fergcorp_countdownTimer_js_language['hour'] = '".addslashes(__('%d hour, ', 'fergcorp_countdownTimer'))."';\n";
		echo "fergcorp_countdownTimer_js_language['hours'] = '".addslashes(__('%d hours, ', 'fergcorp_countdownTimer'))."';\n";
		echo "fergcorp_countdownTimer_js_language['minute'] = '".addslashes(__('%d minute, ', 'fergcorp_countdownTimer'))."';\n";
		echo "fergcorp_countdownTimer_js_language['minutes'] = '".addslashes(__('%d minutes, ', 'fergcorp_countdownTimer'))."';\n";
		echo "fergcorp_countdownTimer_js_language['second'] = '".addslashes(__('%d second, ', 'fergcorp_countdownTimer'))."';\n";
		echo "fergcorp_countdownTimer_js_language['seconds'] = '".addslashes(__('%d seconds, ', 'fergcorp_countdownTimer'))."';\n";
		echo "fergcorp_countdownTimer_js_language['ago'] = '".addslashes(__('%s ago', 'fergcorp_countdownTimer'))."';\n";
		echo "fergcorp_countdownTimer_js_language['in'] = '".addslashes(__('in %s', 'fergcorp_countdownTimer'))."';\n";

		//Pass on details about each timer
		echo "var fergcorp_countdownTimer_js_events = new Array();\n";
		for($i=0; $i < count($fergcorp_countdownTimer_jsUID); $i++){
				echo "fergcorp_countdownTimer_js_events[$i] = new Array()\n";
				echo "fergcorp_countdownTimer_js_events[$i]['id'] 		= \"".$fergcorp_countdownTimer_jsUID[$i]['id']."\";\n";
				echo "fergcorp_countdownTimer_js_events[$i]['targetDate'] 	= \"".$fergcorp_countdownTimer_jsUID[$i]['targetDate']."\";\n";

		}
		echo "fergcorp_countdownTimer_js();\n";
		echo "//-->\n";
		echo "</script>\n";
	}

	/**
	 * Adds the management page in the admin menu
	 *
	 * @since 2.1
	 * @access private
	 * @author Andrew Ferguson
	 */
	function fergcorp_countdownTimer_optionsPage(){		//Action function for adding the configuration panel to the Management Page
				$fergcorp_countdownTimer_add_management_page = add_options_page('Countdown Timer', 'Countdown Timer', 'manage_options', basename(__FILE__), 'fergcorp_countdownTimer_myOptionsSubpanel');
				add_action( "admin_print_scripts-$fergcorp_countdownTimer_add_management_page", 'fergcorp_countdownTimer_LoadUserScripts' );
				add_action( "admin_print_scripts-$fergcorp_countdownTimer_add_management_page", 'fergcorp_countdownTimer_LoadAdminScripts' );
	}
	
	/**
	 * Initialized the options
	 *
	 * @since 2.4
	 * @access private
	 * @author Andrew Ferguson
	 */
	function fergcorp_countdownTimer_init(){	// Init plugin options to white list our options
		register_setting('fergcorp_countdownTimer_options', 'fergcorp_countdownTimer_deleteOneTimeEvents');
		register_setting('fergcorp_countdownTimer_options', 'fergcorp_countdownTimer_timeOffset');
		register_setting('fergcorp_countdownTimer_options', 'fergcorp_countdownTimer_showYear');
		register_setting('fergcorp_countdownTimer_options', 'fergcorp_countdownTimer_showMonth');
		register_setting('fergcorp_countdownTimer_options', 'fergcorp_countdownTimer_showWeek');
		register_setting('fergcorp_countdownTimer_options', 'fergcorp_countdownTimer_showDay');
		register_setting('fergcorp_countdownTimer_options', 'fergcorp_countdownTimer_showHour');
		register_setting('fergcorp_countdownTimer_options', 'fergcorp_countdownTimer_showMinute');
		register_setting('fergcorp_countdownTimer_options', 'fergcorp_countdownTimer_showSecond');
		register_setting('fergcorp_countdownTimer_options', 'fergcorp_countdownTimer_stripZero');
		register_setting('fergcorp_countdownTimer_options', 'fergcorp_countdownTimer_enableJS');
		register_setting('fergcorp_countdownTimer_options', 'fergcorp_countdownTimer_timeSinceTime');
		register_setting('fergcorp_countdownTimer_options', 'fergcorp_countdownTimer_titleSuffix');
		//register_setting('fergcorp_countdownTimer_options', 'fergcorp_countdownTimer_serialDataFilename');
		register_setting('fergcorp_countdownTimer_options', 'fergcorp_countdownTimer_enableShortcodeExcerpt');
		
		register_setting('fergcorp_countdownTimer_options', 'fergcorp_countdownTimer_oneTimeEvent', 'fergcorp_countdownTimer_OneTimeEvent_sanitize');
	}

	add_action('admin_menu', 'fergcorp_countdownTimer_optionsPage');	//Add Action for adding the options page to admin panel
	add_action('admin_init', 'fergcorp_countdownTimer_init');			//Initialized the options
	register_activation_hook( __FILE__, 'fergcorp_countdownTimer_install');
	
	if(get_option('fergcorp_countdownTimer_enableJS')) {
		add_action('wp_footer', 'fergcorp_countdownTimer_js');
	}
	
	if(get_option('fergcorp_countdownTimer_enableShortcodeExcerpt')) {
		add_filter('the_excerpt', 'do_shortcode');
	}

	add_action('wp_head', 'fergcorp_countdownTimer_LoadUserScripts', 1); //Priority needs to be set to 1 so that the scripts can be enqueued before the scripts are printed, since both actions are hooked into the wp_head action.

	function fergcorp_countdownTimer_OneTimeEvent_sanitize($input){
			if(function_exists(date_default_timezone_set)){
				date_default_timezone_set(get_option('timezone_string'));
			}
			$j=0;	//Keep track of how many actual fields are filled, versus how many were sent (there could be empty fields which need to be removed)
			for($i=0; $i < count($input); $i++){
				if($input["date$i"]==""){ //If the date field is empty, ignore the entry
				}
				else{																							//If not, add it to an array so the data can be updated
					$output[$j] = array(	"date" => strtotime($input["date$i"]),	//Date of the event converted to UNIX time
											"text" => $input["text$i"],				//Text associated with the event (i.e. event label)
											"timeSince" => $input["timeSince$i"],	//After the event has occured, should "Time Since" be displayed? Boolean value (0 0 for no or 1 for yes)
											"link" => $input["link$i"],				//Where should the text link to (this can be null)
													); 								//For every field, create an array. Then stick that array into the master array
					$j++;
				}
			}
			/*End One Time Events*/

			/*Begin sorting events by time*/
			for($x=0; $x < $j; $x++){
				for($z=0; $z < $j-1; $z++){
					if(($output[$z+1]["date"] < $output[$z]["date"]) && (array_key_exists($z+1, $output))){
						$temp = $output[$z];
						$output[$z] = $output[$z+1];
						$output[$z+1] = $temp;
					}
				}
			}
			/*End sorting events by time*/
			
			//Leaving this out of 2.4 release
			//$file = fopen(dirname(__FILE__)."/".get_option('fergcorp_countdownTimer_serialDataFilename'), 'wb');
			//fwrite($file, serialize($output));
			//fclose($file);

	return $output;
}

	/**
	 * Loads the appropriate scripts when in the admin page
	 *
	 * @since 2.2
	 * @access private
	 * @author Andrew Ferguson
	 */
	function fergcorp_countdownTimer_LoadAdminScripts() {
	    wp_enqueue_script('postbox'); //These appear to be new functions in WP 2.5
	}
	
	/**
	 * Loads the appropriate scripts
	 *
	 * @since 2.2
	 * @access private
	 * @author Andrew Ferguson
	 */
	function fergcorp_countdownTimer_LoadUserScripts() {
		if(get_option('fergcorp_countdownTimer_enableJS')) {
			wp_enqueue_script('fergcorp_countdowntimer', plugins_url(dirname(plugin_basename(__FILE__)) . "/js/". 'fergcorp_countdownTimer_java.js'), FALSE, get_option("fergcorp_countdownTimer_version"));
			wp_enqueue_script('webkit_sprintf', plugins_url(dirname(plugin_basename(__FILE__)) . "/js/" . 'webtoolkit.sprintf.js'), FALSE, get_option("fergcorp_countdownTimer_version"));
		}
	}
?>
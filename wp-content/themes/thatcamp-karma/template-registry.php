<?php
/**
 * Registry template
 *
 * @package thatcamp
 * @since thatcamp 1.0
 *
 * Template Name: Registry Template
 */

add_action( 'wp_enqueue_scripts', 'thatcamp_admin_scripts' );

?>

<?php get_header(); ?>

<div id="primary" class="main-content">
	<div id="content" class="clearfix feature-box thatcamp-registry" role="main">
		<h1>THATCamp Registry</h1>
		<?php if ( isset( $_GET['success'] ) ) : ?>
			<?php $url = 'http://' . urldecode( $_GET['success'] ) . '.thatcamp.org/'; ?>

			<h2>Success!</h2>

			<p>Your THATCamp has been successfully registered. Visit <a href="<?php echo esc_attr( $url ) ?>"><?php echo esc_html( $url ) ?></a> to get started.</p>
		<?php else : ?>
			<form method="post">

				<p>If you do not have non-required information (such as the dates your THATCamp will be held), you can return and enter it later. Doing so will ensure that all information on <a href="http://thatcamp.org">http://thatcamp.org</a> and related THATCamp resources is correct. Please register each separate event (i.e., both THATCamp Games 2012 and THATCamp Games 2013).</p>
				<p style="color: #f00">* Required</p>

				<div class="registry-section" id="section-thatcamp-name">
					<h3>THATCamp Name <span class="required">*</span></h3>
					<?php if ( isset( $_POST['errors']['thatcamp-name'] ) ) : ?>
						<p class="required-error"><?php echo esc_html( $_POST['errors']['thatcamp-name'] ) ?></p>
					<?php endif ?>
					<p>City names such as THATCamp Austin, regional names such as THATCamp New England, or thematic names such as THATCamp Pedagogy are all fine. If you choose a name that another organizer has used, you must obtain the agreement of that organizer.</p>
					<?php $thatcamp_name = isset( $_POST['thatcamp-name'] ) ? stripslashes( $_POST['thatcamp-name'] ) : '' ?>
					<input name="thatcamp-name" type="text" id="thatcamp-name" value="<?php echo esc_attr( $thatcamp_name ) ?>" />
				</div>

				<div class="registry-section" id="section-site-url">
					<h3>Desired URL <span class="required">*</span></h3>
					<?php if ( isset( $_POST['errors']['site-url'] ) ) : ?>
						<p class="required-error"><?php echo esc_html( $_POST['errors']['site-url'] ) ?></p>
					<?php endif ?>
					<p>THATCamp site URLs are generally constructed thusly: [nameyear].thatcamp.org, e.g., a THATCamp NCPH held in 2012 becomes http://ncph2012.thatcamp.org. Please try to follow this convention when choosing a URL for your THATCamp site.</p>
					<?php $site_url = isset( $_POST['site-url'] ) ? $_POST['site-url'] : '' ?>
					http://<input name="site-url" style="display:inline; width: 200px" type="text" id="site-url" value="<?php echo esc_attr( $site_url ) ?>" />.thatcamp.org
				</div>


				<div class="registry-section" id="section-chief-organizers-name">
					<h3>Chief organizer's name <span class="required">*</span></h3>
					<?php if ( isset( $_POST['errors']['chief-organizers-name'] ) ) : ?>
						<p class="required-error"><?php echo esc_html( $_POST['errors']['chief-organizers-name'] ) ?></p>
					<?php endif ?>
					<p>Usually there is more than one person helping to organize a THATCamp (sharing the work among 2-5 people is ideal), but do please let us know the name of the single best person for us to correspond with. Additional organizers' names and email addresses may be listed in the additional notes field on this for.</p>
					<?php $chief_organizers_name = isset( $_POST['chief-organizers-name'] ) ? stripslashes( $_POST['chief-organizers-name'] ) : '' ?>
					<input name="chief-organizers-name" type="text" id="chief-organizers-name" value="<?php echo esc_attr( $chief_organizers_name ) ?>" />
				</div>

				<div class="registry-section" id="section-email-address">
					<h3>E-mail address <span class="required">*</span></h3>
					<?php if ( isset( $_POST['errors']['email-address'] ) ) : ?>
						<p class="required-error"><?php echo esc_html( $_POST['errors']['email-address'] ) ?></p>
					<?php endif ?>
					<p>E-mail address of the chief organizer or dedicated e-mail address such as thatcamppnw@gmail.com. Note that the e-mail address you enter here will be published to the web. We encourage you to set up a dedicated e-mail address for your THATCamp (and, of course, to make sure that one person checks it regularly) so that the address can safely be made public and so that the account can be passed on to future organizers.</p>
					<?php $email_address = isset( $_POST['email-address'] ) ? stripslashes( $_POST['email-address'] ) : '' ?>
					<input name="email-address" type="text" id="email-address" value="<?php echo esc_attr( $email_address ) ?>" />
				</div>

				<div class="registry-section" id="section-mailing-address">
					<h3>Mailing address <span class="required">*</span></h3>
					<?php if ( isset( $_POST['errors']['mailing-address'] ) ) : ?>
						<p class="required-error"><?php echo esc_html( $_POST['errors']['mailing-address'] ) ?></p>
					<?php endif ?>
					<p>Please enter a mailing address for the chief organizer so that we can send you THATCamp stickers, brochures, posters, and other physical media if necessary. We won't ever send you junk mail.</p>
					<?php $mailing_address = isset( $_POST['mailing-address'] ) ? stripslashes( $_POST['mailing-address'] ) : '' ?>
					<textarea name="mailing-address" id="mailing-address"><?php echo esc_attr( $mailing_address ) ?></textarea>
				</div>

				<div class="registry-section" id="section-location">

					<h3>Location</h3>
					<?php
					$current_location = array();
					foreach ( array( 'Country', 'State', 'Province', 'City' ) as $lkey ) {
						if ( isset( $_POST[ $lkey ] ) ) {
							$current_location[ strtolower( $lkey ) ] = $_POST[ $lkey ];
						}
					}
					?>
					<?php thatcamp_country_picker( $current_location ) ?>

				</div>

				<div class="registry-section" id="section-twitter-account">
					<h3>Twitter account</h3>
					<p>A dedicated Twitter account for your THATCamp such as THATCamp Southern California's @thatcampsocal is not essential, but most organizers have found it both useful and fun to have one. Ify ou have a dedicated Twitter account, please enter it in the format http://twitter.com/thatcampsocal.</p>
					<?php $twitter_account = isset( $_POST['twitter-account'] ) ? stripslashes( $_POST['twitter-account'] ) : '' ?>
					<input name="twitter-account" type="text" id="twitter-account" value="<?php echo esc_attr( $twitter_account ) ?>" />
				</div>

				<div class="registry-section" id="section-start-date">
					<h3>Start date</h3>
					<p>If known, the proposed start date for your THATCamp.</p>
					<?php $thatcamp_start_date = isset( $_POST['thatcamp-start-date'] ) ? $_POST['thatcamp-start-date'] : '' ?>
					<input id="thatcamp_start_date" name="thatcamp-start-date" type="text" value="<?php echo esc_attr( $thatcamp_start_date ) ?>" />
				</div>

				<div class="registry-section" id="section-end-date">
					<h3>End date</h3>
					<p>If known, the proposed end date for your THATCamp.</p>
					<?php $thatcamp_end_date = isset( $_POST['thatcamp-end-date'] ) ? $_POST['thatcamp-end-date'] : '' ?>
					<input id="thatcamp_end_date" name="thatcamp-end-date" type="text" value="<?php echo esc_attr( $thatcamp_end_date ) ?>" />
				</div>

				<div class="registry-section" id="section-additional-notes">
					<h3>Additional notes or questions</h3>
					<?php $additional_notes = isset( $_POST['additional-notes'] ) ? stripslashes( $_POST['additional-notes'] ) : '' ?>
					<textarea name="additional-notes" id="additional-notes"><?php echo esc_attr( $additional_notes ) ?></textarea>
				</div>

				<div class="registry-section" id="section-i-agree">
					<h3>I agree that our THATCamp will be&hellip; <span class="required">*</span></h3>

					<?php if ( isset( $_POST['errors']['i-agree'] ) ) : ?>
						<p class="required-error"><?php echo esc_html( $_POST['errors']['i-agree'] ) ?></p>
					<?php endif ?>

					<ul>
						<li><input type="checkbox" name="i-agree[free]" id="i-agree-free" checked="checked" value="1" /> FREE or CHEAP to attend (registration fees of up to $30 USD are fine)</li>
						<li><input type="checkbox" name="i-agree[open]" id="i-agree-open" checked="checked" value="1" /> OPEN to anyone who wishes to apply or register</li>
						<li><input type="checkbox" name="i-agree[informal]" id="i-agree-informal" checked="checked" value="1" /> INFORMAL and participatory (no presentations, papers, or demos longer than 5 minutes with the possible exception of skills training workshops, though these too should be hands-on if possible)</li>
						<li><input type="checkbox" name="i-agree[public]" id="i-agree-public" checked="checked" value="1" /> PUBLIC on the open web (sessions can be blogged, twittered, photographed, recorded, and posted)</li>
						<li><input type="checkbox" name="i-agree[self-organizing]" id="i-agree-self-organizing" checked="checked" value="1" /> SELF-ORGANIZING (no program committee: all participants are given a chance to help set the agenda, either before or during the unconference)</li>
					</ul>
				</div>

				<?php /* honeypot */ ?>
				<div id="thatcamp-zip-code">
					<label for="zip-code">Don't fill this field in - it's a trick for robots.
						<input type="text" name="zip-code" />
					</label>
				</div>

				<input name="thatcamp-register-submit" type="submit" value="Submit">
			</form>
		<?php endif ?>
	</div>
</div>

<?php get_sidebar( 'stream' ); ?>
<?php get_footer() ?>

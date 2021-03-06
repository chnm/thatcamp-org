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
		<h1 class="post-title red-text">THATCamp Registry</h1>
		 <?php if ( is_user_logged_in() ) : ?>
		
		<?php if ( isset( $_GET['success'] ) ) : ?>
			<?php $url = 'http://' . urldecode( $_GET['success'] ) . '.thatcamp.org/'; ?>

			<h2>Success!</h2>

			<p>Your THATCamp has been successfully registered, and a THATCamp website has been created at <a href="<?php echo esc_attr( $url ) ?>"><?php echo esc_html( $url ) ?></a>. Log in to manage your THATCamp website at <a href="<?php echo esc_attr( $url ) ?>wp-login.php"><?php echo esc_html( $url ) ?>wp-login.php</a>.</p>
			<p>Be sure to read the help documents on <a href="help/organize">how to organize a THATCamp</a>.</p>
		<?php else : ?>
			<form method="post">
				<p style="color: #f00">* Required</p>

				<div class="registry-section" id="section-thatcamp-name">
					<h3>THATCamp name <span class="required">*</span></h3>
					<?php if ( isset( $_POST['errors']['thatcamp-name'] ) ) : ?>
						<p class="required-error"><?php echo esc_html( $_POST['errors']['thatcamp-name'] ) ?></p>
					<?php endif ?>
					<p>Examples: THATCamp New England 2011, THATCamp AHA 2012, THATCamp Games 2013. THATCamp names generally include a city, state, region, theme, or organization followed by the year in which the THATCamp will be held. If you want to use a THATCamp name that another organizer has already used, you must obtain the agreement of that organizer.</p>
					<?php $thatcamp_name = isset( $_POST['thatcamp-name'] ) ? stripslashes( $_POST['thatcamp-name'] ) : '' ?>
					<input name="thatcamp-name" type="text" id="thatcamp-name" value="<?php echo esc_attr( $thatcamp_name ) ?>" />
				</div>

				<div class="registry-section" id="section-site-url">
					<h3>THATCamp site URL <span class="required">*</span></h3>
					<?php if ( isset( $_POST['errors']['site-url'] ) ) : ?>
						<p class="required-error"><?php echo esc_html( $_POST['errors']['site-url'] ) ?></p>
					<?php endif ?>
					<p>Example: http://newengland2011.thatcamp.org. THATCamp site URLs are generally constructed thusly: http://[nameyear].thatcamp.org. Please try to follow this convention when choosing a URL. If you plan to host your own THATCamp site, first complete and submit this form, then see the instructions for redirecting a thatcamp.org site to a stand-alone THATCamp website on our <a href="/help/organize/website">website Help page</a>.</p>
					<?php $site_url = isset( $_POST['site-url'] ) ? $_POST['site-url'] : '' ?>
					http://<input name="site-url" type="text" id="site-url" value="<?php echo esc_attr( $site_url ) ?>" />.thatcamp.org
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

				<div class="registry-section" id="section-start-date">
					<h3>Start date</h3>
					<p>If known, the start date for your THATCamp. One-day THATCamps need only a start date.</p>
					<?php $thatcamp_start_date = isset( $_POST['thatcamp-start-date'] ) ? $_POST['thatcamp-start-date'] : '' ?>
					<input id="thatcamp_start_date" name="thatcamp-start-date" type="text" value="<?php echo esc_attr( $thatcamp_start_date ) ?>" />
				</div>

				<div class="registry-section" id="section-end-date">
					<h3>End date</h3>
					<p>If known, the end date for your multi-day THATCamp. One-day THATCamps do not need an end date.</p>
					<?php $thatcamp_end_date = isset( $_POST['thatcamp-end-date'] ) ? $_POST['thatcamp-end-date'] : '' ?>
					<input id="thatcamp_end_date" name="thatcamp-end-date" type="text" value="<?php echo esc_attr( $thatcamp_end_date ) ?>" />
				</div>

				<div class="registry-section" id="section-i-agree">
					<h3>I agree that our THATCamp will be&hellip; <span class="required">*</span></h3>

					<?php if ( isset( $_POST['errors']['i-agree'] ) ) : ?>
						<p class="required-error"><?php echo esc_html( $_POST['errors']['i-agree'] ) ?></p>
					<?php endif ?>

					<ul>
						<li><input type="checkbox" name="i-agree[free]" id="i-agree-free" checked="checked" value="1" /> FREE or CHEAP to attend (registration fees of up to $30 USD are fine)</li>
						<li><input type="checkbox" name="i-agree[open]" id="i-agree-open" checked="checked" value="1" /> OPEN to anyone who wishes to apply or register (no restricting registration to members of a particular institution, association, organization, discipline, or rank)</li>						
						<li><input type="checkbox" name="i-agree[public]" id="i-agree-public" checked="checked" value="1" /> PUBLIC on the open web (sessions can be blogged, twittered, photographed, recorded, and posted)</li>
						<li><input type="checkbox" name="i-agree[self-organizing]" id="i-agree-self-organizing" checked="checked" value="1" /> SELF-ORGANIZING (no program committee: all participants together will set the agenda, either before or during the unconference)</li>
						<li><input type="checkbox" name="i-agree[informal]" id="i-agree-informal" checked="checked" value="1" /> PARTICIPATORY and spontaneous (most sessions are group discussions, productive co-working sessions, or joyous collaborations of any kind; there should be few if any prepared, monologuing presentations, papers, or demonstrations with the exception of skills training workshops, and even workshops should generally allow for hands-on exercises by the group)</li>
						<li><input type="checkbox" name="i-agree[safe]" id="i-agree-informal" checked="checked" value="1" />SAFE for and welcoming to all participants; THATCamps are required to adopt our <a href="/anti-harassment-policy">anti-harassment policy</a> and to abide by its procedures.</li> 
					</ul>
				</div>

				<?php /* honeypot */ ?>
				<div id="thatcamp-zip-code">
					<label for="zip-code">Don't fill this field in - it's a trick for robots.
						<input type="text" name="zip-code" />
					</label>
				</div>
				<p class="warning">After you click "Submit," a new THATCamp site will be created for you. This process takes awhile; please do not close the page or back out of it while it is happening.</p>
				<input name="thatcamp-register-submit" type="submit" value="Submit">
			</form>
		<?php endif ?>
		<?php else : ?>
		<?php echo ('<h3 style="text-align:center;margin:2em;">You must <a href="/wp-login.php" title="Log in" name="Log in">log in</a> to register a new THATCamp.</h3>
		<p style="margin-bottom: 1.5em;">If you have ever been to a THATCamp, you probably already have a THATCamp account; if you have forgotten your login information, you can <a href="/wp-login.php/?action=lostpassword" title="Reset your password" name="Reset your password">reset your password</a> with your email address. If you do not have a THATCamp account, you can <a href="/signup" title="Sign up for an account" name="Sign up for an account">sign up for an account</a>.</p>'); ?>	
		<?php echo ('When you register a THATCamp, you must agree that your THATCamp will be:
					<ul>
						<li>FREE or CHEAP to attend (registration fees of up to $30 USD are fine)</li>
						<li>OPEN to anyone who wishes to apply or register (no restricting registration to members of a particular institution, association, organization, discipline, or rank)</li>						
						<li>PUBLIC on the open web (sessions can be blogged, twittered, photographed, recorded, and posted)</li>
						<li>SELF-ORGANIZING (no program committee: all participants together will set the agenda, either before or during the unconference)</li>
						<li>PARTICIPATORY and spontaneous (most sessions are group discussions, productive co-working sessions, or joyous collaborations of any kind; there should be few if any prepared, monologuing presentations, papers, or demonstrations with the exception of skills training workshops, and even workshops should generally allow for hands-on exercises by the group)</li>
						<li>SAFE and welcoming to all participants; THATCamps are required to adopt our <a href="/anti-harassment-policy">anti-harassment policy</a> and to abide by its procedures.</li> 
					</ul>
		'); ?>
		<?php endif ?>
	</div>
</div>

<?php get_sidebar( 'documents' ); ?>
<?php get_footer() ?>

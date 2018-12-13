<?php
/**
 * @package WordPress
 * @subpackage Thatcamp
 */

/*
Template Name: Register
*/
?>



<?php get_header(); ?>

	<div id="content" class="narrowcolumn" role="main">

	<?php if (have_posts()) : ?>

		<?php while (have_posts()) : the_post(); ?>

<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
				<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
				
				<div class="entry">
					<?php the_content('Read the rest of this entry &raquo;'); ?>
				</div>

				
			

		<?php endwhile; ?>



	<?php else : ?>
    

		<h2 class="center">Not Found</h2>
		<p class="center">Sorry, but you are looking for something that isn't here.</p>
		<?php get_search_form(); ?>

	<?php endif; ?>
    </div>

<div id="login"><h1><a href="http://wordpress.org/" title="Powered by WordPress">THATCamp London 2010</a></h1>
<p class="message register">Apply for THATCamp London</p>

<form name="registerform" id="registerform" action="http://thatcamplondon.org/wp-login.php?action=register" method="post">
	<p>
		<label>Username<br />

		<input type="text" name="user_login" id="user_login" class="input" value="" size="20" tabindex="10" /></label>
	</p>
	<p>
		<label>E-mail<br />
		<input type="text" name="user_email" id="user_email" class="input" value="" size="25" tabindex="20" /></label>
	</p>
   		<p><label>First Name: <br />
		<input autocomplete="off" name="firstname" id="firstname" size="25" value="" type="text" tabindex="30" /></label><br />

        </p>
               		<p><label>Last Name: <br />
		<input autocomplete="off" name="lastname" id="lastname" size="25" value="" type="text" tabindex="31" /></label><br />
        </p>
               		<p><label>Website: <br />
		<input autocomplete="off" name="website" id="website" size="25" value="" type="text" tabindex="32" /></label><br />
        </p>
               		<p><label>About Yourself: <br />

		<textarea autocomplete="off" name="about" id="about" cols="25" rows="5" tabindex="35"></textarea></label><br />
        <small>Share a little biographical information to fill out your profile. This may be shown publicly.</small>
        </p>
            		
       
                    <p><label>What would you like to present or discuss?: <br />
		<textarea tabindex="36" name="what_would_you_like_to_present_or_discuss" cols="25" rows="5" id="what_would_you_like_to_present_or_discuss" class="custom_textarea"></textarea></label><br /></p>
		
				
				
		        <p><label>Password: <br />

		<input autocomplete="off" name="pass1" id="pass1" size="25" value="" type="password" tabindex="40" /></label><br />
        <label>Confirm Password: <br />
        <input autocomplete="off" name="pass2" id="pass2" size="25" value="" type="password" tabindex="41" /></label>
        <br />
        <span id="pass-strength-result">Too Short</span>
		<small>Hint: Use upper and lower case characters, numbers and symbols like !"?$%^&amp;( in your password. </small></p>
            	<p id="reg_passmail">A password will be e-mailed to you.</p>

	<br class="clear" />
	<p class="submit"><input type="submit" name="wp-submit" id="wp-submit" class="button-primary" value="Register" tabindex="100" /></p>
</form>

<p id="nav">
<a href="http://thatcamplondon.org/wp-login.php">Log in</a> |
<a href="http://thatcamplondon.org/wp-login.php?action=lostpassword" title="Password Lost and Found">Lost your password?</a>
</p>

</div>

<p id="backtoblog"><a href="http://thatcamplondon.org/" title="Are you lost?">&larr; Back to THATCamp London 2010</a></p>

<script type="text/javascript">
try{document.getElementById('user_login').focus();}catch(e){}
</script>


</div>
<?php get_sidebar(); ?>

<?php get_footer(); ?>

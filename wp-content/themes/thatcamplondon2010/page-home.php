<?php
/**
 * @package WordPress
 * @subpackage Thatcamp
 */


/*
Template Name: Home
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

<div class="frontpost">
<h2>Recent session proposals</h2>
<div class="recentpost">
<div class="authoravat">
<div class="avat"><img src='http://london2010.thatcamp.org/wp-content/plugins/user-avatar/user-avatar-pic.php?src=http://london2010.thatcamp.org/wp-content/uploads/avatars/16684/5c1291bcec864-bpfull.jpg&w=75&id=16684&random=1544720829' class='avatar avatar-75 avatar-default' height='75' width='75' style='width: 75px; height: 75px;' alt='avatar' /></div>
<div class="dateavat">John Bradley<br />12.07.2010</div>
</div>
<h3><a href="http://london2010.thatcamp.org/2010/07/a-winner-for-the-developers-challenge-patrick-juola/" rel="bookmark" title="Permanent Link to A Winner for the Developers&#8217; Challenge: Patrick Juola">A Winner for the Developers&#8217; Challenge: Patrick Juola</a></h3>
After the three judges (Geoffrey Rockwell, Michael Sperberg-McQueen, Tobias Blanke) reviewed the entries to the Developers&#8217; Challenge, they chose a winner, and it was announced at the DH2010 final banquet, on Saturday evening , 10th July.
It was Patrick Juola (Department of Mathematics and Computer Science, Duquesne University) for his piece of software called &#8220;Once Upon a Time/Monkeying Around&#8221; &#8212; a game based on computer linguistic methods that could be applied <a rel="nofollow" href="http://london2010.thatcamp.org/2010/07/a-winner-for-the-developers-challenge-patrick-juola/">...</a><div class="readmore"><a href="http://london2010.thatcamp.org/2010/07/a-winner-for-the-developers-challenge-patrick-juola/" rel="bookmark" title="Permanent Link to A Winner for the Developers&#8217; Challenge: Patrick Juola"><img src="http://london2010.thatcamp.org/files/2018/12/readmore.png" /></a></div>
</div>
<hr />
<div class="recentpost">
<div class="authoravat">
<div class="avat"><img src='http://www.gravatar.com/avatar/e7c8611332edb7a71061bb49c90ad6d6?s=75&r=g&d=mm' class='avatar avatar-75 avatar-default' height='75' width='75' style='width: 75px; height: 75px;' alt='avatar' /></div>
<div class="dateavat">tla<br />05.07.2010</div>
</div>
<h3><a href="http://london2010.thatcamp.org/2010/07/digital-history/" rel="bookmark" title="Permanent Link to Digital history">Digital history</a></h3>
As a historian who can very easily be mistaken for a philologist, I have recently been pondering the question of what technology can do for the field of history.  Digital tools have proven themselves in quite a few surrounding fields &#8211; archaeology, philology, text criticism and analysis.  But can we also use computers to help us put all this disparate data together?  Can we use computers to help us keep <a rel="nofollow" href="http://london2010.thatcamp.org/2010/07/digital-history/">...</a><div class="readmore"><a href="http://london2010.thatcamp.org/2010/07/digital-history/" rel="bookmark" title="Permanent Link to Digital history"><img src="http://london2010.thatcamp.org/files/2018/12/readmore.png" /></a></div>
</div>
<hr />
<div class="recentpost">
<div class="authoravat">
<div class="avat"><img src='http://1.gravatar.com/avatar/7079f35a8d450bb4bd3a235fbe2b9e26?s=75&amp;d=http%3A%2F%2Fwww.gravatar.com%2Favatar%2Fad516503a11cd5ca435acc9bb6523536%3Fs%3D75&amp;r=G' class='avatar avatar-75 avatar-default' height='75' width='75' style='width: 75px; height: 75px;' alt='avatar' /></div>
<div class="dateavat">wybo<br />05.07.2010</div>
</div>
<h3><a href="http://london2010.thatcamp.org/2010/07/critical-mass-in-social-dh-applications/" rel="bookmark" title="Permanent Link to Critical Mass in Social DH Applications">Critical Mass in Social DH Applications</a></h3>
What have you done to attract users to your (community centered / interactive / web 2.0) digital humanities application? What do you think could be done to attract users to such an application? What have you seen others do? What worked, what didn&#8217;t?<br /><br /><br /><br /><div class="readmore"><a href="http://london2010.thatcamp.org/2010/07/critical-mass-in-social-dh-applications/" rel="bookmark" title="Permanent Link to Critical Mass in Social DH Applications"><img src="http://london2010.thatcamp.org/files/2018/12/readmore.png" /></a></div>
</div>
<hr />
<div class="recentpost">
<div class="authoravat">
<div class="avat"><img src='http://london2010.thatcamp.org/wp-content/plugins/user-avatar/user-avatar-pic.php?src=http://london2010.thatcamp.org/wp-content/uploads/avatars/6543/5c12941da0ca0-bpfull.jpg&w=75&id=6543&random=1544721437' class='avatar avatar-75 avatar-default' height='75' width='75' style='width: 75px; height: 75px;' alt='avatar' /></div>
<div class="dateavat">techczech<br />05.07.2010</div>
</div>
<h3><a href="http://london2010.thatcamp.org/2010/07/using-online-social-tools-to-bring-practitioners-and-researchers-closer-together/" rel="bookmark" title="Permanent Link to Using online social tools to bring practitioners and researchers closer together">Using online social tools to bring practitioners and researchers closer together</a></h3>
What: The idea is simple. We could probably use a platform for connecting researchers and practitioners that would break down the client/provider relationship currently embedded in the so-called &#8216;evidence-based&#8217; practice and possibly put more emphasis on an &#8216;inquiry-based&#8217; practice.
I&#8217;d be interested in talking with people about what such a platform would look like. This is is possibly related to: http://london2010.thatcamp.org/2010/06/participatory-interdisciplinary-and-digital but with some differences (see below)
Background assumptions: There are broadly three stages to <a rel="nofollow" href="http://london2010.thatcamp.org/2010/07/using-online-social-tools-to-bring-practitioners-and-researchers-closer-together/">...</a><div class="readmore"><a href="http://london2010.thatcamp.org/2010/07/using-online-social-tools-to-bring-practitioners-and-researchers-closer-together/" rel="bookmark" title="Permanent Link to Using online social tools to bring practitioners and researchers closer together"><img src="http://london2010.thatcamp.org/files/2018/12/readmore.png" /></a></div>
</div>
<hr />
<div class="recentpost">
<div class="authoravat">
<div class="avat"><img src='http://1.gravatar.com/avatar/df57831a1dcdb6b6847d1ec2f5deefcc?s=75&amp;d=http%3A%2F%2Fwww.gravatar.com%2Favatar%2Fad516503a11cd5ca435acc9bb6523536%3Fs%3D75&amp;r=G' class='avatar avatar-75 avatar-default' height='75' width='75' style='width: 75px; height: 75px;' alt='avatar' /></div>
<div class="dateavat">mad_rabbit<br />05.07.2010</div>
</div>
<h3><a href="http://london2010.thatcamp.org/2010/07/living-digitized-archives-for-individuals-and-learning/" rel="bookmark" title="Permanent Link to Living digitized archives for individuals and learning">Living digitized archives for individuals and learning</a></h3>
Being a digital media developer/programmer who has worked with museums and other organisations on various projects, I have used media such as digital/digitized photos, films and audio clips/interviews, for the process, which has been collected from (often older) people by the museum/organisation for educational/archival purposes, often for use with schools.
I am also a researcher working on a project (and planning projects) that look at the ways in which older people <a rel="nofollow" href="http://london2010.thatcamp.org/2010/07/living-digitized-archives-for-individuals-and-learning/">...</a><div class="readmore"><a href="http://london2010.thatcamp.org/2010/07/living-digitized-archives-for-individuals-and-learning/" rel="bookmark" title="Permanent Link to Living digitized archives for individuals and learning"><img src="http://london2010.thatcamp.org/files/2018/12/readmore.png" /></a></div>
</div>

</div>
</div>
<?php get_sidebar(); ?>

<?php get_footer(); ?>

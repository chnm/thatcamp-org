<?php
/**
 * RSS2 Feed Template for displaying various faceted feeds
 */
header('Content-Type: text/xml; charset=' . get_option('blog_charset'), true);
header('Status: 200 OK');
?>
<?php echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>'; ?>

<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:feedburner="http://rssnamespace.org/feedburner/ext/1.0"
>

<channel>
	<title>THATCamp | Newest THATCamps</title>
	<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
	<link><?php echo trailingslashit( bp_get_root_domain() ) . trailingslashit( bp_get_groups_root_slug() ) ?>/feed/</link>
	<description>Newest THATCamps</description>
	<generator>http://buddypress.org/?v=<?php echo BP_VERSION ?></generator>
 	<language><?php echo substr(get_bloginfo('language'), 0, 2); ?></language> 
 
	<?php global $groups_template ?>
	<?php if ( bp_has_groups( 'type=newest' ) ) : ?>
		<?php while ( bp_groups() ) : bp_the_group(); ?>
			<item>
				<guid><?php thatcamp_camp_permalink() ?></guid>
				<title><![CDATA[New THATCamp: <?php bp_group_name() ?>]]></title>
				<link><?php thatcamp_camp_permalink() ?></link>
				<description>
					<![CDATA[
					<?php thatcamp_camp_summary() ?>
					]]>
				</description>
			</item>
		<?php endwhile; ?>

	<?php endif; ?>
</channel>
</rss>

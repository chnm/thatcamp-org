<div id="secondary" class="three">
    <p><strong>Here's what others are saying about THATCamp on <a href="http://twitter.com">Twitter</a></strong></p>
    
    <?php 
    include_once(ABSPATH . WPINC . '/rss.php');
    $rss = fetch_rss('http://search.twitter.com/search.atom?q=thatcamp');
    $maxitems = 5;
    $items = array_slice($rss->items, 0, $maxitems);
    ?>
    <ul id="twitter">
    <?php if (empty($items)) echo '<li>No items</li>';
    else
    foreach ( $items as $item ) : ?>
    <?php if($item['author_name'] == 'huberthsu (huberthsu)') continue; ?>
    <li><a href="<?php echo $item['author_uri']; ?>"><img src="<?php echo $item['link_image'] ?>" alt="<?php echo $item['author_name'] ?>" /></a> 
        <p><a href="<?php echo $item['author_uri'] ?>"><?php echo $item['author_name']; ?></a> <?php echo $item['title']; ?></p></li>
    <?php endforeach; ?>
    </ul>
</div>
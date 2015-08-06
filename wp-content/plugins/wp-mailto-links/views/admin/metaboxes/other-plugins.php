<h4><img src="<?php echo WPML::url('images/icon-wp-external-links.png') ?>" width="16" height="16" /> <?php WPML::_e('WP External Links') ?> -
    <?php if (is_plugin_active('wp-external-links/wp-external-links.php')): ?>
        <a href="<?php echo get_bloginfo('url') ?>/wp-admin/admin.php?page=wp_external_links"><?php _e('Settings') ?></a>
    <?php elseif( file_exists( WP_PLUGIN_DIR . '/wp-external-links/wp-external-links.php')): ?>
        <a href="<?php echo get_bloginfo('url') ?>/wp-admin/plugins.php?plugin_status=inactive"><?php WPML::_e('Activate') ?></a>
    <?php else: ?>
        <a href="<?php echo get_bloginfo('url') ?>/wp-admin/plugin-install.php?tab=search&type=term&s=WP+External+Links+freelancephp&plugin-search-input=Search+Plugins"><?php WPML::_e('Get this plugin') ?></a>
    <?php endif; ?>
</h4>
<p><?php WPML::_e('Open external links in a new window or tab, adding "nofollow", set link icon, styling, SEO friendly options and more. Easy install and go.') ?>
    <br /><a href="http://wordpress.org/extend/plugins/wp-external-links/" target="_blank">WordPress.org</a> | <a href="http://www.freelancephp.net/wp-external-links-plugin/" target="_blank">FreelancePHP.net</a>
</p>

<h4><img src="<?php echo WPML::url('images/icon-email-encoder-bundle.png') ?>" width="16" height="16" /> <?php WPML::_e('Email Encoder Bundle') ?> -
    <?php if (is_plugin_active('email-encoder-bundle/email-encoder-bundle.php')): ?>
        <a href="<?php echo get_bloginfo('url') ?>/wp-admin/admin.php?page=email-encoder-bundle/email-encoder-bundle.php"><?php _e('Settings') ?></a>
    <?php elseif(file_exists(WP_PLUGIN_DIR . '/email-encoder-bundle/email-encoder-bundle.php')): ?>
        <a href="<?php echo get_bloginfo('url') ?>/wp-admin/plugins.php?plugin_status=inactive"><?php WPML::_e('Activate') ?></a>
    <?php else: ?>
        <a href="<?php echo get_bloginfo('url') ?>/wp-admin/plugin-install.php?tab=search&type=term&s=WP+Mailto+Links+freelancephp&plugin-search-input=Search+Plugins"><?php WPML::_e('Get this plugin') ?></a>
    <?php endif; ?>
</h4>
<p><?php WPML::_e('Encode mailto links, email addresses or any text and hide them from spambots. Easy to use, plugin works directly when activated.') ?>
    <br /><a href="http://wordpress.org/extend/plugins/email-encoder-bundle/" target="_blank">WordPress.org</a> | <a href="http://www.freelancephp.net/wp-email-encoder-bundle-plugin-3/" target="_blank">FreelancePHP.net</a>
</p>

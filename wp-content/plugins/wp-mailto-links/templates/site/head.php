<style type="text/css" media="all">
/* WP Mailto Links Plugin */
span.wpml-nodis { display:none !important; }
span.wpml-rtl { unicode-bidi:bidi-override !important; direction:rtl !important; display:inline !important; }
.wpml-encoded { position:absolute; margin-top:-0.3em; z-index:1000; color:green; }
<?php if ($className): ?>
.<?php echo $className; ?> { white-space:nowrap; }
<?php endif; ?>

<?php if ($icon): ?>
a.mail-icon-<?php echo $icon; ?> {
    background-image:url("<?php echo plugins_url('/public/images/mail-icon-' . $icon . '.png', WP_MAILTO_LINKS_FILE); ?>");
    background-repeat:no-repeat;
    <?php if ($showBefore): ?>
    background-position:0% 50%; padding-left:18px;
    <?php else: ?>
    background-position:100% 50%; padding-right:18px;
    <?php endif; ?>
}
<?php endif; ?>
</style>

</section> <!-- ends colmask -->

</div> <!-- ends wrap -->

<footer>
    <nav class="container">   
    <?php if ( !function_exists('dynamic_sidebar')
        || !dynamic_sidebar('Footer') ) : ?>
    
    <?php endif; ?>
<div id="logos">
<a href="http://sloan.org"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/sloan.png" alt="The Alfred P. Sloan Foundation" title="The Alfred P. Sloan Foundation" width="250px"></a>
<a href="http://www.pressforward.org" target="_blank"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/pressforward.png" alt="Press Forward" title="Press Forward"></a>
<a href="http://www.chnm.gmu.edu" target="_blank"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/rrchnm.png" alt="Roy Rosenzweig Center for History and New Media" title="Roy Rosenzweig Center for History and New Media"></a>
<a href="http://mellon.org" target="_blank"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/mellon.png" alt="The Andrew W. Mellon Foundation" title="The Andrew W. Mellon Foundation"></a>
<div class="creative-commons">
<a rel="license" href="http://creativecommons.org/licenses/by/3.0/"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by/3.0/88x31.png" title="Creative Commons License"/></a>This work is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by/3.0/">Creative Commons Attribution 3.0 Unported License</a>.
</div>
    </nav>
</footer>

<?php wp_footer(); ?>
</body>
</html>
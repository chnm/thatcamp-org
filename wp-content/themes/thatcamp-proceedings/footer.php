</section> <!-- ends colmask -->

</div> <!-- ends wrap -->

<footer>
    <nav class="container">
    <?php if ( !function_exists('dynamic_sidebar')
        || !dynamic_sidebar('Footer') ) : ?>
    <?php endif; ?>
    </nav>
</footer>

<?php wp_footer(); ?>
</body>
</html>
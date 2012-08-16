<?php /* Arclite/digitalnature */ ?>

<!-- search form -->

    <form method="get" id="searchform" action="<?php bloginfo('url'); ?>/">
      <input type="text" name="s" id="searchbox" class="searchfield" value="<?php _e("Search","arclite"); ?>" onfocus="if(this.value == '<?php _e("Search","arclite"); ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e("Search","arclite"); ?>';}" />
       <input type="submit" value="Go" class="searchbutton" />
    </form>

<!-- /search form -->
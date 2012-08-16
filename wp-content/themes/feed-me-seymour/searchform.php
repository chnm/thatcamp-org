<form method="get" id="searchform" action="<?php echo home_url(); ?>/">
<input type="text" class="search_input" value="Search &amp; Hit Enter" name="s" id="sform" onfocus="if (this.value == 'Search &amp; Hit Enter') {this.value = '';}" onblur="if (this.value == '') {this.value = 'Search &amp; Hit Enter';}" />
<input type="hidden" id="searchsubmit" value="Search" />
</form>

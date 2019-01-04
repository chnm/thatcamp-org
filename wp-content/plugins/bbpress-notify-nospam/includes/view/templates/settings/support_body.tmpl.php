<p><?php _e('The information below is important to help us troubleshoot any issues you may be having with bbpnns. 
Due to the possibly sensitive nature of the information provided, <strong>please send it to <a href="mailto:vinny@usestrict.net">vinny@usestrict.net</a></strong> 
when contacting support in the public wordpress.org forums.', 'bbPress_Notify_noSpam' ) ;?></p>

<p><?php _e('Click the \'Select for Support\' button below, then Ctrl+C (⌘+C on Mac).', 'bbPress_Notify_noSpam' ) ;?></p>

<style>
	#bbpnns-support-data {
		height: 300px;
		overflow: scroll;
		background-color: #eee;
		padding: 10px;
	}
	span.bbpnns-after-select, #submit {
		display: none;
	}
</style>
<div id="bbpnns-support-data">

<pre><?php var_dump($stash->support_vars) ?></pre>

</div>
<p>
 <a id="bbpnns-select-for-support" class="button" href="#"><?php _e('Select for Support')?></a> 
 <span class="bbpnns-after-select"><?php _e('Now Ctrl+C (⌘+C on Mac)', 'bbPress_Notify_noSpam' ) ; ?></span>
</p>

<script>
jQuery(document).ready(function($){

	var selectText = function( containerid ) {

        var node = document.getElementById( containerid );

        if ( document.selection ) {
            var range = document.body.createTextRange();
            range.moveToElementText( node  );
            range.select();
        } else if ( window.getSelection ) {
            var range = document.createRange();
            range.selectNodeContents( node );
            window.getSelection().removeAllRanges();
            window.getSelection().addRange( range );
        }
    };

    $("#bbpnns-select-for-support").on('click', function(e){
        e.preventDefault();
        
		selectText("bbpnns-support-data");
		$(".bbpnns-after-select").show(500);
    });

	
});

</script>
<?php 

/* End if file support_body.tmpl.php */
/* Location: includes/view/templates/settings/support_body.tmpl.php */

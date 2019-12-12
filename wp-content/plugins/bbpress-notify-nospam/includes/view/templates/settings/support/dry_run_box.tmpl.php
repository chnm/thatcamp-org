</div></div> <!-- close support box -->

<div class="postbox">
	<h2 class="hndle"><span><?php _e('Dry Run', 'bbPress_Notify_noSpam'); ?></span></h2>
	<div class="inside">

		<?php _e('<p>This plugin comes with a <em>lot</em> of settings to help you decide who gets emails and who doesn\'t. And then there are all of the add-ons that make even more
		adjustments possible. With all of these moving parts, it\'s virtually impossible to know off hand how many/which users make it 
		to the recipients list.</p>', 'bbPress_Notify_noSpam'); ?>
		
		<?php _e('<p>Use the dry-run tool below to see who your settings will affect. Note that absolutely *no* messages get sent out during this process. 
		To make it as real as possible, you\'ll need to select a topic or a reply to mimic the notification.</p>','bbPress_Notify_noSpam');?>

<hr>

<style>
    #dry-run-tester {
        width: 100%;
    }

     #dry-run-post-type, #dry-run-topic {
        width: 30%;
     }
     
     #results-wrapper .error {
        color:red;
        font-weight:bold;
     }
</style>

<script>
jQuery(document).ready(function($){

	// Make test type field select2
	$("#dry-run-post-type").select2({
	  placeholder: "<?php esc_attr_e( 'Select a Test Type', 'bbPress_Notify_noSpam' ) ;?>",
	  allowClear: true
	});

	// Show hide steps
	// Clear selected topic/reply if changing type.
	$("#dry-run-post-type").on('change', function(){
		$("#step2").show();
		$("#dry-run-post option").remove();
		$("#dry-run-post").change();
	});

	$("#dry-run-post").on('change', function(){
		if ( $(this).val() ) {
			$("#step3").show();
		}
		else {
			$("#step3").hide();
		}
	});
	
	// Topic fetcher
	$("#dry-run-post").select2({
		  ajax: {
			method: 'POST',
		    url: "<?php echo admin_url('admin-ajax.php');?>",
		    dataType: 'json',
		    delay: 250,
		    data: function (params) {
		      return {
			    action:    'bbpnns_dry_run_fetch_posts',
			    nonce:     $("#step2-nonce").val(),
			    post_type: $("#dry-run-post-type").val(),
		        s: params.term,
		        paged: params.page
		      };
		    },
		    processResults: function (out, params) {
		      // parse the results into the format expected by Select2
		      // since we are using custom formatting functions we do not need to
		      // alter the remote JSON data, except to indicate that infinite
		      // scrolling can be used
		      params.page = params.page || 1;
		      return {
		        results: out.data.results,
		        pagination: {
		          more: (params.page * 30) < out.total_count
		        }
		      };
		    },
		    cache: true
		  },
		  placeholder: '<?php _e('Search...', 'bbPress_Notify_noSpam');?>',
		  minimumInputLength: 1,
		});

	$("#dry-run-run-test").on('click', function( e ){
		e.preventDefault();

		$("#results-wrapper").show();
		$("#results-wrapper .error").hide();
		$("#results-wrapper .running").show();
		$("#results-wrapper .spinner").addClass('is-active');

		$.ajax({
			method: 'POST',
		    url: "<?php echo admin_url('admin-ajax.php');?>",
		    dataType: 'json',
		    data: {
				action: 'bbpnns_dry_run_run_test',
				post_type: $("#dry-run-post-type").val(),
				post_id: $("#dry-run-post").val(),
				nonce: $("#step3-nonce").val()
			},
			success: function( out ){

				$("#results-wrapper .running").hide();
				$("#results-wrapper .spinner").removeClass('is-active');
				
				if ( out.success ) {
					$("#test-results").text( out.data.join("---------------------\n") );
					$("#test-results").show();
				}
				else {
					$("#results-wrapper .error").text( out.msg ).show();
				}
			}
		});
	});
});
	
</script>

	<table id="dry-run-tester">
		<tbody>
			<tr id="step1">
    			<td>
    				<select id="dry-run-post-type">
    					<option value="topic"><?php _e( 'Topic', 'bbPress_Notify_noSpam'); ?></option>
    					<option value="reply"><?php _e( 'Reply', 'bbPress_Notify_noSpam'); ?></option>
    				</select>
    			</td>
			</tr>
			<tr id="step2">
				<td>
					<select id="dry-run-post" style="min-width:50%"></select>
					<input type="hidden" id="step2-nonce" value="<?php echo esc_attr( wp_create_nonce( 'dry-run-post-nonce' ) );?>">
				</td>
			</tr>
			
			<tr id="results-wrapper" style="display:none">
				<td colspan="2">
					<p class="running" style="display:none;"><span class="spinner" style="float:left; margin-top:0;"></span><?php _e( 'Running test&hellip;' ); ?></p>
					<p class="error" style="display:none;"></p>
					<textarea id="test-results" readonly style="width:100%; height: 500px; display:none;"></textarea>
				</td>
			</tr>
			<tr id="step3" style="display:none;">
				<td colspan="2">
					<a href="#" id="dry-run-run-test" class="button" style="margin-top:20px;"><?php _e( 'Run Test', 'bbPress_Notify_noSpam' ); ?></a>
					<input type="hidden" id="step3-nonce" value="<?php echo esc_attr( wp_create_nonce( 'dry-run-test-nonce' ) );?>">
				</td>
			</tr>
		</tbody>
	</table>

	 

<?php 

/* End if file dry_run_box.tmpl.php */
/* Location: includes/view/templates/settings/support/dry_run_box.tmpl.php */

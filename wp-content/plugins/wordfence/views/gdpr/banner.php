<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents the persistent banner.
 */
?>
<ul id="wf-gdpr-banner">
	<li><?php _e('Wordfence\'s terms of use and privacy policy have changed', 'wordfence'); ?></li>
	<li><a href="#" class="wf-btn wf-btn-default" id="wf-gdpr-review"><?php _e('Review', 'wordfence'); ?></a></li>
</ul>

<script type="application/javascript">
	(function($) {
		$(function() {
			$('#wf-gdpr-review').on('click', function(e) {
				e.preventDefault();
				e.stopPropagation();

				var prompt = $('#wfTmpl_touppChangedModal').tmpl();
				var promptHTML = $("<div />").append(prompt).html();
				WFAD.colorboxHTML('800px', promptHTML, {overlayClose: false, closeButton: false, className: 'wf-modal', onComplete: function() {
					$('#wf-toupp-changed-cancel').on('click', function(e) {
						e.preventDefault();
						e.stopPropagation();

						WFAD.colorboxClose();
					});

					$('#wf-toupp-changed-agree').on('click', function(e) {
						e.preventDefault();
						e.stopPropagation();

						$('#wf-gdpr-banner').slideUp();
						WFAD.ajax('wordfence_recordTOUPP', {}, function(res) {
							//Do nothing
						});

						WFAD.colorboxClose();
					});
				}});
			});
		});
	})(jQuery);
</script>

<script type="text/x-jquery-template" id="wfTmpl_touppChangedModal">
<div class="wf-modal" id="wf-toupp-changed-modal">
	<div class="wf-modal-header">
		<div class="wf-modal-header-content">
			<div class="wf-modal-title"><?php _e('Wordfence\'s Terms of Use and Privacy Policy', 'wordfence'); ?></div>
		</div>
		<div class="wf-modal-header-action"></div>
	</div>
	<div class="wf-modal-content">
		<p><?php _e('Please review the updated terms of use and privacy policy:', 'wordfence'); ?></p>
		<ul>
			<li><?php _e('<a href="https://www.wordfence.com/terms-of-use/" target="_blank" rel="noopener noreferrer">Terms of Use</a>', 'wordfence'); ?></li>
			<li><?php _e('<a href="https://www.wordfence.com/privacy-policy/" target="_blank" rel="noopener noreferrer">Privacy Policy</a>', 'wordfence'); ?></li>
		</ul>
	</div>
	<div class="wf-modal-footer">
		<ul class="wf-flex-horizontal wf-full-width wf-flex-align-right">
			<li class="wf-padding-add-right"><a href="#" class="wf-onboarding-btn wf-onboarding-btn-default" id="wf-toupp-changed-cancel"><?php _e('Cancel', 'wordfence'); ?></a></li>
			<li><a href="#" class="wf-onboarding-btn wf-onboarding-btn-primary" id="wf-toupp-changed-agree"><?php _e('Agree', 'wordfence'); ?></a></li>
		</ul>
	</div>
</div>
</script>
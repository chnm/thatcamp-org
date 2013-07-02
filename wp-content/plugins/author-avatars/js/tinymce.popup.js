var AA_Tabs = null;

//resize the popup to fit form
function init() {
	// init jquery tabs
	AA_Tabs = jQuery('.aa-tabs,.aa-tabs>ul').tabs({ tabTemplate: '<li><a href="#{href}">#{label}</a></li>' });
	AA_Tabs.tabs('disable', 1);

	// hide or show fields & bind change event handler
	jQuery('#shortcode_type label').click(AA_updateFieldVisibility);

	jQuery('select#user_id').change(function(){
		if(jQuery(this).val()>0){
			jQuery('input#email').hide();
			// just in case set
			jQuery('.fields_type_show_avatar #email').parent().removeClass('aa-form-error');
		}else{
			jQuery('input#email').show();
		}

	});

	// initialise the resizable avatar preview
	AA_init_avatarpreview(jQuery("div.avatar_size_preview"), jQuery('input.avatar_size_input'));

	// check visibility of sortdirection field
	AA_check_sortdirection_status();

	tinyMCEPopup.resizeToInnerSize();
}
tinyMCEPopup.executeOnLoad('init();')


// Checks the value of the shortcode type field and hides/shows other form fields respectively.
function AA_updateFieldVisibility(evt) {
	var selected_value = null;
	if (evt != undefined && evt.currentTarget != undefined) {
		selected_value = jQuery('input', evt.currentTarget).val();
	} else {
		selected_value = jQuery('#shortcode_type :checked').val();
	}

	if (selected_value == 'show_avatar') {
		jQuery('.fields_type_authoravatars').hide();
		jQuery('.fields_type_show_avatar').show();
		AA_Tabs.tabs('disable', 1);
	}
	else if (selected_value == 'authoravatars' ) {
		jQuery('.fields_type_show_avatar').hide();
		jQuery('.fields_type_authoravatars').show();
		AA_Tabs.tabs('enable', 1);
	}
	else {
		jQuery('.fields_type_show_avatar').hide();
		jQuery('.fields_type_authoravatars').hide();
		AA_Tabs.tabs('disable', 1);
	}
}

function insertAuthorAvatarsCode() {
	var error = false;

	// get shortcode type
	var type = jQuery('#shortcode_type :checked').val() || '';
	if (type.length == 0) {
		jQuery('#shortcode_type').addClass('aa-form-error');
		error = true;
	}
	else {
		jQuery('#shortcode_type').removeClass('aa-form-error');
	}

	var tagtext = "[" + type;

	if (type == 'authoravatars') {

		// blogs
		var blogs = jQuery(".fields_type_authoravatars #blogs").val() || [];
	    if (blogs.length > 0) {
	        tagtext += " blogs=" + blogs.join(',');
	    }
 
		// group_by
		var group_by = new Array();
		jQuery(".fields_type_authoravatars #group_by :checked").each(function(i, el) { group_by.push(jQuery(el).val()); });
	    if (group_by.length > 0) {
	        tagtext += " group_by=" + group_by.join(',');
	    }

		// roles
		var roles = new Array();
		jQuery(".fields_type_authoravatars #roles :checked").each(function(i, el) { roles.push(jQuery(el).val()); });
	    if (roles.length > 0) {
	        tagtext += " roles=" + roles.join(',');
	    }

		// hiddenusers
		var hiddenusers = jQuery("#hiddenusers").val() || "";
		if (hiddenusers.length > 0) {
			tagtext += " hiddenusers=" + hiddenusers;
		}

		// user_link
		var user_link = jQuery(".fields_type_authoravatars #user_link").val() || "";
		if (user_link.length > 0) {
			tagtext += " user_link=" + user_link;
		}


		jQuery('.fields_type_authoravatars #display :checked').each(function(i, el) { 
			// show_name
			if ('show_name' == jQuery(el).val()) {
				tagtext += " show_name=true";
			}

			// show_email
			if ('show_email' == jQuery(el).val()) {
				tagtext += " show_email=true";
			}

			// show_postcount
			if ('show_postcount' == jQuery(el).val()) {
				tagtext += " show_postcount=true";
			}

			// show_biography
			if ('show_biography' == jQuery(el).val()) {
				tagtext += " show_biography=true";
			}

			// BBPRESS_post_count
			if ('show_bbpress_post_count' == jQuery(el).val()) {
				tagtext += " show_bbpress_post_count=true";
			}

		 });

		 		// limit
		var limit = jQuery("#limit").val() || "";
		if (limit.length > 0) {
			tagtext += " limit=" + limit;
		}
		// Page size
		var page_size = jQuery("#page_size").val() || "";
		if (page_size.length > 0) {
			tagtext += " page_size=" + page_size;
		}
			// min post count
		var min_post_count = jQuery("#min_post_count").val() || "";
		if (min_post_count.length > 0) {
			tagtext += " min_post_count=" + min_post_count;
		}

		// order
		var order = jQuery("#order").val() || "";
		if (order.length > 0) {
			tagtext += " order=" + order;
		}

		// sort direction
		var sort_dir = jQuery("#sort_direction").val() || "";
		if (order.length > 0 && sort_dir.length > 0) {
			tagtext += "," + sort_dir;
		}

		// render_as_list
		// TODO
	}

	if (type == 'show_avatar') {

		// email or id
		var email = jQuery('.fields_type_show_avatar #email').val() || '';
		var user = jQuery('.fields_type_show_avatar #user_id').val()
		if (email.length > 0 || user > 0) {
			jQuery('.fields_type_show_avatar #email').parent().removeClass('aa-form-error');
			if(user > 0){
				tagtext += " email=" + user;
			}else{
				tagtext += " email=" + email;
			}
			
		}
		else {
			jQuery('#email').parent().addClass('aa-form-error');
			error = true;
		}

		// alignment
		var align = jQuery('.fields_type_show_avatar #alignment :checked').val() || '';
		if (align.length > 0) {
			tagtext += " align=" + align;
		}
		
		// user_link
		var user_link = jQuery(".fields_type_show_avatar #user_link").val() || "";
		if (user_link.length > 0) {
			tagtext += " user_link=" + user_link;
		}

		jQuery('.fields_type_show_avatar #display :checked').each(function(i, el) { 
			// show_name
			if ('show_name' == jQuery(el).val()) {
				tagtext += " show_name=true";
			}

			// show_email
			if ('show_email' == jQuery(el).val()) {
				tagtext += " show_email=true";
			}

			// show_postcount
			if ('show_postcount' == jQuery(el).val()) {
				tagtext += " show_postcount=true";
			}

			// show_biography
			if ('show_biography' == jQuery(el).val()) {
				tagtext += " show_biography=true";
			}

			// BBPRESS_post_count
			if ('show_bbpress_post_count' == jQuery(el).val()) {
				tagtext += " show_bbpress_post_count=true";
			}

		 });


	}

	// avatar_size
	var avatar_size = jQuery("#avatar_size").val() || "";
	if (avatar_size.length > 0) {
		tagtext += " avatar_size=" + avatar_size;
	}

    tagtext += "]";

	if (error == true) {
		return;
	}

    if (window.tinyMCE) {
		window.tinyMCE.execInstanceCommand('content', 'mceInsertContent', false, tagtext);
		tinyMCEPopup.editor.execCommand('mceRepaint');
		tinyMCEPopup.close();
	}
	return;
}
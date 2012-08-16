function AA_init_avatarpreview(preview_node, input_node, options) {
	var defaults = {
		minimum_size: 25,
		maximum_size: 200
	};
	var options = jQuery.extend(defaults, options);

	// make sure we got valid nodes and only use the first one if we got multiple ones.
	var preview_node = jQuery(preview_node).eq(0);
	var input_node = jQuery(input_node).eq(0);
	if (!preview_node || !input_node) return;

	// watch input node value and update preview respectively
	if (!input_node.hasClass('is_resizable')) {
		input_node.bind('keyup', function(evt) {
			// determine size
			var size = this.value;
			if (size < options.minimum_size) size = options.minimum_size;
			if (size > options.maximum_size) size = options.maximum_size;
			size += 'px';

			// update avatar size
			var img_node = jQuery('img', preview_node);
			if (img_node.length > 0) {
				img_node.width(size);
				img_node.height(size);
			}

			// update size of resizable container
			var img_container = jQuery('.ui-resizable-knob, .ui-wrapper', preview_node);
			if (img_container.length > 0) {
				img_container.width(size);
				img_container.height(size);
			}
		});
		input_node.addClass('is_resizable');
	}

	// make preview img resizable and update input node on change
	preview_node.bind('mouseenter', function () {
		if (!preview_node.hasClass('is_resizable')) {
			// setup jquery ui resizable
			jQuery('img', preview_node).resizable({
				handles: "se",
				aspectRatio: 1,
				minWidth: options.minimum_size,
				maxWidth: options.maximum_size,
				minHeight: options.minimum_size,
				maxHeight: options.maximum_size,
				knobHandles: true,
				resize: function(e, ui) {
					input_node.val(ui.size.width); // update the avatar_size input field to the new value.
				}
			});
			preview_node.addClass('is_resizable');
		}
	});
}

function AA_check_sortdirection_status(container) {
	var checkSortDirectionStatus = function() {
		var container = jQuery(this).parent().parent().parent();
		var element = jQuery("select[id$='sort_direction']", container).parent().parent();
		switch(jQuery(this).val()){
			case 'random':
				if (!element.is(":animated") && !element.is(":hidden")) element.slideUp();
				break;
			default:
				if (!element.is(":animated") && element.is(":hidden")) element.slideDown();
		}
	};

	jQuery("select[id$='order']", jQuery(container))
		.bind('change', checkSortDirectionStatus)
		.each(checkSortDirectionStatus);
}

jQuery(function($) {
	// since 3.3: add screen option toggles
	postboxes.add_postbox_toggles(pagenow);

	function template() {
		if ($('.template').attr('checked')) {
			$('.templated').show();
			$('.not_templated').hide();
		} else {
			$('.templated').hide();
			$('.not_templated').show();
		}
		excerpt();
	}
	$('.template').click(template);
	template();
	
	function excerpt() {
		if (!$('.template').attr('checked') && $('.show_excerpt').attr('checked'))
			$('.excerpted').show();
		else
			$('.excerpted').hide();
	}
	$('.show_excerpt,.template').click(excerpt);

	var loaded_demo_web = false;
	function display() {
		if ( !$('#yarpp_display_web .inside').is(':visible') )
			return;
		if ( !loaded_demo_web ) {
			loaded_demo_web = true;
			var demo_web = $('#display_demo_web');
			$.ajax({type:'POST',
				url: ajaxurl,
				data: {
					action: 'yarpp_display_demo',
					domain: 'website',
					'_ajax_nonce': $('#yarpp_display_demo-nonce').val()
				},
				beforeSend:function(){demo_web.html(loading)},
				success:function(html){demo_web.html('<pre>'+html+'</pre>')},
				dataType:'html'});
		}
	}
	$('#yarpp_display_web .handlediv, #yarpp_display_web-hide').click(display);
	display();

	var loaded_demo_rss = false;
	function rss_display() {
		if ( !$('#yarpp_display_rss .inside').is(':visible') )
			return;
		if ($('.rss_display').attr('checked')) {
			$('.rss_displayed').show();
			if ( !loaded_demo_rss ) {
				loaded_demo_rss = true;
				var demo_rss = $('#display_demo_rss');
				$.ajax({type:'POST',
						url: ajaxurl,
						data: {
							action: 'yarpp_display_demo',
							domain: 'rss',
							'_ajax_nonce': $('#yarpp_display_demo-nonce').val()
						},
						beforeSend:function(){demo_rss.html(loading)},
						success:function(html){demo_rss.html('<pre>'+html+'</pre>')},
						dataType:'html'});
			}
			rss_template();
		} else {
			$('.rss_displayed').hide();
		}
	}
	$('.rss_display, #yarpp_display_rss .handlediv, #yarpp_display_rss-hide').click(rss_display);
	rss_display();
	
	function rss_template() {
		if ($('.rss_template').attr('checked')) {
			$('.rss_templated').show();
			$('.rss_not_templated').hide();
		} else {
			$('.rss_templated').hide();
			$('.rss_not_templated').show();
		}
		rss_excerpt();
	}
	$('.rss_template').click(rss_template);
	
	function rss_excerpt() {
		if ($('.rss_display').attr('checked') && $('.rss_show_excerpt').attr('checked'))
			$('.rss_excerpted').show();
		else
			$('.rss_excerpted').hide();
	}
	$('.rss_display,.rss_show_excerpt').click(rss_excerpt);

	var loaded_disallows = false;
	function load_disallows() {
		if ( loaded_disallows || !$('#yarpp_pool .inside').is(':visible') )
			return;
		loaded_disallows = true;
		
		var finished_taxonomies = {},
			term_indices = {};
		function load_disallow(taxonomy) {
			if (taxonomy in finished_taxonomies)
				return;
			var display = $('#exclude_' + taxonomy);
			// only do one query at a time:
			if (display.find('.loading').length)
				return;
			
			if ( taxonomy in term_indices )
				term_indices[taxonomy] = term_indices[taxonomy] + 100;
			else
				term_indices[taxonomy] = 0;
			$.ajax({type:'POST',
					url: ajaxurl,
					data: {	action: 'yarpp_display_exclude_terms',
							taxonomy: taxonomy,
							offset: term_indices[taxonomy],
							'_ajax_nonce': $('#yarpp_display_exclude_terms-nonce').val()
							},
					beforeSend:function(){
						display.append(loading)
					},
					success:function(html){
						display.find('.loading').remove();
						if (':(' == html) { // no more :(
							finished_taxonomies[taxonomy] = true;
							return;
						}
						display.append(html);
					},
					dataType:'html'}
			);
		}
		
		$('.exclude_terms').each(function() {
			var id = jQuery(this).attr('id'), taxonomy;
			if (!id)
				return;
			
			taxonomy = id.replace('exclude_','');
			
			load_disallow(taxonomy);
			$('#exclude_' + taxonomy).parent('.scroll_wrapper').scroll(function() {
				var parent = $(this),
					content = parent.children('div');
				if ( parent.scrollTop() + parent.height() > content.height() - 10 )
					load_disallow(taxonomy);
			})
		})
		
	}
	$('#yarpp_pool .handlediv, #yarpp_pool-hide').click(load_disallows);
	load_disallows();
});
(function($) {
	$(document).ready(function() {
		$('#tabbed').tabs();
		$(".postbox .inside").not('#themeresources.postbox .inside, #themepresets.postbox .inside, #themefontsamples.postbox .inside').hide();
		$('.postbox h3, .postbox .handlediv').click(function() {
			$(this).parent().find('.inside').slideToggle('fast');
		});
		$('.postbox input, .postbox textarea, .postbox select').change(function() {
			$('#savetheme').addClass('remember');
		});
		var z = 100;
		$(".postbox").each(function() {
			$(this).css("zIndex", z);
			z--;
		});
		$('a.openall').click(function() {
			$(this).parent().parent().find('.postbox .inside').slideDown();
			$(this).hide();
			$(this).parent().find('a.closeall').show();
		});
		$('a.closeall').click(function() {
			$(this).parent().parent().find('.postbox .inside').slideUp();
			$(this).hide();
			$(this).parent().find('a.openall').show();	
		});	
		$("#savetheme").click(function() {
			$(".themesbybavotasan").trigger('submit');
			return false;
		});	
		$(".themesbybavotasan").submit(function(){
			$(".ajaxsave").show();
			var data = {
				action: 'save_theme_options',
				option: $(this).serialize()
			};
			$.post(ajaxurl, data,
			function(response){
				$('#savetheme').removeClass('remember');
				$("#message").show();
				$(".ajaxsave").hide();		
				setTimeout(function() { $('#message').slideUp(); }, 5000);
				headerLogo();
			});
			return false;
		});
		function headerLogo() {
			if($('input[name="logo_header"]').val()) {
				var image = $('input[name="logo_header"]').val();
				$(".headerlogo").html('<img src="'+image+'" alt="" />');
			} else {
				$(".headerlogo img").remove();
			}
		}
		headerLogo();
		
		$(".wrap").fadeIn(500);

		// ask the user to confirm the window closing
		window.onbeforeunload = function() {
			if($('#savetheme').attr('class')=="remember") {
				return "If you do, all of your changes will be lost.";
			}
		}
		function sidebarLoc() {
			if($('input[name="sidebar_width2"]:checked').val() != 0) {
				$('#twoSidebar').show();
				$('#oneSidebar').hide();
				if($('input[name="sidebar_location"]:checked').val() < 3) {
					$(".locerror").html('Please select a location for your sidebars.');											
				} else {
					$(".locerror").html();
				}
			} else {
				$('#twoSidebar').hide();
				$('#oneSidebar').show();
				if($('input[name="sidebar_location"]:checked').val() > 2) {
					$(".locerror").html('Please select a location for your sidebar.');											
				} else {
					$(".locerror").html();
				}
			}
		}
		sidebarLoc();
		$('input[name="sidebar_width2"]').change(function() {
			sidebarLoc();
		});
	});
})(jQuery)
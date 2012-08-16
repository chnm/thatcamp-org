
(function ($) {

    if($.tools.validator != undefined){

		// Widget contact form
        $.tools.validator.addEffect("contactform", function(errors, event) {
            $.each(errors, function(index, error) {
                var input = error.input;
                input.addClass('invalid');
            });
        }, function(inputs)  {
            inputs.removeClass('invalid');
        });

		// Widget contact form
        $('.contactform').validator({effect:'contactform'}).submit(function(e) {

			var form = $(this);
            if (!e.isDefaultPrevented()) {
				var $id = form.find('input[name="unique_widget_id"]').val();
               	$("#btn_" + $id).val("Wait..");
				$("#btn_" + $id).attr('disabled', '');
			   	$.post(this.action,{
                    'to':$('input[name="contact_'+$id+'_to"]').val(),
                    'name':$('input[name="contact_'+$id+'_name"]').val(),
                    'email':$('input[name="contact_'+$id+'_email"]').val(),
                    'content':$('textarea[name="contact_'+$id+'_content"]').val()
                },function(data){
                    form.fadeOut('fast', function() {
                         $(this).siblings('p').show();
						 $("#btn_" + $id).val("Submit");
						 $("#btn_" + $id).removeAttr('disabled');
                    });
                });
				e.preventDefault();
            }
        });

		// Comment and search forms. These do not use AJAX POST
		$('.searchform').validator({effect:'contactform'});
		// Comment and search forms. These do not use AJAX POST
		$('#commentform').validator({effect:'contactform'});
    }
}(jQuery));

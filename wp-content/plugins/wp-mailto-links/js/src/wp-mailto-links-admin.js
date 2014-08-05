/* WP Mailto Links - Admin */
/*global jQuery, setTimeout*/
jQuery(function ($) {
    'use strict';

    // update message
    $('#setting-error-settings_updated').click(function () {
        $(this).hide();
    });

    // help link
    $('.wpml-help-link').click(function (e) {
        $('#contextual-help-link').click();
        e.preventDefault();
    });

    // option filter whole page
    $('input#filter_body')
        .change(function () {
            var $i = $('input#filter_posts, input#filter_comments, input#filter_widgets');

            if ($(this).attr('checked')) {
                $i.attr('disabled', true)
                    .attr('checked', true);
            } else {
                $i.attr('disabled', false);
            }
        })
        .change();

    // Workaround for saving disabled checkboxes in options db
    // prepare checkboxes before submit
    $('.wrap form').submit(function () {
        // force value 0 being saved in options
        $('*[type="checkbox"]:not(:checked)')
            .css({ 'visibility': 'hidden' })
            .attr({
                'value': '0',
                'checked': 'checked'
            });
    });

    // enable submit buttons
    $('*[type="submit"]')
        .attr('disabled', false)
        .removeClass('submit'); // remove class to fix button background

    // metabox screen columns
    if ($('[data-js="screen-columns"]').size() > 0) {
        $('input[name="screen_columns"]')
            .click(function () {
                if ($('input[name="screen_columns"]:checked').val() === $(this).val()) {
                    setTimeout(function () {
                        $(this).change();
                    }, 1);
                }
            })
            .change(function () {
                var columns = $('input[name="screen_columns"]:checked').val();
                $('#post-body').removeClass('columns-1').removeClass('columns-2').addClass('columns-' + columns);
            });
    }
});

/* WP Mailto Links - Admin */
/*global jQuery, setTimeout*/
jQuery(function ($) {
    'use strict';

    var $wpmlAdmin = $('.wpml-admin');

    // update message
    $wpmlAdmin.delegate('#setting-error-settings_updated', 'click', function () {
        $(this).hide();
    });

    // help link
    $wpmlAdmin.delegate('.wpml-help-link', 'click', function (e) {
        $('#contextual-help-link').click();
        e.preventDefault();
    });

    // option filter whole page
    $wpmlAdmin.find('input#filter_body')
        .change(function () {
            var $i = $wpmlAdmin.find('input#filter_posts, input#filter_comments, input#filter_widgets');

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
    $wpmlAdmin.find('form').submit(function () {
        // force value 0 being saved in options
        $('*[type="checkbox"]:not(:checked)')
            .css({ 'visibility': 'hidden' })
            .attr({
                'value': '0',
                'checked': 'checked'
            });
    });

    // enable submit buttons
    $wpmlAdmin.find('*[type="submit"]')
        .attr('disabled', false)
        .removeClass('submit'); // remove class to fix button background

    // metabox screen columns
    if ($wpmlAdmin.find('[data-js="screen-columns"]').size() > 0) {
        $wpmlAdmin.find('input[name="screen_columns"]')
            .click(function () {
                if ($wpmlAdmin.find('input[name="screen_columns"]:checked').val() === $(this).val()) {
                    setTimeout(function () {
                        $(this).change();
                    }, 1);
                }
            })
            .change(function () {
                var columns = $wpmlAdmin.find('input[name="screen_columns"]:checked').val();
                $wpmlAdmin.find('#post-body').removeClass('columns-1').removeClass('columns-2').addClass('columns-' + columns);
            });
    }
});

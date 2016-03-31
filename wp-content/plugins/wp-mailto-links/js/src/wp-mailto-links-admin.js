/* WP Mailto Links Admin */
/*global jQuery, wpmlSettings*/
jQuery(function ($) {

    'use strict';

    // Workaround for posting disabled checkboxes
    // prepare checkboxes on submit
    $('.wrap form').on('submit', function () {
        // force value 0 being saved in options
        $('*[type="checkbox"]:not(:checked)')
            .css({
                'visibility': 'hidden'
            })
            .prop({
                'value': '0',
                'checked': 'checked'
            });
    });

    // fill dashicons  select options
    $.get(wpmlSettings.pluginUrl + '/data/json/fontawesome.json', null, function (data) {
        var $select = $('.select-fontawesome');

        // create select options
        fillSelect($select, data.icons, 'unicode', 'className');

        // select saved value
        $select.find('option').each(function () {
            if (this.value === wpmlSettings.fontawesomeValue) {
                $(this).prop('selected', true);
            }
        });
    });

    // fill fontawesome select options
    $.get(wpmlSettings.pluginUrl + '/data/json/dashicons.json', null, function (data) {
        var $select = $('.select-dashicons');

        // create select options
        fillSelect($select, data.icons, 'unicode', 'className');

        // select saved value
        $select.find('option').each(function () {
            if (this.value === wpmlSettings.dashiconsValue) {
                $(this).prop('selected', true);
            }
        });
    });

    // fill select helper function
    function fillSelect($select, list, keyText, keyValue) {
        $.each(list, function (index, item) {
            var value = item[keyValue];
            var text = item[keyText];

            $select.append('<option value="'+ value +'">&#x'+ text +'</option>');
        });
    }

    // filter body + childs
    $('*[name="wp-mailto-links[filter_body]"]').on('change', function () {
        var $fields = $('.filter-body-child');

        if ($(this).prop('checked')) {
            $fields.prop('disabled', true);
            $fields.prop('checked', true);
        } else {
            $fields.prop('disabled', false);
        }
    })
    // trigger immediatly
    .trigger('change');

    // mail icon
    $('body').on('change', '*[name="wp-mailto-links[mail_icon]"]', function () {
        var value = $(this).val();
        var $images = $('.wrap-icon-images');
        var $selectDashicons = $('.wrap-dashicons');
        var $selectFontAwesome = $('.wrap-fontawesome');

        $images.hide();
        $selectDashicons.hide();
        $selectFontAwesome.hide();

        if (value === 'image') {
            $images.show();
        } else if (value === 'dashicons') {
            $selectDashicons.show();
        } else if (value === 'fontawesome') {
            $selectFontAwesome.show();
        }
    });
    // trigger immediatly
    $('*[name="wp-mailto-links[mail_icon]"]:checked').change();

});

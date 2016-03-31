/* WP Mailto Links */
/*global jQuery, window*/
jQuery(function ($) {

    'use strict';

    // encoding method
    function rot13(s) {
        // source: http://jsfromhell.com/string/rot13
        return s.replace(/[a-zA-Z]/g, function (c) {
            return String.fromCharCode((c <= 'Z' ? 90 : 122) >= (c = c.charCodeAt(0) + 13) ? c : c - 26);
        });
    }

    // fetch email from data attribute
    function fetchEmail(el) {
        var email = el.getAttribute('data-enc-email');

        if (!email) {
            return null;
        }

        // replace [at] sign
        email = email.replace(/\[at\]/g, '@');

        // encode
        email = rot13(email);

        return email;
    }

    // replace email in title attribute
    function parseTitle(el) {
        var title = el.getAttribute('title');
        var email = fetchEmail(el);

        if (title && email) {
            title = title.replace('{{email}}', email);
            el.setAttribute('title', title);
        }
    }

    // set input value attribute
    function setInputValue(el) {
        var email = fetchEmail(el);

        if (email) {
            el.setAttribute('value', email);
        }
    }

    // open mailto link
    function mailto(el) {
        var email = fetchEmail(el);

        if (email) {
            window.location.href = 'mailto:' + email;
        }
    }

    // set mailto click
    $('body').on('click', 'a[data-enc-email]', function () {
        mailto(this);
    });

    // parse title attirbute
    $('a[data-enc-email]').each(function () {
        parseTitle(this);
    });

    // parse input fields
    $('input[data-enc-email]').each(function () {
        setInputValue(this);
    });

});

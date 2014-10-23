/* WP Mailto Links */
/*global window, jQuery*/
(function (window) {
    'use strict';

    var document = window.document;

    // add event handler
    function addEvt(el, evt, fn) {
        if (el.attachEvent) {
            // IE method
            el.attachEvent('on' + evt, fn);
        } else if (el.addEventListener) {
            // Standard JS method
            el.addEventListener(evt, fn, false);
        }
    }

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

    // open mailto link
    function mailto(el) {
        var email = fetchEmail(el);

        if (email) {
            window.location.href = 'mailto:' + email;
        }
    }

    // on DOM ready...
    if (window.jQuery) {
    // jQuery DOMready method
        jQuery(function ($) {
            $('body').delegate('a[data-enc-email]', 'click', function () {
                mailto(this);
            });

            $('a[data-enc-email]').each(function () {
                parseTitle(this);
            });
        });
    } else {
    // use onload when jQuery not available
        addEvt(window, 'load', function () {
            var links = document.getElementsByTagName('a');
            var addClick = function (a) {
                addEvt(a, 'click', function () {
                    mailto(a);
                });
            };
            var a;
            var i;

            // check each <a> element
            for (i = 0; i < links.length; i += 1) {
                a = links[i];

                // click event for opening in a new window
                if (a.getAttribute('data-enc-email')) {
                    parseTitle(a);
                    addClick(a);
                }
            }
        });
    }

}(window));

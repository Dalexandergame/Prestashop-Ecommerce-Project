/*
 * Custom code goes here.
 * A template should always ship with an empty custom.js
 */

$(function() {
    if ($("body.page-order").length) {
        setTimeout(function () {
            $([document.documentElement, document.body]).animate({
                scrollTop: $("section.-current").offset().top
            }, 1000);
        }, 2000)
    }
})

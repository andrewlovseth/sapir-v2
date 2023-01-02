(function ($, window, document, undefined) {
    $(document).ready(function ($) {
        // Nav Trigger
        $('.js-nav-trigger').click(function () {
            $('body').toggleClass('nav-overlay-open');
            return false;
        });

        // First Line Wrap Class for Small Caps
        $('.article-body > p.dropcap, .dropcap .article-body > p:first-child').html(function (i, html) {
            return html.replace(/^[^a-zA-Z]*([a-zA-Z])/g, '<span class="first-letter">$1</span>');
        });

        $('.article-link.draft').on('click', function () {
            return false;
        });

        // Open external links in text body in a new window
        $('.article-body a').not('[href^="https://sapirjournal.org/"]').attr('target', '_blank');
    });

    $(document).mouseup(function (e) {
        var menu = $('.site-nav, .js-nav-trigger');

        if (!menu.is(e.target) && menu.has(e.target).length === 0) {
            $('body').removeClass('nav-overlay-open');
        }
    });

    $(document).keyup(function (e) {
        if (e.keyCode == 27) {
            $('body').toggleClass('nav-overlay-open');
        }
    });
})(jQuery, window, document);

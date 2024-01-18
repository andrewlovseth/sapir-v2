(function ($, window, document, undefined) {
    $(document).ready(function ($) {
        // Nav Trigger
        $(".js-nav-trigger").click(function () {
            $("body").toggleClass("nav-overlay-open");
            return false;
        });

        // First Line Wrap Class for Small Caps
        $(".article-body > p.dropcap, .dropcap .article-body > p:first-child").html(function (i, html) {
            return html.replace(/^[^a-zA-Z]*([a-zA-Z])/g, '<span class="first-letter">$1</span>');
        });

        $(".article-link.draft").on("click", function () {
            return false;
        });

        // Open external links in text body in a new window
        $(".article-body a").not('[href^="https://sapirjournal.org/"]').attr("target", "_blank");

        // AUTHOR A-Z TABS
        $(".author__tabs-link").on("click", function () {
            let activeLetter = $(this).data("letter");

            // Active Tab Link
            $(".author__tabs-link").removeClass("active");
            $(this).addClass("active");

            // Active Tab

            $(".author__tabs-group").removeClass("active");
            $('.author__tabs-group[data-letter="' + activeLetter + '"').addClass("active");

            return false;
        });

        // SEARCH TOGGLE
        $(".js-search-trigger").click(function () {
            $("body").toggleClass("search-overlay-open");
            return false;
        });

        // DINGBAT ON LAST P
        $(".last-p").append($("#dingbat").html());

        // NEWSLETTER MODAL
        var urlParams = new URLSearchParams(window.location.search);

        if (urlParams.get("newsletter") === "subscribe") {
            var $modal = $("#newsletter-modal");

            if ($modal.length) {
                $modal[0].showModal();

                // Close modal and update URL when the close button is clicked
                $modal.find(".newsletter-modal__close").on("click", function () {
                    closeModal();
                });

                // Close modal and update URL when the backdrop (modal) is clicked
                $modal.on("click", function (event) {
                    if (event.target === this) {
                        closeModal();
                    }
                });

                // Function to close modal and remove URL parameter
                function closeModal() {
                    $modal[0].close();
                    urlParams.delete("newsletter");
                    window.history.pushState({}, "", window.location.pathname + (urlParams.toString() ? "?" + urlParams.toString() : ""));
                }
            }
        }
    });

    $(document).mouseup(function (e) {
        var search_container = $(".site-header .search, .js-search-trigger");

        if (!search_container.is(e.target) && search_container.has(e.target).length === 0) {
            $("body").removeClass("search-overlay-open");
        }

        var nav_container = $(".header-nav__wrapper, .js-nav-trigger");

        if (!nav_container.is(e.target) && nav_container.has(e.target).length === 0) {
            $("body").removeClass("nav-overlay-open");
        }
    });

    $(document).keyup(function (e) {
        function closeModal() {
            $modal[0].close();
            urlParams.delete("newsletter");
            window.history.pushState({}, "", window.location.pathname + (urlParams.toString() ? "?" + urlParams.toString() : ""));
        }
        if (e.keyCode == 27) {
            $("body").removeClass("nav-overlay-open search-overlay-open");

            var urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get("newsletter") === "subscribe") {
                var $modal = $("#newsletter-modal");
                if ($modal.length) {
                    closeModal();
                }
            }
        }
    });
})(jQuery, window, document);

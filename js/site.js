(function ($, window, document, undefined) {
    $(document).ready(function ($) {
        // Nav Trigger
        $(".js-nav-trigger").click(function () {
            $("body").toggleClass("nav-overlay-open");
            return false;
        });

        // Dropcap first-letter wrapping
        // Skip any leading HTML tags (e.g., <span class="small-caps">) to find the first visible text character
        $(".article-body > p.dropcap, .dropcap .article-body > p:first-child").html(function (i, html) {
            return html.replace(/^(\s*(?:<[^>]+>\s*)*)([a-zA-Z])/, '$1<span class="first-letter">$2</span>');
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
                    window.history.pushState(
                        {},
                        "",
                        window.location.pathname + (urlParams.toString() ? "?" + urlParams.toString() : "")
                    );
                }
            }
        }

        // Hide SMS links on non-mobile or devices without SMS
        const smsLinks = document.querySelectorAll(".share-link__sms");
        if (navigator.userAgent.match(/(iPhone|iPod|iPad|Android)/) || !navigator.maxTouchPoints) {
            smsLinks.forEach((link) => (link.style.display = "none"));
        }

        // Add clipboard functionality with fallback
        document.querySelectorAll(".share-link__copy").forEach((copyLink) => {
            copyLink.addEventListener("click", function (e) {
                e.preventDefault();
                const url = window.location.href.split("?")[0];

                // Try modern clipboard API first
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard
                        .writeText(url)
                        .then(
                            function () {
                                showCopyNotification(this);
                            }.bind(this)
                        )
                        .catch(
                            function () {
                                // Fallback to older execCommand method
                                fallbackCopyToClipboard(url, this);
                            }.bind(this)
                        );
                } else {
                    // Use fallback for non-HTTPS or older browsers
                    fallbackCopyToClipboard(url, this);
                }
            });
        });

        // More robust share modal trigger with fallback methods
        function showShareModal() {
            var $modal = $("#share-modal");

            if ($modal.length) {
                // Try using showModal() first
                try {
                    $modal[0].showModal();
                } catch (e) {
                    // Fallback: If native dialog fails, show using CSS
                    console.warn("Native dialog failed, using CSS fallback");
                    $modal.addClass("modal-visible").attr("open", "");
                    $("body").addClass("modal-open");
                }

                // Close modal handlers
                $modal.find(".share-modal__close").on("click", closeShareModal);

                $modal.on("click", function (event) {
                    if (event.target === this) {
                        closeShareModal();
                    }
                });

                // Add escape key handler
                $(document).on("keydown.shareModal", function (e) {
                    if (e.key === "Escape") {
                        closeShareModal();
                    }
                });
            }
        }

        function closeShareModal() {
            var $modal = $("#share-modal");

            try {
                $modal[0].close();
            } catch (e) {
                // Fallback close method
                $modal.removeClass("modal-visible").removeAttr("open");
                $("body").removeClass("modal-open");
            }

            // Clean up escape key handler
            $(document).off("keydown.shareModal");
        }

        function showCopyNotification(element) {
            const $notification = $(element).find(".share-link__copy-notification");
            $notification.addClass("active");
            setTimeout(function () {
                $notification.removeClass("active");
            }, 1000);
        }

        function fallbackCopyToClipboard(text, element) {
            // Create temporary textarea
            const textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.position = "fixed";
            textArea.style.left = "-999999px";
            document.body.appendChild(textArea);

            try {
                textArea.select();
                document.execCommand("copy");
                showCopyNotification(element);
            } catch (err) {
                console.warn("Fallback clipboard copy failed");
            } finally {
                document.body.removeChild(textArea);
            }
        }

        // Check for share parameters using multiple methods
        const shareUrlParams = new URLSearchParams(window.location.search);
        const shouldShowShare =
            // Check URL parameters
            (shareUrlParams.get("utm_source") === "qr" && shareUrlParams.get("utm_medium") === "print") ||
            // Check localStorage in case parameters were stripped
            localStorage.getItem("showShareModal") === "true";

        if (shouldShowShare) {
            // Store state in case of page refresh
            localStorage.setItem("showShareModal", "true");

            // Ensure DOM is ready
            if (document.readyState === "loading") {
                document.addEventListener("DOMContentLoaded", showShareModal);
            } else {
                showShareModal();
            }

            // Clean up localStorage after showing modal
            localStorage.removeItem("showShareModal");
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
            window.history.pushState(
                {},
                "",
                window.location.pathname + (urlParams.toString() ? "?" + urlParams.toString() : "")
            );
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

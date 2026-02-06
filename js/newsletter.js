/**
 * Newsletter form AJAX submission handler
 * Uses Mailchimp's JSONP endpoint for cross-origin requests
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        var forms = document.querySelectorAll('.newsletter-form');

        forms.forEach(function (form) {
            form.addEventListener('submit', handleSubmit);
        });
    });

    function handleSubmit(e) {
        e.preventDefault();

        var form = e.target;
        var emailInput = form.querySelector('input[type="email"]');
        var successMsg = form.querySelector('.newsletter-form__message--success');
        var errorMsg = form.querySelector('.newsletter-form__message--error');
        var button = form.querySelector('button[type="submit"]');
        var originalButtonText = button.textContent;
        var email = emailInput.value.trim();

        // Reset message state
        successMsg.hidden = true;
        errorMsg.hidden = true;
        successMsg.textContent = '';
        errorMsg.textContent = '';

        // Client-side validation
        if (!email || !isValidEmail(email)) {
            errorMsg.textContent = 'Please enter a valid email address.';
            errorMsg.hidden = false;
            emailInput.focus();
            return;
        }

        // Disable button during submission
        button.disabled = true;
        button.textContent = 'Subscribing...';

        // Build JSONP URL from form action
        var action = form.getAttribute('action');
        var jsonpUrl = action.replace('/post?', '/post-json?');

        // Generate unique callback name to avoid collisions
        var callbackName = 'mc_callback_' + Date.now();

        // Create global callback function
        window[callbackName] = function (response) {
            button.disabled = false;
            button.textContent = originalButtonText;

            if (response.result === 'success') {
                successMsg.textContent = 'Thanks for subscribing!';
                successMsg.hidden = false;
                emailInput.value = '';
            } else {
                var msg = response.msg || 'An error occurred. Please try again.';

                // Handle common Mailchimp error messages
                if (msg.indexOf('already subscribed') !== -1) {
                    msg = "You're already subscribed!";
                } else if (msg.indexOf('too many') !== -1) {
                    msg = 'Too many signup attempts. Please try again later.';
                } else {
                    // Strip HTML tags from Mailchimp error messages
                    msg = msg.replace(/<[^>]*>/g, '');
                }

                errorMsg.textContent = msg;
                errorMsg.hidden = false;
            }

            // Cleanup global callback
            delete window[callbackName];
        };

        // Build form data as URL params
        var formData = new FormData(form);
        var params = new URLSearchParams(formData).toString();

        // Make JSONP request
        var script = document.createElement('script');
        script.src = jsonpUrl + '&c=' + callbackName + '&' + params;

        script.onerror = function () {
            button.disabled = false;
            button.textContent = originalButtonText;
            errorMsg.textContent = 'Connection error. Please try again.';
            errorMsg.hidden = false;
            script.remove();
            delete window[callbackName];
        };

        script.onload = function () {
            script.remove();
        };

        document.body.appendChild(script);
    }

    function isValidEmail(email) {
        // Basic email validation - checks for @ and a domain
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }
})();

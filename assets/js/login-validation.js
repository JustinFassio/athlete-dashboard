(function($) {
    'use strict';

    $(document).ready(function() {
        const form = $('#athlete-dashboard-login-form');
        const submitButton = form.find('input[type="submit"]');

        form.on('submit', function(e) {
            e.preventDefault();
            if (validateForm()) {
                this.submit();
            }
        });

        function validateForm() {
            let isValid = true;
            const username = $('#user_login').val().trim();
            const password = $('#user_pass').val();
            const gdprConsent = $('#gdpr_consent').is(':checked');

            // Username/Email validation
            if (username.length === 0) {
                showError('#user_login', 'Please enter your username or email');
                isValid = false;
            } else {
                removeError('#user_login');
            }

            // Password validation
            if (password.length === 0) {
                showError('#user_pass', 'Please enter your password');
                isValid = false;
            } else {
                removeError('#user_pass');
            }

            // GDPR consent validation
            if (!gdprConsent) {
                showError('#gdpr_consent', 'You must agree to the Privacy Policy');
                isValid = false;
            } else {
                removeError('#gdpr_consent');
            }

            return isValid;
        }

        function showError(field, message) {
            $(field).addClass('error');
            if (!$(field).next('.error-message').length) {
                $('<span class="error-message">' + message + '</span>').insertAfter($(field));
            }
        }

        function removeError(field) {
            $(field).removeClass('error');
            $(field).next('.error-message').remove();
        }
    });
})(jQuery);
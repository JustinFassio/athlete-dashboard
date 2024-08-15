(function($) {
    'use strict';

    $(document).ready(function() {
        const form = $('#athlete-dashboard-register-form');
        const submitButton = form.find('input[type="submit"]');

        form.on('submit', function(e) {
            e.preventDefault();
            if (validateForm()) {
                this.submit();
            }
        });

        function validateForm() {
            let isValid = true;
            const username = $('#username').val().trim();
            const email = $('#email').val().trim();
            const password = $('#password').val();
            const confirmPassword = $('#confirm_password').val();
            const gdprConsent = $('#gdpr_consent').is(':checked');

            // Username validation
            if (username.length < 3) {
                showError('#username', 'Username must be at least 3 characters long');
                isValid = false;
            } else {
                removeError('#username');
            }

            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showError('#email', 'Please enter a valid email address');
                isValid = false;
            } else {
                removeError('#email');
            }

            // Password validation
            if (password.length < 8) {
                showError('#password', 'Password must be at least 8 characters long');
                isValid = false;
            } else {
                removeError('#password');
            }

            // Confirm password validation
            if (password !== confirmPassword) {
                showError('#confirm_password', 'Passwords do not match');
                isValid = false;
            } else {
                removeError('#confirm_password');
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
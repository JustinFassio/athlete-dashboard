<?php
/**
 * Athlete Dashboard Authentication
 *
 * @package AthleteDashboard
 */

class Athlete_Dashboard_Auth {

    public function __construct() {
        add_action('admin_post_nopriv_athlete_dashboard_register', array($this, 'handle_registration'));
        add_action('admin_post_nopriv_athlete_dashboard_login', array($this, 'handle_login'));
        add_action('wp_login', array($this, 'redirect_after_login'), 10, 2);
        add_action('init', array($this, 'handle_email_verification'));
    }

    /**
     * Handle user registration.
     */
    public function handle_registration() {
        if (!isset($_POST['athlete_dashboard_register_nonce_field']) 
            || !wp_verify_nonce($_POST['athlete_dashboard_register_nonce_field'], 'athlete_dashboard_register_nonce')) {
            wp_die('Invalid nonce specified', 'Error', array('response' => 403));
        }

        $username = sanitize_user($_POST['username']);
        $email = sanitize_email($_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        $errors = array();

        if (empty($username)) {
            $errors['username'] = __('Username is required.', 'athlete-dashboard');
        }

        if (empty($email)) {
            $errors['email'] = __('Email address is required.', 'athlete-dashboard');
        } elseif (!is_email($email)) {
            $errors['email'] = __('Invalid email address.', 'athlete-dashboard');
        }

        if (empty($password)) {
            $errors['password'] = __('Password is required.', 'athlete-dashboard');
        } elseif (strlen($password) < 8) {
            $errors['password'] = __('Password must be at least 8 characters long.', 'athlete-dashboard');
        }

        if ($password !== $confirm_password) {
            $errors['confirm_password'] = __('Passwords do not match.', 'athlete-dashboard');
        }

        if (!isset($_POST['gdpr_consent'])) {
            $errors['gdpr_consent'] = __('You must agree to the Privacy Policy.', 'athlete-dashboard');
        }

        if (!empty($errors)) {
            $error_messages = implode('|', $errors);
            wp_safe_redirect(add_query_arg('register_errors', urlencode($error_messages), wp_get_referer()));
            exit;
        }

        if (username_exists($username)) {
            wp_safe_redirect(add_query_arg('register_errors', urlencode(__('Username already exists.', 'athlete-dashboard')), wp_get_referer()));
            exit;
        }

        if (email_exists($email)) {
            wp_safe_redirect(add_query_arg('register_errors', urlencode(__('Email address already exists.', 'athlete-dashboard')), wp_get_referer()));
            exit;
        }

        // Create user
        $user_id = wp_create_user($username, $password, $email);

        if (is_wp_error($user_id)) {
            wp_redirect(add_query_arg('register', 'failed', wp_get_referer()));
            exit;
        }

        // Set user role
        $user = new WP_User($user_id);
        $user->set_role('athlete');

        // Send email verification
        $this->send_verification_email($user_id);

        // Log the user in
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id);

        // Redirect to athlete dashboard
        wp_redirect(home_url('/athlete-dashboard/'));
        exit;
    }

    /**
     * Send verification email to the user.
     *
     * @param int $user_id The user ID.
     */
    private function send_verification_email($user_id) {
        $user = get_user_by('id', $user_id);
        $code = wp_generate_password(20, false);

        update_user_meta($user_id, 'email_verification_code', $code);

        $subject = __('Verify your email for Athlete Dashboard', 'athlete-dashboard');
        $message = sprintf(
            __('Hello %s,\n\nPlease click the following link to verify your email:\n\n%s\n\nThanks!', 'athlete-dashboard'),
            $user->display_name,
            add_query_arg(array('action' => 'verify_email', 'code' => $code), home_url())
        );

        wp_mail($user->user_email, $subject, $message);
    }

    /**
     * Redirect user after login based on their role.
     *
     * @param string $user_login Username of the user logging in.
     * @param WP_User $user WP_User object of the logged-in user.
     */
    public function redirect_after_login($user_login, $user) {
        if (in_array('athlete', (array) $user->roles)) {
            wp_redirect(home_url('/athlete-dashboard/'));
            exit;
        }
    }

    /**
     * Handle email verification process.
     */
    public function handle_email_verification() {
        if (isset($_GET['action']) && $_GET['action'] === 'verify_email' && isset($_GET['code'])) {
            $verification_code = sanitize_text_field($_GET['code']);
            $user = $this->get_user_by_verification_code($verification_code);
            if ($user) {
                update_user_meta($user->ID, 'email_verified', true);
                delete_user_meta($user->ID, 'email_verification_code');
                wp_safe_redirect(home_url('/login?verified=1'));
                exit;
            } else {
                wp_safe_redirect(home_url('/login?verified=0'));
                exit;
            }
        }
    }

    /**
     * Get user by verification code.
     *
     * @param string $code The verification code.
     * @return WP_User|false The user object if found, false otherwise.
     */
    private function get_user_by_verification_code($code) {
        $users = get_users(array(
            'meta_key' => 'email_verification_code',
            'meta_value' => $code,
            'number' => 1,
        ));
        return !empty($users) ? $users[0] : false;
    }

    public function login_redirect($redirect_to, $request, $user) {
        if (isset($user->roles) && is_array($user->roles)) {
            if (in_array('athlete', $user->roles)) {
                return home_url('/athlete-dashboard/');
            }
        }
        return $redirect_to;
    }

        /**
     * Handle user login.
     */
    public function handle_login() {
        if (!isset($_POST['athlete_dashboard_login_nonce_field']) 
            || !wp_verify_nonce($_POST['athlete_dashboard_login_nonce_field'], 'athlete_dashboard_login_nonce')) {
            wp_die('Invalid nonce specified', 'Error', array('response' => 403));
        }

        $user_login = sanitize_user($_POST['user_login']);
        $user_password = $_POST['user_pass'];
        $remember = isset($_POST['rememberme']);

        $errors = array();

        if (empty($user_login)) {
            $errors['user_login'] = __('Username or email is required.', 'athlete-dashboard');
        }

        if (empty($user_password)) {
            $errors['user_pass'] = __('Password is required.', 'athlete-dashboard');
        }

        if (!isset($_POST['gdpr_consent'])) {
            $errors['gdpr_consent'] = __('You must agree to the Privacy Policy.', 'athlete-dashboard');
        }

        if (!empty($errors)) {
            $error_messages = implode('|', $errors);
            wp_safe_redirect(add_query_arg('login_errors', urlencode($error_messages), wp_get_referer()));
            exit;
        }

        $user = wp_signon(array(
            'user_login' => $user_login,
            'user_password' => $user_password,
            'remember' => $remember
        ), is_ssl());

        if (is_wp_error($user)) {
            $error = $user->get_error_message();
            wp_safe_redirect(add_query_arg('login_errors', urlencode($error), wp_get_referer()));
            exit;
        }

        // Check if email is verified
        $email_verified = get_user_meta($user->ID, 'email_verified', true);
        if (!$email_verified) {
            wp_logout();
            wp_safe_redirect(add_query_arg('login_errors', urlencode(__('Please verify your email before logging in.', 'athlete-dashboard')), wp_get_referer()));
            exit;
        }

        wp_safe_redirect(home_url('/athlete-dashboard/'));
        exit;
    }
}

// Initialize the class
new Athlete_Dashboard_Auth();
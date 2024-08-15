<?php
/**
 * Enqueue scripts and styles for the Athlete Dashboard plugin.
 *
 * This function handles the loading of all necessary CSS and JavaScript files
 * for the Athlete Dashboard functionality.
 *
 * @package AthleteDashboard
 */

/**
 * Enqueue scripts and styles for the frontend.
 */
function athlete_dashboard_enqueue_assets() {
    error_log('Athlete Dashboard: Enqueue function called');
    // Enqueue the base stylesheet
    wp_enqueue_style(
        'athlete-dashboard-base-style',
        plugins_url('assets/css/base.css', dirname(__FILE__)),
        array(),
        ATHLETE_DASHBOARD_VERSION
    );

    // Enqueue the authentication stylesheet (covers both registration and login styles)
    wp_enqueue_style(
        'athlete-dashboard-auth-style',
        plugins_url('assets/css/auth.css', dirname(__FILE__)),
        array('athlete-dashboard-base-style'),
        ATHLETE_DASHBOARD_VERSION
    );

    // Enqueue the registration validation javascript
    wp_enqueue_script(
        'athlete-dashboard-registration-validation',
        plugins_url('assets/js/registration-validation.js', dirname(__FILE__)),
        array('jquery'),
        ATHLETE_DASHBOARD_VERSION,
        true
    );

    // Enqueue the login validation javascript
    wp_enqueue_script(
        'athlete-dashboard-login-validation',
        plugins_url('assets/js/login-validation.js', dirname(__FILE__)),
        array('jquery'),
        ATHLETE_DASHBOARD_VERSION,
        true
    );

    // Enqueue the Athlete Dashboard stylesheet
    wp_enqueue_style(
        'athlete-dashboard-style',
        plugins_url('assets/css/athlete-dashboard.css', dirname(__FILE__)),
        array('athlete-dashboard-base-style'),
        ATHLETE_DASHBOARD_VERSION
    );

    // Enqueue the Trailhead-specific stylesheet
    wp_enqueue_style(
        'athlete-dashboard-trailhead-style',
        plugins_url('assets/css/trailhead.css', dirname(__FILE__)),
        array('athlete-dashboard-base-style'),
        ATHLETE_DASHBOARD_VERSION
    );

    // Enqueue the main JavaScript file
    wp_enqueue_script(
        'athlete-dashboard-main-script',
        plugins_url('assets/js/main.js', dirname(__FILE__)),
        array('jquery'),
        ATHLETE_DASHBOARD_VERSION,
        true
    );
}

// Hook the enqueue function to the wp_enqueue_scripts action
add_action('wp_enqueue_scripts', 'athlete_dashboard_enqueue_assets');

/**
 * Enqueue scripts and styles for the Athlete Dashboard admin pages.
 *
 * @param string $hook The current admin page.
 */
function athlete_dashboard_enqueue_admin_assets($hook) {
    // Only load on our plugin's admin pages
    if (strpos($hook, 'athlete-dashboard') === false) {
        return;
    }

    // Enqueue admin styles
    wp_enqueue_style(
        'athlete-dashboard-admin-style',
        plugins_url('assets/css/admin-styles.css', dirname(__FILE__)),
        array(),
        ATHLETE_DASHBOARD_VERSION
    );

    // Enqueue WordPress scripts
    wp_enqueue_script('wp-element');
    wp_enqueue_script('wp-components');
    wp_enqueue_script('wp-api-fetch');

    // Enqueue Chart.js
    wp_enqueue_script(
        'chartjs',
        'https://cdn.jsdelivr.net/npm/chart.js',
        array(),
        '3.7.1',
        true
    );

    // Enqueue our custom admin app script
    wp_enqueue_script(
        'athlete-dashboard-admin-app',
        plugins_url('assets/js/athlete-admin-app.js', dirname(__FILE__)),
        array('wp-element', 'wp-components', 'wp-api-fetch'),
        ATHLETE_DASHBOARD_VERSION,
        true
    );

    // Enqueue our main admin script
    wp_enqueue_script(
        'athlete-dashboard-admin-script',
        plugins_url('assets/js/admin.js', dirname(__FILE__)),
        array('jquery', 'wp-api-fetch', 'athlete-dashboard-admin-app'),
        ATHLETE_DASHBOARD_VERSION,
        true
    );

    // Localize the admin script
    wp_localize_script(
        'athlete-dashboard-admin-script',
        'athleteDashboardAdmin',
        array(
            'nonce' => wp_create_nonce('athlete_dashboard_admin_nonce')
        )
    );

    // Enqueue Dashicons for admin area
    wp_enqueue_style('dashicons');
}

// Hook the admin enqueue function to the admin_enqueue_scripts action
add_action('admin_enqueue_scripts', 'athlete_dashboard_enqueue_admin_assets');
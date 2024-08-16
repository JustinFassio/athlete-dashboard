<?php
/**
 * Plugin Name: Athlete Dashboard
 * Plugin URI: fitcopilot.ai
 * Description: A comprehensive dashboard for athletes to track their progress, manage workouts, and interact with trainers.
 * Version: 1.0.0
 * Author: FitCopilot
 * Author URI: fitcopilot.ai
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: athlete-dashboard
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Define plugin constants
define('AD_VERSION', '1.0.0');
define('AD_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('AD_PLUGIN_URL', plugin_dir_url(__FILE__));
define('AD_TRAILHEAD_SLUG', 'athlete-dashboard');
define('AD_API_URL', 'https://your-ai-web-app-api-url.com/get-trailhead-content');

// Define the constant causing the error
define('ATHLETE_DASHBOARD_VERSION', AD_VERSION);

// Include necessary files
require_once AD_PLUGIN_DIR . 'includes/class-athlete-dashboard.php';
require_once AD_PLUGIN_DIR . 'includes/class-athlete-dashboard-roles.php';
require_once AD_PLUGIN_DIR . 'includes/class-athlete-dashboard-auth.php';
require_once AD_PLUGIN_DIR . 'includes/class-athlete-dashboard-admin.php';
require_once AD_PLUGIN_DIR . 'includes/class-athlete-dashboard-post-types.php';
require_once AD_PLUGIN_DIR . 'includes/class-athlete-dashboard-check-in.php';
require_once AD_PLUGIN_DIR . 'includes/class-athlete-dashboard-admin-page.php';
require_once AD_PLUGIN_DIR . 'includes/class-athlete-dashboard-user-profile.php';
require_once AD_PLUGIN_DIR . 'includes/class-athlete-dashboard-api.php';
require_once AD_PLUGIN_DIR . 'functions/enqueue-scripts.php';
require_once AD_PLUGIN_DIR . 'functions/shortcodes.php';


/**
 * Begins execution of the plugin.
 */
function run_athlete_dashboard() {
    $plugin = new Athlete_Dashboard();
    $plugin->run();

    // Initialize the admin class
    Athlete_Dashboard_Admin::get_instance();

    // Initialize custom post types
    new Athlete_Dashboard_Post_Types();

    // Initialize check-in functionality
    new Athlete_Dashboard_Check_In();

    // Initialize admin page
    new Athlete_Dashboard_Admin_Page();

    // Initialize user profile modifications
    new Athlete_Dashboard_User_Profile();

    // Initialize API
    new Athlete_Dashboard_API();

    // Initialize roles
    new Athlete_Dashboard_Roles();
}
run_athlete_dashboard();

// Activation hook
register_activation_hook(__FILE__, 'activate_athlete_dashboard');

/**
 * Activation hook for the plugin.
 */
function activate_athlete_dashboard() {
    // Ensure roles exist
    $roles = new Athlete_Dashboard_Roles();
    $roles->ensure_roles_exist();
    
    // Create necessary pages
    ad_create_registration_page();
    ad_create_login_page();
    ad_create_athlete_dashboard_page();

    // Create sample offers
    Athlete_Dashboard_Post_Types::create_sample_offers();

    // Flush rewrite rules
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'activate_athlete_dashboard');

/**
 * Create the registration page.
 *
 * This function creates a new page for user registration and sets its template.
 */
function ad_create_registration_page() {
    $registration_page = array(
        'post_title'    => __('Register', 'athlete-dashboard'),
        'post_content'  => '[athlete_dashboard_register]',
        'post_status'   => 'publish',
        'post_author'   => 1,
        'post_type'     => 'page',
    );

    // Insert the page into the database
    $page_id = wp_insert_post($registration_page);

    if ($page_id) {
        // Set the template for the page
        update_post_meta($page_id, '_wp_page_template', 'templates/register.php');
        // Store the page ID in options
        update_option('athlete_dashboard_register_page_id', $page_id);
    }
}

/**
 * Create the login page.
 *
 * This function creates a new page for user login and sets its template.
 */
function ad_create_login_page() {
    $login_page = array(
        'post_title'    => __('Login', 'athlete-dashboard'),
        'post_content'  => '[athlete_dashboard_login]',
        'post_status'   => 'publish',
        'post_author'   => 1,
        'post_type'     => 'page',
    );
    $page_id = wp_insert_post($login_page);
    if ($page_id) {
        update_post_meta($page_id, '_wp_page_template', 'templates/login.php');
        update_option('athlete_dashboard_login_page_id', $page_id);
    }
}

/**
 * Create the athlete dashboard page.
 *
 * This function creates a new page for the athlete dashboard and sets its template.
 */
function ad_create_athlete_dashboard_page() {
    $dashboard_page = array(
        'post_title'    => __('Athlete Dashboard', 'athlete-dashboard'),
        'post_content'  => '',
        'post_status'   => 'publish',
        'post_author'   => 1,
        'post_type'     => 'page',
    );

    // Insert the page into the database
    $page_id = wp_insert_post($dashboard_page);

    if ($page_id) {
        // Set the template for the page
        update_post_meta($page_id, '_wp_page_template', 'templates/dashboard/athlete-dashboard.php');
        // Store the page ID in options
        update_option('athlete_dashboard_page_id', $page_id);
    }

    add_action('wp_footer', function() {
        echo "<!-- Athlete Dashboard plugin is active -->";
    });

    // Assuming your Admin class is in includes/class-athlete-dashboard-admin.php
$admin = new Athlete_Dashboard_Admin();
if ($admin instanceof Athlete_Dashboard_Admin) {
    echo "Autoloading is working correctly!";
} else {
    echo "There might be an issue with autoloading.";
}
}
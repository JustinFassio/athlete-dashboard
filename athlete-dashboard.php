<?php
/**
 * Plugin Name: Athlete Dashboard
 * Plugin URI: [PLACEHOLDER: YOUR_PLUGIN_WEBSITE]
 * Description: A comprehensive dashboard for athletes to track their progress, manage workouts, and interact with trainers.
 * Version: 1.0.0
 * Author: [PLACEHOLDER: YOUR_NAME]
 * Author URI: [PLACEHOLDER: YOUR_WEBSITE]
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

// Include the main Athlete Dashboard class
require_once AD_PLUGIN_DIR . 'includes/class-athlete-dashboard.php';

// Initialize the plugin
function run_athlete_dashboard() {
    $plugin = new Athlete_Dashboard();
    $plugin->run();
}
run_athlete_dashboard();
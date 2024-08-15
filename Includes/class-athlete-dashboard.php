<?php
/**
 * The main plugin class
 *
 * @package AthleteDashboard
 */

class Athlete_Dashboard {
    private $trailhead_post_type;

    /**
     * Initialize the class and set its properties.
     */
    public function __construct() {
        $this->load_dependencies();
    }

    /**
     * Load the required dependencies for this plugin.
     */
    private function load_dependencies() {
        require_once AD_PLUGIN_DIR . 'includes/class-trailhead-post-type.php';
        $this->trailhead_post_type = new Trailhead_Post_Type();
    }

    /**
     * Register all of the hooks related to the plugin functionality.
     */
    public function run() {
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_menu', array($this, 'register_trailhead_page'));
        add_filter('wp_nav_menu_items', array($this, 'add_auth_menu_items'), 10, 2);
        add_action('wp_ajax_save_athlete_dashboard_settings', array($this, 'ajax_save_settings'));
    }

    /**
     * Initialize plugin components.
     */
    public function init() {
        require_once AD_PLUGIN_DIR . 'functions/shortcodes.php';
    }

    /**
     * Enqueue scripts and styles for the plugin.
     */
    public function enqueue_scripts() {
        require_once AD_PLUGIN_DIR . 'functions/enqueue-scripts.php';
    }

    /**
     * Register the Trailhead page in the admin menu.
     */
    public function register_trailhead_page() {
        add_menu_page(
            __('Athlete Trailhead', 'athlete-dashboard'),
            __('Athlete Dashboard', 'athlete-dashboard'),
            'read',
            'edit.php?post_type=trailhead',
            '',
            'dashicons-chart-area',
            30
        );
    }

    /**
     * Render the Trailhead page.
     */
    public function render_trailhead_page() {
        require_once AD_PLUGIN_DIR . 'templates/dashboard/trailhead.php';
    }

    public function display_recent_trailheads() {
        $args = array(
            'post_type' => 'trailhead',
            'posts_per_page' => 5,
            'orderby' => 'date',
            'order' => 'DESC'
        );
        $recent_trailheads = new WP_Query($args);
    
        if ($recent_trailheads->have_posts()) :
            echo '<ul>';
            while ($recent_trailheads->have_posts()) : $recent_trailheads->the_post();
                echo '<li><a href="' . get_edit_post_link() . '">' . get_the_title() . '</a></li>';
            endwhile;
            echo '</ul>';
        else :
            echo '<p>' . __('No recent Trailheads found.', 'athlete-dashboard') . '</p>';
        endif;
        wp_reset_postdata();
    }

    /**
     * Add registration and login menu items to the primary menu.
     *
     * This function adds both the registration and login links to the primary menu
     * for non-logged-in users. For logged-in users, it adds a logout link.
     *
     * @param string $items The HTML list content for the menu items.
     * @param object $args  An object containing wp_nav_menu() arguments.
     * @return string Modified menu items HTML.
     */
    public function add_auth_menu_items($items, $args) {
        if ($args->theme_location == 'primary-menu') {
            if (!is_user_logged_in()) {
                $register_page_id = get_option('athlete_dashboard_register_page_id');
                $login_page_id = get_option('athlete_dashboard_login_page_id');
                
                if ($register_page_id) {
                    $register_page_link = get_permalink($register_page_id);
                    $items .= '<li class="menu-item"><a href="' . esc_url($register_page_link) . '">' . esc_html__('Register', 'athlete-dashboard') . '</a></li>';
                }
                
                if ($login_page_id) {
                    $login_page_link = get_permalink($login_page_id);
                    $items .= '<li class="menu-item"><a href="' . esc_url($login_page_link) . '">' . esc_html__('Login', 'athlete-dashboard') . '</a></li>';
                }
            } else {
                $items .= '<li class="menu-item"><a href="' . esc_url(wp_logout_url(home_url())) . '">' . esc_html__('Logout', 'athlete-dashboard') . '</a></li>';
            }
        }
        return $items;
    }
}
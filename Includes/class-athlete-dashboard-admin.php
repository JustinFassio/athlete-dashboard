<?php
/**
 * Athlete Dashboard Admin
 *
 * @package AthleteDashboard
 */

class Athlete_Dashboard_Admin {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('admin_menu', array($this, 'register_admin_pages'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('wp_ajax_save_athlete_dashboard_settings', array($this, 'ajax_save_settings'));
    }

    public function register_admin_pages() {
        // Main menu item
        add_menu_page(
            __('Athlete Dashboard', 'athlete-dashboard'),
            __('Athlete Dashboard', 'athlete-dashboard'),
            'manage_options',
            'athlete-dashboard',
            array($this, 'render_dashboard_page'),
            'dashicons-chart-area',
            30
        );
    
        // Rename the first submenu item (which is a duplicate of the main menu item)
        add_submenu_page(
            'athlete-dashboard',
            __('Dashboard', 'athlete-dashboard'), // Page title
            __('Dashboard', 'athlete-dashboard'), // Menu title
            'manage_options',
            'athlete-dashboard',
            array($this, 'render_dashboard_page')
        );
    
        // Other submenus remain the same
        add_submenu_page(
            'athlete-dashboard',
            __('Trailheads', 'athlete-dashboard'),
            __('Trailheads', 'athlete-dashboard'),
            'manage_options',
            'edit.php?post_type=trailhead'
        );
    
        add_submenu_page(
            'athlete-dashboard',
            __('Settings', 'athlete-dashboard'),
            __('Settings', 'athlete-dashboard'),
            'manage_options',
            'athlete-dashboard-settings',
            array($this, 'render_settings_page')
        );
    }

    public function register_settings() {
        register_setting('athlete_dashboard_options', 'athlete_dashboard_options');
        
        add_settings_section(
            'athlete_dashboard_general',
            __('General Settings', 'athlete-dashboard'),
            array($this, 'render_general_section'),
            'athlete-dashboard-settings'
        );

        add_settings_field(
            'trial_period_length',
            __('Trial Period Length (days)', 'athlete-dashboard'),
            array($this, 'render_trial_period_length_field'),
            'athlete-dashboard-settings',
            'athlete_dashboard_general'
        );

        add_settings_field(
            'enable_trailhead',
            __('Enable Trailhead Feature', 'athlete-dashboard'),
            array($this, 'render_enable_trailhead_field'),
            'athlete-dashboard-settings',
            'athlete_dashboard_general'
        );

        add_settings_field(
            'api_key',
            __('API Key', 'athlete-dashboard'),
            array($this, 'render_api_key_field'),
            'athlete-dashboard-settings',
            'athlete_dashboard_general'
        );
    }

    public function render_general_section() {
        echo '<p>' . esc_html__('Configure general settings for the Athlete Dashboard.', 'athlete-dashboard') . '</p>';
    }

    public function render_trial_period_length_field() {
        $options = get_option('athlete_dashboard_options');
        $value = isset($options['trial_period_length']) ? $options['trial_period_length'] : '30';
        echo '<input type="number" id="trial_period_length" name="athlete_dashboard_options[trial_period_length]" value="' . esc_attr($value) . '" />';
        echo '<p class="description">' . esc_html__('Enter the number of days for the trial period', 'athlete-dashboard') . '</p>';
    }

    public function render_enable_trailhead_field() {
        $options = get_option('athlete_dashboard_options');
        $value = isset($options['enable_trailhead']) ? $options['enable_trailhead'] : '0';
        echo '<input type="checkbox" id="enable_trailhead" name="athlete_dashboard_options[enable_trailhead]" value="1" ' . checked(1, $value, false) . '/>';
        echo '<label for="enable_trailhead">' . esc_html__('Enable the Trailhead feature for athletes', 'athlete-dashboard') . '</label>';
    }

    public function render_api_key_field() {
        $options = get_option('athlete_dashboard_options');
        $value = isset($options['api_key']) ? $options['api_key'] : '';
        echo '<input type="text" id="api_key" name="athlete_dashboard_options[api_key]" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . esc_html__('Enter your API key for external integrations', 'athlete-dashboard') . '</p>';
    }

    public function render_dashboard_page() {
        // Calculate total athletes
        $total_athletes = count_users()['avail_roles']['athlete'] ?? 0;
        
        // Calculate new athletes in the last 7 days
        $new_athletes = count(get_users(array(
            'role' => 'athlete',
            'date_query' => array(
                'after' => '1 week ago'
            )
        )));
        
        // Calculate total trailheads
        $total_trailheads = wp_count_posts('trailhead')->publish;
    
        // Include the dashboard template
        include AD_PLUGIN_DIR . 'templates/dashboard/admin-dashboard.php';
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

    private function render_recent_activities() {
        $recent_trailheads = get_posts(array(
            'post_type' => 'trailhead',
            'posts_per_page' => 5,
            'orderby' => 'date',
            'order' => 'DESC'
        ));

        if (empty($recent_trailheads)) {
            echo '<p>' . esc_html__('No recent activities.', 'athlete-dashboard') . '</p>';
            return;
        }

        echo '<ul class="athlete-dashboard-recent-activities">';
        foreach ($recent_trailheads as $trailhead) {
            echo '<li>';
            echo '<strong>' . esc_html(get_the_title($trailhead)) . '</strong> - ';
            echo esc_html(get_the_date('', $trailhead));
            echo '</li>';
        }
        echo '</ul>';
    }

    public function render_settings_page() {
        echo '<div class="wrap">';
        echo '<h1>' . esc_html__('Athlete Dashboard Settings', 'athlete-dashboard') . '</h1>';
        echo '<form method="post" action="options.php">';
        
        settings_fields('athlete_dashboard_options');
        do_settings_sections('athlete-dashboard-settings');
        submit_button();

        echo '</form>';
        echo '</div>';
    }

}
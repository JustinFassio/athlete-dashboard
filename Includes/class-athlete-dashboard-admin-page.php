<?php
/**
 * Athlete Admin Page for Athlete Dashboard
 *
 * @package AthleteDashboard
 */

class Athlete_Dashboard_Admin_Page {

    /**
     * Constructor.
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
    }

    /**
     * Add admin menu item.
     */
    public function add_admin_menu() {
        $page_hook_suffix = add_submenu_page(
            'athlete-dashboard', // Parent slug
            __('Athlete Admin', 'athlete-dashboard'),
            __('Athlete Admin', 'athlete-dashboard'),
            'manage_options',
            'athlete-dashboard-admin',
            array($this, 'render_admin_page')
        );
    }

    /**
     * Render the admin page.
     */
    public function render_admin_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'athlete-dashboard'));
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <div id="athlete-admin-app"></div>
        </div>
        <?php
    }

}
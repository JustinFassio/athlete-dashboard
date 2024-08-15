<?php
/**
 * User Profile Modifications for Athlete Dashboard
 *
 * @package AthleteDashboard
 */

class Athlete_Dashboard_User_Profile {

    public function __construct() {
        add_action('show_user_profile', array($this, 'add_athlete_dashboard_fields'));
        add_action('edit_user_profile', array($this, 'add_athlete_dashboard_fields'));
        add_action('personal_options_update', array($this, 'save_athlete_dashboard_fields'));
        add_action('edit_user_profile_update', array($this, 'save_athlete_dashboard_fields'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_profile_scripts'));
    }

    public function add_athlete_dashboard_fields($user) {
        $check_in = new Athlete_Dashboard_Check_In();
        $user_data = $check_in->get_user_check_in_data($user->ID);
        ?>
        <h3><?php _e('Athlete Dashboard Information', 'athlete-dashboard'); ?></h3>
        <table class="form-table">
            <tr>
                <th><label for="active_offer"><?php _e('Active Offer', 'athlete-dashboard'); ?></label></th>
                <td>
                    <?php echo esc_html($user_data['active_offer']); ?>
                </td>
            </tr>
            <tr>
                <th><label for="offer_start_date"><?php _e('Offer Start Date', 'athlete-dashboard'); ?></label></th>
                <td>
                    <?php echo esc_html($user_data['offer_start_date']); ?>
                </td>
            </tr>
            <tr>
                <th><label for="first_check_in_date"><?php _e('First Check-in Date', 'athlete-dashboard'); ?></label></th>
                <td>
                    <?php echo esc_html($user_data['first_check_in_date']); ?>
                </td>
            </tr>
            <tr>
                <th><label for="check_in_count"><?php _e('Check-in Count', 'athlete-dashboard'); ?></label></th>
                <td>
                    <?php echo esc_html($user_data['check_in_count']); ?>
                </td>
            </tr>
        </table>
        <button id="athlete-check-in-button" class="button button-primary"><?php _e('Check In', 'athlete-dashboard'); ?></button>
        <div id="check-in-history"></div>
        <?php
    }

    public function save_athlete_dashboard_fields($user_id) {
        if (!current_user_can('edit_user', $user_id)) {
            return false;
        }

        // We're not saving any fields here, but you could add logic to update user meta if needed
    }

    public function enqueue_profile_scripts() {
        if (is_user_logged_in() && is_account_page()) {
            wp_enqueue_script(
                'athlete-dashboard-profile',
                plugins_url('assets/js/profile.js', dirname(__FILE__)),
                array('jquery'),
                ATHLETE_DASHBOARD_VERSION,
                true
            );

            wp_localize_script('athlete-dashboard-profile', 'athleteDashboardProfile', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('athlete_dashboard_profile_nonce')
            ));
        }
    }
}
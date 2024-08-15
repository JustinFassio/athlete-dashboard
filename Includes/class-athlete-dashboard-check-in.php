<?php
/**
 * Check-in Functionality for Athlete Dashboard
 *
 * @package AthleteDashboard
 */

class Athlete_Dashboard_Check_In {

    public function __construct() {
        add_action('wp_ajax_athlete_dashboard_check_in', array($this, 'ajax_check_in'));
    }

    public function assign_offer_to_user($user_id, $offer_id) {
        // Check if the user exists
        $user = get_user_by('ID', $user_id);
        if (!$user) {
            error_log("Athlete Dashboard: User not found for ID: " . $user_id);
            return false;
        }
    
        // Check if the offer exists
        $offer = get_post($offer_id);
        if (!$offer || $offer->post_type !== 'ad_offer') {
            error_log("Athlete Dashboard: Offer not found for ID: " . $offer_id);
            return false;
        }
    
        // Assign the offer
        $result = update_user_meta($user_id, 'active_offer', $offer_id);
        if ($result === false) {
            error_log("Athlete Dashboard: Failed to update user meta for user ID: " . $user_id);
            return false;
        }
    
        // Update offer start date
        update_user_meta($user_id, 'offer_start_date', current_time('mysql'));
    
        // Reset check-in count
        update_user_meta($user_id, 'check_in_count', 0);
    
        // Remove first check-in date
        delete_user_meta($user_id, 'first_check_in_date');
    
        return true;
    }

    public function user_check_in($user_id) {
        $check_in_count = get_user_meta($user_id, 'check_in_count', true);
        $check_in_count = $check_in_count ? intval($check_in_count) + 1 : 1;
        
        error_log("Athlete Dashboard: Updating check-in count for user {$user_id} to {$check_in_count}");
        
        $update_result = update_user_meta($user_id, 'check_in_count', $check_in_count);
        
        if ($update_result === false) {
            error_log("Athlete Dashboard: Failed to update check-in count for user {$user_id}");
            return false;
        }
    
        if ($check_in_count == 1) {
            update_user_meta($user_id, 'first_check_in_date', current_time('mysql'));
        }
    
        $check_in_history = $this->get_check_in_history($user_id);
        $check_in_history[] = array(
            'date' => current_time('mysql'),
            'count' => $check_in_count
        );
        update_user_meta($user_id, 'check_in_history', $check_in_history);
    
        error_log("Athlete Dashboard: Check-in successful for user {$user_id}. New count: {$check_in_count}");
        return $check_in_count;
    }

    public function ajax_check_in() {
        check_ajax_referer('athlete_dashboard_check_in', 'nonce');

        $user_id = get_current_user_id();
        if (!$user_id) {
            wp_send_json_error('User not logged in');
        }

        $check_in_count = $this->user_check_in($user_id);
        wp_send_json_success(array('check_in_count' => $check_in_count));
    }

    public function get_user_check_in_data($user_id) {
        $active_offer = get_user_meta($user_id, 'active_offer', true);
        $offer_start_date = get_user_meta($user_id, 'offer_start_date', true);
        $first_check_in_date = get_user_meta($user_id, 'first_check_in_date', true);
        $check_in_count = get_user_meta($user_id, 'check_in_count', true);

        return array(
            'active_offer' => $active_offer,
            'offer_start_date' => $offer_start_date,
            'first_check_in_date' => $first_check_in_date,
            'check_in_count' => $check_in_count
        );
    }

    public function get_check_in_history($user_id) {
        $check_ins = get_user_meta($user_id, 'check_in_history', true);
        if (!$check_ins) {
            $check_ins = array();
        }
        return $check_ins;
    }
}
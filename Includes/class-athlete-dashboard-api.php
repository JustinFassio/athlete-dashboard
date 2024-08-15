<?php
/**
 * REST API Endpoints for Athlete Dashboard
 *
 * @package AthleteDashboard
 */

class Athlete_Dashboard_API {

    public function __construct() {
        add_action('rest_api_init', array($this, 'register_routes'));
    }

    public function register_routes() {
        register_rest_route('athlete-dashboard/v1', '/athletes', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_athletes'),
            'permission_callback' => array($this, 'admin_permissions_check'),
        ));

        register_rest_route('athlete-dashboard/v1', '/check-in', array(
            'methods' => 'POST',
            'callback' => array($this, 'check_in_athlete'),
            'permission_callback' => array($this, 'athlete_permissions_check'),
        ));

        register_rest_route('athlete-dashboard/v1', '/check-in-stats', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_check_in_stats'),
            'permission_callback' => array($this, 'admin_permissions_check'),
        ));

        register_rest_route('athlete-dashboard/v1', '/offers', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_offers'),
            'permission_callback' => '__return_true', // Public access
        ));

        register_rest_route('athlete-dashboard/v1', '/assign-offer', array(
            'methods' => 'POST',
            'callback' => array($this, 'assign_offer'),
            'permission_callback' => array($this, 'admin_permissions_check'),
        ));

        register_rest_route('athlete-dashboard/v1', '/athlete-check-in-history', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_athlete_check_in_history'),
            'permission_callback' => array($this, 'athlete_permissions_check'),
        ));
    }

    public function admin_permissions_check() {
        return current_user_can('manage_options');
    }

    public function athlete_permissions_check() {
        return current_user_can('read');
    }

    public function get_athletes() {
        $users = get_users(array('role' => 'athlete'));
        $athletes = array();

        foreach ($users as $user) {
            $check_in = new Athlete_Dashboard_Check_In();
            $user_data = $check_in->get_user_check_in_data($user->ID);

            $athletes[] = array(
                'id' => $user->ID,
                'name' => $user->display_name,
                'email' => $user->user_email,
                'active_offer' => $user_data['active_offer'],
                'check_in_count' => $user_data['check_in_count'],
            );
        }

        return $athletes;
    }

    public function check_in_athlete($request) {
        $user_id = get_current_user_id();
        error_log('Athlete Dashboard: Check-in attempt for user ID: ' . $user_id);
        
        $check_in = new Athlete_Dashboard_Check_In();
        $result = $check_in->user_check_in($user_id);
    
        if ($result) {
            error_log('Athlete Dashboard: Check-in successful. New count: ' . $result);
            return new WP_REST_Response(array('success' => true, 'check_in_count' => $result), 200);
        } else {
            error_log('Athlete Dashboard: Check-in failed for user ID: ' . $user_id);
            return new WP_Error('check_in_failed', __('Failed to check in athlete', 'athlete-dashboard'), array('status' => 500));
        }
    }

    public function get_check_in_stats() {
        $options = get_option('athlete_dashboard_options');
        $trial_period = isset($options['trial_period_length']) ? intval($options['trial_period_length']) : 30;

        $labels = range(1, $trial_period);
        $first_time_check_ins = array_fill(0, $trial_period, 0);
        $total_check_ins = array_fill(0, $trial_period, 0);

        $users = get_users(array('role' => 'athlete'));

        foreach ($users as $user) {
            $user_data = get_user_meta($user->ID);
            $offer_start_date = isset($user_data['offer_start_date'][0]) ? new DateTime($user_data['offer_start_date'][0]) : null;
            $first_check_in_date = isset($user_data['first_check_in_date'][0]) ? new DateTime($user_data['first_check_in_date'][0]) : null;
            $check_in_count = isset($user_data['check_in_count'][0]) ? intval($user_data['check_in_count'][0]) : 0;

            if ($offer_start_date && $first_check_in_date) {
                $days_until_first_check_in = $offer_start_date->diff($first_check_in_date)->days;
                if ($days_until_first_check_in < $trial_period) {
                    $first_time_check_ins[$days_until_first_check_in]++;
                }
            }

            if ($offer_start_date && $check_in_count > 0) {
                for ($i = 0; $i < $check_in_count && $i < $trial_period; $i++) {
                    $total_check_ins[$i]++;
                }
            }
        }

        return array(
            'labels' => $labels,
            'firstTimeCheckIns' => $first_time_check_ins,
            'totalCheckIns' => $total_check_ins,
        );
    }

    public function get_offers() {
        $offers = get_posts(array(
            'post_type' => 'ad_offer',
            'posts_per_page' => -1,
        ));

        $formatted_offers = array();
        foreach ($offers as $offer) {
            $formatted_offers[] = array(
                'id' => $offer->ID,
                'title' => $offer->post_title,
                'content' => $offer->post_content,
                'duration' => get_post_meta($offer->ID, 'duration', true),
                'price' => get_post_meta($offer->ID, 'price', true),
            );
        }

        return $formatted_offers;
    }

    public function assign_offer($request) {
        $athlete_id = $request->get_param('athlete_id');
        $offer_id = $request->get_param('offer_id');
    
        if (!$athlete_id || !$offer_id) {
            return new WP_Error('missing_params', __('Athlete ID and Offer ID are required', 'athlete-dashboard'), array('status' => 400));
        }
    
        $check_in = new Athlete_Dashboard_Check_In();
        $result = $check_in->assign_offer_to_user($athlete_id, $offer_id);
    
        if ($result) {
            return new WP_REST_Response(array('success' => true), 200);
        } else {
            return new WP_Error('assign_offer_failed', __('Failed to assign offer to athlete', 'athlete-dashboard'), array('status' => 500));
        }
    }

    public function get_athlete_check_in_history($request) {
        $user_id = get_current_user_id();
        $check_in = new Athlete_Dashboard_Check_In();
        $history = $check_in->get_check_in_history($user_id);

        if ($history) {
            return new WP_REST_Response($history, 200);
        } else {
            return new WP_Error('no_history', __('No check-in history found', 'athlete-dashboard'), array('status' => 404));
        }
    }
}
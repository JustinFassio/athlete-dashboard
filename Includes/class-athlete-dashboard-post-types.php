<?php
/**
 * Custom Post Types for Athlete Dashboard
 *
 * @package AthleteDashboard
 */

class Athlete_Dashboard_Post_Types {

    public function __construct() {
        add_action('init', array($this, 'register_custom_post_types'));
    }

    public function register_custom_post_types() {
        $this->register_offer_post_type();
        $this->register_workout_post_type();
    }

    private function register_offer_post_type() {
        $labels = array(
            'name'                  => _x('Offers', 'Post type general name', 'athlete-dashboard'),
            'singular_name'         => _x('Offer', 'Post type singular name', 'athlete-dashboard'),
            'menu_name'             => _x('Offers', 'Admin Menu text', 'athlete-dashboard'),
            'add_new'               => __('Add New', 'athlete-dashboard'),
            'add_new_item'          => __('Add New Offer', 'athlete-dashboard'),
            'edit_item'             => __('Edit Offer', 'athlete-dashboard'),
            'view_item'             => __('View Offer', 'athlete-dashboard'),
            'all_items'             => __('All Offers', 'athlete-dashboard'),
            'search_items'          => __('Search Offers', 'athlete-dashboard'),
            'not_found'             => __('No offers found.', 'athlete-dashboard'),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => 'athlete-dashboard',
            'query_var'          => true,
            'rewrite'            => array('slug' => 'offer'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array('title', 'editor', 'custom-fields'),
        );

        register_post_type('ad_offer', $args);
    }

    private function register_workout_post_type() {
        $labels = array(
            'name'                  => _x('Workouts', 'Post type general name', 'athlete-dashboard'),
            'singular_name'         => _x('Workout', 'Post type singular name', 'athlete-dashboard'),
            'menu_name'             => _x('Workouts', 'Admin Menu text', 'athlete-dashboard'),
            'add_new'               => __('Add New', 'athlete-dashboard'),
            'add_new_item'          => __('Add New Workout', 'athlete-dashboard'),
            'edit_item'             => __('Edit Workout', 'athlete-dashboard'),
            'view_item'             => __('View Workout', 'athlete-dashboard'),
            'all_items'             => __('All Workouts', 'athlete-dashboard'),
            'search_items'          => __('Search Workouts', 'athlete-dashboard'),
            'not_found'             => __('No workouts found.', 'athlete-dashboard'),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => 'athlete-dashboard',
            'query_var'          => true,
            'rewrite'            => array('slug' => 'workout'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array('title', 'editor', 'custom-fields'),
        );

        register_post_type('ad_workout', $args);
    }

        /**
     * Create sample offers if they don't exist.
     */
    public static function create_sample_offers() {
        if (get_option('athlete_dashboard_sample_offers_created')) {
            return; // Sample offers have already been created
        }

        $sample_offers = array(
            array(
                'title' => 'Free 7-Day Trial',
                'content' => 'Try our gym for free for 7 days!',
                'meta' => array(
                    'duration' => 7,
                    'price' => 0,
                )
            ),
            array(
                'title' => '1-Month Membership',
                'content' => 'Get full access to our gym for one month.',
                'meta' => array(
                    'duration' => 30,
                    'price' => 49.99,
                )
            ),
            array(
                'title' => '3-Month Membership',
                'content' => 'Save by signing up for a 3-month membership!',
                'meta' => array(
                    'duration' => 90,
                    'price' => 129.99,
                )
            ),
        );

        foreach ($sample_offers as $offer) {
            $post_id = wp_insert_post(array(
                'post_title' => $offer['title'],
                'post_content' => $offer['content'],
                'post_status' => 'publish',
                'post_type' => 'ad_offer',
            ));

            if ($post_id) {
                foreach ($offer['meta'] as $key => $value) {
                    update_post_meta($post_id, $key, $value);
                }
            }
        }

        update_option('athlete_dashboard_sample_offers_created', true);
    }
}
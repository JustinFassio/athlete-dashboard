<?php
/**
 * Trailhead Post Type
 *
 * @package AthleteDashboard
 */

class Trailhead_Post_Type {

    /**
     * Constructor.
     */
    public function __construct() {
        add_action('init', array($this, 'register_post_type'));
        add_action('save_post_trailhead', array($this, 'fetch_trailhead_content'), 10, 3);
    }

    /**
     * Register the Trailhead post type.
     */
    public function register_post_type() {
        $labels = array(
            'name'                  => _x('Trailheads', 'Post type general name', 'athlete-dashboard'),
            'singular_name'         => _x('Trailhead', 'Post type singular name', 'athlete-dashboard'),
            'menu_name'             => _x('Trailheads', 'Admin Menu text', 'athlete-dashboard'),
            'name_admin_bar'        => _x('Trailhead', 'Add New on Toolbar', 'athlete-dashboard'),
            'add_new'               => __('Add New', 'athlete-dashboard'),
            'add_new_item'          => __('Add New Trailhead', 'athlete-dashboard'),
            'new_item'              => __('New Trailhead', 'athlete-dashboard'),
            'edit_item'             => __('Edit Trailhead', 'athlete-dashboard'),
            'view_item'             => __('View Trailhead', 'athlete-dashboard'),
            'all_items'             => __('All Trailheads', 'athlete-dashboard'),
            'search_items'          => __('Search Trailheads', 'athlete-dashboard'),
            'parent_item_colon'     => __('Parent Trailheads:', 'athlete-dashboard'),
            'not_found'             => __('No trailheads found.', 'athlete-dashboard'),
            'not_found_in_trash'    => __('No trailheads found in Trash.', 'athlete-dashboard'),
            'featured_image'        => _x('Trailhead Cover Image', 'Overrides the "Featured Image" phrase for this post type. Added in 4.3', 'athlete-dashboard'),
            'set_featured_image'    => _x('Set cover image', 'Overrides the "Set featured image" phrase for this post type. Added in 4.3', 'athlete-dashboard'),
            'remove_featured_image' => _x('Remove cover image', 'Overrides the "Remove featured image" phrase for this post type. Added in 4.3', 'athlete-dashboard'),
            'use_featured_image'    => _x('Use as cover image', 'Overrides the "Use as featured image" phrase for this post type. Added in 4.3', 'athlete-dashboard'),
            'archives'              => _x('Trailhead archives', 'The post type archive label used in nav menus. Default "Post Archives". Added in 4.4', 'athlete-dashboard'),
            'insert_into_item'      => _x('Insert into trailhead', 'Overrides the "Insert into post"/"Insert into page" phrase (used when inserting media into a post). Added in 4.4', 'athlete-dashboard'),
            'uploaded_to_this_item' => _x('Uploaded to this trailhead', 'Overrides the "Uploaded to this post"/"Uploaded to this page" phrase (used when viewing media attached to a post). Added in 4.4', 'athlete-dashboard'),
            'filter_items_list'     => _x('Filter trailheads list', 'Screen reader text for the filter links heading on the post type listing screen. Default "Filter posts list"/"Filter pages list". Added in 4.4', 'athlete-dashboard'),
            'items_list_navigation' => _x('Trailheads list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default "Posts list navigation"/"Pages list navigation". Added in 4.4', 'athlete-dashboard'),
            'items_list'            => _x('Trailheads list', 'Screen reader text for the items list heading on the post type listing screen. Default "Posts list"/"Pages list". Added in 4.4', 'athlete-dashboard'),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => 'athlete-dashboard', // This line ensures it appears under Athlete Dashboard
            'query_var'          => true,
            'rewrite'            => array('slug' => 'trailhead'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
            'menu_icon'          => 'dashicons-location-alt',
        );
    
        register_post_type('trailhead', $args);
    }
    
    /**
     * Fetch Trailhead content from API and update the post.
     *
     * @param int     $post_id Post ID.
     * @param WP_Post $post    Post object.
     * @param bool    $update  Whether this is an existing post being updated or not.
     */
    public function fetch_trailhead_content($post_id, $post, $update) {
        // Only fetch content for new posts
        if (!$update) {
            $api_url = AD_API_URL;
            $response = wp_remote_get($api_url);
    
            if (!is_wp_error($response) && 200 === wp_remote_retrieve_response_code($response)) {
                $content = wp_remote_retrieve_body($response);
                
                // Update post content with API response
                wp_update_post(array(
                    'ID' => $post_id,
                    'post_content' => wp_kses_post($content)
                ));
    
                // Associate the post with the current user
                $current_user_id = get_current_user_id();
                if ($current_user_id) {
                    wp_update_post(array(
                        'ID' => $post_id,
                        'post_author' => $current_user_id
                    ));
                }
            }
        }
    }
}
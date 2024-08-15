<?php
/**
 * Athlete Dashboard Roles Management
 *
 * @package AthleteDashboard
 */

class Athlete_Dashboard_Roles {
    
    public function __construct() {
        add_action('init', array($this, 'ensure_roles_exist'));
    }

    /**
     * Ensure all custom roles exist.
     */
    public function ensure_roles_exist() {
        $this->create_athlete_role();
    }

    /**
     * Create the Athlete role if it doesn't exist.
     */
    private function create_athlete_role() {
        if (!get_role('athlete')) {
            add_role(
                'athlete',
                __('Athlete', 'athlete-dashboard'),
                array(
                    'read' => true,
                    'edit_posts' => false,
                    'delete_posts' => false,
                    'publish_posts' => false,
                    'upload_files' => true,
                )
            );
        }
    }

    /**
     * Check if a user has the Athlete role.
     *
     * @param int $user_id The user ID to check.
     * @return bool True if the user has the Athlete role, false otherwise.
     */
    public function user_has_athlete_role($user_id) {
        $user = get_userdata($user_id);
        return $user && in_array('athlete', (array) $user->roles);
    }

    /**
     * Get all capabilities of the Athlete role.
     *
     * @return array An array of capabilities.
     */
    public function get_athlete_capabilities() {
        $role = get_role('athlete');
        return $role ? $role->capabilities : array();
    }
}
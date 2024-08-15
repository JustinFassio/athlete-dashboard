<?php
/**
 * Template for the Trailhead (Overview) section
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>

<div class="athlete-dashboard-section trailhead">
    <h2><?php esc_html_e('Trailhead', 'athlete-dashboard'); ?></h2>
    
    <div class="trailhead-summary">
        <h3><?php esc_html_e('Your Fitness Journey at a Glance', 'athlete-dashboard'); ?></h3>
        <?php echo do_shortcode('[athlete_recent_progress]'); ?>
    </div>

    <div class="trailhead-quick-actions">
        <h3><?php esc_html_e('Quick Actions', 'athlete-dashboard'); ?></h3>
        <a href="#" class="trailhead-action-button log-workout"><?php esc_html_e('Log Workout', 'athlete-dashboard'); ?></a>
        <a href="#" class="trailhead-action-button log-meal"><?php esc_html_e('Log Meal', 'athlete-dashboard'); ?></a>
        <a href="#" class="trailhead-action-button view-progress"><?php esc_html_e('View Progress', 'athlete-dashboard'); ?></a>
    </div>

    <div class="trailhead-upcoming">
        <h3><?php esc_html_e('Upcoming', 'athlete-dashboard'); ?></h3>
        <?php echo do_shortcode('[athlete_upcoming_events]'); ?>
    </div>
</div>
<?php
/**
 * Template Name: Athlete Dashboard
 *
 * @package AthleteDashboard
 */

get_header();

// Ensure only logged-in athletes can access this page
if (!is_user_logged_in() || !in_array('athlete', wp_get_current_user()->roles)) {
    wp_redirect(home_url());
    exit;
}

$current_user = wp_get_current_user();
?>

<div class="athlete-dashboard">
    <h1><?php echo esc_html(sprintf(__('Welcome, %s!', 'athlete-dashboard'), $current_user->display_name)); ?></h1>

    <section class="trailhead-section">
        <h2><?php esc_html_e('Your Trailhead', 'athlete-dashboard'); ?></h2>
        <?php
        $trailhead_query = new WP_Query(array(
            'post_type' => 'trailhead',
            'posts_per_page' => 1,
            'author' => $current_user->ID,
        ));

        if ($trailhead_query->have_posts()) :
            while ($trailhead_query->have_posts()) : $trailhead_query->the_post();
                ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <h3><?php the_title(); ?></h3>
                    <div class="trailhead-content">
                        <?php the_content(); ?>
                    </div>
                </article>
                <?php
            endwhile;
            wp_reset_postdata();
        else :
            echo '<p>' . esc_html__('No Trailhead content available yet.', 'athlete-dashboard') . '</p>';
        endif;
        ?>
    </section>

    // Trailhead
<div id="recent-trailheads" class="nav-tab-content" style="display: none;">
    <h3><?php _e('Recent Trailheads', 'athlete-dashboard'); ?></h3>
    <?php $this->display_recent_trailheads(); ?>
</div>

    <!-- Add more sections for other features here -->

</div>

<?php
get_footer();
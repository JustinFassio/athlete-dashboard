<?php
function athlete_dashboard_register_shortcode() {
    ob_start();
    include(AD_PLUGIN_DIR . 'templates/register.php');
    return ob_get_clean();
}
add_shortcode('athlete_dashboard_register', 'athlete_dashboard_register_shortcode');

function athlete_dashboard_login_shortcode() {
    ob_start();
    include(AD_PLUGIN_DIR . 'templates/login.php');
    return ob_get_clean();
}
add_shortcode('athlete_dashboard_login', 'athlete_dashboard_login_shortcode');

function athlete_recent_progress_shortcode() {
    // Placeholder for recent progress data
    $progress_data = array(
        'workouts' => 5,
        'calories_burned' => 2500,
        'miles_run' => 15.5
    );

    ob_start();
    ?>
    <div class="athlete-recent-progress">
        <div class="progress-item">
            <span class="progress-label"><?php esc_html_e('Workouts', 'athlete-dashboard'); ?></span>
            <span class="progress-value"><?php echo esc_html($progress_data['workouts']); ?></span>
        </div>
        <div class="progress-item">
            <span class="progress-label"><?php esc_html_e('Calories Burned', 'athlete-dashboard'); ?></span>
            <span class="progress-value"><?php echo esc_html($progress_data['calories_burned']); ?></span>
        </div>
        <div class="progress-item">
            <span class="progress-label"><?php esc_html_e('Miles Run', 'athlete-dashboard'); ?></span>
            <span class="progress-value"><?php echo esc_html($progress_data['miles_run']); ?></span>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('athlete_recent_progress', 'athlete_recent_progress_shortcode');

function athlete_upcoming_events_shortcode() {
    // Placeholder for upcoming events data
    $events = array(
        array('title' => 'Morning Run', 'date' => '2023-05-15 07:00:00'),
        array('title' => 'Strength Training', 'date' => '2023-05-16 18:30:00'),
        array('title' => 'Yoga Session', 'date' => '2023-05-17 19:00:00')
    );

    ob_start();
    ?>
    <div class="athlete-upcoming-events">
        <?php foreach ($events as $event) : ?>
            <div class="event-item">
                <span class="event-title"><?php echo esc_html($event['title']); ?></span>
                <span class="event-date"><?php echo esc_html(date_i18n('F j, Y g:i a', strtotime($event['date']))); ?></span>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('athlete_upcoming_events', 'athlete_upcoming_events_shortcode');

function display_trailhead_posts_shortcode($atts) {
    $args = array(
        'post_type' => 'trailhead',
        'posts_per_page' => 5,
        'orderby' => 'date',
        'order' => 'DESC'
    );

    $trailhead_query = new WP_Query($args);

    ob_start();

    if ($trailhead_query->have_posts()) :
        while ($trailhead_query->have_posts()) : $trailhead_query->the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="entry-header">
                    <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                </header>
                <div class="entry-content">
                    <?php the_excerpt(); ?>
                </div>
            </article>
            <?php
        endwhile;
        wp_reset_postdata();
    else :
        echo 'No Trailhead posts found.';
    endif;

    return ob_get_clean();
}
add_shortcode('display_trailhead_posts', 'display_trailhead_posts_shortcode');
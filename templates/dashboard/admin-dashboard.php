<div class="wrap athlete-dashboard-admin">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="dashboard-cards">
        <div class="card">
            <h2><?php _e('New Athletes', 'athlete-dashboard'); ?></h2>
            <p class="card-value"><?php echo esc_html($new_athletes); ?></p>
            <p class="card-description"><?php _e('In the last 7 days', 'athlete-dashboard'); ?></p>
        </div>
        <div class="card">
            <h2><?php _e('Total Athletes', 'athlete-dashboard'); ?></h2>
            <p class="card-value"><?php echo esc_html($total_athletes); ?></p>
            <p class="card-description"><?php _e('Active profiles', 'athlete-dashboard'); ?></p>
        </div>
        <div class="card">
            <h2><?php _e('Total Trailheads', 'athlete-dashboard'); ?></h2>
            <p class="card-value"><?php echo esc_html($total_trailheads); ?></p>
            <p class="card-description"><?php _e('Available paths', 'athlete-dashboard'); ?></p>
        </div>
    </div>

    <div class="dashboard-charts">
        <div class="dashboard-graph card">
            <h2><?php _e('Athlete Registrations', 'athlete-dashboard'); ?></h2>
            <canvas id="athlete-registration-chart"></canvas>
        </div>

        <div class="dashboard-graph card">
            <h2><?php _e('Athlete Check-Ins', 'athlete-dashboard'); ?></h2>
            <canvas id="athlete-check-ins-chart"></canvas>
        </div>
    </div>

    <div class="dashboard-recent-activities card">
        <h2><?php _e('Recent Activities', 'athlete-dashboard'); ?></h2>
        <?php $this->display_recent_trailheads(); ?>
    </div>
</div>
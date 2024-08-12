<?php

function athlete_dashboard_enqueue_scripts() {
    wp_enqueue_style('athlete-dashboard-style', AD_PLUGIN_URL . 'assets/css/base.css', array(), AD_VERSION);
    wp_enqueue_script('athlete-dashboard-script', AD_PLUGIN_URL . 'assets/js/main.js', array('jquery'), AD_VERSION, true);
}
add_action('wp_enqueue_scripts', 'athlete_dashboard_enqueue_scripts');
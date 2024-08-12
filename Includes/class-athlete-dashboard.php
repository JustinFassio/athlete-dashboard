<?php

class Athlete_Dashboard {
    public function __construct() {
        // Constructor
    }

    public function run() {
        // Hook into WordPress
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    public function init() {
        // Initialize plugin components
    }

    public function enqueue_scripts() {
        require_once AD_PLUGIN_DIR . 'functions/enqueue-scripts.php';
    }
}
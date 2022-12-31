<?php

if (!defined('ABSPATH')) {
    exit;
}

define('THEME_VERSION', time());
define('VITE_SERVER', 'http://localhost:5173');
define('DIST_FOLDER', 'build');

require get_stylesheet_directory() . '/inc/vite.php';
// Front assets
vite_enqueue_script('assets/js/front.js', 'wp_enqueue_scripts', 'wp_footer');

// Editor assets
vite_enqueue_script('assets/js/editor.js', 'enqueue_block_editor_assets');

// Admin assets
vite_enqueue_script('assets/js/admin.js', 'admin_enqueue_scripts', 'admin_footer');
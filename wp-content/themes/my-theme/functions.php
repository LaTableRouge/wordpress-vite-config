<?php

if (!defined('ABSPATH')) {
    exit;
}

define('THEME_VERSION', time());

require get_stylesheet_directory() . '/inc/vite.php';
// Front assets
vite_enqueue_script('assets/js/scripts.js', 'wp_footer', 'wp_enqueue_scripts');
vite_enqueue_style('assets/scss/styles.scss', 'wp_head', 'wp_enqueue_scripts');
// Admin assets
vite_enqueue_script('assets/js/wp-admin.js', 'admin_footer', 'admin_enqueue_scripts');
vite_enqueue_style('assets/scss/wp-admin.scss', 'admin_head', 'admin_enqueue_scripts');
// Editor assets (build to see the css in editor)
vite_enqueue_style_editor('assets/scss/styles-editor.scss', 'after_setup_theme');
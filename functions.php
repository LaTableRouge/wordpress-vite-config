<?php

if (!defined('ABSPATH')) {
    exit;
}

define('IS_VITE_DEVELOPMENT', true);

require get_template_directory() . '/inc/vite.php';
// Front assets
yourprefix_vite_enqueue_script('/src/scripts/parts.js', 'wp_enqueue_scripts', 'wp_footer', false, 'module');
yourprefix_vite_enqueue_script('/src/scripts/front.js', 'wp_enqueue_scripts', 'wp_footer', false, '');

// Admin assets
yourprefix_vite_enqueue_script('/src/scripts/admin.js', 'admin_enqueue_scripts', 'admin_footer');

// Editor assets
yourprefix_vite_enqueue_script('/src/scripts/editor.js', 'enqueue_block_editor_assets');

<?php

if (!defined('ABSPATH')) {
    exit;
}

define('VITE_SERVER', 'http://localhost:5173');
define('DIST_FOLDER', 'build');
define('DIST_URI', get_stylesheet_directory_uri() . '/' . DIST_FOLDER);
define('DIST_PATH', get_stylesheet_directory() . '/' . DIST_FOLDER);

function vite_fetch_asset_from_manifest($fileThemePath, $assetType) {
    $returnedArray = [];

    $fileName = basename($fileThemePath);
    $fileNameWithoutExtension = substr($fileName, 0, strrpos($fileName, '.'));

    // Use manifest json to know which asset to enqueue
    if (file_exists(DIST_PATH . '/manifest.json')) {
        $manifest = json_decode(file_get_contents(DIST_PATH . '/manifest.json'), true);

        if (is_array($manifest)) {
            $manifest_keys = array_keys($manifest);
            $fileKey = null;
            foreach ($manifest_keys as $key => $asset) {
                if (str_contains($asset, $fileName)) {
                    $fileKey = $asset;
                    break;
                }
            }

            if ($fileKey && isset($manifest[$fileKey])) {
                $returnedArray = [
                    'path' => DIST_URI . "/{$manifest[$fileKey]['file']}",
                    'slug' => "vite_{$fileNameWithoutExtension}_{$assetType}"
                ];
            }
        }
    }

    return $returnedArray;
}

// Append dependencies to DOM
if (defined('IS_VITE_DEVELOPMENT') && IS_VITE_DEVELOPMENT === true) {
    add_action('wp_enqueue_scripts', function() {
        wp_enqueue_script('jquery');
        wp_enqueue_script('wp-i18n');
        echo "<script>
            const wp_params = {
                ajax_url: '" . admin_url('admin-ajax.php') . "',
                stylesheet_directory: '" . get_stylesheet_directory_uri() . "',
                rest_url: '" . get_rest_url(null, '/wp/v2') . "'
            }
        </script>";
    });
}

function vite_enqueue_style($fileThemePath, $hookHMR, $hookBuild) {
    if (defined('IS_VITE_DEVELOPMENT') && IS_VITE_DEVELOPMENT === true) {
        /*
        * ================================ Inject assets in DOM
        * insert link tag for styles
        */
        add_action($hookHMR, function () use ($fileThemePath) {
            echo '<link rel="stylesheet" href="' . VITE_SERVER . strstr(get_stylesheet_directory(), '/wp-content') . '/' . $fileThemePath . '">';
        });
    } else {
        /*
        * ================================ Call assets with WP hooks
        */
        $manifestFileInfos = vite_fetch_asset_from_manifest($fileThemePath, 'style');
        if (!empty($manifestFileInfos)) {
            $filePath = $manifestFileInfos['path'];
            $fileSlug = $manifestFileInfos['slug'];
            add_action(
                $hookBuild,
                function () use ($fileSlug, $filePath) {
                    wp_enqueue_style(
                        $fileSlug,
                        $filePath,
                        [],
                        THEME_VERSION
                    );
                },
                20
            );
        }
    }
}

function vite_enqueue_script($fileThemePath, $hookHMR, $hookBuild, $footerEnqueue = true) {
    if (defined('IS_VITE_DEVELOPMENT') && IS_VITE_DEVELOPMENT === true) {
        /*
        * ================================ Inject assets in DOM
        * insert script tag for scripts
        */
        add_action($hookHMR, function () use ($fileThemePath) {
            echo '<script type="module" crossorigin src="' . VITE_SERVER . strstr(get_stylesheet_directory(), '/wp-content') . '/' . $fileThemePath . '"></script>';
        });
    } else {
        /*
        * ================================ Call assets with WP hooks
        */
        $manifestFileInfos = vite_fetch_asset_from_manifest($fileThemePath, 'script');
        if (!empty($manifestFileInfos)) {
            $filePath = $manifestFileInfos['path'];
            $fileSlug = $manifestFileInfos['slug'];
            add_action(
                $hookBuild,
                function () use ($fileSlug, $filePath, $footerEnqueue) {
                    wp_register_script(
                        $fileSlug,
                        $filePath,
                        ['jquery', 'wp-i18n'], // Libraries to use
                        THEME_VERSION,
                        $footerEnqueue
                    );

                    wp_localize_script(
                        $fileSlug,
                        'wp_params',
                        [
                            'ajax_url' => admin_url('admin-ajax.php'),
                            'rest_url' => get_rest_url(null, '/wp/v2'),
                            'stylesheet_directory' => get_stylesheet_directory_uri()
                        ]
                    );

                    wp_enqueue_script($fileSlug);
                },
                20
            );
        }
    }
}

function vite_enqueue_style_editor($fileThemePath, $hook) {
    if (defined('IS_VITE_DEVELOPMENT') && IS_VITE_DEVELOPMENT === true) {
        add_action(
            $hook,
            function () use ($fileThemePath) {
                add_editor_style(get_stylesheet_directory_uri() . '/' . $fileThemePath);
            },
            20
        );
    } else {
        /*
        * ================================ Call assets with WP hooks
        */
        $manifestFileInfos = vite_fetch_asset_from_manifest($fileThemePath, 'script');
        if (!empty($manifestFileInfos)) {
            $filePath = $manifestFileInfos['path'];

            add_action(
                $hook,
                function () use ($filePath) {
                    add_editor_style($filePath);
                },
                20
            );
        }
    }
}

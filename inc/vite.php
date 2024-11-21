<?php

if (!defined('ABSPATH')) {
    exit;
}

define('YOURPREFIX_VITE_SERVER', 'http://localhost:5173');
define('YOURPREFIX_DIST_FOLDER', 'build');
define('YOURPREFIX_DIST_URI', get_template_directory_uri() . '/' . YOURPREFIX_DIST_FOLDER);
define('YOURPREFIX_DIST_PATH', get_template_directory() . '/' . YOURPREFIX_DIST_FOLDER);

function yourprefix_vite_fetch_asset_from_manifest($fileThemePath, $assetType) {
    $returnedArray = [];

    $fileName = basename($fileThemePath);
    $fileNameWithoutExtension = substr($fileName, 0, strrpos($fileName, '.'));

    // Use manifest json to know which asset to enqueue
    if (file_exists(YOURPREFIX_DIST_PATH . '/.vite/manifest.json')) {
        $manifest = json_decode(file_get_contents(YOURPREFIX_DIST_PATH . '/.vite/manifest.json'), true);

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
                    'path' => YOURPREFIX_DIST_URI . "/{$manifest[$fileKey]['file']}",
                    'slug' => "yourprefix_vite_{$fileNameWithoutExtension}_{$assetType}"
                ];

                // In case of scss files included in Javascript
                if (isset($manifest[$fileKey]['css']) && !empty($manifest[$fileKey]['css'])) {
                    foreach ($manifest[$fileKey]['css'] as $stylePath) {
                        $styleFile = basename($stylePath);
                        $styleFile = substr($styleFile, 0, strrpos($styleFile, '.'));
                        if (strpos($styleFile, 'ver=')) {
                            $styleFile = remove_query_arg('ver', $styleFile);
                        }

                        $returnedArray['css'][] = [
                            'path' => YOURPREFIX_DIST_URI . "/{$stylePath}",
                            'slug' => "yourprefix_vite_{$styleFile}_style"
                        ];
                    }
                }
            }
        }
    }

    return $returnedArray;
}

function yourprefix_vite_enqueue_dev_dependencies() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('wp-i18n');
    wp_enqueue_script('wp-blocks');
    echo "<script>
            var wpparams = {
                ajax_url: '" . admin_url('admin-ajax.php') . "',
                rest_url: '" . esc_url_raw(get_rest_url(null, '/wp/v2')) . "',
                rest_nonce: '" . wp_create_nonce('wp_rest') . "',
                template_directory: '" . get_template_directory_uri() . "',
                plugins_directory: '" . plugins_url() . "',
                pictures_directory: '" . get_template_directory_uri() . '/build/assets/img' . "',
                posts_per_page: '" . get_option('posts_per_page') . "'
            }
        </script>";
}

function yourprefix_vite_enqueue_style($fileThemePath, $hookBuild, $hookDev = false) {
    if (!$hookDev) {
        $hookDev = $hookBuild;
    }

    $adminAsset = str_contains($hookBuild, 'admin') || str_contains($hookBuild, 'block') || str_contains($hookBuild, 'editor');
    if ($adminAsset && !is_admin()) {
        return;
    }

    if (defined('YOURPREFIX_IS_VITE_DEVELOPMENT') && YOURPREFIX_IS_VITE_DEVELOPMENT === true) {
        /*
        * ================================ Inject assets in DOM
        * insert link tag for styles
        */
        $themePath = get_template_directory_uri();
        $themePath = parse_url($themePath, PHP_URL_PATH);

        add_action($hookDev, function () use ($themePath, $fileThemePath) {
            echo '<link rel="stylesheet" href="' . YOURPREFIX_VITE_SERVER . $themePath . $fileThemePath . '">';
        });
    } else {
        /*
        * ================================ Call assets with WP hooks
        */
        $manifestFileInfos = yourprefix_vite_fetch_asset_from_manifest($fileThemePath, 'style');
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
                        time(),
                        'all'
                    );
                },
                20
            );
        }
    }
}

function yourprefix_vite_enqueue_script($fileThemePath, $hookBuild, $hookDev = false, $footerEnqueue = true, $type = '', $order = 20) {
    if (!$hookDev) {
        $hookDev = $hookBuild;
    }

    $adminAsset = str_contains($hookBuild, 'admin') || str_contains($hookBuild, 'block') || str_contains($hookBuild, 'editor');
    if ($adminAsset && !is_admin()) {
        return;
    }

    if (defined('YOURPREFIX_IS_VITE_DEVELOPMENT') && YOURPREFIX_IS_VITE_DEVELOPMENT === true) {
        /*
        * ================================ Inject assets in DOM
        * insert script tag for scripts
        */
        $themePath = get_template_directory_uri();
        $themePath = parse_url($themePath, PHP_URL_PATH);

        remove_action($hookDev, 'yourprefix_vite_enqueue_dev_dependencies');
        add_action($hookDev, 'yourprefix_vite_enqueue_dev_dependencies');
        add_action($hookDev, function () use ($themePath, $fileThemePath) {
            echo '<script type="module" crossorigin src="' . YOURPREFIX_VITE_SERVER . $themePath . $fileThemePath . '"></script>';
        });
    } else {
        /*
        * ================================ Call assets with WP hooks
        */
        $manifestFileInfos = yourprefix_vite_fetch_asset_from_manifest($fileThemePath, 'script');
        if (!empty($manifestFileInfos)) {
            if (isset($manifestFileInfos['css'])) {
                foreach ($manifestFileInfos['css'] as $style) {
                    $filePath = $style['path'];
                    $fileSlug = $style['slug'];

                    add_action(
                        $hookBuild,
                        function () use ($fileSlug, $filePath) {
                            wp_enqueue_style(
                                $fileSlug,
                                $filePath,
                                [],
                                time(),
                                'all'
                            );
                        },
                        $order
                    );
                }
            }

            $filePath = $manifestFileInfos['path'];
            $fileSlug = $manifestFileInfos['slug'];
            add_action(
                $hookBuild,
                function () use ($fileSlug, $filePath, $footerEnqueue, $type) {
                    wp_register_script(
                        $fileSlug,
                        $filePath,
                        ['wp-i18n', 'jquery'], // Libraries to use
                        time(),
                        [
                            'in_footer' => $footerEnqueue,
                            'strategy' => 'defer'
                        ]
                    );

                    wp_set_script_translations(
                        $fileSlug,
                        'simppple',
                        get_template_directory() . '/lang'
                    );

                    wp_localize_script(
                        $fileSlug,
                        'wpparams',
                        [
                            'ajax_url' => admin_url('admin-ajax.php'),
                            'rest_url' => esc_url_raw(get_rest_url(null, '/wp/v2')),
                            'rest_nonce' => wp_create_nonce('wp_rest'),
                            'template_directory' => get_template_directory_uri(),
                            'plugins_directory' => plugins_url(),
                            'pictures_directory' => get_template_directory_uri() . '/build/assets/img',
                            'posts_per_page' => get_option('posts_per_page'),
                        ]
                    );

                    if ($type === 'module') {
                        wp_enqueue_script_module($fileSlug, $filePath);
                    }else {
                        wp_enqueue_script($fileSlug, $filePath);
                    }
                },
                $order
            );
        }
    }
}

function yourprefix_vite_enqueue_style_editor($fileThemePath, $hook, $order = 20) {
    /*
    * ================================ Call assets with WP hooks
    */
    $manifestFileInfos = yourprefix_vite_fetch_asset_from_manifest($fileThemePath, 'script');
    if (!empty($manifestFileInfos)) {
        $filePath = $manifestFileInfos['path'];

        add_action(
            $hook,
            function () use ($filePath) {
                add_editor_style($filePath);
            },
            $order
        );
    } else {
        echo 'Please compile (build/prod) to see the editor style';
    }
}

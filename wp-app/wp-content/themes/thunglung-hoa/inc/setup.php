<?php
/**
 * Theme Setup
 *
 * Register menus, theme supports, and WooCommerce features.
 * @package ThungLungHoa
 * @since 1.0.0
 */

add_action('after_setup_theme', 'tlh_setup');
function tlh_setup() {
    // WooCommerce support
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');

    // General WordPress support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script']);
    add_theme_support('custom-logo', [
        'height'      => 60,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
    ]);

    // Register navigation menus
    register_nav_menus([
        'primary' => __('Menu chính', 'thunglung-hoa'),
        'footer'  => __('Menu chân trang', 'thunglung-hoa'),
    ]);
}

// Create auth pages on theme activation/init
add_action('wp_loaded', 'tlh_create_auth_pages_init');
function tlh_create_auth_pages_init() {
    if (!is_admin()) {
        return;
    }

    $pages = [
        [
            'post_title'   => 'Đăng Nhập',
            'post_name'    => 'dang-nhap',
            'post_content' => '[tlh_login_page]',
        ],
        [
            'post_title'   => 'Đăng Ký',
            'post_name'    => 'dang-ky',
            'post_content' => '[tlh_register_page]',
        ],
        [
            'post_title'   => 'Hồ Sơ',
            'post_name'    => 'ho-so',
            'post_content' => '[tlh_user_profile]',
        ],
    ];

    foreach ($pages as $page) {
        $exists = get_page_by_path($page['post_name']);
        if (!$exists) {
            wp_insert_post([
                'post_title'   => $page['post_title'],
                'post_name'    => $page['post_name'],
                'post_content' => $page['post_content'],
                'post_type'    => 'page',
                'post_status'  => 'publish',
            ]);
        }
    }

    flush_rewrite_rules();
}

// Custom template for auth pages
add_filter('template_include', 'tlh_template_include_auth_pages');
function tlh_template_include_auth_pages($template) {
    if (is_page('dang-nhap') || is_page('dang-ky')) {
        return get_template_directory() . '/page.php';
    }
    return $template;
}

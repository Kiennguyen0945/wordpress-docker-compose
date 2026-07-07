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

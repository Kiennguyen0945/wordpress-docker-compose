<?php
/**
 * Enqueue Scripts & Styles
 *
 * @package ThungLungHoa
 * @since 1.0.0
 */

add_action('wp_enqueue_scripts', 'tlh_enqueue');
function tlh_enqueue() {
    $theme_version = '1.0.0';
    $css_dir = get_template_directory_uri() . '/assets/css/';
    $js_dir  = get_template_directory_uri() . '/assets/js/';

    // Google Fonts
    wp_enqueue_style('tlh-google-fonts', 'https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,500;0,600;0,700;1,500&family=Be+Vietnam+Pro:wght@300;400;500;600;700&display=swap', [], null);

    // ========== CSS Modules (moi nguoi mot file) ==========
    wp_enqueue_style('tlh-base',        $css_dir . 'base.css',        [], $theme_version);
    wp_enqueue_style('tlh-header',      $css_dir . 'header.css',      [], $theme_version);
    wp_enqueue_style('tlh-footer',      $css_dir . 'footer.css',      [], $theme_version);
    wp_enqueue_style('tlh-home',        $css_dir . 'home.css',        [], $theme_version);
    wp_enqueue_style('tlh-shop',        $css_dir . 'shop.css',        [], $theme_version);
    wp_enqueue_style('tlh-product',     $css_dir . 'product.css',     [], $theme_version);
    wp_enqueue_style('tlh-cart',        $css_dir . 'cart.css',        [], $theme_version);
    wp_enqueue_style('tlh-checkout',    $css_dir . 'checkout.css',    [], $theme_version);
    wp_enqueue_style('tlh-user',        $css_dir . 'user.css',        [], $theme_version);
    wp_enqueue_style('tlh-components',  $css_dir . 'components.css',  [], $theme_version);
    wp_enqueue_style('tlh-woocommerce', $css_dir . 'woocommerce.css', [], $theme_version);
    wp_enqueue_style('tlh-responsive',  $css_dir . 'responsive.css',  [], $theme_version);

    // ========== JavaScript Modules ==========
    wp_enqueue_script('tlh-main',       $js_dir . 'main.js',       [], $theme_version, true);
    wp_enqueue_script('tlh-filters',    $js_dir . 'filters.js',    [], $theme_version, true);
    wp_enqueue_script('tlh-checkout',   $js_dir . 'checkout.js',   [], $theme_version, true);

    // Localize for AJAX
    wp_localize_script('tlh-filters', 'tlh_ajax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('tlh_ajax_nonce'),
    ]);
}

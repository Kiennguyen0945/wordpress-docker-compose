<?php
/**
 * Thung Lũng Hoa Theme Functions
 *
 * @package ThungLungHoa
 * @since 1.0.0
 */

// =========================================================
// 1. THEME SETUP
// =========================================================
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

// =========================================================
// 2. ENQUEUE SCRIPTS & STYLES
// =========================================================
add_action('wp_enqueue_scripts', 'tlh_enqueue');
function tlh_enqueue() {
    // Google Fonts
    wp_enqueue_style('tlh-google-fonts', 'https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,500;0,600;0,700;1,500&family=Be+Vietnam+Pro:wght@300;400;500;600;700&display=swap', [], null);

    // Theme stylesheet
    wp_enqueue_style('tlh-style', get_stylesheet_uri(), [], '1.0.0');
}

// =========================================================
// 3. WOOCOMMERCE CUSTOMISATIONS
// =========================================================

/**
 * Remove default WooCommerce wrappers – we use our own header/footer.
 */
remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);

add_action('woocommerce_before_main_content', 'tlh_wrapper_start', 10);
add_action('woocommerce_after_main_content', 'tlh_wrapper_end', 10);

function tlh_wrapper_start() {
    echo '<div class="container shop-layout">';
    echo '<aside>';
    tlh_shop_sidebar();
    echo '</aside>';
    echo '<div>';
}

function tlh_wrapper_end() {
    echo '</div></div>';
}

/**
 * Shop sidebar with filters (widget area).
 */
function tlh_shop_sidebar() {
    if (is_active_sidebar('shop-sidebar')) {
        dynamic_sidebar('shop-sidebar');
    } else {
        // Default fallback widgets
        the_widget('WC_Widget_Layered_Nav', ['title' => 'Hoa theo dịp'], ['before_widget' => '<div class="filter-block">', 'after_widget' => '</div>', 'before_title' => '<h4>', 'after_title' => '</h4>']);
        the_widget('WC_Widget_Price_Filter', ['title' => 'Khoảng giá'], ['before_widget' => '<div class="filter-block">', 'after_widget' => '</div>', 'before_title' => '<h4>', 'after_title' => '</h4>']);
    }
}

/**
 * Register shop sidebar.
 */
add_action('widgets_init', 'tlh_widgets_init');
function tlh_widgets_init() {
    register_sidebar([
        'name'          => __('Shop Sidebar', 'thunglung-hoa'),
        'id'            => 'shop-sidebar',
        'description'   => __('Sidebar cho trang cửa hàng.', 'thunglung-hoa'),
        'before_widget' => '<div class="filter-block">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4>',
        'after_title'   => '</h4>',
    ]);
}

/**
 * Change number of products per row and per page.
 */
add_filter('loop_shop_columns', 'tlh_loop_columns');
function tlh_loop_columns() {
    return 3;
}

add_filter('loop_shop_per_page', 'tlh_products_per_page');
function tlh_products_per_page() {
    return 12;
}

/**
 * Custom breadcrumb.
 */
add_filter('woocommerce_breadcrumb_defaults', 'tlh_breadcrumb_defaults');
function tlh_breadcrumb_defaults() {
    return [
        'delimiter'   => '<span class="sep">/</span>',
        'wrap_before' => '<div class="woocommerce-breadcrumb container">',
        'wrap_after'  => '</div>',
        'before'      => '',
        'after'       => '',
        'home'        => 'Trang chủ',
    ];
}

/**
 * Move sale flash and price in single product.
 */
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
add_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 25);

// =========================================================
// 4. HELPER FUNCTIONS
// =========================================================

/**
 * Get cart item count for header.
 */
function tlh_cart_count() {
    if (class_exists('WooCommerce')) {
        return WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
    }
    return 0;
}

/**
 * SVG logo mark used in header/footer.
 */
function tlh_logo_svg() {
    return '<svg class="logo-mark" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg">
        <g transform="translate(20,20)">
            <g class="fill-primary" opacity="0.92">
                <path transform="rotate(0)"   d="M0,0 C-6,-5 -6,-15 0,-19 C6,-15 6,-5 0,0 Z"/>
                <path transform="rotate(72)"  d="M0,0 C-6,-5 -6,-15 0,-19 C6,-15 6,-5 0,0 Z"/>
                <path transform="rotate(144)" d="M0,0 C-6,-5 -6,-15 0,-19 C6,-15 6,-5 0,0 Z"/>
                <path transform="rotate(216)" d="M0,0 C-6,-5 -6,-15 0,-19 C6,-15 6,-5 0,0 Z"/>
                <path transform="rotate(288)" d="M0,0 C-6,-5 -6,-15 0,-19 C6,-15 6,-5 0,0 Z"/>
            </g>
            <circle r="4.2" class="fill-accent"/>
        </g>
    </svg>';
}

/**
 * Search icon SVG.
 */
function tlh_search_icon() {
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>';
}

/**
 * Cart icon SVG.
 */
function tlh_cart_icon() {
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h2l2.4 12.2a2 2 0 0 0 2 1.8h8.6a2 2 0 0 0 2-1.6L21.5 9H6.5"/><circle cx="10" cy="21.5" r="1"/><circle cx="18" cy="21.5" r="1"/></svg>';
}

/**
 * Wave divider SVG.
 */
function tlh_wave_divider() {
    return '<svg class="wave-divider" viewBox="0 0 1200 44" preserveAspectRatio="none"><path d="M0,22 C150,44 350,0 600,20 C850,40 1050,4 1200,22 L1200,44 L0,44 Z"/></svg>';
}

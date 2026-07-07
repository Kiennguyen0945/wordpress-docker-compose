<?php
/**
 * WooCommerce Customisations
 *
 * All hooks, filters, and overrides for WooCommerce.
 * @package ThungLungHoa
 * @since 1.0.0
 */

// Remove default wrappers
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
        the_widget('WC_Widget_Layered_Nav', ['title' => 'Hoa theo dịp'], ['before_widget' => '<div class="filter-block">', 'after_widget' => '</div>', 'before_title' => '<h4>', 'after_title' => '</h4>']);
        the_widget('WC_Widget_Price_Filter', ['title' => 'Khoảng giá'], ['before_widget' => '<div class="filter-block">', 'after_widget' => '</div>', 'before_title' => '<h4>', 'after_title' => '</h4>']);
    }
}

/**
 * Register shop sidebar widget area.
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
 * Products per row & per page.
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
 * Custom breadcrumb defaults.
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
 * Move price position in single product summary.
 */
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
add_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 25);

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

/**
 * Fix: Replace fancy Unicode quotes with ASCII quotes in add-to-cart message
 * to prevent UTF-8 multi-byte character truncation causing text to be cut off.
 */
add_filter('wc_add_to_cart_message_html', 'tlh_fix_add_to_cart_quotes', 10, 3);
function tlh_fix_add_to_cart_quotes($message, $products, $show_qty) {
    $message = str_replace("\xe2\x80\x9c", '"', $message);
    $message = str_replace("\xe2\x80\x9d", '"', $message);
    $message = str_replace('&ldquo;', '"', $message);
    $message = str_replace('&rdquo;', '"', $message);
    return $message;
}

/**
 * Add "Miễn phí vận chuyển" fee line (amount = 0) to cart.
 * Cart Block sẽ hiển thị dòng này trong Order Summary.
 * JS (cart.js) sẽ đổi "0₫" thành "MIỄN PHÍ".
 */
add_action('woocommerce_cart_calculate_fees', 'tlh_add_free_shipping_fee');
function tlh_add_free_shipping_fee($cart) {
    if (is_admin() && !wp_doing_ajax()) return;
    if ($cart->get_cart_contents_count() === 0) return;

    // Chỉ thêm nếu chưa có dòng nào tên "Miễn phí vận chuyển"
    $exists = false;
    foreach ($cart->get_fees() as $fee) {
        if ($fee->name === 'Miễn phí vận chuyển') {
            $exists = true;
            break;
        }
    }
    if (!$exists) {
        $cart->add_fee('Miễn phí vận chuyển', 0, false, '');
    }
}

/**
 * Intercept Cart Block rendering when cart is empty.
 * Hiển thị giao diện giỏ hàng trống custom thay vì màn hình trắng.
 * Không ảnh hưởng đến cart có sản phẩm.
 * Không cần sửa database.
 */
add_filter('render_block', 'tlh_cart_block_empty_fallback', 10, 2);
function tlh_cart_block_empty_fallback($block_content, $block) {
    // Chỉ xử lý Cart Block
    if ($block['blockName'] !== 'woocommerce/cart') {
        return $block_content;
    }

    // Chỉ xử lý khi giỏ hàng trống
    if (!WC()->cart->is_empty()) {
        return $block_content;
    }

    // Render custom empty cart template
    ob_start();
    wc_get_template('cart/cart-empty.php');
    return ob_get_clean();
}

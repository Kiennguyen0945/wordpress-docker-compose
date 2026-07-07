<?php
/**
 * Shipping Configuration
 *
 * Người phụ trách: [Người A] — sửa file này khi làm tính năng bỏ/miễn phí ship.
 *
 * @package ThungLungHoa
 * @since 1.0.0
 */

/**
 * Set free shipping for all orders.
 * Bật dòng bên dưới để miễn phí ship toàn bộ:
 */
// add_filter('woocommerce_package_rates', 'tlh_free_shipping', 100);

function tlh_free_shipping($rates) {
    // Chỉ giữ lại free shipping method, xóa các method khác
    foreach ($rates as $rate_id => $rate) {
        if ('free_shipping' !== $rate->method_id) {
            unset($rates[$rate_id]);
        }
    }
    return $rates;
}

/**
 * Set minimum order amount for free shipping.
 */
add_filter('woocommerce_free_shipping_min_amount', 'tlh_free_shipping_min_amount');
function tlh_free_shipping_min_amount() {
    return 0; // 0 = miễn phí ship mọi đơn hàng
}

/**
 * Hide shipping methods when free shipping is available.
 */
add_filter('woocommerce_shipping_methods', 'tlh_hide_shipping_when_free');
function tlh_hide_shipping_when_free($available_methods) {
    if (isset($available_methods['free_shipping'])) {
        // Chỉ giữ free shipping
        $free = $available_methods['free_shipping'];
        $available_methods = [];
        $available_methods['free_shipping'] = $free;
    }
    return $available_methods;
}

/**
 * Custom shipping label.
 */
add_filter('woocommerce_shipping_method_title', 'tlh_shipping_label', 10, 2);
function tlh_shipping_label($title, $method) {
    if ('free_shipping' === $method->method_id) {
        return 'Miễn phí giao hàng';
    }
    return $title;
}

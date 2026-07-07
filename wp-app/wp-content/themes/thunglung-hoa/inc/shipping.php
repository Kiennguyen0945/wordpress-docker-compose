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
 * Xoá cache phí ship mỗi lần load trang → đảm bảo code luôn chạy.
 */
add_action('woocommerce_before_calculate_totals', 'tlh_clear_rates_cache');
function tlh_clear_rates_cache() {
    if (WC()->session) {
        $i = 0;
        while (WC()->session->__isset('shipping_for_package_' . $i)) {
            WC()->session->__unset('shipping_for_package_' . $i);
            $i++;
        }
    }
}

/**
 * Chuyển TẤT CẢ phương thức vận chuyển thành Miễn phí (0đ).
 * Shop sẽ trao đổi phí ship trực tiếp với khách hàng.
 */
add_filter('woocommerce_package_rates', 'tlh_set_free_shipping', 100, 2);
function tlh_set_free_shipping($rates, $package) {
    foreach ($rates as $rate_id => $rate) {
        $rate->cost  = 0;
        $rate->taxes = array();
        $rate->label = 'Miễn phí vận chuyển';
    }
    return $rates;
}

/**
 * Set minimum order amount for free shipping.
 */
add_filter('woocommerce_free_shipping_min_amount', 'tlh_free_shipping_min_amount');
function tlh_free_shipping_min_amount() {
    return 0;
}

/**
 * Custom shipping label (fallback).
 */
add_filter('woocommerce_shipping_method_title', 'tlh_shipping_label', 10, 2);
function tlh_shipping_label($title, $method) {
    return 'Miễn phí vận chuyển';
}

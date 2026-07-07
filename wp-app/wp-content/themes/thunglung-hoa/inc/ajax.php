<?php
/**
 * AJAX Handlers
 *
 * Product filtering, sorting, lazy-load, and any async operations.
 * Người phụ trách: [Người B] — sửa file này khi làm tính năng lọc sản phẩm.
 *
 * @package ThungLungHoa
 * @since 1.0.0
 */

/**
 * AJAX: Filter products by category, price, and sort order.
 */
add_action('wp_ajax_tlh_filter_products', 'tlh_filter_products');
add_action('wp_ajax_nopriv_tlh_filter_products', 'tlh_filter_products');

function tlh_filter_products() {
    check_ajax_referer('tlh_ajax_nonce', 'nonce');

    $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '';
    $orderby  = isset($_POST['orderby'])  ? sanitize_text_field($_POST['orderby'])  : 'date';
    $order    = isset($_POST['order'])    ? sanitize_text_field($_POST['order'])    : 'DESC';
    $min_price = isset($_POST['min_price']) ? floatval($_POST['min_price']) : 0;
    $max_price = isset($_POST['max_price']) ? floatval($_POST['max_price']) : 0;

    $args = [
        'status'     => 'publish',
        'visibility' => 'visible',
        'limit'      => 12,
        'orderby'    => $orderby,
        'order'      => $order,
    ];

    if ($category) {
        $args['category'] = [$category];
    }

    if ($max_price > 0) {
        $args['meta_query'] = [
            [
                'key'     => '_price',
                'value'   => [$min_price, $max_price],
                'compare' => 'BETWEEN',
                'type'    => 'NUMERIC',
            ],
        ];
    }

    $products = wc_get_products($args);

    ob_start();
    foreach ($products as $product) {
        wc_get_template_part('content', 'product');
    }
    $html = ob_get_clean();

    wp_send_json_success(['html' => $html]);
}

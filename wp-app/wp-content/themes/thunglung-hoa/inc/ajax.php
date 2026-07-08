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
 * AJAX: Filter products by category, price, sort, and pagination.
 *
 * Nhận: category[], orderby, order, paged, min_price, max_price
 * Trả về: html (product grid), pagination, count, max_num_pages
 */
add_action('wp_ajax_tlh_filter_products', 'tlh_filter_products');
add_action('wp_ajax_nopriv_tlh_filter_products', 'tlh_filter_products');

function tlh_filter_products() {
    check_ajax_referer('tlh_ajax_nonce', 'nonce');

    // ─── Parse params ──────────────────────────────────────────────────
    $categories = isset( $_POST['category'] ) ? (array) $_POST['category'] : array();
    $categories = array_map( 'sanitize_text_field', $categories );
    $categories = array_filter( $categories );

    $orderby  = isset( $_POST['orderby'] )  ? sanitize_text_field( $_POST['orderby'] ) : 'date';
    $order    = isset( $_POST['order'] )    ? sanitize_text_field( $_POST['order'] )   : 'DESC';
    $paged    = isset( $_POST['paged'] )    ? max( 1, intval( $_POST['paged'] ) )      : 1;
    $per_page = 12;
    $min_price = isset( $_POST['min_price'] ) ? floatval( $_POST['min_price'] ) : 0;
    $max_price = isset( $_POST['max_price'] ) ? floatval( $_POST['max_price'] ) : 0;

    // ─── Build WP_Query args ───────────────────────────────────────────
    $args = array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => $per_page,
        'paged'          => $paged,
        'tax_query'      => array(
            array(
                'taxonomy' => 'product_visibility',
                'field'    => 'slug',
                'terms'    => array( 'exclude-from-catalog' ),
                'operator' => 'NOT IN',
            ),
        ),
        'meta_query'     => array(),
    );

    // Orderby mapping — hỗ trợ date, price, popularity
    switch ( $orderby ) {
        case 'price':
            $args['orderby']  = 'meta_value_num';
            $args['meta_key'] = '_price';
            break;
        case 'popularity':
            $args['orderby']  = 'meta_value_num';
            $args['meta_key'] = 'total_sales';
            break;
        default:
            $args['orderby'] = 'date';
    }
    $args['order'] = $order;

    // Category filter (multiple)
    if ( ! empty( $categories ) ) {
        $args['tax_query'][] = array(
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => $categories,
            'operator' => 'IN',
        );
    }

    // Price range filter
    if ( $max_price > 0 ) {
        $args['meta_query'][] = array(
            'key'     => '_price',
            'value'   => array( $min_price, $max_price ),
            'compare' => 'BETWEEN',
            'type'    => 'NUMERIC',
        );
    }

    $query = new WP_Query( $args );

    // ─── Render product grid HTML ──────────────────────────────────────
    ob_start();
    if ( $query->have_posts() ) :
        while ( $query->have_posts() ) :
            $query->the_post();
            global $product;
            $product = wc_get_product( get_the_ID() );
            wc_get_template_part( 'content', 'product' );
        endwhile;
    endif;
    $html = ob_get_clean();
    wp_reset_postdata();

    // ─── Pagination HTML ──────────────────────────────────────────────
    $pagination_html = '';
    if ( $query->max_num_pages > 1 ) {
        $pagination_html = '<nav class="pagination">';
        $pagination_html .= paginate_links( array(
            'base'      => '#?page=%#%',
            'format'    => '?page=%#%',
            'total'     => $query->max_num_pages,
            'current'   => $paged,
            'type'      => 'plain',
            'prev_text' => '←',
            'next_text' => '→',
            'end_size'  => 2,
            'mid_size'  => 2,
            'add_args'  => false,
        ) );
        $pagination_html .= '</nav>';
    }

    // ─── Response ──────────────────────────────────────────────────────
    wp_send_json_success( array(
        'html'          => $html,
        'pagination'    => $pagination_html,
        'count'         => (int) $query->found_posts,
        'max_num_pages' => (int) $query->max_num_pages,
    ) );
}

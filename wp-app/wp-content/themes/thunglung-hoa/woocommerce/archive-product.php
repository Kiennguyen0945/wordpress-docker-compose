<?php
/**
 * Shop / Archive page
 *
 * @package ThungLungHoa
 */

get_header('shop');
?>

<section class="shop-header">
  <div class="container">
    <?php if (apply_filters('woocommerce_show_page_title', true)) : ?>
      <span class="eyebrow">Cửa hàng</span>
      <h1 style="margin:14px 0 12px;"><?php woocommerce_page_title(); ?></h1>
    <?php endif; ?>
    <p class="lede">Mỗi bó hoa đều được cắm trong ngày. Chọn theo câu chuyện bạn muốn kể, hoặc theo loài hoa bạn yêu thích nhất.</p>
  </div>
</section>

<?php
if (woocommerce_product_loop()) {
    do_action('woocommerce_before_shop_loop');

    echo '<div class="shop-toolbar">';
    woocommerce_result_count();
    woocommerce_catalog_ordering();
    echo '</div>';

    woocommerce_product_loop_start();

    if (wc_get_loop_prop('total')) {
        while (have_posts()) {
            the_post();
            do_action('woocommerce_shop_loop');
            wc_get_template_part('content', 'product');
        }
    }

    woocommerce_product_loop_end();

    do_action('woocommerce_after_shop_loop');
} else {
    do_action('woocommerce_no_products_found');
}

get_footer('shop');

<?php
/**
 * Related products
 *
 * @package ThungLungHoa
 */

if (!defined('ABSPATH')) {
    exit;
}

if ($related_products) : ?>

  <section class="best-seller related">
    <div class="container">
      <div class="section-head">
        <span class="eyebrow">Gợi ý cho bạn</span>
        <h2>Sản phẩm liên quan</h2>
      </div>
      <?php woocommerce_product_loop_start(); ?>
        <?php foreach ($related_products as $related_product) : ?>
          <?php
          $post_object = get_post($related_product->get_id());
          setup_postdata($GLOBALS['post'] =& $post_object);
          wc_get_template_part('content', 'product');
          ?>
        <?php endforeach; ?>
      <?php woocommerce_product_loop_end(); ?>
    </div>
  </section>

<?php endif;
wp_reset_postdata();

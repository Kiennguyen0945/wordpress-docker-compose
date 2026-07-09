<?php
/**
 * Empty cart page
 *
 * Override of WooCommerce cart/cart-empty.php for Royal Flower Studio.
 * Hiển thị giỏ hàng trống kèm gợi ý sản phẩm mới, đồng bộ với thẻ sản phẩm ở trang chủ.
 *
 * @package ThungLungHoa
 */

defined('ABSPATH') || exit;

/*
 * NOTICE: Chúng tôi KHÔNG gọi do_action('woocommerce_cart_is_empty') ở đây
 * vì hook đó render ra thông báo mặc định "Your cart is currently empty"
 * (wc_empty_cart_message) và notices wrapper (woocommerce_output_all_notices).
 * Chúng tôi đã thay thế bằng giao diện giỏ hàng trống custom hoàn toàn bên dưới.
 */
?>

<!-- Empty Cart State -->
<div class="empty-cart">
  <div class="empty-cart-icon">😢</div>
  <h2 class="empty-cart-title">Giỏ hàng của bạn đang trống!</h2>
  <p class="empty-cart-desc">Hãy khám phá bộ sưu tập hoa tươi và chọn cho mình những bó hoa yêu thích nhé.</p>
  <a href="<?php echo esc_url(apply_filters('woocommerce_return_to_shop_redirect', wc_get_page_permalink('shop'))); ?>" class="btn btn-accent btn-lg">Mua sắm ngay</a>

  <div class="empty-cart-divider">• &nbsp;• &nbsp;•</div>

  <!-- Suggested Products -->
  <div class="empty-cart-suggestions">
    <div class="section-head" style="text-align:center;">
      <span class="eyebrow">Gợi ý cho bạn</span>
      <h2>Sản phẩm mới</h2>
    </div>

    <div class="grid-4">
      <?php
      $new_products = wc_get_products([
        'orderby'    => 'date',
        'order'      => 'DESC',
        'limit'      => 4,
        'status'     => 'publish',
        'visibility' => 'visible',
      ]);

      if (empty($new_products)) {
          $new_products = wc_get_products([
            'limit'      => 4,
            'status'     => 'publish',
            'visibility' => 'visible',
          ]);
      }

      foreach ($new_products as $np) :
        $npid  = $np->get_id();
        $nlink = get_permalink($npid);
      ?>
        <a href="<?php echo esc_url($nlink); ?>" class="product-card">
          <div class="product-media blob blob-a blob-frame">
            <?php if ($np->is_on_sale()) : ?>
              <span class="badge">Bán chạy</span>
            <?php endif; ?>
            <span class="wish" aria-label="Yêu thích">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20s-7-4.4-9.5-9A5 5 0 0 1 12 6a5 5 0 0 1 9.5 5c-2.5 4.6-9.5 9-9.5 9Z"/></svg>
            </span>
            <?php echo $np->get_image('woocommerce_thumbnail', ['style' => 'width:100%;height:100%;object-fit:cover;']); ?>
          </div>
          <div class="product-name"><?php echo esc_html($np->get_name()); ?></div>
          <p class="product-cat"><?php echo wp_strip_all_tags(wc_get_product_category_list($npid, ', ')); ?></p>
          <div class="product-meta">
            <span class="product-price"><?php echo $np->get_price_html(); ?></span>
            <?php if ($np->get_average_rating()) : ?>
              <span class="stars"><?php echo str_repeat('★', round($np->get_average_rating())) . str_repeat('☆', 5 - round($np->get_average_rating())); ?></span>
            <?php endif; ?>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</div>

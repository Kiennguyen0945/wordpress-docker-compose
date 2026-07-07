<?php
/**
 * Cart page
 *
 * @package ThungLungHoa
 */

defined('ABSPATH') || exit;

get_header('shop');

do_action('woocommerce_before_cart');
?>

<section class="checkout-layout container">

    <div class="section-head" style="grid-column:1/-1;">
      <span class="eyebrow">Giỏ hàng</span>
      <h1>Giỏ hàng của bạn</h1>
    </div>

    <?php if (WC()->cart->is_empty()) : ?>
      <div style="text-align:center; padding:60px 0;">
        <p class="lede" style="margin-bottom:24px;">Giỏ hàng đang trống.</p>
        <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="btn btn-accent btn-lg">Mua sắm ngay</a>
      </div>
    <?php else : ?>
      <div class="checkout-columns">
        <form class="cart-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
          <table class="cart-table woocommerce-cart-form__contents">
            <thead>
              <tr>
                <th>Sản phẩm</th>
                <th>Giá</th>
                <th>Số lượng</th>
                <th>Tạm tính</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php do_action('woocommerce_before_cart_contents'); ?>

              <?php
              foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                  $_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                  $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

                  if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {
                      $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink() : '', $cart_item, $cart_item_key);
                      ?>
                      <tr class="woocommerce-cart-form__cart-item cart-item">
                        <td class="ci-product">
                          <div class="cart-item-main">
                            <div class="ci-img blob blob-b blob-frame">
                              <?php
                              $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image('thumbnail', ['style' => 'width:100%;height:100%;object-fit:contain;']), $cart_item, $cart_item_key);
                              echo $thumbnail;
                              ?>
                            </div>
                            <div class="ci-info">
                              <h4>
                                <?php if (!$product_permalink) : ?>
                                  <?php echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key)); ?>
                                <?php else : ?>
                                  <a href="<?php echo esc_url($product_permalink); ?>"><?php echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key)); ?></a>
                                <?php endif; ?>
                              </h4>
                              <?php echo wc_get_formatted_cart_item_data($cart_item); ?>
                            </div>
                          </div>
                        </td>

                        <td class="ci-price">
                          <?php echo apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key); ?>
                        </td>

                        <td class="ci-qty">
                          <?php
                          if ($_product->is_sold_individually()) {
                              echo '1';
                          } else {
                              $product_quantity = woocommerce_quantity_input([
                                  'input_name'   => "cart[{$cart_item_key}][qty]",
                                  'input_value'  => $cart_item['quantity'],
                                  'max_value'    => $_product->get_max_purchase_quantity(),
                                  'min_value'    => '0',
                                  'product_name' => $_product->get_name(),
                              ], $_product, false);
                              echo $product_quantity;
                          }
                          ?>
                        </td>

                        <td class="ci-subtotal">
                          <?php echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); ?>
                        </td>

                        <td class="ci-remove">
                          <?php echo apply_filters('woocommerce_cart_item_remove_link', sprintf(
                              '<a href="%s" class="remove" aria-label="%s">&times;</a>',
                              esc_url(wc_get_cart_remove_url($cart_item_key)),
                              esc_attr__('Xóa sản phẩm này', 'thunglung-hoa')
                          ), $cart_item_key); ?>
                        </td>
                      </tr>
                      <?php
                  }
              }
              ?>

              <?php do_action('woocommerce_cart_contents'); ?>

              <tr class="cart-actions">
                <td colspan="5">
                  <div class="cart-actions-row">
                    <?php if (wc_coupons_enabled()) : ?>
                      <div class="coupon">
                        <input type="text" name="coupon_code" class="input-text" id="coupon_code" placeholder="<?php esc_attr_e('Mã giảm giá', 'thunglung-hoa'); ?>" />
                        <button type="submit" class="btn btn-outline" name="apply_coupon" value="<?php esc_attr_e('Áp dụng', 'thunglung-hoa'); ?>"><?php esc_html_e('Áp dụng', 'thunglung-hoa'); ?></button>
                        <?php do_action('woocommerce_cart_coupon'); ?>
                      </div>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-outline" name="update_cart" value="<?php esc_attr_e('Cập nhật giỏ hàng', 'thunglung-hoa'); ?>">Cập nhật</button>
                    <?php do_action('woocommerce_cart_actions'); ?>
                    <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
                  </div>
                </td>
              </tr>

              <?php do_action('woocommerce_after_cart_contents'); ?>
            </tbody>
          </table>

          <?php do_action('woocommerce_after_cart_table'); ?>
        </form>

        <div class="panel panel-sticky">
          <div class="panel-body">
            <h3>Tóm tắt đơn hàng</h3>

            <?php foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) : ?>
              <?php $_product = $cart_item['data']; ?>
              <div class="panel-row">
                <span><?php echo esc_html($_product->get_name()); ?> × <?php echo $cart_item['quantity']; ?></span>
                <span><?php echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); ?></span>
              </div>
            <?php endforeach; ?>

            <?php if (WC()->cart->get_cart_discount_total()) : ?>
              <div class="panel-row panel-discount">
                <span>Giảm giá</span>
                <span>-<?php echo WC()->cart->get_cart_discount_total(); ?></span>
              </div>
            <?php endif; ?>

            <?php if (WC()->cart->needs_shipping()) : ?>
              <div class="panel-row panel-shipping">
                <span>Phí vận chuyển</span>
                <span class="ship-free">Miễn phí – 0₫</span>
              </div>
            <?php endif; ?>

            <div class="panel-row panel-total">
              <span>Tổng cộng</span>
              <span><?php echo WC()->cart->get_cart_total(); ?></span>
            </div>

            <div class="panel-actions">
              <a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="btn btn-accent btn-block">Thanh toán ngay</a>
              <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="btn btn-outline btn-block">Tiếp tục mua sắm</a>
            </div>
          </div>
        </div>
      </div>

      <?php do_action('woocommerce_cart_collaterals'); ?>

    <?php endif; ?>

</section>

<?php
do_action('woocommerce_after_cart');

get_footer('shop');

<?php
/**
 * Cart page — Classic Cart Template
 *
 * Layout 2 cột: bảng sản phẩm (trái) + thanh tổng cộng (phải).
 * Giao diện đồng bộ với cart-empty.php.
 *
 * @package ThungLungHoa
 */

defined('ABSPATH') || exit;

get_header('shop');

do_action('woocommerce_before_cart');

$has_items = ! WC()->cart->is_empty();
?>

<section class="checkout-layout container cart-layout">

<?php if (!$has_items) : ?>
  <!-- Fallback (cart-empty.php đã xử lý, chỉ để phòng) -->
  <div style="text-align:center; padding:80px 0; grid-column:1/-1;">
    <p class="lede" style="margin-bottom:24px;">Giỏ hàng đang trống.</p>
    <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="btn btn-accent btn-lg">Mua sắm ngay</a>
  </div>
<?php else : ?>

  <form class="cart-main-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
  <div class="checkout-columns">

    <!-- ========== LEFT: Danh sách sản phẩm ========== -->
    <div class="cart-products">

      <table class="cart-table cart-table--compact">
        <thead>
          <tr>
            <th>SẢN PHẨM</th>
            <th class="th-total">TỔNG</th>
          </tr>
        </thead>
        <tbody>
          <?php do_action('woocommerce_before_cart_contents'); ?>

          <?php foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) :
            $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
            $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

            if (!$_product || !$_product->exists() || $cart_item['quantity'] <= 0
              || !apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {
              continue;
            }

            $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink() : '', $cart_item, $cart_item_key);

            $short_desc = wp_trim_words(
              wp_strip_all_tags($_product->get_short_description() ?: $_product->get_description()),
              12, '…'
            );
          ?>
            <tr class="cart-item">
              <td class="ci-product">
                <div class="cart-item-main">
                  <!-- Image -->
                  <div class="ci-img blob blob-b blob-frame">
                    <?php echo apply_filters('woocommerce_cart_item_thumbnail',
                      $_product->get_image('thumbnail', ['style' => 'width:100%;height:100%;object-fit:contain;']),
                      $cart_item, $cart_item_key
                    ); ?>
                  </div>

                  <!-- Info -->
                  <div class="ci-info">
                    <h4>
                      <?php if ($product_permalink) : ?>
                        <a href="<?php echo esc_url($product_permalink); ?>">
                          <?php echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key)); ?>
                        </a>
                      <?php else : ?>
                        <?php echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key)); ?>
                      <?php endif; ?>
                    </h4>

                    <div class="ci-unit-price">
                      <?php echo apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key); ?>
                    </div>

                    <?php if ($short_desc) : ?>
                      <p class="ci-desc"><?php echo esc_html($short_desc); ?></p>
                    <?php endif; ?>

                    <?php echo wc_get_formatted_cart_item_data($cart_item); ?>

                    <div class="ci-actions-row">
                      <?php if ($_product->is_sold_individually()) : ?>
                        <span class="qty-solo">1</span>
                      <?php else : ?>
                        <div class="qty-stepper">
                          <button type="button" class="qty-btn qty-minus" aria-label="Giảm số lượng">−</button>
                          <?php
                          woocommerce_quantity_input([
                            'input_name'   => "cart[{$cart_item_key}][qty]",
                            'input_value'  => $cart_item['quantity'],
                            'max_value'    => $_product->get_max_purchase_quantity(),
                            'min_value'    => '1',
                            'product_name' => $_product->get_name(),
                          ], $_product);
                          ?>
                          <button type="button" class="qty-btn qty-plus" aria-label="Tăng số lượng">+</button>
                        </div>
                      <?php endif; ?>

                      <?php echo apply_filters('woocommerce_cart_item_remove_link', sprintf(
                        '<a href="%s" class="remove-btn" aria-label="%s">🗑</a>',
                        esc_url(wc_get_cart_remove_url($cart_item_key)),
                        esc_attr__('Xoá sản phẩm này', 'thunglung-hoa')
                      ), $cart_item_key); ?>
                    </div>
                  </div>
                </div>
              </td>

              <td class="ci-total">
                <?php echo apply_filters('woocommerce_cart_item_subtotal',
                  WC()->cart->get_product_subtotal($_product, $cart_item['quantity']),
                  $cart_item, $cart_item_key
                ); ?>
              </td>
            </tr>
          <?php endforeach; ?>

          <?php do_action('woocommerce_cart_contents'); ?>
        </tbody>
      </table>

      <!-- Update & nonce -->
      <div class="cart-form-foot">
        <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
        <?php do_action('woocommerce_cart_actions'); ?>
        <button type="submit" class="btn btn-outline btn-sm" name="update_cart" value="<?php esc_attr_e('Cập nhật giỏ hàng', 'thunglung-hoa'); ?>">🔄 Cập nhật</button>
      </div>

      <?php do_action('woocommerce_after_cart_table'); ?>
    </div>

    <!-- ========== RIGHT: Tổng cộng giỏ hàng ========== -->
    <div class="panel panel-sticky">
      <div class="panel-body">
        <h3>TỔNG CỘNG GIỎ HÀNG</h3>

        <?php if (wc_coupons_enabled()) : ?>
          <div class="panel-coupon">
            <div class="coupon-toggle">
              <span>➕ Thêm mã giảm giá</span>
              <span class="coupon-arrow">▾</span>
            </div>
            <div class="coupon-form" style="display:none;">
              <input type="text" name="coupon_code" class="input-text" id="coupon_code" placeholder="<?php esc_attr_e('Mã giảm giá', 'thunglung-hoa'); ?>" />
              <button type="submit" class="btn btn-accent btn-sm" name="apply_coupon" value="<?php esc_attr_e('Áp dụng', 'thunglung-hoa'); ?>">Áp dụng</button>
              <?php do_action('woocommerce_cart_coupon'); ?>
            </div>
          </div>
        <?php endif; ?>

        <!-- Line items recap -->
        <?php foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) :
          $_product = $cart_item['data']; ?>
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
            <span class="ship-free">Miễn phí — MIỄN PHÍ</span>
          </div>
        <?php endif; ?>

        <div class="panel-row panel-total">
          <span>Tổng ước tính</span>
          <span><?php echo WC()->cart->get_cart_total(); ?></span>
        </div>

        <div class="panel-actions">
          <a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="btn btn-dark btn-block">Tiến hành thanh toán</a>
          <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="btn btn-outline btn-block">Tiếp tục mua sắm</a>
        </div>
      </div>
    </div>

  </div><!-- /checkout-columns -->
  </form>

  <!-- Quantity stepper JS + Coupon toggle JS -->
  <script>
  (function() {
    /* --- Stepper -/+ --- */
    document.querySelectorAll('.qty-stepper').forEach(function(stepper) {
      var minus = stepper.querySelector('.qty-minus');
      var plus  = stepper.querySelector('.qty-plus');
      var input = stepper.querySelector('.qty');
      if (!minus || !plus || !input) return;

      minus.addEventListener('click', function() {
        var val = parseInt(input.value) || 1;
        if (val > 1) {
          input.value = val - 1;
          input.dispatchEvent(new Event('change', { bubbles: true }));
        }
      });

      plus.addEventListener('click', function() {
        var val = parseInt(input.value) || 1;
        var max = parseFloat(input.getAttribute('max'));
        if (!isNaN(max) && val >= max) return;
        input.value = val + 1;
        input.dispatchEvent(new Event('change', { bubbles: true }));
      });
    });

    /* --- Coupon toggle accordion --- */
    var toggle = document.querySelector('.coupon-toggle');
    if (toggle) {
      toggle.addEventListener('click', function() {
        var form = this.nextElementSibling;
        if (!form) return;
        var hidden = form.style.display === 'none' || !form.style.display;
        form.style.display = hidden ? 'flex' : 'none';
        this.querySelector('.coupon-arrow').textContent = hidden ? '▴' : '▾';
      });
    }

    /* --- Auto-submit cart on quantity change (debounced) --- */
    var qtyTimer = null;
    document.querySelector('.cart-main-form').addEventListener('change', function(e) {
      if (e.target.classList.contains('qty')) {
        clearTimeout(qtyTimer);
        qtyTimer = setTimeout(function() {
          var btn = document.querySelector('button[name="update_cart"]');
          if (btn) btn.click();
        }, 600);
      }
    });
  })();
  </script>

<?php endif; ?>
</section>

<?php
do_action('woocommerce_after_cart');
get_footer('shop');

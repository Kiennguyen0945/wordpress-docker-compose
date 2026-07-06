<?php
/**
 * Checkout page
 *
 * @package ThungLungHoa
 */

defined('ABSPATH') || exit;

get_header('shop');

do_action('woocommerce_before_checkout_form', $checkout);

if ($checkout->is_registration_enabled() && $checkout->is_registration_required() && !is_user_logged_in()) {
    echo apply_filters('woocommerce_checkout_must_be_logged_in_message', __('Bạn phải đăng nhập để thanh toán.', 'thunglung-hoa'));
    wc_get_template('checkout/form-login.php', ['checkout' => $checkout]);
    get_footer('shop');
    return;
}
?>

<section class="checkout-layout">
  <div class="container">

    <div class="section-head">
      <span class="eyebrow">Thanh toán</span>
      <h1>Thông tin nhận hoa</h1>
    </div>

    <form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data">

      <div class="checkout-columns">

        <div class="checkout-form-col">
          <?php if ($checkout->get_checkout_fields()) : ?>
            <?php do_action('woocommerce_checkout_before_customer_details'); ?>
            <?php do_action('woocommerce_checkout_billing'); ?>
            <?php do_action('woocommerce_checkout_shipping'); ?>
            <?php do_action('woocommerce_checkout_after_customer_details'); ?>
          <?php endif; ?>
        </div>

        <div class="checkout-summary-col">
          <div class="panel panel-sticky">
            <div class="panel-body">
              <h3>Đơn hàng của bạn</h3>

              <?php do_action('woocommerce_checkout_before_order_review'); ?>

              <table class="cart-table checkout-review-table">
                <thead>
                  <tr>
                    <th>Sản phẩm</th>
                    <th>Tạm tính</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                      $_product = $cart_item['data'];
                      ?>
                      <tr>
                        <td><?php echo esc_html($_product->get_name()); ?> × <strong><?php echo $cart_item['quantity']; ?></strong></td>
                        <td><?php echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); ?></td>
                      </tr>
                  <?php } ?>
                </tbody>
                <tfoot>
                  <tr class="cart-subtotal">
                    <th>Tạm tính</th>
                    <td><?php wc_cart_totals_subtotal_html(); ?></td>
                  </tr>
                  <?php foreach (WC()->cart->get_coupons() as $code => $coupon) : ?>
                    <tr class="cart-discount coupon-<?php echo esc_attr(sanitize_title($code)); ?>">
                      <th><?php wc_cart_totals_coupon_label($coupon); ?></th>
                      <td><?php wc_cart_totals_coupon_html($coupon); ?></td>
                    </tr>
                  <?php endforeach; ?>
                  <?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()) : ?>
                    <?php do_action('woocommerce_review_order_before_shipping'); ?>
                    <?php wc_cart_totals_shipping_html(); ?>
                    <?php do_action('woocommerce_review_order_after_shipping'); ?>
                  <?php endif; ?>
                  <tr class="order-total">
                    <th><?php esc_html_e('Tổng cộng', 'thunglung-hoa'); ?></th>
                    <td><?php wc_cart_totals_order_total_html(); ?></td>
                  </tr>
                </tfoot>
              </table>

              <?php do_action('woocommerce_checkout_order_review'); ?>

              <?php do_action('woocommerce_checkout_after_order_review'); ?>
            </div>
          </div>
        </div>

      </div>

      <div class="checkout-payment">
        <?php do_action('woocommerce_checkout_payment'); ?>
      </div>

    </form>

  </div>
</section>

<?php
do_action('woocommerce_after_checkout_form', $checkout);

get_footer('shop');

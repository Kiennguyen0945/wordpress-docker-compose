<?php
/**
 * Simple product add to cart
 *
 * @package ThungLungHoa
 */

defined('ABSPATH') || exit;

global $product;

if (!$product->is_purchasable()) {
    return;
}

if ($product->is_in_stock()) : ?>

  <form class="add-to-cart-form" action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink())); ?>" method="post" enctype='multipart/form-data'>
    <?php do_action('woocommerce_before_add_to_cart_form'); ?>

    <?php do_action('woocommerce_before_add_to_cart_button'); ?>

    <div class="qty-row">
      <div class="qty-stepper">
        <button type="button" class="qty-btn qty-minus">−</button>
        <input type="number" name="quantity" class="qty-input" value="1" min="1" max="<?php echo esc_attr($product->get_max_purchase_quantity()); ?>" step="1" />
        <button type="button" class="qty-btn qty-plus">+</button>
      </div>
      <button type="submit" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>" class="btn btn-accent btn-lg single_add_to_cart_button">Thêm vào giỏ</button>
    </div>

    <?php do_action('woocommerce_after_add_to_cart_button'); ?>
  </form>

  <script>
  document.addEventListener('DOMContentLoaded', function() {
    var steppers = document.querySelectorAll('.qty-stepper');
    steppers.forEach(function(stepper) {
      var minus = stepper.querySelector('.qty-minus');
      var plus = stepper.querySelector('.qty-plus');
      var input = stepper.querySelector('.qty-input');
      minus.addEventListener('click', function() {
        var val = parseInt(input.value) || 1;
        if (val > 1) input.value = val - 1;
      });
      plus.addEventListener('click', function() {
        var val = parseInt(input.value) || 1;
        var max = parseInt(input.getAttribute('max')) || 99;
        if (val < max) input.value = val + 1;
      });
    });
  });
  </script>

<?php endif; ?>

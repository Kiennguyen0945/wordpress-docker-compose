<?php
/**
 * Single variation cart button — Theme override with qty-stepper
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 10.5.2
 */

defined( 'ABSPATH' ) || exit;

global $product;
?>
<div class="woocommerce-variation-add-to-cart variations_button">
	<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

	<div class="qty-row">
		<div class="qty-stepper">
			<button type="button" class="qty-btn qty-minus">−</button>
			<?php
			woocommerce_quantity_input(
				array(
					'min_value'   => $product->get_min_purchase_quantity(),
					'max_value'   => $product->get_max_purchase_quantity(),
					'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : $product->get_min_purchase_quantity(),
				)
			);
			?>
			<button type="button" class="qty-btn qty-plus">+</button>
		</div>
		<button type="submit" class="btn btn-accent btn-lg single_add_to_cart_button"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>
	</div>

	<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>

	<input type="hidden" name="add-to-cart" value="<?php echo absint( $product->get_id() ); ?>" />
	<input type="hidden" name="product_id" value="<?php echo absint( $product->get_id() ); ?>" />
	<input type="hidden" name="variation_id" class="variation_id" value="0" />
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
	var steppers = document.querySelectorAll('.woocommerce-variation-add-to-cart .qty-stepper');
	steppers.forEach(function(stepper) {
		var minus = stepper.querySelector('.qty-minus');
		var plus = stepper.querySelector('.qty-plus');
		var input = stepper.querySelector('.qty') || stepper.querySelector('.qty-input');
		if (!minus || !plus || !input) return;
		minus.addEventListener('click', function() {
			var val = parseInt(input.value) || 1;
			var min = parseInt(input.getAttribute('min')) || 1;
			if (val > min) input.value = val - 1;
			input.dispatchEvent(new Event('change', { bubbles: true }));
		});
		plus.addEventListener('click', function() {
			var val = parseInt(input.value) || 1;
			var max = parseInt(input.getAttribute('max')) || 99;
			if (val < max) input.value = val + 1;
			input.dispatchEvent(new Event('change', { bubbles: true }));
		});
	});
});
</script>

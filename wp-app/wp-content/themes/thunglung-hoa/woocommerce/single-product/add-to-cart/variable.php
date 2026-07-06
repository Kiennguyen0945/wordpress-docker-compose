<?php
/**
 * Variable product add to cart
 *
 * @package ThungLungHoa
 */

defined('ABSPATH') || exit;

global $product;

$attribute_keys  = array_keys($attributes);
$variations_json = wp_json_encode($available_variations);
$variations_attr = function_exists('wc_esc_json') ? wc_esc_json($variations_json) : _wp_specialchars($variations_json, ENT_QUOTES, 'UTF-8', true);

do_action('woocommerce_before_add_to_cart_form');
?>

<form class="add-to-cart-form variations_form" action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink())); ?>" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint($product->get_id()); ?>" data-product_variations="<?php echo $variations_attr; ?>">
  <?php do_action('woocommerce_before_add_to_cart_button'); ?>

  <div class="variations">
    <?php foreach ($attributes as $attribute_name => $options) : ?>
      <div class="var-row">
        <label for="<?php echo esc_attr(sanitize_title($attribute_name)); ?>"><?php echo wc_attribute_label($attribute_name); ?></label>
        <?php
        wc_dropdown_variation_attribute_options([
            'options'   => $options,
            'attribute' => $attribute_name,
            'product'   => $product,
        ]);
        ?>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="single_variation_wrap">
    <?php
    do_action('woocommerce_single_variation');
    ?>
  </div>

  <?php do_action('woocommerce_after_add_to_cart_button'); ?>
</form>

<?php
do_action('woocommerce_after_add_to_cart_form');

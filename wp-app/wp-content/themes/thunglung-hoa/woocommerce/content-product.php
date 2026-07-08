<?php
/**
 * Product card template for loops
 *
 * @package ThungLungHoa
 */

defined('ABSPATH') || exit;

global $product;

// Ensure visibility.
if (empty($product) || !$product->is_visible()) {
    return;
}
?>
<a href="<?php the_permalink(); ?>" <?php wc_product_class('product-card', $product); ?>>
  <div class="product-media blob blob-a blob-frame">
    <?php if ($product->is_on_sale()) : ?>
      <span class="badge">Bán chạy</span>
    <?php endif; ?>
    <span class="wish" aria-label="Yêu thích">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20s-7-4.4-9.5-9A5 5 0 0 1 12 6a5 5 0 0 1 9.5 5c-2.5 4.6-9.5 9-9.5 9Z"/></svg>
    </span>
    <?php echo $product->get_image('woocommerce_thumbnail', ['style' => 'width:100%;height:100%;object-fit:cover;']); ?>
  </div>
  <div class="product-name"><?php the_title(); ?></div>
  <p class="product-cat"><?php echo wp_strip_all_tags(wc_get_product_category_list($product->get_id(), ', ')); ?></p>
  <div class="product-meta">
    <span class="product-price"><?php echo $product->get_price_html(); ?></span>
    <?php if ($product->get_average_rating()) : ?>
      <span class="stars"><?php echo str_repeat('★', round($product->get_average_rating())) . str_repeat('☆', 5 - round($product->get_average_rating())); ?></span>
    <?php endif; ?>
  </div>
</a>

<?php
/**
 * Single product content
 *
 * @package ThungLungHoa
 */

defined('ABSPATH') || exit;

global $product;
?>

<?php
do_action('woocommerce_before_single_product');

if (post_password_required()) {
    echo get_the_password_form();
    return;
}
?>

<div class="pd-layout container" id="product-<?php the_ID(); ?>">

  <div class="pd-gallery">
    <div class="pd-gallery-main blob blob-a blob-frame">
      <?php if ($product->get_image_id()) : ?>
        <?php echo wp_get_attachment_image($product->get_image_id(), 'woocommerce_single', false, ['style' => 'width:100%;height:100%;object-fit:contain;']); ?>
      <?php else : ?>
        <svg viewBox="0 0 300 300" width="60%">
          <rect x="50" y="50" width="200" height="200" class="fill-secondary" stroke="var(--line)" stroke-width="2" rx="20"/>
          <text x="150" y="160" text-anchor="middle" fill="var(--text)" font-size="14" font-family="var(--sans)">Hình ảnh sản phẩm</text>
        </svg>
      <?php endif; ?>
    </div>
    <div class="pd-thumbs">
      <?php
      $attachment_ids = $product->get_gallery_image_ids();
      if ($attachment_ids) {
          // First thumb: main product image
          echo '<div class="pd-thumb active">';
          echo wp_get_attachment_image($product->get_image_id(), 'thumbnail', false, ['style' => 'width:100%;height:100%;object-fit:contain;']);
          echo '</div>';
          foreach ($attachment_ids as $attachment_id) {
              echo '<div class="pd-thumb">';
              echo wp_get_attachment_image($attachment_id, 'thumbnail', false, ['style' => 'width:100%;height:100%;object-fit:contain;']);
              echo '</div>';
          }
      } elseif ($product->get_image_id()) {
          // Only main image, no gallery extras
          echo '<div class="pd-thumb active">';
          echo wp_get_attachment_image($product->get_image_id(), 'thumbnail', false, ['style' => 'width:100%;height:100%;object-fit:contain;']);
          echo '</div>';
      }
      ?>
    </div>
  </div>

  <div class="pd-info">
    <?php if ($rating = $product->get_average_rating()) : ?>
      <div class="stars-row">
        <span class="stars"><?php echo str_repeat('★', round($rating)) . str_repeat('☆', 5 - round($rating)); ?></span>
        <span><?php echo $rating; ?> · <?php echo $product->get_review_count(); ?> đánh giá</span>
      </div>
    <?php endif; ?>

    <h1><?php the_title(); ?></h1>

    <div class="pd-price">
      <?php echo $product->get_price_html(); ?>
    </div>

    <div class="pd-desc">
      <?php echo wpautop(wp_strip_all_tags($product->get_short_description() ?: $product->get_description())); ?>
    </div>

    <?php
    // Add to cart — only the form, not full summary (title/price/desc are rendered above)
    woocommerce_template_single_add_to_cart();
    ?>

    <div class="delivery-strip">
      <div class="d-item">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.2 2"/></svg>
        Giao trong 2 giờ nội thành
      </div>
      <div class="d-item">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l7 3v6c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V6Z"/><path d="M9 12l2 2 4-4"/></svg>
        Đảm bảo tươi 5 ngày
      </div>
      <div class="d-item">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 9h18"/></svg>
        Kèm thiệp viết tay
      </div>
    </div>
  </div>
</div>

<?php
// Tabs — full-width section below product detail
woocommerce_output_product_data_tabs();

// Related products
woocommerce_output_related_products();

do_action('woocommerce_after_single_product');

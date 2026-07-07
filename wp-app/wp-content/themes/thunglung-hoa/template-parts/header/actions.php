<?php
/**
 * Header: Search + Cart icons
 *
 * @package ThungLungHoa
 */
?>
<div class="header-actions">
  <a href="#" class="icon-btn" aria-label="Tìm kiếm">
    <?php echo tlh_search_icon(); ?>
  </a>
  <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="icon-btn" aria-label="Giỏ hàng">
    <?php echo tlh_cart_icon(); ?>
    <?php if (tlh_cart_count() > 0) : ?>
      <span class="cart-count"><?php echo esc_html(tlh_cart_count()); ?></span>
    <?php endif; ?>
  </a>
</div>

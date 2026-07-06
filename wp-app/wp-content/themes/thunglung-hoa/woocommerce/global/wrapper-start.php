<?php
/**
 * Content wrapper start
 *
 * @package ThungLungHoa
 */
?>
<div class="container shop-layout">
  <aside>
    <?php if (is_active_sidebar('shop-sidebar')) : ?>
      <?php dynamic_sidebar('shop-sidebar'); ?>
    <?php else : ?>
      <div class="filter-block">
        <p style="color:#8a7f75; font-size:.88rem;">Thêm widget vào <strong>Shop Sidebar</strong> để hiển thị bộ lọc tại đây.</p>
      </div>
    <?php endif; ?>
  </aside>
  <div>

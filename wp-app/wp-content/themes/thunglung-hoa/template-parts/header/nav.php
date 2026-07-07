<?php
/**
 * Header: Main navigation
 *
 * @package ThungLungHoa
 */
?>
<nav class="main-nav">
  <?php
  if (has_nav_menu('primary')) {
      wp_nav_menu([
          'theme_location' => 'primary',
          'container'      => false,
          'menu_class'     => '',
          'fallback_cb'    => false,
          'depth'          => 1,
          'items_wrap'     => '%3$s',
      ]);
  } else {
      ?>
      <a href="<?php echo esc_url(home_url('/')); ?>">Trang chủ</a>
      <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>">Cửa hàng</a>
      <a href="<?php echo esc_url(home_url('/#footer')); ?>">Liên hệ</a>
      <?php
  }
  ?>
</nav>

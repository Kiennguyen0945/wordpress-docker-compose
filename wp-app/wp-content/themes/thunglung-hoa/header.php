<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php
if (function_exists('wp_body_open')) {
    wp_body_open();
}
?>

<!-- ============ HEADER ============ -->
<header class="site-header">
  <div class="header-row container">
    <a href="<?php echo esc_url(home_url('/')); ?>" class="logo">
      <?php echo tlh_logo_svg(); ?>
      <?php bloginfo('name'); ?>
    </a>

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
          <a href="<?php echo esc_url(home_url('/#why')); ?>">Về chúng tôi</a>
          <a href="<?php echo esc_url(home_url('/#footer')); ?>">Liên hệ</a>
          <?php
      }
      ?>
    </nav>

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
  </div>
</header>

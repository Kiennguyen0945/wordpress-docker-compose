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
  <div class="account-menu">
    <button type="button" class="icon-btn account-toggle" aria-label="Tài khoản" aria-expanded="false" aria-controls="account-popover">
      <?php echo tlh_user_icon(); ?>
    </button>
    <div class="account-popover" id="account-popover" hidden>
      <?php if (tlh_is_customer_logged_in()) : ?>
        <?php $current_user = wp_get_current_user(); ?>
        <div class="account-popover__header">
          <span class="account-popover__eyebrow">Tài khoản</span>
          <strong><?php echo esc_html($current_user->display_name ?: $current_user->user_email); ?></strong>
        </div>
        <a href="<?php echo esc_url(tlh_customer_profile_url()); ?>">Thông tin cá nhân</a>
        <a href="<?php echo esc_url(home_url('/cai-dat')); ?>">Cài đặt</a>
        <a href="<?php echo esc_url(tlh_customer_logout_url()); ?>">Đăng xuất</a>
      <?php else : ?>
        <div class="account-popover__header">
          <span class="account-popover__eyebrow">Xin chào</span>
          <strong>Vui lòng đăng nhập</strong>
        </div>
        <a href="<?php echo esc_url(home_url('/dang-nhap')); ?>">Đăng nhập</a>
        <a href="<?php echo esc_url(home_url('/dang-ky')); ?>">Đăng ký tài khoản</a>
        <a href="<?php echo esc_url(home_url('/cai-dat')); ?>">Cài đặt</a>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php if (!tlh_is_customer_logged_in()) : ?>
  <div class="auth-modal" id="auth-modal" hidden>
    <div class="auth-modal__backdrop" data-auth-close></div>
    <div class="auth-modal__dialog" role="dialog" aria-modal="true">
      <button type="button" class="auth-modal__close" aria-label="Đóng" data-auth-close>&times;</button>
      <div class="auth-panel" data-auth-panel="login">
        <?php get_template_part('template-parts/user/login', null, ['form_suffix' => 'modal', 'is_modal' => true]); ?>
      </div>
      <div class="auth-panel" data-auth-panel="register" hidden>
        <?php get_template_part('template-parts/user/register', null, ['form_suffix' => 'modal', 'is_modal' => true]); ?>
      </div>
    </div>
  </div>
<?php endif; ?>

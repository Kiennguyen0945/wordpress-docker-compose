<?php
/**
 * User: Login form
 *
 * @package ThungLungHoa
 */
$form_suffix = isset($args['form_suffix']) ? sanitize_key($args['form_suffix']) : '';
$form_id     = $form_suffix ? 'tlh-login-form-' . $form_suffix : 'tlh-login-form';
$email_id    = $form_suffix ? 'login-email-' . $form_suffix : 'login-email';
$password_id = $form_suffix ? 'login-password-' . $form_suffix : 'login-password';
$remember_id = $form_suffix ? 'remember-' . $form_suffix : 'remember';
$is_modal    = !empty($args['is_modal']);
?>
<div class="user-form-wrapper">
  <h2>Đăng nhập</h2>
  <form class="user-form" id="<?php echo esc_attr($form_id); ?>" data-auth-action="tlh_login" method="post">
    <div class="form-group">
      <label for="<?php echo esc_attr($email_id); ?>">Email <span class="req">*</span></label>
      <input type="email" id="<?php echo esc_attr($email_id); ?>" name="email" required placeholder="email@example.com">
    </div>
    <div class="form-group">
      <label for="<?php echo esc_attr($password_id); ?>">Mật khẩu <span class="req">*</span></label>
      <input type="password" id="<?php echo esc_attr($password_id); ?>" name="password" required>
    </div>
    <div class="form-group" style="flex-direction:row;align-items:center;gap:8px;">
      <input type="checkbox" id="<?php echo esc_attr($remember_id); ?>" name="remember" style="width:auto;">
      <label for="<?php echo esc_attr($remember_id); ?>" style="margin:0;">Ghi nhớ đăng nhập</label>
    </div>
    <button type="submit" class="btn btn-accent btn-block">Đăng nhập</button>
  </form>
  <p style="text-align:center;margin-top:18px;font-size:.9rem;">
    Chưa có tài khoản?
    <?php if ($is_modal) : ?>
      <a href="<?php echo esc_url(home_url('/dang-ky')); ?>" class="link-accent" data-auth-switch="register">Đăng ký ngay</a>
    <?php else : ?>
      <a href="<?php echo esc_url(home_url('/dang-ky')); ?>" class="link-accent">Đăng ký ngay</a>
    <?php endif; ?>
  </p>
</div>

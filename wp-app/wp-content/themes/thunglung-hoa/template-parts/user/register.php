<?php
/**
 * User: Register form
 *
 * @package ThungLungHoa
 */
$form_suffix  = isset($args['form_suffix']) ? sanitize_key($args['form_suffix']) : '';
$form_id      = $form_suffix ? 'tlh-register-form-' . $form_suffix : 'tlh-register-form';
$firstname_id = $form_suffix ? 'reg-firstname-' . $form_suffix : 'reg-firstname';
$lastname_id  = $form_suffix ? 'reg-lastname-' . $form_suffix : 'reg-lastname';
$email_id     = $form_suffix ? 'reg-email-' . $form_suffix : 'reg-email';
$phone_id     = $form_suffix ? 'reg-phone-' . $form_suffix : 'reg-phone';
$password_id  = $form_suffix ? 'reg-password-' . $form_suffix : 'reg-password';
$is_modal     = !empty($args['is_modal']);
?>
<div class="user-form-wrapper">
  <h2>Đăng ký tài khoản</h2>
  <form class="user-form" id="<?php echo esc_attr($form_id); ?>" data-auth-action="tlh_register" method="post">
    <div class="form-row">
      <div class="form-group">
        <label for="<?php echo esc_attr($firstname_id); ?>">Họ <span class="req">*</span></label>
        <input type="text" id="<?php echo esc_attr($firstname_id); ?>" name="firstname" required>
      </div>
      <div class="form-group">
        <label for="<?php echo esc_attr($lastname_id); ?>">Tên <span class="req">*</span></label>
        <input type="text" id="<?php echo esc_attr($lastname_id); ?>" name="lastname" required>
      </div>
    </div>
    <div class="form-group">
      <label for="<?php echo esc_attr($email_id); ?>">Email <span class="req">*</span></label>
      <input type="email" id="<?php echo esc_attr($email_id); ?>" name="email" required placeholder="email@example.com">
    </div>
    <div class="form-group">
      <label for="<?php echo esc_attr($phone_id); ?>">Số điện thoại</label>
      <input type="tel" id="<?php echo esc_attr($phone_id); ?>" name="phone" placeholder="09xx xxx xxx">
    </div>
    <div class="form-group">
      <label for="<?php echo esc_attr($password_id); ?>">Mật khẩu <span class="req">*</span></label>
      <input type="password" id="<?php echo esc_attr($password_id); ?>" name="password" required minlength="6">
    </div>
    <button type="submit" class="btn btn-accent btn-block">Đăng ký</button>
  </form>
  <p style="text-align:center;margin-top:18px;font-size:.9rem;">
    Đã có tài khoản?
    <?php if ($is_modal) : ?>
      <a href="<?php echo esc_url(home_url('/dang-nhap')); ?>" class="link-accent" data-auth-switch="login">Đăng nhập</a>
    <?php else : ?>
      <a href="<?php echo esc_url(home_url('/dang-nhap')); ?>" class="link-accent">Đăng nhập</a>
    <?php endif; ?>
  </p>
</div>

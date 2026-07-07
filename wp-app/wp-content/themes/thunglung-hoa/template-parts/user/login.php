<?php
/**
 * User: Login form
 *
 * @package ThungLungHoa
 */
?>
<div class="user-form-wrapper">
  <h2>Đăng nhập</h2>
  <form class="user-form" id="tlh-login-form" method="post">
    <div class="form-group">
      <label for="login-email">Email <span class="req">*</span></label>
      <input type="email" id="login-email" name="email" required placeholder="email@example.com">
    </div>
    <div class="form-group">
      <label for="login-password">Mật khẩu <span class="req">*</span></label>
      <input type="password" id="login-password" name="password" required>
    </div>
    <div class="form-group" style="flex-direction:row;align-items:center;gap:8px;">
      <input type="checkbox" id="remember" name="remember" style="width:auto;">
      <label for="remember" style="margin:0;">Ghi nhớ đăng nhập</label>
    </div>
    <button type="submit" class="btn btn-accent btn-block">Đăng nhập</button>
  </form>
  <p style="text-align:center;margin-top:18px;font-size:.9rem;">
    Chưa có tài khoản? <a href="<?php echo esc_url(home_url('/dang-ky')); ?>" class="link-accent">Đăng ký ngay</a>
  </p>
</div>

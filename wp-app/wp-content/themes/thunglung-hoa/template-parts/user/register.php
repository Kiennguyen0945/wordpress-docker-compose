<?php
/**
 * User: Register form
 *
 * @package ThungLungHoa
 */
?>
<div class="user-form-wrapper">
  <h2>Đăng ký tài khoản</h2>
  <form class="user-form" id="tlh-register-form" method="post">
    <div class="form-row">
      <div class="form-group">
        <label for="reg-firstname">Họ <span class="req">*</span></label>
        <input type="text" id="reg-firstname" name="firstname" required>
      </div>
      <div class="form-group">
        <label for="reg-lastname">Tên <span class="req">*</span></label>
        <input type="text" id="reg-lastname" name="lastname" required>
      </div>
    </div>
    <div class="form-group">
      <label for="reg-email">Email <span class="req">*</span></label>
      <input type="email" id="reg-email" name="email" required placeholder="email@example.com">
    </div>
    <div class="form-group">
      <label for="reg-phone">Số điện thoại</label>
      <input type="tel" id="reg-phone" name="phone" placeholder="09xx xxx xxx">
    </div>
    <div class="form-group">
      <label for="reg-password">Mật khẩu <span class="req">*</span></label>
      <input type="password" id="reg-password" name="password" required minlength="6">
    </div>
    <button type="submit" class="btn btn-accent btn-block">Đăng ký</button>
  </form>
  <p style="text-align:center;margin-top:18px;font-size:.9rem;">
    Đã có tài khoản? <a href="<?php echo esc_url(home_url('/dang-nhap')); ?>" class="link-accent">Đăng nhập</a>
  </p>
</div>

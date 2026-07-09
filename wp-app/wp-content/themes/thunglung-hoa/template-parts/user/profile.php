<?php
/**
 * User: Profile page
 *
 * @package ThungLungHoa
 */
$user = wp_get_current_user();
$billing_phone = get_user_meta($user->ID, 'billing_phone', true);
$dob = get_user_meta($user->ID, 'date_of_birth', true);
$gender = get_user_meta($user->ID, 'gender', true);
$recipient_first = get_user_meta($user->ID, 'billing_first_name', true);
$recipient_last = get_user_meta($user->ID, 'billing_last_name', true);
$address_1 = get_user_meta($user->ID, 'billing_address_1', true);
$address_2 = get_user_meta($user->ID, 'billing_address_2', true);
$city = get_user_meta($user->ID, 'billing_city', true);
$state = get_user_meta($user->ID, 'billing_state', true);
$postcode = get_user_meta($user->ID, 'billing_postcode', true);
$orders = wc_get_orders(['customer_id' => $user->ID, 'limit' => 5, 'orderby' => 'date', 'order' => 'DESC']);
$order_count = wc_get_customer_order_count($user->ID);
$points = intval(get_user_meta($user->ID, 'loyalty_points', true));
?>
<div class="user-profile-wrapper">
  <div class="profile-layout">
    <!-- ========== CỘT TRÁI ========== -->
    <div class="profile-col profile-col--left">
      <div class="profile-header-card">
        <div>
          <div class="profile-avatar">
            <?php echo get_avatar($user->ID, 96, '', '', ['class' => 'avatar-img']); ?>
          </div>
          <div class="profile-name">
            <h2><?php echo esc_html($user->display_name ?: $user->user_email); ?></h2>
            <p class="profile-email"><?php echo esc_html($user->user_email); ?></p>
          </div>
        </div>
        <div class="profile-summary-list">
          <div class="profile-stat">
            <strong><?php echo esc_html($order_count); ?></strong>
            <span>Đơn hàng</span>
          </div>
          <div class="profile-stat">
            <strong><?php echo esc_html($points); ?></strong>
            <span>Điểm thưởng</span>
          </div>
          <div class="profile-stat">
            <strong>5+</strong>
            <span>Voucher</span>
          </div>
        </div>
      </div>

      <section id="profile-info" class="profile-section">
        <h2>Thông tin cá nhân</h2>
        <div class="profile-grid">
          <div class="profile-card">
            <strong>Họ</strong>
            <span><?php echo esc_html($user->first_name ?: 'Chưa cập nhật'); ?></span>
          </div>
          <div class="profile-card">
            <strong>Tên</strong>
            <span><?php echo esc_html($user->last_name ?: 'Chưa cập nhật'); ?></span>
          </div>
          <div class="profile-card">
            <strong>Email</strong>
            <span><?php echo esc_html($user->user_email); ?></span>
          </div>
          <div class="profile-card">
            <strong>Số điện thoại</strong>
            <span><?php echo esc_html($billing_phone ?: 'Chưa cập nhật'); ?></span>
          </div>
          <div class="profile-card">
            <strong>Ngày sinh</strong>
            <span><?php echo esc_html($dob ?: 'Chưa cập nhật'); ?></span>
          </div>
          <div class="profile-card">
            <strong>Giới tính</strong>
            <span><?php echo esc_html($gender ?: 'Chưa cập nhật'); ?></span>
          </div>
        </div>
      </section>

      <section id="shipping-address" class="profile-section">
        <h2>Địa chỉ giao hàng</h2>
        <div class="card-panel">
          <div class="address-line"><strong><?php echo esc_html(trim($recipient_first . ' ' . $recipient_last) ?: $user->display_name); ?></strong></div>
          <div class="address-line"><?php echo esc_html($billing_phone ?: 'Chưa cập nhật'); ?></div>
          <div class="address-line"><?php echo esc_html($address_1 ?: 'Chưa cập nhật'); ?></div>
          <div class="address-line"><?php echo esc_html($address_2 ?: 'Chưa cập nhật'); ?></div>
          <div class="address-line"><?php echo esc_html($city ?: 'Chưa cập nhật') . ($city && $state ? ', ' : '') . esc_html($state ?: ''); ?></div>
          <div class="address-line"><?php echo esc_html($postcode ?: 'Chưa cập nhật'); ?></div>
          <a href="#profile-form" class="btn btn-outline-accent mt-16">Sửa địa chỉ</a>
        </div>
      </section>

      <section id="password" class="profile-section">
        <h2>Đổi mật khẩu</h2>
        <form class="user-form" id="tlh-password-form" method="post">
          <div class="form-row">
            <div class="form-group">
              <label for="current-password">Mật khẩu hiện tại</label>
              <input type="password" id="current-password" name="current_password" required>
            </div>
            <div class="form-group">
              <label for="new-password">Mật khẩu mới</label>
              <input type="password" id="new-password" name="new_password" required>
            </div>
            <div class="form-group full">
              <label for="confirm-password">Nhập lại mật khẩu</label>
              <input type="password" id="confirm-password" name="confirm_password" required>
            </div>
          </div>
          <button type="submit" class="btn btn-accent">Đổi mật khẩu</button>
        </form>
      </section>

      <section id="profile-form" class="profile-section">
        <h2>Cập nhật hồ sơ</h2>
        <form class="user-form" id="tlh-profile-form" method="post">
          <div class="form-row">
            <div class="form-group">
              <label for="profile-firstname">Họ</label>
              <input type="text" id="profile-firstname" name="firstname" value="<?php echo esc_attr($user->first_name); ?>" required>
            </div>
            <div class="form-group">
              <label for="profile-lastname">Tên</label>
              <input type="text" id="profile-lastname" name="lastname" value="<?php echo esc_attr($user->last_name); ?>" required>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="profile-phone">Số điện thoại</label>
              <input type="tel" id="profile-phone" name="phone" value="<?php echo esc_attr($billing_phone); ?>" maxlength="11">
            </div>
            <div class="form-group">
              <label for="profile-dob">Ngày sinh</label>
              <input type="date" id="profile-dob" name="dob" value="<?php echo esc_attr($dob); ?>">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group full">
              <label for="profile-gender">Giới tính</label>
              <select id="profile-gender" name="gender">
                <option value="" <?php selected($gender, ''); ?>>Chưa chọn</option>
                <option value="nam" <?php selected($gender, 'nam'); ?>>Nam</option>
                <option value="nu" <?php selected($gender, 'nu'); ?>>Nữ</option>
                <option value="khac" <?php selected($gender, 'khac'); ?>>Khác</option>
              </select>
            </div>
          </div>

          <div class="profile-subsection">
            <h3>Địa chỉ giao hàng</h3>
            <div class="form-row">
              <div class="form-group">
                <label for="recipient-first">Tên người nhận</label>
                <input type="text" id="recipient-first" name="recipient_first" value="<?php echo esc_attr($recipient_first); ?>">
              </div>
              <div class="form-group">
                <label for="recipient-last">Tên người nhận (phụ)</label>
                <input type="text" id="recipient-last" name="recipient_last" value="<?php echo esc_attr($recipient_last); ?>">
              </div>
            </div>
            <div class="form-row">
              <div class="form-group form-group--span2">
                <label for="address-1">Địa chỉ chi tiết</label>
                <input type="text" id="address-1" name="address_1" value="<?php echo esc_attr($address_1); ?>">
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label for="address-2">Phường/Xã</label>
                <input type="text" id="address-2" name="address_2" value="<?php echo esc_attr($address_2); ?>">
              </div>
              <div class="form-group">
                <label for="city">Tỉnh/Thành</label>
                <input type="text" id="city" name="city" value="<?php echo esc_attr($city); ?>">
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label for="state">Quận/Huyện</label>
                <input type="text" id="state" name="state" value="<?php echo esc_attr($state); ?>">
              </div>
              <div class="form-group">
                <label for="postcode">Mã bưu điện</label>
                <input type="text" id="postcode" name="postcode" value="<?php echo esc_attr($postcode); ?>">
              </div>
            </div>
          </div>

          <button type="submit" class="btn btn-accent">Cập nhật hồ sơ</button>
        </form>
      </section>
    </div>

    <!-- ========== CỘT PHẢI ========== -->
    <div class="profile-col profile-col--right">
      <section id="orders" class="profile-section">
        <h2>Lịch sử đơn hàng</h2>
        <?php if (empty($orders)) : ?>
          <p>Bạn chưa có đơn hàng nào.</p>
        <?php else : ?>
          <div class="order-list">
            <?php foreach ($orders as $order) : ?>
              <div class="order-item">
                <div>
                  <strong>#<?php echo esc_html($order->get_order_number()); ?></strong>
                  <div><?php echo esc_html(wc_format_datetime($order->get_date_created())); ?></div>
                </div>
                <div><?php echo wp_kses_post($order->get_formatted_order_total()); ?></div>
                <div><?php echo esc_html(wc_get_order_status_name($order->get_status())); ?></div>
                <div><a href="<?php echo esc_url($order->get_view_order_url()); ?>">Xem</a></div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </section>

      <section id="wishlist" class="profile-section">
        <h2>Yêu thích</h2>
        <p>Danh sách yêu thích sẽ được hiển thị tại đây khi bạn lưu sản phẩm.</p>
      </section>

      <section id="vouchers" class="profile-section">
        <h2>Mã giảm giá</h2>
        <div class="card-panel voucher-grid">
          <div class="voucher-item">Giảm 10%</div>
          <div class="voucher-item">Miễn phí vận chuyển</div>
        </div>
      </section>

      <section id="profile-newsletter" class="profile-section">
        <h2>Nhận ưu đãi sớm nhất</h2>
        <div class="card-panel">
          <p style="font-size:.88rem; color:#5b5049; margin-bottom:14px;">Đăng ký để nhận ưu đãi dịp lễ và mẫu hoa mới mỗi tuần.</p>
          <div class="newsletter-row">
            <input type="email" placeholder="Email của bạn">
            <button>Gửi</button>
          </div>
        </div>
      </section>

      <section id="profile-support" class="profile-section">
        <h2>Liên hệ hỗ trợ</h2>
        <div class="card-panel support-card">
          <p>Cần trợ giúp? Liên hệ với chúng tôi qua:</p>
          <div class="support-links">
            <span>📞 Hotline: 0909 xxx xxx</span>
            <span>✉️ Email: support@thunglunghoa.vn</span>
            <span>💬 Zalo: 0909 xxx xxx</span>
          </div>
        </div>
      </section>
    </div>
  </div>
</div>

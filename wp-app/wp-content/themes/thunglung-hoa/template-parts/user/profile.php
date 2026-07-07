<?php
/**
 * User: Profile page
 *
 * @package ThungLungHoa
 */
$user = wp_get_current_user();
?>
<div class="user-profile-wrapper">
  <h2>Hồ sơ cá nhân</h2>

  <div class="profile-header-card">
    <div class="profile-avatar">
      <?php echo get_avatar($user->ID, 96, '', '', ['class' => 'avatar-img']); ?>
    </div>
    <div class="profile-name">
      <h3><?php echo esc_html($user->display_name); ?></h3>
      <span class="profile-email"><?php echo esc_html($user->user_email); ?></span>
    </div>
  </div>

  <form class="user-form" id="tlh-profile-form" method="post">
    <div class="form-row">
      <div class="form-group">
        <label for="profile-firstname">Họ</label>
        <input type="text" id="profile-firstname" name="firstname" value="<?php echo esc_attr($user->first_name); ?>">
      </div>
      <div class="form-group">
        <label for="profile-lastname">Tên</label>
        <input type="text" id="profile-lastname" name="lastname" value="<?php echo esc_attr($user->last_name); ?>">
      </div>
    </div>
    <div class="form-group">
      <label for="profile-phone">Số điện thoại</label>
      <input type="tel" id="profile-phone" name="phone" value="<?php echo esc_attr(get_user_meta($user->ID, 'billing_phone', true)); ?>">
    </div>
    <div class="form-group">
      <label for="profile-address">Địa chỉ giao hàng</label>
      <textarea id="profile-address" name="address" rows="2"><?php echo esc_textarea(get_user_meta($user->ID, 'billing_address_1', true)); ?></textarea>
    </div>
    <button type="submit" class="btn btn-accent">Cập nhật hồ sơ</button>
  </form>

  <div class="profile-section">
    <h3>Đơn hàng của tôi</h3>
    <?php
    $orders = wc_get_orders(['customer_id' => $user->ID, 'limit' => 5]);
    if (empty($orders)) {
        echo '<p style="color:#8a7f75;">Bạn chưa có đơn hàng nào.</p>';
    } else {
        echo '<div class="order-list">';
        foreach ($orders as $order) {
            echo '<div class="order-item">';
            echo '<span class="order-id">#' . esc_html($order->get_order_number()) . '</span>';
            echo '<span class="order-date">' . esc_html(wc_format_datetime($order->get_date_created())) . '</span>';
            echo '<span class="order-status">' . esc_html(wc_get_order_status_name($order->get_status())) . '</span>';
            echo '<span class="order-total">' . wp_kses_post($order->get_formatted_order_total()) . '</span>';
            echo '</div>';
        }
        echo '</div>';
    }
    ?>
  </div>

  <div class="profile-section" style="text-align:right;">
    <a href="<?php echo wp_logout_url(home_url()); ?>" class="btn btn-outline-accent">Đăng xuất</a>
  </div>
</div>

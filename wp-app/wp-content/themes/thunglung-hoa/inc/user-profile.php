<?php
/**
 * User Profile
 *
 * Người phụ trách: [Người A] — sửa file này khi làm trang hồ sơ cá nhân.
 *
 * @package ThungLungHoa
 * @since 1.0.0
 */

/**
 * Custom profile page shortcode: [tlh_user_profile]
 */
add_shortcode('tlh_user_profile', 'tlh_render_profile');
function tlh_render_profile() {
    if (!tlh_is_customer_logged_in()) {
        return '<p>Vui lòng <a href="' . esc_url(home_url('/dang-nhap')) . '">đăng nhập</a> để xem hồ sơ.</p>';
    }

    ob_start();
    get_template_part('template-parts/user/profile');
    return ob_get_clean();
}

/**
 * Handle profile update via AJAX.
 */
add_action('wp_ajax_tlh_update_profile', 'tlh_handle_update_profile');
function tlh_handle_update_profile() {
    check_ajax_referer('tlh_ajax_nonce', 'nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Vui lòng đăng nhập để cập nhật hồ sơ.']);
    }

    $user_id = get_current_user_id();
    $first_name = isset($_POST['firstname']) ? sanitize_text_field(wp_unslash($_POST['firstname'])) : '';
    $last_name  = isset($_POST['lastname']) ? sanitize_text_field(wp_unslash($_POST['lastname'])) : '';
    $phone      = isset($_POST['phone']) ? sanitize_text_field(wp_unslash($_POST['phone'])) : '';
    $dob        = isset($_POST['dob']) ? sanitize_text_field(wp_unslash($_POST['dob'])) : '';
    $gender     = isset($_POST['gender']) ? sanitize_text_field(wp_unslash($_POST['gender'])) : '';
    $recipient_first = isset($_POST['recipient_first']) ? sanitize_text_field(wp_unslash($_POST['recipient_first'])) : '';
    $recipient_last  = isset($_POST['recipient_last']) ? sanitize_text_field(wp_unslash($_POST['recipient_last'])) : '';
    $address_1  = isset($_POST['address_1']) ? sanitize_text_field(wp_unslash($_POST['address_1'])) : '';
    $address_2  = isset($_POST['address_2']) ? sanitize_text_field(wp_unslash($_POST['address_2'])) : '';
    $city       = isset($_POST['city']) ? sanitize_text_field(wp_unslash($_POST['city'])) : '';
    $state      = isset($_POST['state']) ? sanitize_text_field(wp_unslash($_POST['state'])) : '';
    $postcode   = isset($_POST['postcode']) ? sanitize_text_field(wp_unslash($_POST['postcode'])) : '';

    if (!$first_name || !$last_name) {
        wp_send_json_error(['message' => 'Vui lòng nhập họ và tên.']);
    }

    $update_result = wp_update_user([
        'ID'         => $user_id,
        'first_name' => $first_name,
        'last_name'  => $last_name,
        'display_name' => trim($first_name . ' ' . $last_name),
    ]);

    if (is_wp_error($update_result)) {
        wp_send_json_error(['message' => 'Không thể cập nhật hồ sơ. Vui lòng thử lại.']);
    }

    update_user_meta($user_id, 'billing_phone', $phone);
    update_user_meta($user_id, 'date_of_birth', $dob);
    update_user_meta($user_id, 'gender', $gender);
    update_user_meta($user_id, 'billing_first_name', $recipient_first);
    update_user_meta($user_id, 'billing_last_name', $recipient_last);
    update_user_meta($user_id, 'billing_address_1', $address_1);
    update_user_meta($user_id, 'billing_address_2', $address_2);
    update_user_meta($user_id, 'billing_city', $city);
    update_user_meta($user_id, 'billing_state', $state);
    update_user_meta($user_id, 'billing_postcode', $postcode);

    wp_send_json_success(['message' => 'Cập nhật hồ sơ thành công.']);
}

add_action('wp_ajax_tlh_update_password', 'tlh_handle_update_password');
function tlh_handle_update_password() {
    check_ajax_referer('tlh_ajax_nonce', 'nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Vui lòng đăng nhập để đổi mật khẩu.']);
    }

    $user_id = get_current_user_id();
    $current_password = isset($_POST['current_password']) ? (string) wp_unslash($_POST['current_password']) : '';
    $new_password     = isset($_POST['new_password']) ? (string) wp_unslash($_POST['new_password']) : '';
    $confirm_password = isset($_POST['confirm_password']) ? (string) wp_unslash($_POST['confirm_password']) : '';

    if (!$current_password || !$new_password || !$confirm_password) {
        wp_send_json_error(['message' => 'Vui lòng điền đầy đủ thông tin đổi mật khẩu.']);
    }

    if ($new_password !== $confirm_password) {
        wp_send_json_error(['message' => 'Mật khẩu mới và nhập lại không khớp.']);
    }

    if (strlen($new_password) < 6) {
        wp_send_json_error(['message' => 'Mật khẩu mới cần ít nhất 6 ký tự.']);
    }

    $user = wp_get_current_user();
    if (!wp_check_password($current_password, $user->user_pass, $user_id)) {
        wp_send_json_error(['message' => 'Mật khẩu hiện tại không đúng.']);
    }

    wp_set_password($new_password, $user_id);
    wp_set_auth_cookie($user_id, true);

    wp_send_json_success(['message' => 'Đổi mật khẩu thành công.']);
}

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
    if (!is_user_logged_in()) {
        return '<p>Vui lòng <a href="' . wp_login_url() . '">đăng nhập</a> để xem hồ sơ.</p>';
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
    // TODO: Implement profile update logic here
    wp_send_json_error(['message' => 'Chưa triển khai']);
}

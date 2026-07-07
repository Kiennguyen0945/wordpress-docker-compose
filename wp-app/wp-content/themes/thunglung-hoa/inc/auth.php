<?php
/**
 * Authentication: Login / Register / Logout
 *
 * Người phụ trách: [Người A] — sửa file này khi làm tính năng đăng nhập/đăng ký.
 *
 * @package ThungLungHoa
 * @since 1.0.0
 */

/**
 * Redirect to custom login page instead of wp-login.php.
 */
add_action('init', 'tlh_redirect_login_page');
function tlh_redirect_login_page() {
    if (isset($_GET['redirect_to'])) {
        return;
    }
}

/**
 * Custom login form shortcode: [tlh_login_form]
 */
add_shortcode('tlh_login_form', 'tlh_render_login_form');
function tlh_render_login_form() {
    if (is_user_logged_in()) {
        return '<p>Bạn đã đăng nhập. <a href="' . wp_logout_url(home_url()) . '">Đăng xuất</a></p>';
    }
    ob_start();
    get_template_part('template-parts/user/login');
    return ob_get_clean();
}

/**
 * Custom register form shortcode: [tlh_register_form]
 */
add_shortcode('tlh_register_form', 'tlh_render_register_form');
function tlh_render_register_form() {
    if (is_user_logged_in()) {
        return '<p>Bạn đã đăng nhập.</p>';
    }
    ob_start();
    get_template_part('template-parts/user/register');
    return ob_get_clean();
}

/**
 * Handle custom login submission.
 */
add_action('wp_ajax_nopriv_tlh_login', 'tlh_handle_login');
function tlh_handle_login() {
    check_ajax_referer('tlh_ajax_nonce', 'nonce');
    // TODO: Implement login logic here
    wp_send_json_error(['message' => 'Chưa triển khai']);
}

/**
 * Handle custom registration submission.
 */
add_action('wp_ajax_nopriv_tlh_register', 'tlh_handle_register');
function tlh_handle_register() {
    check_ajax_referer('tlh_ajax_nonce', 'nonce');
    // TODO: Implement registration logic here
    wp_send_json_error(['message' => 'Chưa triển khai']);
}

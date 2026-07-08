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
 * Keep customer accounts on the storefront, not in the WordPress dashboard.
 */
add_action('admin_init', 'tlh_redirect_customer_from_admin');
function tlh_redirect_customer_from_admin() {
    if (wp_doing_ajax() || !is_user_logged_in() || current_user_can('manage_options')) {
        return;
    }

    $user = wp_get_current_user();
    if (in_array('customer', (array) $user->roles, true)) {
        wp_safe_redirect(home_url('/ho-so'));
        exit;
    }
}

add_filter('show_admin_bar', 'tlh_hide_admin_bar_for_customers');
function tlh_hide_admin_bar_for_customers($show) {
    if (is_user_logged_in() && !current_user_can('manage_options')) {
        return false;
    }

    return $show;
}

function tlh_is_customer_logged_in() {
    if (!is_user_logged_in()) {
        return false;
    }

    $user = wp_get_current_user();
    return in_array('customer', (array) $user->roles, true);
}

/**
 * Customer logout should always return to the flower shop homepage.
 */
function tlh_customer_profile_url() {
    $profile_page = get_page_by_path('ho-so');
    if ($profile_page instanceof WP_Post) {
        return get_permalink($profile_page);
    }
    return home_url('/ho-so');
}

function tlh_customer_logout_url() {
    return wp_nonce_url(
        add_query_arg('tlh_customer_logout', '1', home_url('/')),
        'tlh_customer_logout'
    );
}

add_action('init', 'tlh_handle_customer_logout');
function tlh_handle_customer_logout() {
    if (empty($_GET['tlh_customer_logout'])) {
        return;
    }

    if (!isset($_GET['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'tlh_customer_logout')) {
        wp_safe_redirect(home_url('/'));
        exit;
    }

    if (tlh_is_customer_logged_in()) {
        wp_logout();
    }

    wp_safe_redirect(home_url('/'));
    exit;
}

add_filter('logout_redirect', 'tlh_customer_logout_redirect', 10, 3);
function tlh_customer_logout_redirect($redirect_to, $requested_redirect_to, $user) {
    if ($user instanceof WP_User && in_array('customer', (array) $user->roles, true)) {
        return home_url('/');
    }

    return $redirect_to ?: home_url('/');
}

add_filter('woocommerce_logout_default_redirect_url', 'tlh_woocommerce_logout_redirect_home');
function tlh_woocommerce_logout_redirect_home() {
    return home_url('/');
}

/**
 * Custom login form shortcode: [tlh_login_form]
 */
add_shortcode('tlh_login_form', 'tlh_render_login_form');
function tlh_render_login_form() {
    if (tlh_is_customer_logged_in()) {
        return '<p>Bạn đã đăng nhập. <a href="' . esc_url(tlh_customer_logout_url()) . '">Đăng xuất</a></p>';
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
    if (tlh_is_customer_logged_in()) {
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
add_action('wp_ajax_tlh_login', 'tlh_handle_login');
function tlh_handle_login() {
    check_ajax_referer('tlh_ajax_nonce', 'nonce');

    if (is_user_logged_in() && !tlh_is_customer_logged_in()) {
        wp_send_json_error(['message' => 'Bạn đang đăng nhập WP Admin. Vui lòng dùng cửa sổ ẩn danh để đăng nhập tài khoản khách hàng.']);
    }

    $email    = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
    $password = isset($_POST['password']) ? (string) wp_unslash($_POST['password']) : '';
    $remember = !empty($_POST['remember']);

    if (!$email || !$password) {
        wp_send_json_error(['message' => 'Vui lòng nhập đầy đủ email và mật khẩu.']);
    }

    if (!is_email($email)) {
        wp_send_json_error(['message' => 'Email không hợp lệ.']);
    }

    $user = get_user_by('email', $email);
    if (!$user) {
        wp_send_json_error(['message' => 'Tài khoản không tồn tại.']);
    }

    if (!in_array('customer', (array) $user->roles, true)) {
        wp_send_json_error(['message' => 'Trang này chỉ dành cho tài khoản khách hàng. Tài khoản quản trị vẫn giữ đăng nhập trong WP Admin.']);
    }

    $signed_in = wp_signon([
        'user_login'    => $user->user_login,
        'user_password' => $password,
        'remember'      => $remember,
    ], is_ssl());

    if (is_wp_error($signed_in)) {
        wp_send_json_error(['message' => 'Email hoặc mật khẩu không đúng.']);
    }

    wp_send_json_success([
        'message'  => 'Đăng nhập thành công.',
        'redirect' => home_url('/'),
    ]);
}

/**
 * Handle custom registration submission.
 */
add_action('wp_ajax_nopriv_tlh_register', 'tlh_handle_register');
add_action('wp_ajax_tlh_register', 'tlh_handle_register');
function tlh_handle_register() {
    check_ajax_referer('tlh_ajax_nonce', 'nonce');

    if (tlh_is_customer_logged_in()) {
        wp_send_json_error(['message' => 'Bạn đã đăng nhập tài khoản khách hàng.']);
    }

    if (is_user_logged_in()) {
        wp_send_json_error(['message' => 'Bạn đang đăng nhập WP Admin. Vui lòng dùng cửa sổ ẩn danh để đăng ký tài khoản khách hàng.']);
    }

    $first_name = isset($_POST['firstname']) ? sanitize_text_field(wp_unslash($_POST['firstname'])) : '';
    $last_name  = isset($_POST['lastname']) ? sanitize_text_field(wp_unslash($_POST['lastname'])) : '';
    $email      = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
    $phone      = isset($_POST['phone']) ? sanitize_text_field(wp_unslash($_POST['phone'])) : '';
    $password   = isset($_POST['password']) ? (string) wp_unslash($_POST['password']) : '';

    if (!$first_name || !$last_name || !$email || !$password) {
        wp_send_json_error(['message' => 'Vui lòng nhập đầy đủ thông tin bắt buộc.']);
    }

    if (!is_email($email)) {
        wp_send_json_error(['message' => 'Email không hợp lệ.']);
    }

    if (email_exists($email)) {
        wp_send_json_error(['message' => 'Email này đã được đăng ký.']);
    }

    if (strlen($password) < 6) {
        wp_send_json_error(['message' => 'Mật khẩu cần ít nhất 6 ký tự.']);
    }

    $email_parts    = explode('@', $email);
    $base_username  = sanitize_user($email_parts[0], true);
    $username      = $base_username ?: 'user';
    $suffix        = 1;

    while (username_exists($username)) {
        $username = ($base_username ?: 'user') . $suffix;
        $suffix++;
    }

    if (function_exists('wc_create_new_customer')) {
        $user_id = wc_create_new_customer($email, $username, $password);
    } else {
        $user_id = wp_insert_user([
            'user_login' => $username,
            'user_pass'  => $password,
            'user_email' => $email,
            'role'       => 'customer',
        ]);
    }

    if (is_wp_error($user_id)) {
        wp_send_json_error(['message' => 'Không thể tạo tài khoản. Vui lòng thử lại.']);
    }

    wp_update_user([
        'ID'           => $user_id,
        'first_name'   => $first_name,
        'last_name'    => $last_name,
        'display_name' => trim($first_name . ' ' . $last_name),
    ]);

    $customer = new WP_User($user_id);
    $customer->set_role('customer');

    if ($phone) {
        update_user_meta($user_id, 'billing_phone', $phone);
    }
    update_user_meta($user_id, 'billing_first_name', $first_name);
    update_user_meta($user_id, 'billing_last_name', $last_name);

    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id, true, is_ssl());

    wp_send_json_success([
        'message'  => 'Đăng ký thành công.',
        'redirect' => home_url('/'),
    ]);
}

/**
 * Shortcode: Login page
 */
add_shortcode('tlh_login_page', 'tlh_shortcode_login_page');
function tlh_shortcode_login_page() {
    ob_start();
    ?>
    <div style="min-height: calc(100vh - 140px); display: flex; align-items: center; justify-content: center; padding: 20px 0;">
        <div style="width: 100%; max-width: 480px;">
            <?php get_template_part('template-parts/user/login'); ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Shortcode: Register page
 */
add_shortcode('tlh_register_page', 'tlh_shortcode_register_page');
function tlh_shortcode_register_page() {
    ob_start();
    ?>
    <div style="min-height: calc(100vh - 140px); display: flex; align-items: center; justify-content: center; padding: 20px 0;">
        <div style="width: 100%; max-width: 480px;">
            <?php get_template_part('template-parts/user/register'); ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

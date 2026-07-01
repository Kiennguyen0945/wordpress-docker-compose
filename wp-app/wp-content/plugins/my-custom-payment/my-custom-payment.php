<?php
/**
 * Plugin Name:       My Custom Payment
 * Plugin URI:        https://example.com/my-custom-payment
 * Description:       Cổng thanh toán tùy chỉnh cho WooCommerce — hỗ trợ chuyển khoản / ví điện tử.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Author:            Your Name
 * Author URI:        https://example.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       my-custom-payment
 * Domain Path:       /languages
 * Requires Plugins:  woocommerce
 *
 * WC requires at least: 7.0
 * WC tested up to:      9.0
 */

defined( 'ABSPATH' ) || exit;

// ─── Định nghĩa hằng số ───────────────────────────────────────
define( 'MCP_PLUGIN_FILE', __FILE__ );
define( 'MCP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'MCP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'MCP_VERSION', '1.0.0' );

/**
 * Kiểm tra WooCommerce có đang hoạt động không.
 * Nếu chưa, hiển thị thông báo admin và dừng plugin.
 */
function mcp_check_woocommerce() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', function () {
			$class   = 'notice notice-error is-dismissible';
			$message = __( 'My Custom Payment yêu cầu WooCommerce được cài đặt và kích hoạt.', 'my-custom-payment' );
			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
		} );
		return false;
	}
	return true;
}

/**
 * Nạp file class gateway và đăng ký với WooCommerce.
 */
function mcp_init_gateway() {
	if ( ! mcp_check_woocommerce() ) {
		return;
	}

	require_once MCP_PLUGIN_DIR . 'includes/class-payment-gateway.php';

	add_filter( 'woocommerce_payment_gateways', function ( $gateways ) {
		$gateways[] = 'WC_Gateway_My_Custom_Payment';
		return $gateways;
	} );
}
add_action( 'plugins_loaded', 'mcp_init_gateway' );

/**
 * Đăng ký Blocks Integration cho Cart & Checkout Blocks.
 * Chỉ chạy khi WooCommerce >= 8.3 (có hỗ trợ blocks).
 */
function mcp_register_blocks_integration() {
	if ( ! mcp_check_woocommerce() ) {
		return;
	}

	require_once MCP_PLUGIN_DIR . 'includes/class-blocks-integration.php';

	add_action( 'woocommerce_blocks_payment_method_type_registration', function ( $payment_method_registry ) {
		/** 
		 * $payment_method_registry là instance của 
		 * Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry
		 */
		$payment_method_registry->register( new WC_MCP_Blocks_Integration() );
	} );
}
add_action( 'woocommerce_blocks_loaded', 'mcp_register_blocks_integration' );

/**
 * Thêm link "Cấu hình" trên trang plugin.
 */
function mcp_add_plugin_action_links( $links ) {
	$settings_url = admin_url( 'admin.php?page=wc-settings&tab=checkout&section=mcp_' . sanitize_title( __( 'My Custom Payment', 'my-custom-payment' ) ) );
	$links[] = sprintf( '<a href="%s">%s</a>', esc_url( $settings_url ), esc_html__( 'Cấu hình', 'my-custom-payment' ) );
	return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( MCP_PLUGIN_FILE ), 'mcp_add_plugin_action_links' );

<?php
/**
 * Blocks Integration — My Custom Payment
 *
 * Đăng ký gateway với WooCommerce Cart & Checkout Blocks.
 *
 * @package My_Custom_Payment
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;

/**
 * WC_MCP_Blocks_Integration
 *
 * Tích hợp My Custom Payment vào Checkout Block.
 */
final class WC_MCP_Blocks_Integration extends AbstractPaymentMethodType {

	/**
	 * Tên payment method (phải trùng với $this->id trong gateway).
	 *
	 * @var string
	 */
	protected $name = 'mcp_custom_payment';

	/**
	 * Initializes settings từ option.
	 */
	public function initialize() {
		$this->settings = get_option( 'woocommerce_mcp_custom_payment_settings', array() );
	}

	/**
	 * Kiểm tra gateway có đang được bật không.
	 *
	 * @return boolean
	 */
	public function is_active() {
		return filter_var( $this->get_setting( 'enabled', false ), FILTER_VALIDATE_BOOLEAN );
	}

	/**
	 * Đăng ký script cho frontend Checkout Block.
	 *
	 * @return array
	 */
	public function get_payment_method_script_handles() {
		$asset_path = MCP_PLUGIN_DIR . 'assets/js/block-checkout.asset.php';
		$deps       = array( 'wc-blocks-registry', 'wc-settings', 'wp-element', 'wp-html-entities', 'wp-i18n' );
		$version    = MCP_VERSION;

		if ( file_exists( $asset_path ) ) {
			$asset = require $asset_path;
			$deps  = array_merge( $deps, $asset['dependencies'] ?? array() );
			$version = $asset['version'] ?? $version;
		}

		wp_register_script(
			'wc-payment-method-mcp-custom-payment',
			MCP_PLUGIN_URL . 'assets/js/block-checkout.js',
			$deps,
			$version,
			true
		);

		return array( 'wc-payment-method-mcp-custom-payment' );
	}

	/**
	 * Dữ liệu truyền xuống frontend JS (qua getPaymentMethodData).
	 *
	 * @return array
	 */
	public function get_payment_method_data() {
		$gateway = new WC_Gateway_My_Custom_Payment();

		return array(
			'title'           => $this->get_setting( 'title' ),
			'description'     => $this->get_setting( 'description' ),
			'bank_info'       => $this->get_setting( 'bank_info', '' ),
			'supports'        => $this->get_supported_features(),
			'publishable_key' => $gateway->get_publishable_key(),
			'test_mode'       => 'yes' === $this->get_setting( 'test_mode', 'yes' ),
			'is_stripe'       => $gateway->is_stripe_configured(),
		);
	}
}

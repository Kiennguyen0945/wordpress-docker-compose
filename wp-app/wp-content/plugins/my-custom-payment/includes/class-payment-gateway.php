<?php
/**
 * WC_Gateway_My_Custom_Payment
 *
 * Cổng thanh toán tùy chỉnh cho WooCommerce.
 * Ở phiên bản 1.0 hoạt động ở chế độ "Mock" — tự động xác nhận thanh toán.
 *
 * @package My_Custom_Payment
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Kiểm tra class WC_Payment_Gateway đã tồn tại (tránh lỗi nếu WooCommerce chưa load).
 */
if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
	return;
}

class WC_Gateway_My_Custom_Payment extends WC_Payment_Gateway {

	/**
	 * Constructor — thiết lập các thuộc tính và hook.
	 */
	public function __construct() {
		$this->id                 = 'mcp_custom_payment';
		$this->icon               = apply_filters( 'mcp_gateway_icon', MCP_PLUGIN_URL . 'assets/icon.png' );
		$this->has_fields         = true;
		$this->method_title       = __( 'My Custom Payment', 'my-custom-payment' );
		$this->method_description = __( 'Cổng thanh toán tùy chỉnh — hỗ trợ chuyển khoản ngân hàng, ví điện tử, hoặc các phương thức thanh toán nội bộ. Ở chế độ Mock (giả lập) cho phiên bản 1.0.', 'my-custom-payment' );

		// ─── Khai báo supports cho blocks ────────────────────
		$this->supports = array(
			'products',
		);

		// ─── Load settings ────────────────────────────────────
		$this->init_form_fields();
		$this->init_settings();

		// ─── Gán title / description từ settings ──────────────
		$this->title       = $this->get_option( 'title', __( 'Chuyển khoản ngân hàng', 'my-custom-payment' ) );
		$this->description = $this->get_option( 'description', __( 'Thanh toán qua chuyển khoản ngân hàng hoặc ví điện tử. Đơn hàng sẽ được xử lý sau khi nhận được thanh toán.', 'my-custom-payment' ) );

		// ─── Hooks ────────────────────────────────────────────
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_scripts' ) );

		// ─── Biến hỗ trợ logging ──────────────────────────────
		if ( defined( 'MCP_DEBUG' ) && MCP_DEBUG ) {
			$this->log = wc_get_logger();
		}
	}

	/**
	 * Khai báo các trường cấu hình trong admin.
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled' => array(
				'title'   => __( 'Bật / Tắt', 'my-custom-payment' ),
				'type'    => 'checkbox',
				'label'   => __( 'Bật My Custom Payment', 'my-custom-payment' ),
				'default' => 'yes',
			),
			'title' => array(
				'title'       => __( 'Tên hiển thị', 'my-custom-payment' ),
				'type'        => 'text',
				'description' => __( 'Tên phương thức thanh toán hiển thị ở trang thanh toán.', 'my-custom-payment' ),
				'default'     => __( 'Chuyển khoản ngân hàng', 'my-custom-payment' ),
				'desc_tip'    => true,
			),
			'description' => array(
				'title'       => __( 'Mô tả', 'my-custom-payment' ),
				'type'        => 'textarea',
				'description' => __( 'Mô tả ngắn về phương thức thanh toán hiển thị ở trang checkout.', 'my-custom-payment' ),
				'default'     => __( 'Thanh toán qua chuyển khoản ngân hàng hoặc ví điện tử. Đơn hàng sẽ được xử lý sau khi nhận được thanh toán.', 'my-custom-payment' ),
			),
			'instructions' => array(
				'title'       => __( 'Hướng dẫn thanh toán', 'my-custom-payment' ),
				'type'        => 'textarea',
				'description' => __( 'Thông tin hướng dẫn chi tiết hiển thị sau khi đặt hàng (trang cảm ơn & email).', 'my-custom-payment' ),
				'default'     => __( 'Cảm ơn bạn đã đặt hàng! Vui lòng chuyển khoản theo thông tin bên dưới và ghi chú mã đơn hàng để chúng tôi đối soát.', 'my-custom-payment' ),
			),
			'bank_info' => array(
				'title'       => __( 'Thông tin tài khoản ngân hàng', 'my-custom-payment' ),
				'type'        => 'textarea',
				'description' => __( 'Thông tin tài khoản ngân hàng để khách hàng chuyển khoản.', 'my-custom-payment' ),
				'default'     => "Ngân hàng: Vietcombank\nChủ tài khoản: CÔNG TY ABC\nSố tài khoản: 1234567890\nChi nhánh: Hồ Chí Minh",
			),
		);
	}

	/**
	 * Enqueue CSS / JS cho frontend (nếu cần).
	 */
	public function enqueue_frontend_scripts() {
		if ( ! is_checkout() ) {
			return;
		}

		$css_file = MCP_PLUGIN_URL . 'assets/frontend.css';
		if ( file_exists( MCP_PLUGIN_DIR . 'assets/frontend.css' ) ) {
			wp_enqueue_style( 'mcp-frontend', $css_file, array(), MCP_VERSION );
		}
	}

	/**
	 * Hiển thị trường nhập liệu riêng ở trang checkout (nếu có).
	 */
	public function payment_fields() {
		// Hiển thị mô tả
		if ( $this->description ) {
			echo wp_kses_post( wpautop( wptexturize( $this->description ) ) );
		}

		// Hiển thị thông tin tài khoản ngân hàng (nếu có)
		$bank_info = $this->get_option( 'bank_info', '' );
		if ( $bank_info ) {
			printf(
				'<div class="mcp-bank-info" style="background:#f8f9fa;padding:15px;border-radius:6px;margin-top:10px;font-size:14px;line-height:1.7;">%s</div>',
				wp_kses_post( nl2br( esc_html( $bank_info ) ) )
			);
		}
	}

	/**
	 * Xử lý thanh toán (Mock — giả lập thành công).
	 *
	 * @param int $order_id ID của đơn hàng.
	 * @return array Kết quả trả về cho WooCommerce.
	 */
	public function process_payment( $order_id ) {
		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			wc_add_notice( __( 'Không tìm thấy đơn hàng. Vui lòng thử lại.', 'my-custom-payment' ), 'error' );
			return array(
				'result' => 'failure',
			);
		}

		// ─── 1. Ghi log nếu đang debug ────────────────────────
		$this->log_debug( sprintf( 'Bắt đầu xử lý đơn hàng #%d — trạng thái hiện tại: %s', $order_id, $order->get_status() ) );

		// ─── 2. Ghi chú vào đơn hàng ──────────────────────────
		$order->add_order_note(
			__( '[My Custom Payment] Thanh toán đã được xác nhận (chế độ Mock — chưa kết nối API thật).', 'my-custom-payment' )
		);

		// ─── 3. Chuyển trạng thái đơn hàng → Processing ───────
		$order->update_status(
			'processing',
			__( '[My Custom Payment] Đơn hàng chuyển sang xử lý.', 'my-custom-payment' )
		);

		// ─── 4. Giảm tồn kho ──────────────────────────────────
		wc_maybe_reduce_stock_levels( $order_id );

		// ─── 5. Xóa giỏ hàng ──────────────────────────────────
		if ( isset( WC()->cart ) ) {
			WC()->cart->empty_cart();
		}

		// ─── 6. Lưu transaction ID giả ────────────────────────
		$mock_transaction_id = 'MOCK_' . $order_id . '_' . current_time( 'timestamp' );
		$order->set_transaction_id( $mock_transaction_id );
		$order->save();

		// ─── 7. Ghi log ───────────────────────────────────────
		$this->log_debug( sprintf( 'Đơn hàng #%d đã xử lý thành công. Redirect về trang cảm ơn.', $order_id ) );

		// ─── 8. Trả về kết quả ────────────────────────────────
		return array(
			'result'   => 'success',
			'redirect' => $this->get_return_url( $order ),
		);
	}

	/**
	 * Ghi log debug nếu được bật.
	 *
	 * @param string $message Nội dung log.
	 */
	private function log_debug( $message ) {
		if ( defined( 'MCP_DEBUG' ) && MCP_DEBUG && isset( $this->log ) ) {
			$this->log->info( $message, array( 'source' => 'mcp_custom_payment' ) );
		}
	}
}

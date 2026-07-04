<?php
/**
 * WC_Gateway_My_Custom_Payment
 *
 * Cổng thanh toán tùy chỉnh cho WooCommerce — tích hợp Stripe Checkout.
 * - Nếu cấu hình Stripe API keys: tạo Stripe Checkout Session, redirect sang Stripe.
 * - Nếu chưa cấu hình: tự động fallback về chế độ Mock (giả lập) như v1.0.
 *
 * @package My_Custom_Payment
 * @since   2.0.0
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
		$this->method_description = __( 'Cổng thanh toán tùy chỉnh — hỗ trợ Stripe Checkout (Sandbox/Live) hoặc chế độ Mock (giả lập).', 'my-custom-payment' );

		// ─── Khai báo supports cho blocks ────────────────────
		$this->supports = array(
			'products',
		);

		// ─── Load settings ────────────────────────────────────
		$this->init_form_fields();
		$this->init_settings();

		// ─── Gán title / description từ settings ──────────────
		$this->title       = $this->get_option( 'title', __( 'Thẻ tín dụng / Thẻ ghi nợ (Stripe)', 'my-custom-payment' ) );
		$this->description = $this->get_option( 'description', __( 'Thanh toán an toàn qua Stripe — chấp nhận Visa, Mastercard, và nhiều phương thức khác.', 'my-custom-payment' ) );

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
				'default'     => __( 'Thẻ tín dụng / Thẻ ghi nợ (Stripe)', 'my-custom-payment' ),
				'desc_tip'    => true,
			),
			'description' => array(
				'title'       => __( 'Mô tả', 'my-custom-payment' ),
				'type'        => 'textarea',
				'description' => __( 'Mô tả ngắn hiển thị ở trang checkout.', 'my-custom-payment' ),
				'default'     => __( 'Thanh toán an toàn qua Stripe — chấp nhận Visa, Mastercard, và nhiều phương thức khác.', 'my-custom-payment' ),
			),
			'test_mode' => array(
				'title'   => __( 'Chế độ Sandbox (Test)', 'my-custom-payment' ),
				'type'    => 'checkbox',
				'label'   => __( 'Bật chế độ Sandbox — sử dụng Test API keys. Bỏ tick để dùng Live keys.', 'my-custom-payment' ),
				'default' => 'yes',
			),
			'test_publishable_key' => array(
				'title'       => __( 'Test Publishable Key', 'my-custom-payment' ),
				'type'        => 'text',
				'description' => __( 'Stripe Publishable Key (pk_test_...) — dùng ở frontend.', 'my-custom-payment' ),
				'default'     => '',
				'desc_tip'    => true,
			),
			'test_secret_key' => array(
				'title'       => __( 'Test Secret Key', 'my-custom-payment' ),
				'type'        => 'password',
				'description' => __( 'Stripe Secret Key (sk_test_...) — dùng để gọi API.', 'my-custom-payment' ),
				'default'     => '',
				'desc_tip'    => true,
			),
			'test_webhook_secret' => array(
				'title'       => __( 'Test Webhook Secret', 'my-custom-payment' ),
				'type'        => 'password',
				'description' => __( 'Stripe Webhook Signing Secret (whsec_...) — dùng để verify webhook.', 'my-custom-payment' ),
				'default'     => '',
				'desc_tip'    => true,
			),
			'live_publishable_key' => array(
				'title'       => __( 'Live Publishable Key', 'my-custom-payment' ),
				'type'        => 'text',
				'description' => __( 'Stripe Publishable Key (pk_live_...) — dùng ở frontend khi chạy thật.', 'my-custom-payment' ),
				'default'     => '',
				'desc_tip'    => true,
			),
			'live_secret_key' => array(
				'title'       => __( 'Live Secret Key', 'my-custom-payment' ),
				'type'        => 'password',
				'description' => __( 'Stripe Secret Key (sk_live_...) — dùng để gọi API thật.', 'my-custom-payment' ),
				'default'     => '',
				'desc_tip'    => true,
			),
			'live_webhook_secret' => array(
				'title'       => __( 'Live Webhook Secret', 'my-custom-payment' ),
				'type'        => 'password',
				'description' => __( 'Stripe Webhook Signing Secret (whsec_...) cho môi trường Live.', 'my-custom-payment' ),
				'default'     => '',
				'desc_tip'    => true,
			),
			'instructions' => array(
				'title'       => __( 'Hướng dẫn thanh toán', 'my-custom-payment' ),
				'type'        => 'textarea',
				'description' => __( 'Thông tin hướng dẫn chi tiết hiển thị sau khi đặt hàng (trang cảm ơn & email).', 'my-custom-payment' ),
				'default'     => __( 'Cảm ơn bạn đã đặt hàng! Thanh toán của bạn đang được xử lý qua Stripe.', 'my-custom-payment' ),
			),
			'bank_info' => array(
				'title'       => __( 'Thông tin tài khoản ngân hàng (Mock)', 'my-custom-payment' ),
				'type'        => 'textarea',
				'description' => __( 'Chỉ dùng khi fallback về chế độ Mock (khi chưa nhập Stripe keys).', 'my-custom-payment' ),
				'default'     => "Ngân hàng: Vietcombank\nChủ tài khoản: CÔNG TY ABC\nSố tài khoản: 1234567890\nChi nhánh: Hồ Chí Minh",
			),
		);
	}

	/**
	 * Lấy Publishable Key tương ứng với chế độ hiện tại.
	 *
	 * @return string
	 */
	public function get_publishable_key() {
		if ( 'yes' === $this->get_option( 'test_mode' ) ) {
			return $this->get_option( 'test_publishable_key' );
		}
		return $this->get_option( 'live_publishable_key' );
	}

	/**
	 * Lấy Secret Key tương ứng với chế độ hiện tại.
	 *
	 * @return string
	 */
	public function get_secret_key() {
		if ( 'yes' === $this->get_option( 'test_mode' ) ) {
			return $this->get_option( 'test_secret_key' );
		}
		return $this->get_option( 'live_secret_key' );
	}

	/**
	 * Kiểm tra xem Stripe đã được cấu hình đầy đủ chưa.
	 *
	 * @return bool
	 */
	public function is_stripe_configured() {
		$secret_key = $this->get_secret_key();
		$pub_key    = $this->get_publishable_key();
		return ! empty( $secret_key ) && ! empty( $pub_key );
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

		// Nếu Stripe chưa được cấu hình, show bank info (mock mode)
		if ( ! $this->is_stripe_configured() ) {
			$bank_info = $this->get_option( 'bank_info', '' );
			if ( $bank_info ) {
				printf(
					'<div class="mcp-bank-info" style="background:#f8f9fa;padding:15px;border-radius:6px;margin-top:10px;font-size:14px;line-height:1.7;">%s</div>',
					wp_kses_post( nl2br( esc_html( $bank_info ) ) )
				);
			}
		} else {
			// Hiển thị badge chế độ sandbox/live
			$mode = 'yes' === $this->get_option( 'test_mode' )
				? __( '🔒 Sandbox (Test)', 'my-custom-payment' )
				: __( 'Live', 'my-custom-payment' );

			printf(
				'<p style="margin:10px 0 0;font-size:13px;color:#666;"><em>%s</em></p>',
				esc_html( $mode )
			);
		}
	}

	/**
	 * Xử lý thanh toán — ưu tiên Stripe, fallback Mock.
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

		// ─── Nếu Stripe được cấu hình → tạo Checkout Session ─
		if ( $this->is_stripe_configured() ) {
			return $this->process_stripe_payment( $order );
		}

		// ─── Fallback: Mock payment (giả lập) ────────────────
		return $this->process_mock_payment( $order );
	}

	/**
	 * Xử lý thanh toán qua Stripe Checkout Session.
	 *
	 * @param WC_Order $order Đơn hàng.
	 * @return array
	 */
	private function process_stripe_payment( $order ) {
		$order_id    = $order->get_id();
		$secret_key  = $this->get_secret_key();
		$webhook_secret = 'yes' === $this->get_option( 'test_mode' )
			? $this->get_option( 'test_webhook_secret' )
			: $this->get_option( 'live_webhook_secret' );
		$currency    = $order->get_currency();

		require_once MCP_PLUGIN_DIR . 'includes/class-stripe-api.php';
		$stripe = new MCP_Stripe_API( $secret_key, $webhook_secret );

		// ─── Kiểm tra stock thực tế ──────────────────────────
		foreach ( $order->get_items() as $item ) {
			$product = $item->get_product();
			if ( $product && $product->managing_stock() ) {
				$stock = $product->get_stock_quantity();
				if ( $stock < $item->get_quantity() ) {
					$this->log_debug( "Hết hàng: {$item->get_name()} (stock={$stock}, need={$item->get_quantity()})", $order_id );
					wc_add_notice(
						sprintf( __( 'Sản phẩm "%s" đã hết hàng.', 'my-custom-payment' ), $item->get_name() ),
						'error'
					);
					return array( 'result' => 'failure' );
				}
			}
		}

		// ─── Giảm stock NGAY LẬP TỨC (reserve cho đơn này) ──
		wc_maybe_reduce_stock_levels( $order_id );

		// ─── Đặt thời gian hết hạn: 15 phút ──────────────────
		$expires_at = time() + ( 15 * MINUTE_IN_SECONDS );
		$order->update_meta_data( '_mcp_payment_expires_at', $expires_at );
		$order->update_meta_data( '_mcp_stripe_currency', $currency );

		// ─── Quy đổi số tiền đúng với Stripe ─────────────────
		//   VND (zero-decimal): giữ nguyên
		//   USD, EUR,... (2 decimal): * 100
		$is_zero_decimal = in_array( strtoupper( $currency ), array(
			'BIF', 'CLP', 'DJF', 'GNF', 'JPY', 'KMF', 'KRW',
			'MGA', 'PYG', 'RWF', 'UGX', 'VND', 'VUV', 'XAF',
			'XOF', 'XPF',
		), true );

		/**
		 * Quy đổi số tiền sang đơn vị nhỏ nhất của Stripe.
		 *
		 * @param float $amount Số tiền gốc.
		 * @return int
		 */
		$to_stripe_amount = function ( $amount ) use ( $is_zero_decimal ) {
			if ( $is_zero_decimal ) {
				return intval( round( $amount ) );
			}
			return intval( round( $amount * 100 ) );
		};

		// ─── Chuẩn bị line items ─────────────────────────────
		$line_items = array();
		foreach ( $order->get_items() as $item ) {
			$product = $item->get_product();
			// Sử dụng get_total() / get_quantity() để lấy unit_amount
			// (get_total() trả về tổng dòng = đơn giá * số lượng)
			$item_total   = (float) $item->get_total();
			$item_qty     = $item->get_quantity();
			$unit_amount  = $item_qty > 0 ? $item_total / $item_qty : 0;

			$line_items[] = array(
				'price_data' => array(
					'currency'     => $currency,
					'product_data' => array(
						'name' => $item->get_name(),
					),
					'unit_amount'  => $to_stripe_amount( $unit_amount ),
				),
				'quantity' => $item_qty,
			);
		}

		// ─── Phí ship ────────────────────────────────────────
		if ( (float) $order->get_shipping_total() > 0 ) {
			$line_items[] = array(
				'price_data' => array(
					'currency'     => $currency,
					'product_data' => array(
						'name' => __( 'Phí vận chuyển', 'my-custom-payment' ),
					),
					'unit_amount'  => $to_stripe_amount( $order->get_shipping_total() ),
				),
				'quantity' => 1,
			);
		}

		// ─── Tính tổng line items ────────────────────────────
		$line_total = 0;
		foreach ( $line_items as $li ) {
			$line_total += $li['price_data']['unit_amount'] * $li['quantity'];
		}

		$total_stripe = $to_stripe_amount( $order->get_total() );
		$diff = $total_stripe - $line_total;

		// Nếu có chênh lệch dương (thuế, phí), thêm adjustment item
		// KHÔNG thêm item nếu diff âm — Stripe không chấp nhận unit_amount < 0
		if ( $diff > 1 ) {
			$line_items[] = array(
				'price_data' => array(
					'currency'     => $currency,
					'product_data' => array(
						'name' => __( 'Thuế / Phí khác', 'my-custom-payment' ),
					),
					'unit_amount'  => abs( $diff ),
				),
				'quantity' => 1,
			);
		}

		// ─── Tạo Checkout Session ────────────────────────────
		$session = $stripe->create_checkout_session( array(
			'mode'                => 'payment',
			'success_url'         => add_query_arg( array(
				'mcp-stripe-return' => '1',
				'session_id'        => '{CHECKOUT_SESSION_ID}',
				'order_id'          => $order_id,
			), $this->get_return_url( $order ) ),
			'cancel_url'          => $order->get_cancel_order_url(),
			'client_reference_id' => (string) $order_id,
			'customer_email'      => $order->get_billing_email(),
			'line_items'          => $line_items,
			'metadata'            => array(
				'order_id' => $order_id,
			),
			'payment_intent_data' => array(
				'metadata' => array(
					'order_id' => (string) $order_id,
				),
			),
		) );

		if ( false === $session || empty( $session->id ) || empty( $session->url ) ) {
			// Tạo session thất bại → hoàn stock
			$this->log_debug( 'Stripe Checkout Session creation failed. Restoring stock.', $order_id );
			wc_increase_stock_levels( $order_id );

			wc_add_notice(
				__( 'Không thể kết nối đến Stripe. Vui lòng thử lại sau.', 'my-custom-payment' ),
				'error'
			);
			return array( 'result' => 'failure' );
		}

		// ─── Lưu meta ────────────────────────────────────────
		$order->update_meta_data( '_mcp_stripe_session_id', sanitize_text_field( $session->id ) );
		if ( ! empty( $session->payment_intent ) ) {
			$order->update_meta_data(
				'_mcp_stripe_payment_intent_id',
				sanitize_text_field( $session->payment_intent )
			);
		}
		$order->save();

		$this->log_debug(
			sprintf(
				'Stripe session created: %s, payment_intent: %s — redirect to Stripe.',
				$session->id,
				$session->payment_intent ?? 'N/A'
			),
			$order_id
		);

		// ─── Redirect đến Stripe Checkout ────────────────────
		return array(
			'result'   => 'success',
			'redirect' => esc_url_raw( $session->url ),
		);
	}

	/**
	 * Xử lý thanh toán Mock (fallback khi chưa có Stripe keys).
	 *
	 * @param WC_Order $order Đơn hàng.
	 * @return array
	 */
	private function process_mock_payment( $order ) {
		$order_id = $order->get_id();

		$this->log_debug( sprintf( 'MOCK: Bắt đầu xử lý đơn hàng #%d', $order_id ), $order_id );

		// ─── 1. Ghi chú vào đơn hàng ──────────────────────────
		$order->add_order_note(
			__( '[My Custom Payment] Thanh toán đã được xác nhận (chế độ Mock — chưa kết nối Stripe).', 'my-custom-payment' )
		);

		// ─── 2. Chuyển trạng thái → Processing ────────────────
		$order->update_status(
			'processing',
			__( '[My Custom Payment] Đơn hàng chuyển sang xử lý (Mock).', 'my-custom-payment' )
		);

		// ─── 3. Giảm tồn kho ──────────────────────────────────
		wc_maybe_reduce_stock_levels( $order_id );

		// ─── 4. Xóa giỏ hàng ──────────────────────────────────
		if ( isset( WC()->cart ) ) {
			WC()->cart->empty_cart();
		}

		// ─── 5. Transaction ID giả ────────────────────────────
		$mock_transaction_id = 'MOCK_' . $order_id . '_' . current_time( 'timestamp' );
		$order->set_transaction_id( $mock_transaction_id );
		$order->save();

		$this->log_debug( sprintf( 'MOCK: Đơn hàng #%d xử lý thành công.', $order_id ), $order_id );

		return array(
			'result'   => 'success',
			'redirect' => $this->get_return_url( $order ),
		);
	}

	/**
	 * Ghi log debug nếu được bật.
	 *
	 * @param string $message  Nội dung log.
	 * @param int    $order_id (Optional) Order ID.
	 */
	private function log_debug( $message, $order_id = 0 ) {
		if ( defined( 'MCP_DEBUG' ) && MCP_DEBUG && isset( $this->log ) ) {
			$context = array( 'source' => 'mcp_custom_payment' );
			if ( $order_id ) {
				$context['order_id'] = $order_id;
			}
			$this->log->info( $message, $context );
		}
	}
}

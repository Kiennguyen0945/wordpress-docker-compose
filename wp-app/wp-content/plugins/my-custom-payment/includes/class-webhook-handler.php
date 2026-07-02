<?php
/**
 * Webhook / IPN Handler
 *
 * Tiếp nhận và xử lý sự kiện từ Stripe (webhook).
 * Endpoint: {site_url}/wc-api/mcp-stripe-webhook/
 *
 * @package My_Custom_Payment
 * @since   2.0.0
 */

defined( 'ABSPATH' ) || exit;

class MCP_Webhook_Handler {

	/**
	 * Logger.
	 *
	 * @var WC_Logger|null
	 */
	private $log;

	/**
	 * Constructor — hook vào WooCommerce API và REST API.
	 */
	public function __construct() {
		if ( defined( 'MCP_DEBUG' ) && MCP_DEBUG ) {
			$this->log = wc_get_logger();
		}

		// Đăng ký endpoint với WooCommerce API (legacy)
		add_action( 'woocommerce_api_mcp_stripe_webhook', array( $this, 'handle_webhook' ) );
	}

	/**
	 * Xử lý webhook qua REST API.
	 * Endpoint: POST /wp-json/mcp/v1/webhook
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public static function handle_rest_webhook( $request ) {
		$payload  = $request->get_body();
		$signature = $request->get_header( 'stripe-signature' ) ?? '';

		if ( empty( $payload ) ) {
			return new WP_Error( 'mcp_empty_payload', 'Empty payload', array( 'status' => 400 ) );
		}

		// Tạo instance để dùng các method xử lý
		$handler = new self();

		$handler->log_debug( 'REST webhook received. Signature: ' . substr( $signature, 0, 30 ) . '...' );

		// ─── Lấy thông tin cấu hình Stripe ───────────────────
		$settings = get_option( 'woocommerce_mcp_custom_payment_settings', array() );
		$secret_key     = $settings['test_secret_key'] ?? '';
		$webhook_secret = $settings['test_webhook_secret'] ?? '';
		$test_mode      = 'yes' === ( $settings['test_mode'] ?? 'yes' );

		if ( ! $test_mode ) {
			$secret_key     = $settings['live_secret_key'] ?? '';
			$webhook_secret = $settings['live_webhook_secret'] ?? '';
		}

		if ( empty( $secret_key ) ) {
			$handler->log_error( 'REST webhook bị bỏ qua: Stripe chưa được cấu hình.' );
			return new WP_Error( 'mcp_not_configured', 'Stripe not configured', array( 'status' => 200 ) );
		}

		// ─── Khởi tạo Stripe API và verify signature ─────────
		require_once MCP_PLUGIN_DIR . 'includes/class-stripe-api.php';
		$stripe = new MCP_Stripe_API( $secret_key, $webhook_secret );

		$event = $stripe->verify_webhook_signature( $payload, $signature );

		if ( false === $event ) {
			$handler->log_error( 'REST webhook signature verification FAILED.' );
			return new WP_Error( 'mcp_signature_failed', 'Signature verification failed', array( 'status' => 401 ) );
		}

		$handler->log_debug( 'REST webhook verified. Event type: ' . ( $event->type ?? 'unknown' ) );

		// ─── Xử lý theo từng loại event ──────────────────────
		switch ( $event->type ?? '' ) {
			case 'payment_intent.succeeded':
				$handler->handle_payment_intent_succeeded( $event->data->object ?? new stdClass() );
				break;

			case 'payment_intent.payment_failed':
				$handler->handle_payment_intent_failed( $event->data->object ?? new stdClass() );
				break;

			case 'payment_intent.canceled':
				$handler->handle_payment_intent_canceled( $event->data->object ?? new stdClass() );
				break;

			default:
				$handler->log_debug( 'REST webhook bỏ qua event: ' . ( $event->type ?? 'null' ) );
				break;
		}

		return new WP_REST_Response( array( 'status' => 'ok' ), 200 );
	}

	/**
	 * Xử lý webhook từ Stripe.
	 */
	public function handle_webhook() {
		// ─── Đọc raw POST data ────────────────────────────────
		$payload = file_get_contents( 'php://input' );
		$signature = isset( $_SERVER['HTTP_STRIPE_SIGNATURE'] )
			? sanitize_text_field( wp_unslash( $_SERVER['HTTP_STRIPE_SIGNATURE'] ) )
			: '';

		$this->log_debug( 'Webhook received. Signature: ' . substr( $signature, 0, 30 ) . '...' );

		if ( empty( $payload ) ) {
			$this->log_error( 'Webhook nhận được request rỗng.' );
			status_header( 400 );
			exit;
		}

		// ─── Lấy thông tin cấu hình Stripe ───────────────────
		$settings = get_option( 'woocommerce_mcp_custom_payment_settings', array() );
		$secret_key     = $settings['test_secret_key'] ?? '';
		$webhook_secret = $settings['test_webhook_secret'] ?? '';
		$test_mode      = 'yes' === ( $settings['test_mode'] ?? 'yes' );

		if ( ! $test_mode ) {
			$secret_key     = $settings['live_secret_key'] ?? '';
			$webhook_secret = $settings['live_webhook_secret'] ?? '';
		}

		if ( empty( $secret_key ) ) {
			$this->log_error( 'Webhook bị bỏ qua: Stripe chưa được cấu hình.' );
			status_header( 200 );
			exit;
		}

		// ─── Khởi tạo Stripe API và verify signature ─────────
		require_once MCP_PLUGIN_DIR . 'includes/class-stripe-api.php';
		$stripe = new MCP_Stripe_API( $secret_key, $webhook_secret );

		$event = $stripe->verify_webhook_signature( $payload, $signature );

		if ( false === $event ) {
			$this->log_error( 'Webhook signature verification FAILED. Từ chối request.' );
			status_header( 401 );
			exit;
		}

		$this->log_debug( 'Webhook verified. Event type: ' . ( $event->type ?? 'unknown' ) );

		// ─── Xử lý theo từng loại event ──────────────────────
		switch ( $event->type ?? '' ) {
			case 'payment_intent.succeeded':
				$this->handle_payment_intent_succeeded( $event->data->object ?? new stdClass() );
				break;

			case 'payment_intent.payment_failed':
				$this->handle_payment_intent_failed( $event->data->object ?? new stdClass() );
				break;

			case 'payment_intent.canceled':
				$this->handle_payment_intent_canceled( $event->data->object ?? new stdClass() );
				break;

			default:
				$this->log_debug( 'Bỏ qua event không cần xử lý: ' . ( $event->type ?? 'null' ) );
				break;
		}

		// ─── Trả về 200 OK cho Stripe ────────────────────────
		status_header( 200 );
		exit;
	}

	/**
	 * Tra cứu đơn hàng qua _mcp_stripe_payment_intent_id meta.
	 *
	 * @param string $intent_id Stripe PaymentIntent ID.
	 * @return WC_Order|false
	 */
	private function get_order_by_intent( $intent_id ) {
		if ( empty( $intent_id ) ) {
			return false;
		}
		$orders = wc_get_orders( array(
			'limit'          => 1,
			'return'         => 'objects',
			'meta_key'       => '_mcp_stripe_payment_intent_id',
			'meta_value'     => sanitize_text_field( $intent_id ),
			'meta_compare'   => '=',
		) );
		return ! empty( $orders ) ? $orders[0] : false;
	}

	/**
	 * Tra cứu đơn hàng qua Checkout Session (fallback khi metadata chưa được set).
	 *
	 * Tìm các order có _mcp_stripe_session_id, retrieve session từ Stripe,
	 * kiểm tra nếu session.payment_intent == $intent_id.
	 *
	 * @param string $intent_id Stripe PaymentIntent ID.
	 * @return WC_Order|false
	 */
	private function get_order_by_pi_session( $intent_id ) {
		if ( empty( $intent_id ) ) {
			return false;
		}

		// Lấy tất cả order có _mcp_stripe_session_id
		$orders = wc_get_orders( array(
			'limit'          => 10,
			'return'         => 'objects',
			'meta_key'       => '_mcp_stripe_session_id',
			'meta_compare'   => 'EXISTS',
		) );

		$settings = get_option( 'woocommerce_mcp_custom_payment_settings', array() );
		$secret_key = $settings['test_secret_key'] ?? '';
		if ( 'yes' !== ( $settings['test_mode'] ?? 'yes' ) ) {
			$secret_key = $settings['live_secret_key'] ?? '';
		}
		require_once MCP_PLUGIN_DIR . 'includes/class-stripe-api.php';
		$stripe = new MCP_Stripe_API( $secret_key );

		foreach ( $orders as $order ) {
			$session_id = $order->get_meta( '_mcp_stripe_session_id' );
			if ( empty( $session_id ) ) {
				continue;
			}
			$session = $stripe->retrieve_checkout_session( $session_id );
			if ( $session && isset( $session->payment_intent ) && $session->payment_intent === $intent_id ) {
				$this->log_debug( "Tìm thấy order #{$order->get_id()} từ Checkout Session." );
				return $order;
			}
		}

		return false;
	}

	/**
	 * Xử lý sự kiện payment_intent.succeeded.
	 *
	 * @param object $intent Stripe PaymentIntent object.
	 */
	private function handle_payment_intent_succeeded( $intent ) {
		$intent_id = $intent->id ?? '';
		$order_id = isset( $intent->metadata->order_id ) ? (int) $intent->metadata->order_id : 0;

		// ─── Tra cứu order ───────────────────────────────────
		$order = false;
		if ( $order_id ) {
			$order = wc_get_order( $order_id );
		}
		if ( ! $order ) {
			// Fallback: tra cứu bằng _mcp_stripe_payment_intent_id meta
			$order = $this->get_order_by_intent( $intent_id );
		}
		if ( ! $order ) {
			// Fallback: lấy Checkout Session từ PaymentIntent, tìm order_id trong metadata
			$order = $this->get_order_by_pi_session( $intent_id );
		}
		if ( ! $order ) {
			$this->log_error( "payment_intent.succeeded: không tìm thấy order từ intent {$intent_id}" );
			return;
		}

		// ─── Lưu PaymentIntent ID vào order meta ─────────────
		$saved_intent = $order->get_meta( '_mcp_stripe_payment_intent_id' );
		if ( empty( $saved_intent ) ) {
			$order->update_meta_data( '_mcp_stripe_payment_intent_id', sanitize_text_field( $intent_id ) );
		}

		if ( $order->has_status( array( 'processing', 'completed' ) ) ) {
			$this->log_debug( "Đơn hàng #{$order->get_id()} đã được xử lý trước đó." );
			return;
		}

		// ─── Kiểm tra tổng tiền (tính đúng zero-decimal) ─────
		$currency = $intent->currency ?? strtolower( $order->get_currency() );
		$is_zero_decimal = in_array( strtoupper( $currency ), array(
			'BIF', 'CLP', 'DJF', 'GNF', 'JPY', 'KMF', 'KRW',
			'MGA', 'PYG', 'RWF', 'UGX', 'VND', 'VUV', 'XAF',
			'XOF', 'XPF',
		), true );
		$expected_amount = $is_zero_decimal
			? intval( round( $order->get_total() ) )
			: intval( round( $order->get_total() * 100 ) );

		$received_amount = $intent->amount_received ?? $intent->amount ?? 0;
		if ( $expected_amount > 0 && abs( (float) $received_amount - $expected_amount ) > 1 ) {
			$this->log_error( "WEBHOOK SECURITY: Số tiền không khớp! Order #{$order->get_id()}: {$expected_amount} != Intent: {$received_amount}" );
			$order->update_status( 'on-hold', __( '[My Custom Payment] Cảnh báo bảo mật: Số tiền từ Stripe không khớp.', 'my-custom-payment' ) );
			return;
		}

		// ─── Lưu transaction ID ──────────────────────────────
		$order->set_transaction_id( sanitize_text_field( $intent_id ) );

		// ─── Cập nhật trạng thái ─────────────────────────────
		$order->add_order_note(
			sprintf(
				/* translators: %s: Stripe PaymentIntent ID */
				__( '[My Custom Payment] Thanh toán Stripe thành công. PaymentIntent: %s', 'my-custom-payment' ),
				$intent_id
			)
		);

		$order->update_status(
			'processing',
			__( '[My Custom Payment] Đơn hàng đã được thanh toán qua Stripe.', 'my-custom-payment' )
		);

		// ─── Xoá reserved stock — wc_maybe_reduce_stock_levels
		//     đã được gọi ở process_payment, nên không cần làm gì thêm.
		$order->delete_meta_data( '_mcp_payment_expires_at' );
		$order->save();

		$this->log_debug( "Đơn hàng #{$order->get_id()} đã cập nhật thành processing qua webhook (payment_intent.succeeded)." );
	}

	/**
	 * Xử lý sự kiện payment_intent.payment_failed.
	 *
	 * @param object $intent Stripe PaymentIntent object.
	 */
	private function handle_payment_intent_failed( $intent ) {
		$intent_id = $intent->id ?? '';
		$order_id  = isset( $intent->metadata->order_id ) ? (int) $intent->metadata->order_id : 0;

		// Tra cứu order
		$order = false;
		if ( $order_id ) {
			$order = wc_get_order( $order_id );
		}
		if ( ! $order ) {
			$this->log_error( "payment_intent.payment_failed: không tìm thấy order từ intent {$intent_id}" );
			return;
		}

		if ( $order->has_status( array( 'processing', 'completed' ) ) ) {
			return;
		}

		$error_msg = '';
		if ( isset( $intent->last_payment_error->message ) ) {
			$error_msg = $intent->last_payment_error->message;
		}

		// ─── Hoàn stock ──────────────────────────────────────
		wc_increase_stock_levels( $order->get_id() );

		$order->update_status(
			'failed',
			sprintf(
				/* translators: %s: lỗi từ Stripe */
				__( '[My Custom Payment] Thanh toán thất bại: %s', 'my-custom-payment' ),
				$error_msg ?: 'unknown error'
			)
		);

		$order->save();
		$this->log_debug( "Đơn hàng #{$order->get_id()} chuyển sang failed (payment_intent.payment_failed). Stock hoàn lại." );
	}

	/**
	 * Xử lý sự kiện payment_intent.canceled.
	 *
	 * @param object $intent Stripe PaymentIntent object.
	 */
	private function handle_payment_intent_canceled( $intent ) {
		$intent_id = $intent->id ?? '';
		$order_id  = isset( $intent->metadata->order_id ) ? (int) $intent->metadata->order_id : 0;

		$order = false;
		if ( $order_id ) {
			$order = wc_get_order( $order_id );
		}
		if ( ! $order ) {
			$this->log_error( "payment_intent.canceled: không tìm thấy order từ intent {$intent_id}" );
			return;
		}

		if ( $order->has_status( array( 'processing', 'completed' ) ) ) {
			return;
		}

		// ─── Hoàn stock ──────────────────────────────────────
		wc_increase_stock_levels( $order->get_id() );

		$order->update_status(
			'cancelled',
			__( '[My Custom Payment] PaymentIntent đã bị hủy trên Stripe.', 'my-custom-payment' )
		);

		$order->save();
		$this->log_debug( "Đơn hàng #{$order->get_id()} chuyển sang cancelled (payment_intent.canceled). Stock hoàn lại." );
	}

	// ──── Hủy đơn Pending quá hạn (gọi từ cronjob) ─────────

	/**
	 * Dọn các đơn Pending đã quá hạn thanh toán.
	 *
	 * Query orders status=pending AND _mcp_payment_expires_at < now.
	 * Hủy PaymentIntent trên Stripe, hoàn stock, chuyển cancelled.
	 */
	public static function cleanup_expired_orders() {
		$log = wc_get_logger();

		$orders = wc_get_orders( array(
			'limit'          => 50,
			'status'         => array( 'pending', 'on-hold' ),
			'meta_key'       => '_mcp_payment_expires_at',
			'meta_compare'   => 'EXISTS',
			'return'         => 'ids',
		) );

		$now = time();
		$cleaned = 0;

		foreach ( $orders as $order_id ) {
			$order       = wc_get_order( $order_id );
			$expires_at  = $order ? $order->get_meta( '_mcp_payment_expires_at' ) : 0;

			if ( ! $order || ! $expires_at || (int) $expires_at > $now ) {
				continue;
			}

			// Đã xử lý rồi thì bỏ qua
			if ( $order->has_status( array( 'processing', 'completed', 'cancelled', 'failed' ) ) ) {
				continue;
			}

			$log->info( "Cron: cleaning expired order #{$order_id}", array( 'source' => 'mcp_cron' ) );

			// ─── Hủy PaymentIntent trên Stripe ────────────────
			$intent_id = $order->get_meta( '_mcp_stripe_payment_intent_id' );
			if ( ! empty( $intent_id ) ) {
				$settings     = get_option( 'woocommerce_mcp_custom_payment_settings', array() );
				$secret_key   = $settings['test_secret_key'] ?? '';
				$test_mode    = 'yes' === ( $settings['test_mode'] ?? 'yes' );
				if ( ! $test_mode ) {
					$secret_key = $settings['live_secret_key'] ?? '';
				}

				if ( ! empty( $secret_key ) ) {
					require_once MCP_PLUGIN_DIR . 'includes/class-stripe-api.php';
					$stripe_api = new MCP_Stripe_API( $secret_key );
					$stripe_api->cancel_payment_intent( $intent_id );
					$log->info( "Cron: cancelled PaymentIntent {$intent_id} for order #{$order_id}", array( 'source' => 'mcp_cron' ) );
				}
			}

			// ─── Hoàn stock ───────────────────────────────────
			wc_increase_stock_levels( $order_id );

			// ─── Chuyển trạng thái ───────────────────────────
			$order->update_status(
				'cancelled',
				__( '[My Custom Payment] Đơn hàng hết hạn thanh toán (quá 15 phút).', 'my-custom-payment' )
			);
			$order->save();

			$cleaned++;
		}

		if ( $cleaned > 0 ) {
			$log->info( "Cron: cleaned {$cleaned} expired orders.", array( 'source' => 'mcp_cron' ) );
		}
	}

	/**
	 * Xử lý return URL (khi khách được Stripe redirect về).
	 */
	public static function handle_return() {
		$order_id = isset( $_GET['order_id'] ) ? absint( $_GET['order_id'] ) : 0;
		$session_id = isset( $_GET['session_id'] ) ? sanitize_text_field( wp_unslash( $_GET['session_id'] ) ) : '';

		if ( ! $order_id || ! $session_id ) {
			wp_safe_redirect( wc_get_cart_url() );
			exit;
		}

		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			wp_safe_redirect( wc_get_cart_url() );
			exit;
		}

		// Kiểm tra trạng thái — nếu webhook đã xử lý thì redirect thẳng đến thank-you
		if ( $order->has_status( array( 'processing', 'completed' ) ) ) {
			wp_safe_redirect( $order->get_checkout_order_received_url() );
			exit;
		}

		// Nếu chưa, chờ 1s rồi redirect đến thank-you (webhook sẽ handle ngay sau đó)
		wp_safe_redirect( $order->get_checkout_order_received_url() );
		exit;
	}

	/**
	 * Ghi log debug.
	 *
	 * @param string $message
	 */
	private function log_debug( $message ) {
		if ( $this->log ) {
			$this->log->info( $message, array( 'source' => 'mcp_webhook' ) );
		}
	}

	/**
	 * Ghi log lỗi.
	 *
	 * @param string $message
	 */
	private function log_error( $message ) {
		if ( $this->log ) {
			$this->log->error( $message, array( 'source' => 'mcp_webhook' ) );
		}
		error_log( 'MCP Webhook: ' . $message );
	}
}

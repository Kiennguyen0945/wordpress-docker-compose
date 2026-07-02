<?php
/**
 * Stripe API Helper
 *
 * Tương tác với Stripe Checkout Sessions API (Sandbox/Live).
 * Dùng wp_remote_post() — không cần thư viện bên ngoài.
 *
 * @package My_Custom_Payment
 * @since   2.0.0
 */

defined( 'ABSPATH' ) || exit;

class MCP_Stripe_API {

	/**
	 * Stripe API base URL.
	 */
	const API_BASE = 'https://api.stripe.com/v1';

	/**
	 * Secret key (test hoặc live).
	 *
	 * @var string
	 */
	private $secret_key;

	/**
	 * Webhook secret (để verify signature).
	 *
	 * @var string
	 */
	private $webhook_secret;

	/**
	 * Logger.
	 *
	 * @var WC_Logger|null
	 */
	private $log;

	/**
	 * Constructor.
	 *
	 * @param string $secret_key     Stripe Secret Key.
	 * @param string $webhook_secret Stripe Webhook Signing Secret.
	 */
	public function __construct( $secret_key, $webhook_secret = '' ) {
		$this->secret_key     = $secret_key;
		$this->webhook_secret = $webhook_secret;

		if ( defined( 'MCP_DEBUG' ) && MCP_DEBUG ) {
			$this->log = wc_get_logger();
		}
	}

	/**
	 * Tạo Stripe Checkout Session.
	 *
	 * @param array $params Tham số Checkout Session (xem Stripe docs).
	 * @return object|false Stripe response object, hoặc false nếu lỗi.
	 */
	public function create_checkout_session( $params ) {
		$response = $this->post( '/checkout/sessions', $params );

		if ( is_wp_error( $response ) ) {
			$this->log_error( 'Stripe API error (create_checkout_session): ' . $response->get_error_message() );
			return false;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ) );
		$code = wp_remote_retrieve_response_code( $response );

		if ( $code < 200 || $code >= 300 ) {
			$err_msg = isset( $body->error->message ) ? $body->error->message : 'Unknown Stripe error';
			$this->log_error( "Stripe API returned HTTP {$code}: {$err_msg}" );
			return false;
		}

		return $body;
	}

	/**
	 * Lấy thông tin Checkout Session từ Stripe.
	 *
	 * @param string $session_id ID của Stripe Checkout Session.
	 * @return object|false
	 */
	public function retrieve_checkout_session( $session_id ) {
		$response = $this->get( '/checkout/sessions/' . rawurlencode( $session_id ) );

		if ( is_wp_error( $response ) ) {
			$this->log_error( 'Stripe API error (retrieve_checkout_session): ' . $response->get_error_message() );
			return false;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ) );
		$code = wp_remote_retrieve_response_code( $response );

		if ( $code < 200 || $code >= 300 ) {
			$err_msg = isset( $body->error->message ) ? $body->error->message : 'Unknown Stripe error';
			$this->log_error( "Stripe API returned HTTP {$code}: {$err_msg}" );
			return false;
		}

		return $body;
	}

	/**
	 * Tạo Stripe PaymentIntent.
	 *
	 * @param array $params Tham số PaymentIntent.
	 * @return object|false
	 */
	public function create_payment_intent( $params ) {
		$response = $this->post( '/payment_intents', $params );

		if ( is_wp_error( $response ) ) {
			$this->log_error( 'Stripe API error (create_payment_intent): ' . $response->get_error_message() );
			return false;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ) );
		$code = wp_remote_retrieve_response_code( $response );

		if ( $code < 200 || $code >= 300 ) {
			$err_msg = isset( $body->error->message ) ? $body->error->message : 'Unknown Stripe error';
			$this->log_error( "Stripe API returned HTTP {$code}: {$err_msg}" );
			return false;
		}

		return $body;
	}

	/**
	 * Lấy thông tin PaymentIntent từ Stripe.
	 *
	 * @param string $intent_id ID của PaymentIntent.
	 * @return object|false
	 */
	public function retrieve_payment_intent( $intent_id ) {
		$response = $this->get( '/payment_intents/' . rawurlencode( $intent_id ) );

		if ( is_wp_error( $response ) ) {
			$this->log_error( 'Stripe API error (retrieve_payment_intent): ' . $response->get_error_message() );
			return false;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ) );
		$code = wp_remote_retrieve_response_code( $response );

		if ( $code < 200 || $code >= 300 ) {
			$err_msg = isset( $body->error->message ) ? $body->error->message : 'Unknown Stripe error';
			$this->log_error( "Stripe API returned HTTP {$code}: {$err_msg}" );
			return false;
		}

		return $body;
	}

	/**
	 * Hủy PaymentIntent trên Stripe.
	 *
	 * @param string $intent_id ID của PaymentIntent cần hủy.
	 * @return object|false
	 */
	public function cancel_payment_intent( $intent_id ) {
		$response = $this->post( '/payment_intents/' . rawurlencode( $intent_id ) . '/cancel', array() );

		if ( is_wp_error( $response ) ) {
			$this->log_error( 'Stripe API error (cancel_payment_intent): ' . $response->get_error_message() );
			return false;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ) );
		$code = wp_remote_retrieve_response_code( $response );

		if ( $code < 200 || $code >= 300 ) {
			$err_msg = isset( $body->error->message ) ? $body->error->message : 'Unknown Stripe error';
			$this->log_error( "Stripe API returned HTTP {$code}: {$err_msg}" );
			return false;
		}

		return $body;
	}

	/**
	 * Xác thực chữ ký Webhook từ Stripe.
	 *
	 * @param string $payload   Nội dung request body (raw JSON).
	 * @param string $signature Giá trị header Stripe-Signature.
	 * @return object|false     Event object nếu hợp lệ, false nếu không.
	 */
	public function verify_webhook_signature( $payload, $signature ) {
		if ( empty( $this->webhook_secret ) ) {
			$this->log_error( 'Webhook secret chưa được cấu hình.' );
			return false;
		}

		try {
			$expected = hash_hmac( 'sha256', $payload, $this->webhook_secret );
			$parts    = explode( ',', $signature );

			$received_sig = '';
			$timestamp    = '';
			foreach ( $parts as $part ) {
				$part = trim( $part );
				if ( str_starts_with( $part, 'v1=' ) ) {
					$received_sig = substr( $part, 3 );
				} elseif ( str_starts_with( $part, 't=' ) ) {
					$timestamp = substr( $part, 2 );
				}
			}

			if ( empty( $received_sig ) || empty( $timestamp ) ) {
				$this->log_error( 'Webhook signature thiếu v1= hoặc t=.' );
				return false;
			}

			// Tạo signed payload: timestamp + '.' + payload
			$signed_payload  = $timestamp . '.' . $payload;
			$computed_sig    = hash_hmac( 'sha256', $signed_payload, $this->webhook_secret );

			if ( ! hash_equals( $computed_sig, $received_sig ) ) {
				$this->log_error( 'Webhook signature không khớp. Có thể ai đó đang giả mạo.' );
				return false;
			}

			// Kiểm tra timestamp không quá 5 phút (chống replay attack)
			if ( abs( (int) $timestamp - time() ) > 300 ) {
				$this->log_error( 'Webhook timestamp quá cũ — có thể là replay attack.' );
				return false;
			}

			return json_decode( $payload );
		} catch ( Exception $e ) {
			$this->log_error( 'Lỗi verify webhook: ' . $e->getMessage() );
			return false;
		}
	}

	/**
	 * Gửi POST request tới Stripe API.
	 *
	 * @param string $path   Đường dẫn (vd: /checkout/sessions).
	 * @param array  $params Body params.
	 * @return array|WP_Error
	 */
	private function post( $path, $params ) {
		$url = self::API_BASE . $path;

		$this->log_debug( "POST {$url}" );
		$this->log_debug( 'Params: ' . wp_json_encode( $this->mask_sensitive( $params ) ) );

		$response = wp_remote_post( $url, array(
			'headers' => array(
				'Authorization' => 'Bearer ' . $this->secret_key,
				'Content-Type'  => 'application/x-www-form-urlencoded',
			),
			'body'    => $params,
			'timeout' => 30,
		) );

		$this->log_debug( 'Response code: ' . wp_remote_retrieve_response_code( $response ) );

		return $response;
	}

	/**
	 * Gửi GET request tới Stripe API.
	 *
	 * @param string $path Đường dẫn.
	 * @return array|WP_Error
	 */
	private function get( $path ) {
		$url = self::API_BASE . $path;

		$this->log_debug( "GET {$url}" );

		$response = wp_remote_get( $url, array(
			'headers' => array(
				'Authorization' => 'Bearer ' . $this->secret_key,
			),
			'timeout' => 30,
		) );

		$this->log_debug( 'Response code: ' . wp_remote_retrieve_response_code( $response ) );

		return $response;
	}

	/**
	 * Che giấu thông tin nhạy cảm (secret key, ...) trước khi log.
	 *
	 * @param array $data Dữ liệu cần mask.
	 * @return array
	 */
	private function mask_sensitive( $data ) {
		$masked = $data;
		$sensitive_keys = array( 'api_key', 'secret_key', 'password', 'token' );

		foreach ( $sensitive_keys as $key ) {
			if ( isset( $masked[ $key ] ) && is_string( $masked[ $key ] ) ) {
				$masked[ $key ] = substr( $masked[ $key ], 0, 6 ) . '...';
			}
		}

		return $masked;
	}

	/**
	 * Ghi log debug.
	 *
	 * @param string $message
	 */
	private function log_debug( $message ) {
		if ( $this->log ) {
			$this->log->info( $message, array( 'source' => 'mcp_stripe_api' ) );
		}
	}

	/**
	 * Ghi log lỗi.
	 *
	 * @param string $message
	 */
	private function log_error( $message ) {
		if ( $this->log ) {
			$this->log->error( $message, array( 'source' => 'mcp_stripe_api' ) );
		}
		// Luôn ghi error log ngay cả khi MCP_DEBUG tắt
		error_log( 'MCP Stripe API: ' . $message );
	}
}

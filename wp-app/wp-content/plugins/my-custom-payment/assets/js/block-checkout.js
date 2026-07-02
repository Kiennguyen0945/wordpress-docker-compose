/**
 * My Custom Payment — Checkout Block integration
 *
 * Đăng ký payment method với WooCommerce Cart & Checkout Blocks.
 * Hỗ trợ cả Stripe Checkout và Mock (fallback) mode.
 *
 * @package My_Custom_Payment
 * @since   2.0.0
 */

( function () {
	'use strict';

	const { registerPaymentMethod } = window.wc.wcBlocksRegistry;
	const { __ } = window.wp.i18n;
	const { getPaymentMethodData } = window.wc.wcSettings;
	const { decodeEntities } = window.wp.htmlEntities;
	const { sanitizeHTML } = window.wc.sanitize;
	const { createElement, RawHTML } = window.wp.element;

	// Lấy dữ liệu từ PHP (truyền qua get_payment_method_data)
	const settings = getPaymentMethodData( 'mcp_custom_payment', {} );
	const defaultTitle = __( 'Thẻ tín dụng / Thẻ ghi nợ (Stripe)', 'my-custom-payment' );
	const title = decodeEntities( settings?.title || '' ) || defaultTitle;
	const isStripe = settings?.is_stripe === true;
	const testMode = settings?.test_mode === true;

	// Nội dung hiển thị khi phương thức được chọn
	const Content = () => {
		const descriptionHTML = settings?.description
			? sanitizeHTML( settings.description )
			: '';

		let extraHTML = '';

		if ( isStripe ) {
			// Chế độ Stripe: hiển thị badge
			const modeText = testMode
				? __( '🔒 Sandbox (Test)', 'my-custom-payment' )
				: __( '🔐 Live', 'my-custom-payment' );
			extraHTML = '<p style="margin:10px 0 0;font-size:13px;color:#666;"><em>' + modeText + '</em></p>';
		} else {
			// Chế độ Mock: hiển thị thông tin tài khoản ngân hàng
			const bankInfo = settings?.bank_info || '';
			if ( bankInfo ) {
				const lines = bankInfo.split( '\n' );
				const formatted = lines
					.map( function ( line ) {
						return line.trim();
					} )
					.filter( Boolean )
					.join( '<br>' );
				extraHTML = '<div class="mcp-bank-info" style="background:#f8f9fa;padding:15px;border-radius:6px;margin-top:10px;font-size:14px;line-height:1.7;">' + formatted + '</div>';
			}
		}

		const children = extraHTML
			? descriptionHTML + extraHTML
			: descriptionHTML || __( 'Thanh toán qua Stripe.', 'my-custom-payment' );

		return createElement( RawHTML, {
			children: children,
		} );
	};

	// Label hiển thị ở danh sách phương thức
	const LabelComponent = function ( { components } ) {
		const { PaymentMethodLabel } = components;
		return createElement( PaymentMethodLabel, { text: title } );
	};

	// Đăng ký payment method
	registerPaymentMethod( {
		name: 'mcp_custom_payment',
		label: createElement( LabelComponent ),
		content: createElement( Content ),
		edit: createElement( Content ),
		canMakePayment: function () {
			return true;
		},
		ariaLabel: title,
		supports: {
			features: settings?.supports ?? [],
		},
	} );
} )();

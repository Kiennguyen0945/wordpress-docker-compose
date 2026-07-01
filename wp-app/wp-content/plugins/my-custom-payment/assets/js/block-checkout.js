/**
 * My Custom Payment — Checkout Block integration
 *
 * Đăng ký payment method với WooCommerce Cart & Checkout Blocks.
 *
 * @package My_Custom_Payment
 * @since   1.0.0
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
	const defaultTitle = __( 'Chuyển khoản ngân hàng', 'my-custom-payment' );
	const title = decodeEntities( settings?.title || '' ) || defaultTitle;

	// Nội dung hiển thị khi phương thức được chọn
	const Content = () => {
		const descriptionHTML = settings?.description
			? sanitizeHTML( settings.description )
			: '';

		const bankInfo = settings?.bank_info || '';

		// Nếu có thông tin tài khoản NH, hiển thị thêm
		let bankInfoHTML = '';
		if ( bankInfo ) {
			const lines = bankInfo.split( '\n' );
			bankInfoHTML = lines
				.map( function ( line ) {
					return line.trim();
				} )
				.filter( Boolean )
				.join( '<br>' );
		}

		const children = bankInfoHTML
			? descriptionHTML + '<div class="mcp-bank-info">' + bankInfoHTML + '</div>'
			: descriptionHTML;

		return createElement( RawHTML, {
			children: children || __( 'Thanh toán qua chuyển khoản ngân hàng.', 'my-custom-payment' ),
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

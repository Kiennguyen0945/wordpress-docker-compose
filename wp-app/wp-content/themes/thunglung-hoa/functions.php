<?php
/**
 * Thung Lũng Hoa Theme Functions
 *
 * Đây là file index — chỉ require các module trong inc/.
 * Mỗi người sửa file module riêng của mình, không ai sửa file này.
 *
 * @package ThungLungHoa
 * @since 1.0.0
 */

// =========================================================
// MODULE: Mỗi file trong inc/ là một module độc lập
// Người A sửa: setup, helpers, auth, user-profile, shipping
// Người B sửa: enqueue, woocommerce, ajax
// =========================================================

require_once get_template_directory() . '/inc/setup.php';
require_once get_template_directory() . '/inc/enqueue.php';
require_once get_template_directory() . '/inc/woocommerce.php';
require_once get_template_directory() . '/inc/helpers.php';
require_once get_template_directory() . '/inc/ajax.php';
require_once get_template_directory() . '/inc/auth.php';
require_once get_template_directory() . '/inc/user-profile.php';
require_once get_template_directory() . '/inc/shipping.php';


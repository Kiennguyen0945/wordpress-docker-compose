=== My Custom Payment ===
Contributors: yourname
Requires at least: 6.0
Tested up to:      6.7
Requires PHP:      8.0
Stable tag:        1.0.0
License:           GPLv2 or later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html
Tags:             woocommerce, payment-gateway, thanh-toan, chuyen-khoan

Cổng thanh toán tùy chỉnh cho WooCommerce. Ở phiên bản 1.0 hoạt động ở chế độ Mock (giả lập).

== Description ==

Plugin bổ sung phương thức thanh toán **My Custom Payment** vào WooCommerce.

**Tính năng chính (v1.0):**
* Thêm phương thức thanh toán tùy chỉnh tại trang checkout.
* Hiển thị thông tin tài khoản ngân hàng cho khách hàng.
* Tự động xác nhận đơn hàng và chuyển sang trạng thái "Đang xử lý" (Processing).
* Giảm tồn kho và xóa giỏ hàng sau khi đặt hàng thành công.
* Cho phép quản trị viên tùy chỉnh tên, mô tả, thông tin tài khoản ngân hàng từ admin.

== Installation ==

1. Đảm bảo bạn đã cài đặt và kích hoạt **WooCommerce**.
2. Upload thư mục `my-custom-payment` vào `/wp-content/plugins/`.
3. Vào **Plugins** → kích hoạt plugin **My Custom Payment**.
4. Vào **WooCommerce → Settings → Payments** → bật **My Custom Payment**.
5. Nhập thông tin tài khoản ngân hàng và tùy chỉnh nếu cần.

== Frequently Asked Questions ==

= Plugin có thu phí không? =

Không. Plugin hoàn toàn miễn phí.

= Làm sao để kết nối với API thật? =

Phiên bản 1.0 đang ở chế độ Mock. Các phiên bản sau sẽ hỗ trợ kết nối API thật.

== Changelog ==

= 1.0.0 =
* Phiên bản đầu tiên.
* Hỗ trợ thanh toán Mock (giả lập).
* Hiển thị thông tin tài khoản ngân hàng.
* Tự động chuyển đơn hàng sang Processing.

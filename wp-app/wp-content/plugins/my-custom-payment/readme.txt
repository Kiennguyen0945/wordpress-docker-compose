=== My Custom Payment ===
Contributors: yourname
Requires at least: 6.0
Tested up to:      6.7
Requires PHP:      8.0
Stable tag:        2.0.0
License:           GPLv2 or later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html
Tags:             woocommerce, payment-gateway, stripe, thanh-toan, chuyen-khoan

Cổng thanh toán tùy chỉnh cho WooCommerce — tích hợp Stripe Checkout (Sandbox/Live) và chế độ Mock (giả lập).

== Description ==

Plugin bổ sung phương thức thanh toán **My Custom Payment** vào WooCommerce, sử dụng **Stripe Checkout** làm nền tảng xử lý thanh toán.

**Tính năng chính (v2.0):**
* Tích hợp **Stripe Checkout** — khách hàng được redirect sang trang thanh toán an toàn của Stripe.
* Hỗ trợ **Sandbox (Test)** và **Live** mode — chuyển đổi dễ dàng trong admin.
* **Webhook REST API** — xử lý sự kiện thanh toán qua REST API endpoint, đáng tin cậy hơn legacy `wc-api`.
* **Xác thực chữ ký Webhook** (HMAC SHA256) — chống giả mạo và replay attack.
* **Kiểm tra số tiền** trước khi xử lý đơn hàng — chống gian lận thanh toán.
* **Flow đơn hàng đầy đủ**: giữ stock ngay khi đặt hàng → webhook xác nhận → huỷ đơn + hoàn stock nếu quá hạn.
* **Cronjob dọn đơn quá hạn**: tự động huỷ đơn Pending sau 15 phút và hoàn lại stock.
* **Fallback Mock mode** — plugin vẫn chạy được ngay cả khi chưa nhập Stripe keys.
* Ghi log đầy đủ với WC_Logger (bật qua hằng số `MCP_DEBUG`).
* Không cần thư viện bên ngoài — dùng `wp_remote_post()` và `wp_remote_get()`.

== Luồng hoạt động (Checkout Flow) ==

Mô tả chi tiết luồng xử lý đơn hàng từ lúc khách đặt hàng đến khi thanh toán hoàn tất:

=== 1. Khởi tạo đơn hàng và chuyển hướng đến Stripe ===

- Khách hàng thêm sản phẩm vào giỏ → vào trang Checkout → chọn **My Custom Payment** → bấm **Place Order**.
- Plugin gọi Stripe API tạo **Checkout Session** với tham số:
  - `payment_intent_data.metadata.order_id` = ID đơn hàng.
  - `line_items` = sản phẩm trong giỏ.
  - `mode = payment`, `currency = vnd`.
- Kiểm tra **zero-decimal currency**: với VND (và các đồng tiền như JPY, KRW...), số tiền gửi lên Stripe là số nguyên, không nhân 100.
- Stock được giữ ngay lập tức qua `wc_maybe_reduce_stock_levels()`.
- Đơn hàng ghi nhận `_mcp_stripe_session_id` và `_mcp_payment_expires_at` (thời gian hết hạn = now + 15 phút).
- Khách được redirect sang `checkout.stripe.com`.

=== 2. Thanh toán trên Stripe ===

- Khách nhập thông tin thẻ (sandbox: `4242 4242 4242 4242`,任何 date trong tương lai, any CVC).
- Stripe xử lý thanh toán và gửi webhook về plugin.
- Nếu thành công: Stripe redirect khách về trang **Order Confirmation** (`/checkout/order-received/{id}/`).

=== 3. Xử lý Webhook ===

Plugin lắng nghe webhook tại endpoint REST API:

    POST {site_url}/wp-json/mcp/v1/webhook

Các sự kiện được xử lý:

- **`payment_intent.succeeded`**:
  1. Verify chữ ký HMAC SHA256.
  2. Tra cứu đơn hàng qua `metadata.order_id` (ưu tiên) → fallback `_mcp_stripe_payment_intent_id` meta → fallback Checkout Session.
  3. Lưu `_mcp_stripe_payment_intent_id` vào order meta.
  4. Kiểm tra số tiền khớp với đơn hàng (chống gian lận).
  5. Set `transaction_id`, thêm order note.
  6. Chuyển trạng thái đơn hàng từ **Pending payment → Processing**.
  7. Xoá `_mcp_payment_expires_at`.

- **`payment_intent.payment_failed`**:
  1. Chuyển đơn hàng sang **Failed**.
  2. Hoàn stock qua `wc_increase_stock_levels()`.

- **`payment_intent.canceled`**:
  1. Chuyển đơn hàng sang **Cancelled**.
  2. Hoàn stock.

=== 4. Cronjob dọn đơn quá hạn ===

- Chạy mỗi **5 phút** (hook: `mcp_cleanup_expired_orders`).
- Query các đơn `Pending` / `On-hold` có `_mcp_payment_expires_at < now`.
- Với mỗi đơn quá hạn:
  1. Huỷ PaymentIntent trên Stripe (nếu có).
  2. Hoàn stock.
  3. Chuyển đơn hàng sang **Cancelled** với ghi chú "Đơn hàng hết hạn thanh toán (quá 15 phút)."

=== 5. Sơ đồ sequence ===

```
Khách hàng          Stripe            Plugin WooCommerce
    │                  │                     │
    │── Place Order ───│─────────────────────│──► Tạo Checkout Session
    │                  │                     │──► Giữ stock
    │                  │                     │──► redirect → Stripe
    │◄─ Redirect ──────│                     │
    │── Nhập thẻ ──────│                     │
    │── Pay ───────────│─────────────────────│──► Webhook payment_intent.succeeded
    │                  │                     │──► Verify signature
    │                  │                     │──► Kiểm tra số tiền
    │                  │                     │──► Cập nhật Order → Processing
    │                  │                     │──► Xoá _mcp_payment_expires_at
    │◄─ Redirect ──────│                     │
    │── Order Confirm ─│                     │
```

== Installation ==

1. Đảm bảo bạn đã cài đặt và kích hoạt **WooCommerce**.
2. Upload thư mục `my-custom-payment` vào `/wp-content/plugins/`.
3. Vào **Plugins** → kích hoạt plugin **My Custom Payment**.
4. Vào **WooCommerce → Settings → Payments** → bật **My Custom Payment**.
5. Nhập Stripe API keys (xem hướng dẫn bên dưới).
6. Nếu dùng Stripe CLI local: chạy `stripe listen --forward-to http://localhost/wp-json/mcp/v1/webhook`.

== Frequently Asked Questions ==

= Plugin có thu phí không? =

Không. Plugin hoàn toàn miễn phí.

= Làm sao để kết nối với Stripe? =

Vào WooCommerce → Settings → Payments → My Custom Payment. Bật chế độ Sandbox, nhập **Test Publishable Key**, **Test Secret Key** và **Test Webhook Secret** từ Stripe Dashboard. Bỏ tick Sandbox và nhập Live keys khi sẵn sàng chạy thật.

Các bước lấy keys:
1. Vào https://dashboard.stripe.com/register → tạo tài khoản Sandbox.
2. Vào **Developers → API keys** → copy **Publishable key** (`pk_test_...`) và **Secret key** (`sk_test_...`).
3. Vào **Developers → Webhooks** → bấm **Add endpoint**:
   - Endpoint URL: `https://your-site.com/wp-json/mcp/v1/webhook`
   - Chọn sự kiện: `payment_intent.succeeded`, `payment_intent.payment_failed`, `payment_intent.canceled`.
4. Sau khi tạo, copy **Signing secret** (`whsec_...`) và dán vào trường **Webhook Secret** trong admin.

= Có cần cài thư viện Stripe PHP không? =

Không. Plugin dùng `wp_remote_post()` và `wp_remote_get()` để gọi Stripe API, không cần Composer hay thư viện bên ngoài.

= Webhook hoạt động thế nào? =

Plugin đăng ký hai endpoint webhook:

1. **REST API** (khuyên dùng): `POST {site_url}/wp-json/mcp/v1/webhook`
   - Đăng ký qua `rest_api_init`, trả về JSON response.
   - Hoạt động ổn định, không phụ thuộc rewrite rules.

2. **Legacy WC-API** (dự phòng): `{site_url}/wc-api/mcp-stripe-webhook/`
   - Đăng ký qua hook `woocommerce_api_`.
   - Yêu cầu rewrite rules được flush.

Dùng Stripe CLI để test local:
```
stripe listen --forward-to http://localhost/wp-json/mcp/v1/webhook
```

Hoặc dùng Docker (xem docker-compose.yml):
```
docker compose --profile stripe up -d stripe-cli
```

= Làm sao để test thanh toán thành công? =

Dùng thẻ test của Stripe:
- **Số thẻ**: `4242 4242 4242 4242`
- **Ngày hết hạn**: bất kỳ trong tương lai (vd: `12/34`)
- **CVC**: 3 chữ số bất kỳ (vd: `123`)

= Làm sao để test thanh toán thất bại? =

Stripe cung cấp các số thẻ test:
- **Thẻ bị từ chối**: `4000 0000 0000 0002`
- **Thẻ không đủ tiền**: `4000 0000 0000 9995`
- **Thẻ hết hạn**: `4000 0000 0000 0069`
- Chi tiết: https://docs.stripe.com/testing

== Changelog ==

= 2.0.0 =
* Tích hợp Stripe Checkout (Sandbox/Live) với PaymentIntent.
* Thêm Webhook Handler xác thực chữ ký và cập nhật trạng thái đơn hàng (6 sự kiện).
* Thêm REST API endpoint `/wp-json/mcp/v1/webhook` cho webhook.
* Flow đơn hàng đầy đủ: giữ stock → webhook → processing, huỷ + hoàn stock khi fail/expire.
* Cronjob dọn đơn quá hạn (mỗi 5 phút).
* Xoá `_mcp_payment_expires_at` sau thanh toán thành công.
* Hỗ trợ Docker Compose cho Stripe CLI.
* Thêm trường cấu hình: Test/Live keys, Webhook Secret, chế độ Sandbox.
* Fallback Mock mode khi chưa nhập Stripe keys.
* Ghi log bảo mật với WC_Logger.

= 1.0.0 =
* Phiên bản đầu tiên.
* Hỗ trợ thanh toán Mock (giả lập).
* Hiển thị thông tin tài khoản ngân hàng.
* Tự động chuyển đơn hàng sang Processing.

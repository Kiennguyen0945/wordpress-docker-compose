# PROMPT AI — Kế hoạch phát triển WordPress E-commerce (Cửa hàng hoa)

> **Ngày lập:** 04/07/2026
> **Mô hình AI:** DeepSeek Pro (GitHub Copilot)
> **Mục tiêu:** Hoàn thiện cấu hình WooCommerce cho cửa hàng hoa + tùy chỉnh nâng cao

---

## PHẦN 1: PHÂN TÍCH & ĐÁNH GIÁ ƯU TIÊN

### Ma trận đánh giá (Priority × Difficulty × Impact)

| # | Công việc | Độ khó | Ảnh hưởng | Ưu tiên | Cần AI? | Ghi chú |
|---|----------|--------|-----------|---------|---------|---------|
| 1 | Thiết lập danh mục sản phẩm (Categories) | ★☆☆ Dễ | ★★★ Lớn | **P1** | ❌ Không | Làm trực tiếp trên WP Admin |
| 2 | Thiết lập thuộc tính (Attributes: màu sắc, kích thước) | ★☆☆ Dễ | ★★★ Lớn | **P1** | ❌ Không | Làm trực tiếp trên WP Admin |
| 3 | Đăng tải sản phẩm mẫu (Simple Product) | ★☆☆ Dễ | ★★★ Lớn | **P2** | ❌ Không | Làm trực tiếp trên WP Admin |
| 4 | Đăng tải sản phẩm mẫu (Variable Product) | ★★☆ TB | ★★★ Lớn | **P2** | ⚠️ Có thể | WP Admin + AI hỗ trợ nếu cần import hàng loạt |
| 5 | Thiết lập vùng vận chuyển + phí ship | ★★☆ TB | ★★★ Lớn | **P3** | ⚠️ Một phần | Cơ bản làm trên WP; nâng cao (API, theo cân nặng) cần AI |
| 6 | Cấu hình COD (Trả tiền khi nhận hàng) | ★☆☆ Dễ | ★★☆ TB | **P4** | ❌ Không | Bật sẵn trong WooCommerce |
| 7 | Cấu hình Chuyển khoản ngân hàng (BACS) | ★☆☆ Dễ | ★★☆ TB | **P4** | ❌ Không | Có sẵn trong WooCommerce, chỉ cần điền thông tin |
| 8 | Tùy chỉnh trang My Account (thêm trạng thái đơn hàng, địa chỉ) | ★★★ Khó | ★★★ Lớn | **P5** | ✅ Có | Cần code custom plugin/theme |
| 9 | Tích hợp MoMo | ★★★ Khó | ★★☆ TB | **P6** | ✅ Có | Cần plugin hoặc code custom |
| 10 | Tích hợp VNPay | ★★★ Khó | ★★☆ TB | **P6** | ✅ Có | Cần plugin hoặc code custom |
| 11 | Tích hợp PayPal | ★★☆ TB | ★★☆ TB | **P6** | ⚠️ Một phần | Có plugin chính thức, chủ yếu config |
| 12 | QR Code chuyển khoản | ★★☆ TB | ★★☆ TB | **P6** | ✅ Có | Cần code custom hiển thị QR |
| 13 | Chụp ảnh giao diện + Báo cáo | ★☆☆ Dễ | ★☆☆ Nhỏ | **P7** | ❌ Không | Thao tác thủ công |

### Thứ tự triển khai khuyến nghị

```
P1 → P2 → P3 → P4 → P5 → P6 → P7
```

**Nguyên tắc:** Dễ + Ảnh hưởng lớn → Làm trước. Cần AI/Code nhiều → Làm sau (cần thời gian test).

---

## PHẦN 2: PHÂN LOẠI — WORDPRESS THỦ CÔNG vs CẦN AI

### ✅ Có thể thao tác trực tiếp trên WordPress Admin (không cần AI)

| Công việc | Cách thực hiện |
|----------|---------------|
| Tạo danh mục (Hoa sinh nhật, Hoa khai trương, Hoa cưới...) | Products → Categories → Add New |
| Tạo thuộc tính (Màu sắc, Kích thước bó, Loại hoa...) | Products → Attributes → Add New |
| Thêm sản phẩm đơn giản | Products → Add New → Simple Product |
| Thêm sản phẩm biến thể (số lượng ít) | Products → Add New → Variable Product → thêm variations |
| Bật COD | WooCommerce → Settings → Payments → COD → Enable |
| Cấu hình BACS (chuyển khoản) | WooCommerce → Settings → Payments → BACS → Enable + điền STK |
| Tạo Shipping Zones cơ bản | WooCommerce → Settings → Shipping → Add Zone |
| Cài đặt PayPal plugin | Plugins → Add New → "WooCommerce PayPal Payments" |

### ⚠️ Cần AI hỗ trợ (code/custom)

| Công việc | Lý do cần AI |
|----------|-------------|
| Import sản phẩm hàng loạt (CSV) | Cần script hoặc file CSV mẫu |
| Phí ship theo cân nặng phức tạp | Cần code custom shipping method hoặc config nâng cao |
| Tích hợp MoMo | Cần plugin custom hoặc code tích hợp API |
| Tích hợp VNPay | Cần plugin custom hoặc code tích hợp API |
| QR Code chuyển khoản | Cần code hiển thị QR động trên checkout |
| Tùy chỉnh My Account: thêm trạng thái "Đang cắm hoa", "Đang giao" | Cần code custom order status |
| Tùy chỉnh My Account: thêm field "Địa chỉ cơ quan" | Cần code custom fields |
| Tùy chỉnh giao diện My Account | Cần code custom template |

---

## PHẦN 3: DANH SÁCH PROMPT AI CHI TIẾT (theo thứ tự ưu tiên)

---

### PROMPT #1 ⭐ P2 — Tạo script WP-CLI để import sản phẩm mẫu hàng loạt

> **Ước tính:** 1 prompt
> **Thời điểm:** Sau khi đã tạo xong Categories & Attributes thủ công (P1)

**Lý do cần AI:** Nhập tay 20-30 sản phẩm mất rất nhiều thời gian. AI có thể sinh script WP-CLI với dữ liệu mẫu thực tế cho cửa hàng hoa.

**Input cho AI:**
- Danh sách danh mục đã tạo (vd: hoa-sinh-nhat, hoa-khai-truong, hoa-cuoi, hoa-tinh-yeu...)
- Danh sách thuộc tính đã tạo (mau-sac, kich-thuoc)
- Cấu trúc sản phẩm mong muốn (tên, giá, mô tả, ảnh placeholder)

**Prompt chi tiết:**

```
Tôi đang xây dựng cửa hàng hoa trên WordPress + WooCommerce. Tôi cần một script WP-CLI 
bằng Bash để import hàng loạt sản phẩm mẫu.

Yêu cầu:
1. Script chạy bằng: docker compose run --rm wpcli wp eval-file /var/www/html/import-products.php
2. Tạo ~20 sản phẩm hoa, bao gồm:
   - 10 sản phẩm đơn giản (Simple Product): hoa bó, lẵng hoa, giỏ hoa
   - 10 sản phẩm có biến thể (Variable Product): mỗi sản phẩm có biến thể theo kích thước 
     (Nhỏ / Vừa / Lớn) với giá khác nhau
3. Dữ liệu mẫu:

Danh mục

Cấu trúc danh mục

Hoa theo dịp

├── Hoa chia buồn (slug: hoa-chia-buon)

├── Hoa chúc mừng (slug: hoa-chuc-mung)

├── Hoa cưới hỏi (slug: hoa-cuoi-hoi)

├── Hoa khai trương (slug: hoa-khai-truong)

└── Hoa sinh nhật (slug: hoa-sinh-nhat)

Hoa theo kiểu

├── Giỏ hoa (slug: gio-hoa)

├── Hoa bình (slug: hoa-binh)

├── Hoa bó (slug: hoa-bo)

│ └── Mô tả: Các mẫu hoa bó đẹp cho sinh nhật, tình yêu, chúc mừng.

├── Hộp hoa (slug: hop-hoa)

└── Lẵng hoa (slug: lang-hoa)


Thuộc tính

Kích thước

slug: kich-thuoc
Order by: Custom ordering

Các giá trị:

Lớn

Nhỏ

Vừa

Thuộc tính

Loại hoa chính

slug: loai-hoa-chinh
Order by: Custom ordering

Các giá trị:

Hoa cẩm tú cầu

Hoa hồng

Hoa hướng dương

Hoa lan

Hoa ly

Hoa tulip

Thuộc tính

Màu sắc

slug: mau-sac
Order by: Custom ordering

Các giá trị:

Cam

Đỏ

Hồng

Pastel

Tím

Trắng

Vàng

Xanh


   - Giá: Simple 200,000-2,000,000 VND; Variable mỗi size chênh 100,000-200,000 VND
4. Mỗi sản phẩm có: tên tiếng Việt có ý nghĩa, mô tả ngắn, giá, SKU tự sinh, 
   ảnh placeholder (dùng ảnh có sẵn trong WP hoặc để trống)
5. Set stock_status = 'instock' cho tất cả
6. Script phải tự kiểm tra xem danh mục/thuộc tính đã tồn tại chưa, nếu chưa thì tự tạo

Output: File PHP chạy qua WP-CLI, in ra danh sách sản phẩm đã tạo kèm ID.
```

**Output mong muốn:** File `wp-app/import-products.php` — chạy 1 lần là có đủ 20 sản phẩm.

---

### PROMPT #2 ⭐ P3 — Cấu hình phí ship nâng cao (theo khoảng cách/quận)

> **Ước tính:** 1 prompt
> **Thời điểm:** Sau khi có sản phẩm mẫu

**Prompt chi tiết:**

```
Tôi cần cấu hình shipping cho cửa hàng hoa WordPress WooCommerce.

Yêu cầu:
1. Tạo code snippet PHP (dán vào functions.php của theme hoặc dùng plugin Code Snippets) 
   để thêm shipping method "Giao hàng nội thành" và "Giao hàng ngoại thành" 
   với logic tính phí như sau:
   - Nội thành (quận 1,3,5,10, Phú Nhuận, Bình Thạnh): Đồng giá 30,000 VND
   - Ngoại thành (các quận còn lại): 50,000 VND
   - Miễn phí ship cho đơn hàng trên 1,000,000 VND
2. Hiển thị radio button cho khách chọn khu vực giao hàng tại checkout
3. Validate: nếu khách chưa chọn khu vực thì hiện thông báo lỗi khi submit
4. Lưu khu vực đã chọn vào order meta để admin xem trong đơn hàng

Output: Code snippet PHP đầy đủ, có comment giải thích.
```

**Output mong muốn:** Snippet PHP copy-paste vào `functions.php`.

---

### PROMPT #3 ⭐ P5 — Tùy chỉnh trang My Account

> **Ước tính:** 2-3 prompt (chia nhỏ)
> **Thời điểm:** Sau khi cấu hình thanh toán & vận chuyển

#### Prompt 3a — Thêm Custom Order Statuses (1 prompt)

```
Tôi cần thêm 2 trạng thái đơn hàng tùy chỉnh cho cửa hàng hoa WooCommerce:

1. "Đang cắm hoa" (slug: wc-preparing-flowers) — trạng thái sau khi đơn hàng được xác nhận, 
   trước khi giao
2. "Đang giao hàng" (slug: wc-out-for-delivery) — trạng thái đang trên đường giao
   (giữ lại "Đã giao" mặc định là completed)

Yêu cầu:
- Code snippet PHP cho functions.php
- Các trạng thái mới phải xuất hiện trong dropdown chọn trạng thái ở Admin Order page
- Phải xuất hiện trong bộ lọc đơn hàng ở Admin
- Tích hợp với email notification: khi chuyển sang "Đang cắm hoa" → gửi email cho khách
  (dùng template email có sẵn của WooCommerce)
- Trên trang My Account của khách, hiển thị đúng tên tiếng Việt thay vì slug

Output: Code snippet đầy đủ kèm hướng dẫn cài đặt.
```

#### Prompt 3b — Thêm Custom Fields cho Address (1 prompt)

```
Tôi cần thêm trường "Loại địa chỉ" cho phần Address trong WooCommerce My Account 
và Checkout.

Yêu cầu:
1. Thêm select box "Loại địa chỉ" với 3 lựa chọn: "Nhà riêng", "Văn phòng/Cơ quan", "Khác"
2. Hiển thị trong cả: Checkout page (billing & shipping) + My Account → Addresses
3. Lưu vào user meta và order meta
4. Hiển thị giá trị đã chọn trong Admin Order Detail
5. Tương thích với WooCommerce blocks checkout (nếu đang dùng)

Output: Code snippet PHP + hook đầy đủ.
```

#### Prompt 3c — Customize My Account Dashboard (1 prompt)

```
Tôi muốn tùy chỉnh giao diện trang My Account của WooCommerce:

Yêu cầu:
1. Thêm 1 tab mới "Theo dõi đơn hàng" trong My Account navigation, 
   hiển thị danh sách đơn hàng với trạng thái trực quan (icon + màu sắc):
   - 🟡 Đang cắm hoa (vàng)
   - 🟠 Đang giao (cam)  
   - 🟢 Đã giao (xanh)
   - ⚪ Đã hủy (xám)
2. Trang Dashboard mặc định: thêm box hiển thị tổng quan:
   - Số đơn hàng đang xử lý
   - Đơn hàng gần đây nhất (kèm trạng thái)
3. Responsive, mobile-friendly

Output: Code tạo custom template override trong theme + CSS đi kèm.
```

---

### PROMPT #4 ⭐ P6 — Tích hợp cổng thanh toán MoMo

> **Ước tính:** 2 prompt
> **Thời điểm:** Sau khi các tính năng cốt lõi hoạt động ổn định

#### Prompt 4a — Tạo plugin MoMo Gateway (1 prompt)

```
Tôi cần tạo một plugin WordPress để tích hợp cổng thanh toán MoMo vào WooCommerce.

Yêu cầu:
1. Plugin name: "MoMo Payment Gateway for WooCommerce"
2. Class chính: WC_Gateway_MoMo extends WC_Payment_Gateway
3. Tích hợp MoMo ATLM (All-in-One) — thanh toán qua QR code hoặc redirect
4. Admin config fields:
   - partner_code, access_key, secret_key
   - test_mode (checkbox)
   - endpoint URLs: test (https://test-payment.momo.vn) và live
5. Flow thanh toán:
   - process_payment() → gọi MoMo API tạo payment request → redirect khách sang MoMo
   - return_url: xử lý kết quả, validate signature (HMAC SHA256)
   - ipn_url: webhook nhận callback từ MoMo → cập nhật trạng thái đơn hàng
6. Xử lý lỗi: timeout, khách hủy thanh toán, sai chữ ký
7. Text domain: momo-woocommerce
8. Tuân thủ chuẩn WordPress coding standards

Output: Cấu trúc plugin hoàn chỉnh gồm:
- momo-woocommerce/momo-woocommerce.php (main file)
- momo-woocommerce/includes/class-wc-gateway-momo.php
- momo-woocommerce/includes/class-momo-api.php
- momo-woocommerce/includes/class-momo-ipn.php
```

#### Prompt 4b — Tạo plugin QR Code chuyển khoản ngân hàng (1 prompt)

```
Tôi cần 1 plugin hiển thị QR Code chuyển khoản ngân hàng trên trang Thank You 
và trong email xác nhận đơn hàng.

Yêu cầu:
1. Tạo QR code từ thông tin: Số TK + Tên ngân hàng + Số tiền + Nội dung CK (mã đơn hàng)
2. Sử dụng thư viện PHP QR code (endroid/qr-code hoặc tương tự) 
   hoặc Google Charts API để sinh QR
3. Admin settings:
   - Tên ngân hàng (text)
   - Số tài khoản (text)
   - Tên chủ tài khoản (text)
   - Nội dung chuyển khoản mẫu (text, hỗ trợ placeholder {order_id})
4. Hiển thị QR trên:
   - Trang Thank You (sau khi đặt hàng với phương thức BACS)
   - Trong email "Processing Order" / "Order On Hold"
5. QR code phải chứa đủ thông tin theo chuẩn VietQR / NAPAS nếu có thể

Output: Plugin hoàn chỉnh hoặc code snippet ngắn gọn.
```

---

### PROMPT #5 ⭐ P6 — Tích hợp VNPay

> **Ước tính:** 1-2 prompt
> **Thời điểm:** Sau MoMo hoặc song song

**Prompt chi tiết:**

```
Tôi cần tạo plugin tích hợp VNPay vào WooCommerce.

Yêu cầu:
1. Plugin name: "VNPay Gateway for WooCommerce"
2. Class WC_Gateway_VNPay extends WC_Payment_Gateway
3. Admin fields:
   - vnp_TmnCode, vnp_HashSecret (HMAC SHA512)
   - test_mode → URL: https://sandbox.vnpayment.vn
4. Flow:
   - process_payment() → tạo redirect URL theo chuẩn VNPay v2.0
   - return_url → validate checksum → cập nhật đơn hàng
   - ipn_url → xử lý callback async
5. Hỗ trợ các ngân hàng nội địa + QR code
6. Text domain: vnpay-woocommerce

Output: Cấu trúc plugin hoàn chỉnh giống cấu trúc MoMo.
```

---

### PROMPT #6 (Dự phòng) — Import Attributes + Categories qua WP-CLI

> **Ước tính:** 1 prompt
> **Trường hợp:** Nếu không muốn làm thủ công P1

```
Tạo script WP-CLI PHP để import danh mục và thuộc tính cho cửa hàng hoa.

Danh mục cần tạo (phân cấp):
- Hoa theo dịp
  - Hoa sinh nhật
  - Hoa khai trương  
  - Hoa cưới hỏi
  - Hoa chúc mừng
  - Hoa chia buồn
- Hoa theo kiểu
  - Bó hoa
  - Lẵng hoa
  - Giỏ hoa
  - Hộp hoa
  - Hoa bình/bình hoa

Thuộc tính:
- Kích thước: Nhỏ | Vừa | Lớn
- Màu sắc chủ đạo: Đỏ | Hồng | Trắng | Vàng | Tím | Cam | Xanh
- Loại hoa chính: Hoa hồng | Hoa ly | Hoa hướng dương | Hoa lan | Hoa cẩm tú cầu | Hoa tulip

Yêu cầu: Script PHP chạy qua WP-CLI, tự động tạo tất cả, kèm slug chuẩn.
```

---

## PHẦN 4: TỔNG KẾT — SỐ LƯỢNG PROMPT & THỜI GIAN DỰ KIẾN

| # | Prompt | Số prompt | Độ phức tạp | Thời gian AI xử lý | Thao tác thêm |
|---|--------|-----------|-------------|-------------------|---------------|
| 1 | Import sản phẩm mẫu (WP-CLI) | 1 | ★★☆ | ~5-10 phút | Chạy script + kiểm tra |
| 2 | Cấu hình phí ship nâng cao | 1 | ★★☆ | ~5 phút | Copy code + test |
| 3a | Custom Order Statuses | 1 | ★★☆ | ~5 phút | Copy code + test email |
| 3b | Custom Address Fields | 1 | ★★☆ | ~5 phút | Copy code + test |
| 3c | Custom My Account Dashboard | 1 | ★★★ | ~10 phút | Copy code + test CSS |
| 4a | Plugin MoMo Gateway | 1 | ★★★ | ~10 phút | Cài plugin + test sandbox |
| 4b | QR Code chuyển khoản | 1 | ★★☆ | ~5 phút | Copy code + test |
| 5 | Plugin VNPay Gateway | 1 | ★★★ | ~10 phút | Cài plugin + test sandbox |
| **Tổng** | | **8 prompts** | | **~55-60 phút** | |

### Công việc làm thủ công trên WordPress (không cần prompt AI)

| # | Công việc | Thời gian dự kiến |
|---|----------|-------------------|
| 1 | Tạo danh mục sản phẩm | 10-15 phút |
| 2 | Tạo thuộc tính sản phẩm | 10 phút |
| 3 | Cấu hình COD | 2 phút |
| 4 | Cấu hình BACS (chuyển khoản) | 3 phút |
| 5 | Tạo Shipping Zones cơ bản | 15 phút |
| 6 | Cài đặt PayPal plugin (nếu cần) | 10 phút |
| 7 | Chụp ảnh giao diện + viết báo cáo | 30 phút |
| **Tổng thủ công** | | **~1.5 giờ** |

### Lộ trình tổng thể

```
Ngày 1: P1 (Thủ công: Categories + Attributes) → Prompt #1 (Import sản phẩm)
Ngày 2: P3 (Thủ công: Shipping Zones) → Prompt #2 (Phí ship nâng cao)
Ngày 3: P4 (Thủ công: COD + BACS)
Ngày 4: Prompt #3a, #3b, #3c (Tùy chỉnh My Account)
Ngày 5: Prompt #4a, #4b (MoMo + QR Code)
Ngày 6: Prompt #5 (VNPay) + Test tổng thể
Ngày 7: P7 (Chụp ảnh + Báo cáo)
```

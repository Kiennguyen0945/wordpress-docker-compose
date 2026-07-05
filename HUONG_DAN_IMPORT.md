# HƯỚNG DẪN IMPORT SẢN PHẨM VÀO CỬA HÀNG HOA

## 📋 Yêu cầu hệ thống

- **Docker** đã được cài đặt
- **WordPress + WooCommerce** đã chạy
- Đã clone/pull source code từ GitHub

## 🚀 Cách import sản phẩm

### 1. Mở terminal

Mở terminal (Command Prompt, PowerShell, hoặc Git Bash).

### 2. Di chuyển vào thư mục project

```bash
cd /đường/dẫn/tới/thư/mục/ecommerce-wordpress
```

### 3. Chạy lệnh import

```bash
docker compose run --rm wpcli wp eval-file /var/www/html/import-products.php
```

Chờ khoảng 30 giây để script chạy xong. Kết quả sẽ hiện như sau:

```
═══════════════════════════════════════════════════════
  🏪 IMPORT SẢN PHẨM HOA MẪU — WooCommerce
═══════════════════════════════════════════════════════
...
✅ TẬP DỮ LIỆU IMPORT HOÀN TẤT!
Tổng số sản phẩm: 20
  - Đơn giản:    10
  - Biến thể:   10
Tổng số biến thể: 30
```

### 4. Kiểm tra kết quả

Mở trình duyệt vào trang web:
- **Trang shop:** http://127.0.0.1/shop
- **Trang quản trị:** http://127.0.0.1/wp-admin → Products → All Products

## 📦 Sản phẩm được import

### Sản phẩm đơn giản (10 sản phẩm)

| # | Tên sản phẩm | Giá | Danh mục |
|---|-------------|-----|----------|
| 1 | Bó hoa hồng tình yêu | 500.000₫ | Hoa bó, Hoa sinh nhật |
| 2 | Lẵng hoa khai trương độc đỉnh | 1.500.000₫ | Lẵng hoa, Hoa khai trương |
| 3 | Hộp hoa sinh nhật Pastel | 350.000₫ | Hộp hoa, Hoa sinh nhật |
| 4 | Bó hoa tươi hồn nhiên | 350.000₫ | Hoa bó, Hoa chúc mừng |
| 5 | Giỏ hoa chúc mừng đa sắc | 800.000₫ | Giỏ hoa, Hoa chúc mừng |
| 6 | Bình hoa văn phòng cao cấp | 650.000₫ | Hoa bình, Hoa chúc mừng |
| 7 | Lẵng hoa chia buồn vĩnh biệt | 1.200.000₫ | Lẵng hoa, Hoa chia buồn |
| 8 | Sơn hoa hồng thường xương 60 bông | 2.000.000₫ | Hoa bó, Hoa sinh nhật |
| 9 | Bó hoa tươi mini xinh xắn | 250.000₫ | Hoa bó, Hoa chúc mừng |
| 10 | Hộp hoa sữa tắm – Rose Lux | 950.000₫ | Hộp hoa, Hoa cưới hỏi |

### Sản phẩm biến thể (10 sản phẩm × 3 kích thước)

| # | Tên sản phẩm | Giá Nhỏ | Giá Vừa | Giá Lớn |
|---|-------------|---------|---------|---------|
| 1 | Bó hoa hồng Pastel | 350.000₫ | 500.000₫ | 650.000₫ |
| 2 | Lẵng hoa văn phòng hiện đại | 500.000₫ | 650.000₫ | 800.000₫ |
| 3 | Giỏ quà hoa tươi đa năng | 650.000₫ | 800.000₫ | 950.000₫ |
| 4 | Hộp hoa bất ngờ – Surprise Box | 800.000₫ | 950.000₫ | 1.100.000₫ |
| 5 | Bình hoa cưới mini để bàn | 950.000₫ | 1.100.000₫ | 1.250.000₫ |
| 6 | Bó hoa hướng dương rực rỡ | 1.100.000₫ | 1.250.000₫ | 1.400.000₫ |
| 7 | Lẵng hoa cầu hôn sang trọng | 1.300.000₫ | 1.450.000₫ | 1.600.000₫ |
| 8 | Hoa bó baby mini | 200.000₫ | 350.000₫ | 500.000₫ |
| 9 | Hộp quà tri ân giám đốc | 1.500.000₫ | 1.650.000₫ | 1.800.000₫ |
| 10 | Bình hoa phong thủy mang lộc | 1.700.000₫ | 1.850.000₫ | 2.000.000₫ |

## 🔄 Import lại (nếu cần)

Nếu muốn chạy lại, script sẽ tự động **bỏ qua** danh mục & thuộc tính đã có và chỉ tạo sản phẩm mới.

> ⚠️ **Lưu ý:** Mỗi lần chạy sẽ tạo thêm sản phẩm mới (không ghi đè). Nếu muốn import lại từ đầu, vào WordPress Admin → Products → chọn tất cả → Delete.

## 🛠 Xử lý lỗi thường gặp

| Lỗi | Nguyên nhân | Cách xử lý |
|-----|------------|------------|
| `WooCommerce chưa được kích hoạt!` | Chưa cài WooCommerce | Vào WP Admin → Plugins → Cài WooCommerce |
| `Kết nối database thất bại` | Docker chưa chạy | Chạy `docker compose up -d` trước |
| `wpcli: command not found` | Sai thư mục | Đảm bảo đang ở thư mục `ecommerce-wordpress` |

## 📁 Cấu trúc file import

```
wp-app/
├── import-products.php      ← File import chính (chạy cái này)
├── fix-all.php              ← Fix tổng hợp: giá + attribute terms + price cache
├── fix-prices.php           ← Fix giá nếu bị sai (x1000)
├── fix-variations.php       ← Fix variation attribute
├── rebuild-prices.php       ← Xây lại price cache
```

> **Cách dùng:** Chỉ cần chạy `import-products.php` là đủ. Nếu gặp lỗi, chạy `fix-all.php` trước, sau đó chạy lại `import-products.php`.

## 📞 Hỗ trợ

Nếu gặp vấn đề, hãy chụp ảnh terminal và gửi cho mình nhé!

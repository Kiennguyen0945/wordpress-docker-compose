# 🌸 HƯỚNG DẪN IMPORT SẢN PHẨM VÀO CỬA HÀNG HOA

> Dành cho bạn — hướng dẫn chi tiết từ A→Z để chạy được cửa hàng hoa trên máy tính.

---

## 📋 YÊU CẦU TRƯỚC KHI BẮT ĐẦU

Bạn cần cài sẵn những thứ này trên máy:

| Phần mềm | Tải ở đâu | Ghi chú |
|----------|----------|---------|
| **Docker Desktop** | https://www.docker.com/products/docker-desktop/ | Cài xong nhớ khởi động Docker lên |
| **Git** | https://git-scm.com/downloads | Để tải code về máy |
| **Trình duyệt** | Chrome / Edge / Cốc Cốc | Để xem website |

---

## 🚀 HƯỚNG DẪN TỪNG BƯỚC (dành cho người mới)

### Bước 0: Mở Terminal

- **Windows:** Mở **Git Bash** (hoặc Command Prompt / PowerShell)
- **macOS / Linux:** Mở **Terminal**

### Bước 1: Tải source code về máy

Gõ lệnh sau để tải project về (chọn 1 trong 2 cách):

**Cách 1 — Clone bằng Git:**
```bash
git clone <đường-dẫn-github-của-project>
cd ecommerce-wordpress
```

**Cách 2 — Giải nén file ZIP:**
Nếu bạn có file `.zip`, giải nén ra, rồi mở terminal và di chuyển vào thư mục vừa giải nén:
```bash
cd /đường/dẫn/tới/thư/mục/ecommerce-wordpress
```

> 💡 **Mẹo:** Trên Windows, bạn có thể gõ `cd ` (có khoảng trắng) rồi kéo thả thư mục vào cửa sổ terminal — đường dẫn sẽ tự điền.

### Bước 2: Tạo file cấu hình

Trong thư mục project, tạo file `.env` từ file mẫu:

- **Linux / macOS / Git Bash:**
  ```bash
  cp env.example .env
  ```
- **Windows (CMD):**
  ```cmd
  copy env.example .env
  ```

Sau đó mở file `.env` lên (bằng Notepad hoặc bất kỳ trình soạn thảo nào) và **kiểm tra các thông số**:

```ini
IP=127.0.0.1              # Giữ nguyên
PORT=80                   # Nếu cổng 80 bị trùng, đổi thành 8080
DB_ROOT_PASSWORD=password # Có thể đổi nếu muốn
DB_NAME=wordpress         # Giữ nguyên
```

> ⚠️ **Nếu đổi PORT=8080**, sau này vào web sẽ là `http://127.0.0.1:8080` thay vì `http://127.0.0.1`.

### Bước 3: Khởi động Docker

**Lần đầu tiên**, bạn cần khởi động các container:

```bash
docker compose up -d
```

Lệnh này sẽ tải về các hình ảnh (images) cần thiết và chạy WordPress + MySQL.  
⏳ **Lần đầu chạy sẽ hơi lâu** (5-15 phút tuỳ internet). Các lần sau sẽ nhanh hơn.

Để kiểm tra xem đã chạy xong chưa, gõ:
```bash
docker compose ps
```
Bạn sẽ thấy các container có trạng thái `Up` (đang chạy).

### Bước 4: Cài WordPress (lần đầu)

Mở trình duyệt vào địa chỉ:
- **http://127.0.0.1** (nếu PORT=80)
- **http://127.0.0.1:8080** (nếu PORT=8080)

Bạn sẽ thấy màn hình cài đặt WordPress. Làm theo các bước:

1. Chọn ngôn ngữ → **Tiếng Việt** (hoặc English)
2. Điền thông tin:
   - **Tên website:** `FlowerShop` (hoặc tên bạn muốn)
   - **Tên đăng nhập:** `admin`
   - **Mật khẩu:** Chọn mật khẩu mạnh (ví dụ: `Admin@123`)
   - **Email:** Email của bạn
3. Nhấn **Cài đặt WordPress**

Sau khi cài xong, bạn sẽ thấy trang WordPress.

### Bước 5: Cài plugin WooCommerce

Vào **Plugins → Add New**, gõ "WooCommerce" vào ô tìm kiếm, nhấn **Install Now** → **Activate**.

> ⏳ WooCommerce cỡ ~30MB, cần chờ tải về. Nếu bị chậm là do internet, hãy kiên nhẫn.

Sau khi kích hoạt, WooCommerce sẽ hiện wizard hướng dẫn — bạn có thể tắt wizard đi cũng được (nhấn "Skip" hoặc ra ngoài).

Kiểm tra: Vào **Plugins → Installed Plugins**, thấy **WooCommerce** hiện màu xanh "Active" là OK.

### Bước 6: Import sản phẩm mẫu

Mở lại terminal, gõ lệnh:

```bash
docker compose run --rm wpcli wp eval-file /var/www/html/import-products.php
```

Chờ khoảng 30 giây để script chạy. Kết quả thành công sẽ hiện:

```
═══════════════════════════════════════════════════════
  🏪 IMPORT SẢN PHẨM HOA MẪU — WooCommerce
═══════════════════════════════════════════════════════

[OK] WooCommerce đã kích hoạt.

─── Bước 1. Tạo Danh mục (Categories) ───
  [TAO] Đã tạo danh mục: Hoa theo dịp ...
  ...

─── Bước 2. Tạo Thuộc tính (Attributes) ───
  ...

─── Bước 3. Tạo Sản phẩm đơn giản ───
  ...

─── Bước 4. Tạo Sản phẩm biến thể ───
  ...

═══════════════════════════════════════════════════════
  ✅ TẬP DỮ LIỆU IMPORT HOÀN TẤT!
═══════════════════════════════════════════════════════

Tổng số sản phẩm: 20
  - Đơn giản:    10
  - Biến thể:   10
Tổng số biến thể: 30
```

> ❌ **Lỗi `WooCommerce chưa được kích hoạt`** → Quay lại Bước 5, cài WooCommerce và kích hoạt.

> ❌ **Lỗi `Kết nối database thất bại`** → Gõ `docker compose up -d` để khởi động lại, đợi 30s rồi chạy lại.

### Bước 7: Kiểm tra kết quả

Mở trình duyệt vào:

- **🛒 Trang shop:** http://127.0.0.1/shop  
  (hoặc http://127.0.0.1:8080/shop nếu đổi port)
  
- **🔧 Trang quản trị:** http://127.0.0.1/wp-admin  
  Đăng nhập bằng tài khoản đã tạo ở Bước 4 → **Products → All Products**

Bạn sẽ thấy 20 sản phẩm hoa đã được import sẵn.

---

## 📱 NẾU BẠN MUỐN XEM TRÊN ĐIỆN THOẠI CÙNG MẠNG

Nếu bạn và bạn tôi cùng mạng WiFi, tôi có thể xem shop của bạn bằng cách:

1. Tìm IP máy bạn:
   - **Windows:** Mở CMD, gõ `ipconfig` → tìm dòng `IPv4 Address` (ví dụ `192.168.1.10`)
   - **macOS / Linux:** Mở Terminal, gõ `ifconfig | grep inet`
2. Tôi vào trình duyệt gõ: `http://192.168.1.10` (thay bằng IP máy bạn)

> ⚠️ Nhớ tắt firewall nếu không vào được.

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
├── fix-stock.php            ← Bật quản lý tồn kho (nếu sản phẩm thiếu stock)
├── rebuild-prices.php       ← Xây lại price cache
```

> **Cách dùng:** Chỉ cần chạy `import-products.php` là đủ. Nếu gặp lỗi, chạy `fix-all.php` trước, sau đó chạy lại `import-products.php`.

## 📦 Quản lý tồn kho (Stock)

Script import hiện đã bật quản lý tồn kho cho tất cả sản phẩm:
- **Sản phẩm đơn giản:** Tồn kho mặc định = **100**
- **Biến thể (variation):** Tồn kho mặc định = **50** mỗi size
- Khi có đơn hàng, số lượng sẽ tự động trừ đi

> ⚠️ Nếu đã import trước đó và sản phẩm **không có số lượng tồn kho**, chạy lệnh sau để fix:
> ```bash
> docker compose run --rm wpcli wp eval-file /var/www/html/fix-stock.php
> ```

## 📞 Hỗ trợ

Nếu gặp vấn đề, hãy chụp ảnh terminal và gửi cho mình nhé!

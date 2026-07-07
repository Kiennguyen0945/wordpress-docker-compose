# 🌸 Hướng Dẫn Collaborative cho Theme Thung Lũng Hoa

> Dành cho 2 người cùng code WordPress theme mà không sợ conflict!

---

## 📋 Mục lục

1. [Cấu trúc theme sau khi tái cấu trúc](#-cấu-trúc-theme-mới)
2. [Ai làm gì? — Phân công chi tiết](#-phân-công-công-việc)
3. [Git workflow từng bước](#-git-workflow)
4. [Nguyên tắc vàng](#-nguyên-tắc-vàng-quan-trọng)
5. [Hướng dẫn code từng module](#-hướng-dẫn-code-từng-module)

---

## 📁 Cấu trúc theme mới

Sau khi tái cấu trúc, theme `thunglung-hoa` được tổ chức như sau:

```
thunglung-hoa/
├── style.css                     # 🚫 KHÔNG SỬA — chỉ header comment
├── functions.php                 # 🚫 KHÔNG SỬA — chỉ require module
│
├── inc/                          # ⭐ PHP MODULES
│   ├── setup.php                 # (A) Theme setup: menus, supports
│   ├── enqueue.php               # (B) Load CSS + JS
│   ├── woocommerce.php           # (B) WooCommerce hooks
│   ├── helpers.php               # (A) SVG icons, helpers
│   ├── ajax.php                  # (B) Lọc sản phẩm AJAX
│   ├── auth.php                  # (A) Đăng nhập/Đăng ký
│   ├── user-profile.php          # (A) Hồ sơ cá nhân
│   └── shipping.php              # (A) Miễn phí ship
│
├── template-parts/               # ⭐ HTML TEMPLATES
│   ├── header/
│   │   ├── nav.php               # (B) Menu chính
│   │   └── actions.php           # (B) Search + cart icons
│   ├── footer/
│   │   ├── brand.php             # (A) Logo + social
│   │   ├── links.php             # (B) Footer links
│   │   └── newsletter.php        # (A) Newsletter form
│   ├── product/
│   │   └── filters.php           # (B) Bộ lọc sản phẩm
│   └── user/
│       ├── login.php             # (A) Form đăng nhập
│       ├── register.php          # (A) Form đăng ký
│       └── profile.php           # (A) Trang hồ sơ
│
├── assets/
│   ├── css/                      # ⭐ CSS MODULES
│   │   ├── base.css              # (Chung)
│   │   ├── header.css            # (B)
│   │   ├── footer.css            # (B)
│   │   ├── home.css              # (Chung)
│   │   ├── shop.css              # (B) Sửa layout, filter
│   │   ├── product.css           # (B)
│   │   ├── cart.css              # (B)
│   │   ├── checkout.css          # (B)
│   │   ├── user.css              # (A) Login, register, profile
│   │   ├── components.css        # (Chung)
│   │   ├── woocommerce.css       # (B)
│   │   └── responsive.css        # (Chung)
│   └── js/
│       ├── main.js               # (Chung)
│       ├── filters.js            # (B) Lọc sản phẩm AJAX
│       └── checkout.js           # (B)
│
├── woocommerce/                  # 🚫 KHÔNG SỬA
├── header.php                    # 🚫 KHÔNG SỬA
├── footer.php                    # 🚫 KHÔNG SỬA
├── front-page.php                # 🚫 KHÔNG SỬA
├── page.php, 404.php...          # 🚫 KHÔNG SỬA
└── index.php                     # 🚫 KHÔNG SỬA
```

**Chú thích:**
- **(A)** = Người A sửa
- **(B)** = Người B sửa
- **(Chung)** = Cả 2 có thể sửa nhưng báo nhau trước
- **🚫 KHÔNG SỬA** = Tuyệt đối không động vào

---

## 🎯 Phân công công việc

### 7 Task cần làm:

| # | Task | Người A | Người B | File cần sửa |
|---|------|---------|---------|-------------|
| 1 | **Trang hồ sơ cá nhân** | ✅ | ❌ | `inc/user-profile.php`, `template-parts/user/profile.php`, `assets/css/user.css` |
| 2 | **Lọc sản phẩm** (Mới nhất, giá thấp→cao, cao→thấp) | ❌ | ✅ | `inc/ajax.php`, `template-parts/product/filters.php`, `assets/js/filters.js`, `assets/css/shop.css` |
| 3 | **Đăng nhập / Đăng ký / Đăng xuất** | ✅ | ❌ | `inc/auth.php`, `template-parts/user/login.php`, `template-parts/user/register.php` |
| 4 | **Cài đặt (Settings)** | ✅ | ❌ | Tạo page + shortcode trong WP Admin |
| 5 | **Chỉnh layout full element** | ❌ | ✅ | `assets/css/shop.css`, `assets/css/product.css`, `assets/css/cart.css` |
| 6 | **Bỏ phí ship / Miễn phí** | ✅ | ❌ | `inc/shipping.php` (code có sẵn, chỉ cần bỏ comment) |
| 7 | **Bỏ "Về chúng tôi" → thêm thanh toán** | ❌ | ✅ | `template-parts/header/nav.php`, `template-parts/footer/links.php` |

### Quy tắc:

- **Người A** chỉ sửa file có dán nhãn `(A)` hoặc `✅ Người A`
- **Người B** chỉ sửa file có dán nhãn `(B)` hoặc `✅ Người B`
- **Không ai sửa file của người kia** — đây là nguyên tắc số 1!

---

## 🔀 Git Workflow

### Lần đầu tiên — Khởi tạo (1 người làm)

```bash
cd /home/kien/ecommerce-wordpress

# Tạo .gitignore
echo "wp-data/
.env" > .gitignore

# Commit lần đầu
git init
git add -A
git commit -m "Initial: theme Thung Lung Hoa modular structure"
```

### Mỗi ngày — Quy trình chuẩn

#### 👤 Người A — Làm task hồ sơ, auth, ship

```bash
# 1. Luôn bắt đầu từ main mới nhất
git checkout main
git pull origin main

# 2. Tạo branch riêng
git checkout -b feature/user-auth-profile

# 3. Code trên branch của mình
# Chỉ sửa: inc/auth.php, inc/user-profile.php, inc/shipping.php
#          template-parts/user/*
#          assets/css/user.css

# 4. Commit thường xuyên (mỗi khi xong 1 chức năng nhỏ)
git add inc/auth.php template-parts/user/login.php
git commit -m "Add login form template"

git add inc/user-profile.php template-parts/user/profile.php
git commit -m "Add user profile page"

# 5. Đẩy lên remote
git push origin feature/user-auth-profile

# 6. Khi hoàn thành task → merge vào main
git checkout main
git pull origin main
git merge feature/user-auth-profile
git push origin main
```

#### 👤 Người B — Làm task lọc, layout, thanh toán

```bash
# 1. Luôn bắt đầu từ main mới nhất
git checkout main
git pull origin main

# 2. Tạo branch riêng
git checkout -b feature/filters-layout

# 3. Code trên branch của mình
# Chỉ sửa: inc/ajax.php
#          template-parts/product/filters.php
#          template-parts/header/nav.php
#          template-parts/footer/links.php
#          assets/js/filters.js
#          assets/css/shop.css

# 4. Commit thường xuyên
git add inc/ajax.php template-parts/product/filters.php
git commit -m "Add AJAX product filters"

git add assets/js/filters.js assets/css/shop.css
git commit -m "Add filter JS + shop CSS"

# 5. Đẩy lên remote
git push origin feature/filters-layout

# 6. Merge khi xong
git checkout main
git pull origin main
git merge feature/filters-layout
git push origin main
```

### Khi có conflict — Xử lý thế nào?

**Nếu merge báo conflict**, làm theo các bước:

```bash
# Kiểm tra file bị conflict
git status
# => "both modified: inc/enqueue.php"

# Mở file đó trong VS Code, tìm:
# <<<<<<< HEAD
# code của bạn
# =======
# code của người kia
# >>>>>>> branch-name

# Giữ lại code ĐÚNG, xóa dòng <<<<, ====, >>>>
# Sau đó:
git add inc/enqueue.php
git commit -m "Resolve conflict in enqueue.php"
```

> **Nguyên tắc**: Nếu conflict ở file KHÔNG PHẢI của mình → gọi người kia ra hỏi trước khi sửa!

---

## ⚡ Nguyên tắc vàng (quan trọng)

1. **🚫 KHÔNG sửa `functions.php`** — file này chỉ `require` các module, đã hoàn thiện.
2. **🚫 KHÔNG sửa `style.css`** — file này chỉ có header comment.
3. **🚫 KHÔNG sửa `header.php`, `footer.php`, `front-page.php`** — đã dùng `get_template_part()`.
4. **🚫 KHÔNG sửa file trong `woocommerce/`** — giữ nguyên.
5. **👤 Mỗi người chỉ sửa file đã phân công** — không đụng file của người kia.
6. **💬 Luôn commit trước khi merge** — commit nhỏ, thường xuyên.
7. **🌿 Luôn tạo branch riêng** — không code trực tiếp trên `main`.
8. **📢 Báo nhau trước khi sửa file "(Chung)"** — như `base.css`, `home.css`, `responsive.css`.

---

## 📝 Hướng dẫn code từng module

### Module PHP (`inc/`)

Mỗi file trong `inc/` tự động được nạp bởi `functions.php`. Chỉ cần tạo function + hook là chạy.

Ví dụ — thêm shortcode trong `inc/auth.php`:

```php
add_shortcode('tlh_login_form', 'tlh_render_login_form');
function tlh_render_login_form() {
    ob_start();
    get_template_part('template-parts/user/login');
    return ob_get_clean();
}
```

### Template Parts (`template-parts/`)

Dùng `get_template_part()` để gọi. Đã cập nhật `header.php` và `footer.php` để dùng các template part.

### CSS (`assets/css/`)

Mỗi module CSS chỉ style cho phần của nó:
- `header.css` → `.site-header`, `.header-row`, `.main-nav`, `.header-actions`
- `footer.css` → `.site-footer`, `.footer-grid`, `.footer-col`
- `shop.css` → `.shop-layout`, `.filter-block`, `ul.products`

> ⚠️ **Responsive**: KHÔNG viết `@media` trong các file CSS module. Viết hết vào `responsive.css`.

### JavaScript (`assets/js/`)

Đã có sẵn 3 file:
- `main.js` — global interactions
- `filters.js` — AJAX filter (dành cho Người B)
- `checkout.js` — thanh toán

Dùng `tlh_ajax` object để gọi AJAX:

```javascript
fetch(tlh_ajax.ajax_url, {
  method: 'POST',
  body: formData,
})
```

---

## 🚀 Checklist hoàn thành dự án

Khi nào task nào hoàn thành, đánh dấu `[x]`:

- [ ] **Task 1**: Trang hồ sơ cá nhân → Người A
- [ ] **Task 2**: Lọc sản phẩm (mới nhất, giá) → Người B
- [ ] **Task 3**: Đăng nhập / Đăng ký / Đăng xuất → Người A
- [ ] **Task 4**: Cài đặt → Người A
- [ ] **Task 5**: Chỉnh layout full element → Người B
- [ ] **Task 6**: Bỏ phí ship / Miễn phí → Người A
- [ ] **Task 7**: Bỏ "Về chúng tôi", thêm thanh toán → Người B

---

> **Liên hệ**: Nếu có thắc mắc, hỏi nhau qua group chat hoặc gọi điện. Đừng tự ý sửa file của người kia!
>
> *Chúc 2 bạn code vui vẻ, không conflict!* 🌸

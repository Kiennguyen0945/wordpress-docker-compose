<?php
/**
 * WP-CLI Import Script — Import 20 sản phẩm hoa mẫu cho WooCommerce
 *
 * Cách chạy:
 *   docker compose run --rm wpcli wp eval-file /var/www/html/import-products.php
 *
 * Script này tự động:
 *   - Kiểm tra và tạo danh mục (Categories) nếu chưa tồn tại
 *   - Kiểm tra và tạo thuộc tính (Attributes) nếu chưa tồn tại
 *   - Tạo 10 sản phẩm đơn giản (Simple Product)
 *   - Tạo 10 sản phẩm có biến thể (Variable Product) với 3 kích thước Nhỏ/Vừa/Lớn
 *   - Gán hình ảnh placeholder (dùng ảnh mặc định nếu có, nếu không thì bỏ qua)
 *   - Set stock_status = 'instock' cho tất cả
 */

defined('ABSPATH') || exit;

// ─── CẤU HÌNH GIÁ ──────────────────────────────────────────
// Các mức giá cho sản phẩm đơn giản (VND)
$simple_prices = [
    250000, 350000, 500000, 650000, 800000,
    950000, 1200000, 1500000, 1800000, 2000000,
];

// Giá biến thể: giá cơ bản chênh theo kích thước
$size_price_modifier = [
    'Nhỏ'  => 0,
    'Vừa'  => 150000,
    'Lớn'  => 300000,
];

$base_variable_prices = [
    350000, 500000, 650000, 800000, 950000,
    1100000, 1300000, 1500000, 1700000, 2000000,
];

// ─── ĐỊNH NGHĨA DANH MỤC ─────────────────────────────────────
$categories = [
    // Parent → Child structure
    ['name' => 'Hoa theo dịp',   'slug' => 'hoa-theo-dip',     'parent' => ''],
    ['name' => 'Hoa sinh nhật',  'slug' => 'hoa-sinh-nhat',    'parent' => 'hoa-theo-dip'],
    ['name' => 'Hoa khai trương', 'slug' => 'hoa-khai-truong',  'parent' => 'hoa-theo-dip'],
    ['name' => 'Hoa cưới hỏi',   'slug' => 'hoa-cuoi-hoi',     'parent' => 'hoa-theo-dip'],
    ['name' => 'Hoa chúc mừng',  'slug' => 'hoa-chuc-mung',    'parent' => 'hoa-theo-dip'],
    ['name' => 'Hoa chia buồn',   'slug' => 'hoa-chia-buon',    'parent' => 'hoa-theo-dip'],

    ['name' => 'Hoa theo kiểu',   'slug' => 'hoa-theo-kieu',    'parent' => ''],
    ['name' => 'Hoa bó',          'slug' => 'hoa-bo',           'parent' => 'hoa-theo-kieu'],
    ['name' => 'Lẵng hoa',        'slug' => 'lang-hoa',         'parent' => 'hoa-theo-kieu'],
    ['name' => 'Giỏ hoa',          'slug' => 'gio-hoa',          'parent' => 'hoa-theo-kieu'],
    ['name' => 'Hộp hoa',         'slug' => 'hop-hoa',          'parent' => 'hoa-theo-kieu'],
    ['name' => 'Hoa bình',        'slug' => 'hoa-binh',         'parent' => 'hoa-theo-kieu'],
];

// ─── ĐỊNH NGHĨA THUỘC TÍNH ──────────────────────────────────
$attributes = [
    'kich-thuoc' => [
        'name'      => 'Kích thước',
        'slug'      => 'kich-thuoc',
        'orderby'   => 'menu_order',
        'values'    => ['Nhỏ', 'Vừa', 'Lớn'],
    ],
    'mau-sac' => [
        'name'      => 'Màu sắc',
        'slug'      => 'mau-sac',
        'orderby'   => 'menu_order',
        'values'    => ['Đỏ', 'Hồng', 'Trắng', 'Vàng', 'Tím', 'Cam', 'Xanh', 'Pastel'],
    ],
    'loai-hoa-chinh' => [
        'name'      => 'Loại hoa chính',
        'slug'      => 'loai-hoa-chinh',
        'orderby'   => 'menu_order',
        'values'    => ['Hoa hồng', 'Hoa ly', 'Hoa hướng dương', 'Hoa lan', 'Hoa cẩm tú cầu', 'Hoa tulip'],
    ],
];

// ─── SẢN PHẨM MẪU (Simple Products) ─────────────────────────
$simple_products = [
    // [name, description, price, category_slug, mau_sac, loai_hoa_chinh]
    [
        'name'        => 'Bó hoa hồng tình yêu',
        'desc'        => 'Bó hoa hồng đỏ tươi thắm, gồm 20 bông hoa hồng Đà Lạt cao cấp. Kèm gói quà và thiệp chúc mừng. Phù hợp cho ngày kỷ niệm, tỏ tình.',
        'price'       => 500000,
        'categories'  => ['hoa-bo', 'hoa-sinh-nhat'],
        'mau_sac'     => ['Đỏ'],
        'loai_hoa'    => 'Hoa hồng',
    ],
    [
        'name'        => 'Lẵng hoa khai trương độc đỉnh',
        'desc'        => 'Lẵng hoa khổng lồ với đa dạng loài hoa. Thiết kế sang trọng, phù hợp cho khai trương cửa hàng, văn phòng mới. In logo trên lẵng (liên hệ).',
        'price'       => 1500000,
        'categories'  => ['lang-hoa', 'hoa-khai-truong'],
        'mau_sac'     => ['Vàng', 'Đỏ'],
        'loai_hoa'    => 'Hoa hướng dương',
    ],
    [
        'name'        => 'Hộp hoa sinh nhật Pastel',
        'desc'        => 'Hộp quà hình tym đỗ đựng 9 bông hồng Pastel cao cấp. Hoa được bao quanh bởi mít bắc Giang, cẩm thựch và phụ kiện trang trí.',
        'price'       => 350000,
        'categories'  => ['hop-hoa', 'hoa-sinh-nhat'],
        'mau_sac'     => ['Pastel'],
        'loai_hoa'    => 'Hoa hồng',
    ],
    [
        'name'        => 'Bó hoa tưới hồn nhiên',
        'desc'        => 'Bó hoa phong cách thiên nhiên với hoa ly cam, hoa cẩm tú cầu trắng và lá trang trí. Thích hợp tặng bạn bè, đồng nghiệp.',
        'price'       => 350000,
        'categories'  => ['hoa-bo', 'hoa-chuc-mung'],
        'mau_sac'     => ['Cam', 'Trắng'],
        'loai_hoa'    => 'Hoa ly',
    ],
    [
        'name'        => 'Giỏ hoa chúc mừng đa sắc',
        'desc'        => 'Giỏ hoa tổng hợp nhiều loài hoa với thiết kế hiện đại. Phù hợp chúc mừng tốt nghiệp, tân gia, sinh nhật trên 50 tuổi.',
        'price'       => 800000,
        'categories'  => ['gio-hoa', 'hoa-chuc-mung'],
        'mau_sac'     => ['Hồng', 'Tím', 'Trắng'],
        'loai_hoa'    => 'Hoa lan',
    ],
    [
        'name'        => 'Bình hoa văn phòng cao cấp',
        'desc'        => 'Bình hoa cắm sẵn với hoa hồng tím và hoa cẩm tú cầu. Bình gốm cao 25cm. Đẻ bàn làm việc, bàn tiếp khách.
',
        'price'       => 650000,
        'categories'  => ['hoa-binh', 'hoa-chuc-mung'],
        'mau_sac'     => ['Tím'],
        'loai_hoa'    => 'Hoa cẩm tú cầu',
    ],
    [
        'name'        => 'Lẵng hoa chia buồn vĩnh biệt',
        'desc'        => 'Lẵng hoa chia buồn sang trọng màu trắng. Thiết kế tối giản nhưng tinh tế, phù hợp đám tang, lễ vĩnh biệt. Giá bao gồm thiệp chia buồn.',
        'price'       => 1200000,
        'categories'  => ['lang-hoa', 'hoa-chia-buon'],
        'mau_sac'     => ['Trắng'],
        'loai_hoa'    => 'Hoa lan',
    ],
    [
        'name'        => 'Sơn hoa hồng thường xương 60 bông',
        'desc'        => 'Xiệp 60 bông hoa hồng thướu xương nhập khẩu. Gân lá dầy, hoa to, nhiều cánh. Phù hợp nhân dịp sinh nhật lần thứ 60 hoặc kỷ niệm 60 năm.',
        'price'       => 2000000,
        'categories'  => ['hoa-bo', 'hoa-sinh-nhat'],
        'mau_sac'     => ['Đỏ'],
        'loai_hoa'    => 'Hoa hồng',
    ],
    [
        'name'        => 'Bó hoa tưới mini xinh xắn',
        'desc'        => 'Bó hoa nhỏ xinh gồm hoa tulip và hoa cẩm tú cầu. Phù hợp làm quà đi kèm, gấp gọn dễ mang theo.',
        'price'       => 250000,
        'categories'  => ['hoa-bo', 'hoa-chuc-mung'],
        'mau_sac'     => ['Vàng', 'Xanh'],
        'loai_hoa'    => 'Hoa tulip',
    ],
    [
        'name'        => 'Hộp hoa sữa tắm - Rose Lux',
        'desc'        => 'Combo hộp hoa 12 bông hồng trắng cao cấp kèm sữa tắm handmade tặng kèm. Hoa nhập khẩu từ Ecuador, bảo quản 7-10 ngày.',
        'price'       => 950000,
        'categories'  => ['hop-hoa', 'hoa-cuoi-hoi'],
        'mau_sac'     => ['Trắng'],
        'loai_hoa'    => 'Hoa hồng',
    ],
];

// ─── SẢN PHẨM BIẾN THỂ (Variable Products) ──────────────────
$variable_products = [
    [
        'name'        => 'Bó hoa hồng Pastel đa kích thước',
        'desc'        => 'Bó hoa hồng Pastel nhịp nhàng từ màu hồng tới màu kem. Nhiều kích thước chọn lọc theo nhu cầu.',
        'base_price'  => 350000,
        'categories'  => ['hoa-bo', 'hoa-sinh-nhat'],
        'mau_sac'     => ['Hồng', 'Pastel'],
        'loai_hoa'    => 'Hoa hồng',
    ],
    [
        'name'        => 'Lẵng hoa văn phòng hiện đại',
        'desc'        => 'Lẵng hoa thiết kế tối giản, phù hợp bàn tiếp khách, lobby khách sạn. Đa dạng kích cỡ.',
        'base_price'  => 500000,
        'categories'  => ['lang-hoa', 'hoa-chuc-mung'],
        'mau_sac'     => ['Trắng', 'Xanh'],
        'loai_hoa'    => 'Hoa lan',
    ],
    [
        'name'        => 'Giỏ quà hoa tươi đa năng',
        'desc'        => 'Giỏ hoa với sự kết hợp nhiều loài hoa phối màu hài hòa. Có thể đặt nhiều kích cỡ khác nhau.',
        'base_price'  => 650000,
        'categories'  => ['gio-hoa', 'hoa-sinh-nhat'],
        'mau_sac'     => ['Vàng', 'Cam'],
        'loai_hoa'    => 'Hoa hướng dương',
    ],
    [
        'name'        => 'Hộp hoa bất ngờ - Surprise Box',
        'desc'        => 'Hộp quà bất ngờ khi mở sẽ thấy bó hoa tươi xinh xắn. Thiết kế hộp đẹp, phù hợp tặng online.',
        'base_price'  => 800000,
        'categories'  => ['hop-hoa', 'hoa-sinh-nhat'],
        'mau_sac'     => ['Hồng', 'Tím'],
        'loai_hoa'    => 'Hoa tulip',
    ],
    [
        'name'        => 'Bình hoa cưới mini để bàn',
        'desc'        => 'Bình hoa cắm kiểu Hàn Quốc với hoa hồng và cẩm tú cầu. Phù hợp tiệc cưới hoặc để bàn phòng new.',
        'base_price'  => 950000,
        'categories'  => ['hoa-binh', 'hoa-cuoi-hoi'],
        'mau_sac'     => ['Trắng', 'Pastel'],
        'loai_hoa'    => 'Hoa cẩm tú cầu',
    ],
    [
        'name'        => 'Bó hoa hướng dương rực rỡ',
        'desc'        => 'Bó hoa hướng dương to lớn, luôn quay về phía mặt trời. Tượng trưng cho niềm vui và sự tự tin.',
        'base_price'  => 1100000,
        'categories'  => ['hoa-bo', 'hoa-chuc-mung'],
        'mau_sac'     => ['Vàng'],
        'loai_hoa'    => 'Hoa hướng dương',
    ],
    [
        'name'        => 'Lẵng hoa cầu hôn sang trọng',
        'desc'        => 'Lẵng hoa đỏ rực với hoa hồng nhập khẩu và hoa cẩm tú cầu. Dùng cho các buổi cầu hôn ngoại cảnh.',
        'base_price'  => 1300000,
        'categories'  => ['lang-hoa', 'hoa-cuoi-hoi'],
        'mau_sac'     => ['Đỏ'],
        'loai_hoa'    => 'Hoa hồng',
    ],
    [
        'name'        => 'Hoa bó baby mini',
        'desc'        => 'Bó hoa mini xinh xắn với baby breath (thạch thảo) kết hợp hoa hồng tím. Giá nhẹ túi cho các bạn học sinh.',
        'base_price'  => 200000,
        'categories'  => ['hoa-bo', 'hoa-sinh-nhat'],
        'mau_sac'     => ['Tím', 'Hồng'],
        'loai_hoa'    => 'Hoa ly',
    ],
    [
        'name'        => 'Hộp quà tri ân giám đốc',
        'desc'        => 'Hộp quà tri ân bằng gỗ cao cấp bên trong là hoa tươi và rượu vang. Dành tặng cấp trên, đối tác.',
        'base_price'  => 1500000,
        'categories'  => ['hop-hoa', 'hoa-khai-truong'],
        'mau_sac'     => ['Đỏ', 'Vàng'],
        'loai_hoa'    => 'Hoa lan',
    ],
    [
        'name'        => 'Bình hoa phong thủy mang lạc',
        'desc'        => 'Bình hoa phong thủy với hoa đồng tiền, hoa hồng và màu sắc may mắn. Mang lại tài lộc cho gia chủ.',
        'base_price'  => 1700000,
        'categories'  => ['hoa-binh', 'hoa-khai-truong'],
        'mau_sac'     => ['Đỏ', 'Vàng'],
        'loai_hoa'    => 'Hoa hướng dương',
    ],
];

// ─── HÀM TIỆN ÍCH ───────────────────────────────────────────

/**
 * Kiểm tra và tạo danh mục nếu chưa tồn tại.
 */
function ensure_category($name, $slug, $parent_slug = '') {
    $existing = get_term_by('slug', $slug, 'product_cat');
    if ($existing) {
        echo "  [OK] Danh mục đã tồn tại: {$name} (slug: {$slug})\n";
        return $existing->term_id;
    }

    $parent_id = 0;
    if (!empty($parent_slug)) {
        $parent = get_term_by('slug', $parent_slug, 'product_cat');
        if ($parent) {
            $parent_id = $parent->term_id;
        }
    }

    $result = wp_insert_term($name, 'product_cat', [
        'slug'   => $slug,
        'parent' => $parent_id,
    ]);

    if (is_wp_error($result)) {
        echo "  [ERR] Không thể tạo danh mục {$name}: " . $result->get_error_message() . "\n";
        return 0;
    }

    echo "  [TAO] Đã tạo danh mục: {$name} (slug: {$slug}, ID: {$result['term_id']})\n";
    return $result['term_id'];
}

/**
 * Kiểm tra và tạo thuộc tính (attribute) nếu chưa tồn tại,
 * đồng thời thêm các giá trị (terms) cho thuộc tính đó.
 */
function ensure_attribute($attr_name, $attr_slug, $attr_orderby, $values) {
    global $wpdb;

    // Kiểm tra attribute trong bảng woocommerce_attribute_taxonomies
    $attribute_id = $wpdb->get_var($wpdb->prepare(
        "SELECT attribute_id FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_name = %s",
        $attr_slug
    ));

    if (!$attribute_id) {
        // Tạo attribute mới
        $result = $wpdb->insert(
            $wpdb->prefix . 'woocommerce_attribute_taxonomies',
            [
                'attribute_name'    => $attr_slug,
                'attribute_label'   => $attr_name,
                'attribute_type'    => 'select',
                'attribute_orderby' => $attr_orderby,
                'attribute_public'  => 1,
            ]
        );

        if (!$result) {
            echo "  [ERR] Không thể tạo thuộc tính {$attr_name}\n";
            return;
        }

        // Clear cache để WordPress nhận biết attribute mới
        delete_transient('wc_attribute_taxonomies');
        wp_cache_delete('woocommerce_attribute_taxonomies', 'options');

        echo "  [TAO] Đã tạo thuộc tính: {$attr_name} (slug: {$attr_slug})\n";
    } else {
        echo "  [OK] Thuộc tính đã tồn tại: {$attr_name} (slug: {$attr_slug})\n";
    }

    // Đảm bảo taxonomy được đăng ký
    $taxonomy = 'pa_' . $attr_slug;
    if (!taxonomy_exists($taxonomy)) {
        register_taxonomy($taxonomy, 'product', [
            'label'  => $attr_name,
            'public' => true,
        ]);
        flush_rewrite_rules();
    }

    // Thêm các giá trị (terms) cho attribute
    foreach ($values as $term_name) {
        $term_slug = sanitize_title($term_name);
        $existing_term = term_exists($term_slug, $taxonomy);

        if (!$existing_term) {
            $result = wp_insert_term($term_name, $taxonomy, ['slug' => $term_slug]);
            if (is_wp_error($result)) {
                echo "  [ERR] Không thể tạo giá trị '{$term_name}' cho {$attr_name}: " . $result->get_error_message() . "\n";
            } else {
                echo "  [TAO] Đã tạo giá trị: {$term_name} (thuộc {$attr_name})\n";
            }
        }
    }

    // Tải lại attribute taxonomies
    flush_rewrite_rules();
}

/**
 * Lấy term IDs từ slugs.
 */
function get_category_ids($slugs) {
    $ids = [];
    foreach ($slugs as $slug) {
        $term = get_term_by('slug', $slug, 'product_cat');
        if ($term) {
            $ids[] = $term->term_id;
        }
    }
    return $ids;
}

/**
 * Lấy term ID của một attribute taxonomy.
 */
function get_attribute_term_id($attr_slug, $term_slug) {
    $taxonomy = 'pa_' . $attr_slug;
    $term = get_term_by('slug', $term_slug, $taxonomy);
    return $term ? $term->term_id : 0;
}

/**
 * Gán hình ảnh từ URL external — nếu không tải được thì bỏ qua.
 */
function set_product_image($product_id, $image_url = '') {
    if (empty($image_url)) {
        return;
    }

    // Thử tải ảnh từ URL
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $attachment_id = media_sideload_image($image_url, $product_id, null, 'id');

    if (!is_wp_error($attachment_id)) {
        set_post_thumbnail($product_id, $attachment_id);
        echo "  [ANH] Đã gán ảnh cho sản phẩm ID {$product_id}\n";
    }
}

/**
 * Tạo sản phẩm đơn giản (Simple Product).
 */
function create_simple_product($data, $index) {
    $sku = 'FLW-S' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);

    $product = new WC_Product_Simple();
    $product->set_name($data['name']);
    $product->set_description($data['desc']);
    $product->set_short_description($data['desc']);
    $product->set_regular_price($data['price']);
    $product->set_sku($sku);
    $product->set_stock_status('instock');
    $product->set_manage_stock(false);
    $product->set_category_ids(get_category_ids($data['categories']));

    // Gán thuộc tính màu sắc (dạng product level, không phải variation)
    $attributes = [];

    // Màu sắc
    $color_term_ids = [];
    foreach ($data['mau_sac'] as $color_name) {
        $term = get_term_by('slug', sanitize_title($color_name), 'pa_mau-sac');
        if ($term) {
            $color_term_ids[] = $term->term_id;
        }
    }
    if (!empty($color_term_ids)) {
        $attr_color = new WC_Product_Attribute();
        $attr_color->set_id(wc_attribute_taxonomy_id_by_name('pa_mau-sac'));
        $attr_color->set_name('pa_mau-sac');
        $attr_color->set_options($color_term_ids);
        $attr_color->set_visible(true);
        $attr_color->set_variation(false);
        $attributes[] = $attr_color;
    }

    // Loại hoa chính
    $flower_term = get_term_by('slug', sanitize_title($data['loai_hoa']), 'pa_loai-hoa-chinh');
    if ($flower_term) {
        $attr_flower = new WC_Product_Attribute();
        $attr_flower->set_id(wc_attribute_taxonomy_id_by_name('pa_loai-hoa-chinh'));
        $attr_flower->set_name('pa_loai-hoa-chinh');
        $attr_flower->set_options([$flower_term->term_id]);
        $attr_flower->set_visible(true);
        $attr_flower->set_variation(false);
        $attributes[] = $attr_flower;
    }

    $product->set_attributes($attributes);
    $product_id = $product->save();

    if ($product_id) {
        echo "  [TAO] Simple Product #{$index}: {$data['name']} — (SKU: {$sku}, ID: {$product_id}, Giá: " . number_format($data['price'], 0, ',', '.') . "đ)\n";
    }

    return $product_id;
}

/**
 * Tạo sản phẩm biến thể (Variable Product).
 */
function create_variable_product($data, $index) {
    $sku = 'FLW-V' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);

    $product = new WC_Product_Variable();
    $product->set_name($data['name']);
    $product->set_description($data['desc']);
    $product->set_short_description($data['desc']);
    $product->set_sku($sku);
    $product->set_stock_status('instock');
    $product->set_manage_stock(false);
    $product->set_category_ids(get_category_ids($data['categories']));

    // Tạo thuộc tính "Kích thước" dùng cho variation
    $attr_size_term_ids = [];
    foreach (['Nhỏ', 'Vừa', 'Lớn'] as $size_name) {
        $term = get_term_by('slug', sanitize_title($size_name), 'pa_kich-thuoc');
        if ($term) {
            $attr_size_term_ids[] = $term->term_id;
        }
    }

    $attr_size = new WC_Product_Attribute();
    $attr_size->set_id(wc_attribute_taxonomy_id_by_name('pa_kich-thuoc'));
    $attr_size->set_name('pa_kich-thuoc');
    $attr_size->set_options($attr_size_term_ids);
    $attr_size->set_visible(true);
    $attr_size->set_variation(true); // Quan trọng: đây là variation attribute

    $attributes = [$attr_size];
    $product->set_attributes($attributes);
    $product_id = $product->save();

    if (!$product_id) {
        echo "  [ERR] Không thể tạo Variable Product: {$data['name']}\n";
        return 0;
    }

    echo "  [TAO] Variable Product #{$index}: {$data['name']} — (SKU: {$sku}, ID: {$product_id})\n";

    // Tạo các biến thể (variations) cho 3 kích thước
    $size_price_modifier = [
        'Nhỏ'  => 0,
        'Vừa'  => 150000,
        'Lớn'  => 300000,
    ];

    foreach ($size_price_modifier as $size_name => $modifier) {
        $variation_price = $data['base_price'] + $modifier;

        $variation = new WC_Product_Variation();
        $variation->set_parent_id($product_id);
        $variation->set_sku($sku . '-' . sanitize_title($size_name));
        $variation->set_regular_price($variation_price);
        $variation->set_stock_status('instock');
        $variation->set_manage_stock(false);

        // Gán thuộc tính kích thước cho variation
        $size_term = get_term_by('slug', sanitize_title($size_name), 'pa_kich-thuoc');
        if ($size_term) {
            $variation->set_attributes(['pa_kich-thuoc' => $size_term->slug]);
        }

        $variation->save();

        echo "    [VAR] Kích thước {$size_name}: " . number_format($variation_price, 0, ',', '.') . "đ (SKU: {$variation->get_sku()}, ID: {$variation->get_id()})\n";
    }

    // Cập nhật giá khoảng (min/max) cho sản phẩm cha
    $product->save();

    return $product_id;
}

// ═══════════════════════════════════════════════════════════
// MAIN EXECUTION
// ═══════════════════════════════════════════════════════════

echo "\n═══════════════════════════════════════════════════════\n";
echo "  🏪 IMPORT SẢN PHẨM HOA MẪU — WooCommerce\n";
echo "═══════════════════════════════════════════════════════\n\n";

// ─── Bước 1: Đảm bảo WooCommerce đã kích hoạt ───────────────
if (!class_exists('WooCommerce')) {
    echo "[LOI] WooCommerce chưa được kích hoạt! Vui lòng kích hoạt WooCommerce trước.\n";
    exit(1);
}
echo "[OK] WooCommerce đã kích hoạt.\n\n";

// ─── Bước 2: Tạo danh mục ───────────────────────────────────
echo "─── Bước 1. Tạo Danh mục (Categories) ───\n";
foreach ($categories as $cat) {
    ensure_category($cat['name'], $cat['slug'], $cat['parent']);
}
echo "\n";

// ─── Bước 3: Tạo thuộc tính ─────────────────────────────────
echo "─── Bước 2. Tạo Thuộc tính (Attributes) ───\n";
foreach ($attributes as $attr_slug => $attr) {
    ensure_attribute($attr['name'], $attr['slug'], $attr['orderby'], $attr['values']);
}
echo "\n";

// ─── Bước 4: Tạo sản phẩm đơn giản ──────────────────────────
echo "─── Bước 3. Tạo Sản phẩm đơn giản (Simple Products) ───\n";
$simple_ids = [];
foreach ($simple_products as $i => $product_data) {
    $id = create_simple_product($product_data, $i);
    if ($id) {
        $simple_ids[] = $id;
    }
}
echo "  => Đã tạo " . count($simple_ids) . "/" . count($simple_products) . " sản phẩm đơn giản.\n\n";

// ─── Bước 5: Tạo sản phẩm biến thể ──────────────────────────
echo "─── Bước 4. Tạo Sản phẩm biến thể (Variable Products) ───\n";
$variable_ids = [];
foreach ($variable_products as $i => $product_data) {
    $id = create_variable_product($product_data, $i);
    if ($id) {
        $variable_ids[] = $id;
    }
}
echo "\n  => Đã tạo " . count($variable_ids) . "/" . count($variable_products) . " sản phẩm biến thể.\n\n";

// ─── Tổng kết ───────────────────────────────────────────────
echo "═══════════════════════════════════════════════════════\n";
echo "  ✅ TẬP ĐỮ LIỆU IMPORT HOÀN TẤT!\n";
echo "═══════════════════════════════════════════════════════\n\n";

$total = count($simple_ids) + count($variable_ids);
echo "  Tổng số sản phẩm: {$total}\n";
echo "    - Đơn giản:    " . count($simple_ids) . "\n";
echo "    - Biến thể:   " . count($variable_ids) . "\n\n";

// Tính tổng số biến thể (variations)
$total_variations = count($variable_ids) * 3;
echo "  Tổng số biến thể: {$total_variations}\n";
echo "\n";

echo "  Danh sách sản phẩm đơn giản (ID): " . implode(', ', $simple_ids) . "\n";
echo "  Danh sách sản phẩm biến thể (ID): " . implode(', ', $variable_ids) . "\n";
echo "\n═══════════════════════════════════════════════════════\n";

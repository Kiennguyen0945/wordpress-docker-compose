<?php
/**
 * WP-CLI Fix Script — Sửa lỗi variations bị thiếu attribute
 *
 * Vấn đề: Các variation không có meta attribute_pa_kich-thuoc
 *          vì lỗi tìm term sai slug trong script import.
 *
 * Cách chạy:
 *   docker compose run --rm wpcli wp eval-file /var/www/html/fix-variations.php
 */

defined('ABSPATH') || exit;

echo "\n═══════════════════════════════════════════════════════\n";
echo "  🔧 FIX VARIATION ATTRIBUTES — WooCommerce\n";
echo "═══════════════════════════════════════════════════════\n\n";

if (!class_exists('WooCommerce')) {
    echo "[LOI] WooCommerce chưa được kích hoạt!\n";
    exit(1);
}

global $wpdb;

// ─── Bước 1: Gắn attribute_pa_kich-thuoc cho các variations ───
echo "─── Bước 1. Gắn attribute cho variations ───\n";

// Lấy tất cả variations của sản phẩm FLW-V
$variations = $wpdb->get_results(
    "SELECT p.ID as variation_id, p.post_parent as parent_id,
            pm.meta_value as sku
     FROM {$wpdb->posts} p
     INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_sku'
     WHERE p.post_type = 'product_variation'
     AND pm.meta_value LIKE 'FLW-V%-%'
     ORDER BY p.ID"
);

$fixed = 0;
$size_map = [
    'nho' => 'Nhỏ',
    'vua' => 'Vừa',
    'lon' => 'Lớn',
];

foreach ($variations as $var) {
    // Lấy size từ SKU: FLW-V001-nho → nho
    $sku_parts = explode('-', $var->sku);
    $size_slug = end($sku_parts);

    if (!isset($size_map[$size_slug])) {
        echo "  [SKIP] Variation ID {$var->variation_id} (SKU: {$var->sku}): không xác định size\n";
        continue;
    }

    $size_name = $size_map[$size_slug];

    // Tìm term bằng NAME (không dùng slug vì slug bị prefix)
    $term = get_term_by('name', $size_name, 'pa_kich-thuoc');
    if (!$term) {
        echo "  [ERR] Không tìm thấy term '{$size_name}' trong pa_kich-thuoc\n";
        continue;
    }

    // Kiểm tra xem đã có attribute chưa
    $existing_attr = get_post_meta($var->variation_id, 'attribute_pa_kich-thuoc', true);
    if (!empty($existing_attr)) {
        echo "  [OK] Đã có attribute: {$var->sku} → {$existing_attr}\n";
        continue;
    }

    // Gắn attribute cho variation
    update_post_meta($var->variation_id, 'attribute_pa_kich-thuoc', $term->slug);
    echo "  [FIX] {$var->sku} (ID: {$var->variation_id}) → attribute_pa_kich-thuoc = {$term->slug}\n";
    $fixed++;
}

echo "  => Đã sửa {$fixed} variations.\n\n";

// ─── Bước 2: Cập nhật lại price cache cho variable products ───
echo "─── Bước 2. Cập nhật price cache ───\n";

$parent_ids = $wpdb->get_col(
    "SELECT DISTINCT p.post_parent FROM {$wpdb->posts} p
     INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_sku'
     WHERE p.post_type = 'product_variation'
     AND pm.meta_value LIKE 'FLW-V%-%'"
);

foreach ($parent_ids as $pid) {
    // Xoá cache cũ
    delete_post_meta($pid, '_price');
    delete_post_meta($pid, '_min_variation_price');
    delete_post_meta($pid, '_max_variation_price');
    delete_post_meta($pid, '_min_regular_variation_price');
    delete_post_meta($pid, '_max_regular_variation_price');

    // Force recalculate
    $product = wc_get_product($pid);
    if ($product && $product->is_type('variable')) {
        $product->save();
        echo "  [FIX] Variable Product ID {$pid}: đã cập nhật price range\n";
    }
}

// ─── Tổng kết ──────────────────────────────────────────────
echo "\n═══════════════════════════════════════════════════════\n";
echo "  ✅ FIX HOÀN TẤT!\n";
echo "═══════════════════════════════════════════════════════\n";
echo "  Đã sửa {$fixed} variations\n\n";

<?php
/**
 * WP-CLI Fix Script — Sửa giá sản phẩm bị lỗi chia 1000
 *
 * Cách chạy:
 *   docker compose run --rm wpcli wp eval-file /var/www/html/fix-prices.php
 *
 * Script này:
 *   - Nhân tất cả _regular_price lên 1000 để sửa lỗi /1000 trong script import
 *   - Chỉ fix các sản phẩm import mẫu (SKU có tiền tố FLW-)
 *   - Sau đó cập nhật giá khoảng (min/max) cho sản phẩm biến thể cha
 */

defined('ABSPATH') || exit;

echo "\n═══════════════════════════════════════════════════════\n";
echo "  🔧 FIX GIÁ SẢN PHẨM — WooCommerce\n";
echo "═══════════════════════════════════════════════════════\n\n";

if (!class_exists('WooCommerce')) {
    echo "[LOI] WooCommerce chưa được kích hoạt!\n";
    exit(1);
}

global $wpdb;

// ─── Bước 1: Fix sản phẩm đơn giản (Simple Products) ─────
echo "─── Bước 1. Fix giá sản phẩm đơn giản (Simple Products) ───\n";

$simple_ids = $wpdb->get_col(
    "SELECT p.ID FROM {$wpdb->posts} p 
     INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_sku'
     WHERE p.post_type = 'product' 
     AND p.post_status = 'publish'
     AND pm.meta_value LIKE 'FLW-S%'
     AND p.ID NOT IN (SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_product_type' AND meta_value = 'variable')"
);

$fixed_simple = 0;
foreach ($simple_ids as $pid) {
    $old_price = get_post_meta($pid, '_regular_price', true);
    if ($old_price === '' || $old_price === false) continue;

    $new_price = $old_price * 1000;
    update_post_meta($pid, '_regular_price', $new_price);
    update_post_meta($pid, '_price', $new_price);

    $sku = get_post_meta($pid, '_sku', true);
    $name = get_the_title($pid);
    echo "  [FIX] {$name} (SKU: {$sku}, ID: {$pid}): " . number_format($old_price, 0, ',', '.') . "đ → " . number_format($new_price, 0, ',', '.') . "đ\n";
    $fixed_simple++;
}
echo "  => Đã sửa {$fixed_simple} sản phẩm đơn giản.\n\n";

// ─── Bước 2: Fix biến thể (Variations) ─────────────────────
echo "─── Bước 2. Fix giá biến thể (Variations) ───\n";

$variation_ids = $wpdb->get_col(
    "SELECT p.ID FROM {$wpdb->posts} p 
     INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_sku'
     WHERE p.post_type = 'product_variation'
     AND pm.meta_value LIKE 'FLW-V%-%'
     ORDER BY p.ID"
);

$fixed_variations = 0;
foreach ($variation_ids as $vid) {
    $old_price = get_post_meta($vid, '_regular_price', true);
    if ($old_price === '' || $old_price === false) continue;

    $new_price = $old_price * 1000;
    update_post_meta($vid, '_regular_price', $new_price);
    update_post_meta($vid, '_price', $new_price);

    $sku = get_post_meta($vid, '_sku', true);
    echo "  [FIX] Variation ID {$vid} (SKU: {$sku}): " . number_format($old_price, 0, ',', '.') . "đ → " . number_format($new_price, 0, ',', '.') . "đ\n";
    $fixed_variations++;
}
echo "  => Đã sửa {$fixed_variations} biến thể.\n\n";

// ─── Bước 3: Cập nhật giá khoảng cho Variable Products ────
echo "─── Bước 3. Cập nhật giá khoảng (min/max) cho sản phẩm cha ───\n";

$variable_ids = $wpdb->get_col(
    "SELECT p.ID FROM {$wpdb->posts} p 
     INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_sku'
     WHERE p.post_type = 'product' 
     AND pm.meta_value LIKE 'FLW-V%'
     AND p.ID NOT IN (SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_product_type' AND meta_value != 'variable')"
);

$fixed_parents = 0;
foreach ($variable_ids as $pid) {
    // Force WooCommerce to recalculate min/max prices
    $product = wc_get_product($pid);
    if ($product && $product->is_type('variable')) {
        $product->save(); // Recalculate price range on save
        $name = get_the_title($pid);
        echo "  [FIX] Variable Product {$name} (ID: {$pid}): đã cập nhật lại giá khoảng\n";
        $fixed_parents++;
    }
}
echo "  => Đã cập nhật {$fixed_parents} sản phẩm biến thể cha.\n\n";

// ─── Tổng kết ──────────────────────────────────────────────
echo "═══════════════════════════════════════════════════════\n";
echo "  ✅ FIX GIÁ HOÀN TẤT!\n";
echo "═══════════════════════════════════════════════════════\n\n";
echo "  Đã sửa: {$fixed_simple} sản phẩm đơn giản\n";
echo "           {$fixed_variations} biến thể\n";
echo "           {$fixed_parents} sản phẩm biến thể cha\n\n";

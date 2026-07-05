<?php
/**
 * WP-CLI Fix-All Script — Sửa tất cả lỗi sản phẩm import
 *
 * Cách chạy:
 *   docker compose run --rm wpcli wp eval-file /var/www/html/fix-all.php
 *
 * Các lỗi được sửa:
 *   1. Giá bị chia 1000     → nhân 1000
 *   2. Thiếu attribute terms → gán object terms cho variable products
 *   3. Price cache sai       → rebuild
 */

defined('ABSPATH') || exit;

if (!class_exists('WooCommerce')) {
    echo "[LOI] WooCommerce chưa được kích hoạt!\n";
    exit(1);
}

echo "\n═══════════════════════════════════════════════════════\n";
echo "  🔧 FIX-ALL — SỬA LỖI SẢN PHẨM IMPORT\n";
echo "═══════════════════════════════════════════════════════\n\n";

global $wpdb;

// ─── 1️⃣ Fix giá (x1000) ───────────────────────────────────
echo "─── 1. SỬA GIÁ (x1000) ───\n";

// Simple products
$simple_ids = $wpdb->get_col(
    "SELECT p.ID FROM {$wpdb->posts} p
     INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_sku' AND pm.meta_value LIKE 'FLW-S%'
     WHERE p.post_type = 'product'"
);
$fixed = 0;
foreach ($simple_ids as $pid) {
    $old = get_post_meta($pid, '_regular_price', true);
    if ($old > 0 && $old < 10000) { // Chỉ fix nếu giá < 10.000 (lỗi chia 1000)
        $new = $old * 1000;
        update_post_meta($pid, '_regular_price', $new);
        update_post_meta($pid, '_price', $new);
        echo "  ID {$pid}: " . number_format($old, 0, ',', '.') . "đ → " . number_format($new, 0, ',', '.') . "đ\n";
        $fixed++;
    }
}
echo "  => Đã sửa {$fixed} simple products\n\n";

// Variations
$var_ids = $wpdb->get_col(
    "SELECT p.ID FROM {$wpdb->posts} p
     INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_sku' AND pm.meta_value LIKE 'FLW-V%-%'
     WHERE p.post_type = 'product_variation'"
);
$fixed_v = 0;
foreach ($var_ids as $vid) {
    $old = get_post_meta($vid, '_regular_price', true);
    if ($old > 0 && $old < 10000) {
        $new = $old * 1000;
        update_post_meta($vid, '_regular_price', $new);
        update_post_meta($vid, '_price', $new);
        echo "  Var ID {$vid}: " . number_format($old, 0, ',', '.') . "đ → " . number_format($new, 0, ',', '.') . "đ\n";
        $fixed_v++;
    }
}
echo "  => Đã sửa {$fixed_v} variations\n\n";

// ─── 2️⃣ Gán attribute terms cho variable products ───────────
echo "─── 2. GÁN ATTRIBUTE TERMS ───\n";
$parent_ids = $wpdb->get_col(
    "SELECT p.ID FROM {$wpdb->posts} p
     INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_sku' AND pm.meta_value LIKE 'FLW-V%'
     WHERE p.post_type = 'product'"
);

// Lấy term IDs cho kích thước
$size_terms = [];
foreach (['Nhỏ', 'Vừa', 'Lớn'] as $name) {
    $t = get_term_by('name', $name, 'pa_kich-thuoc');
    if ($t) $size_terms[] = $t->term_id;
}

$fixed_terms = 0;
foreach ($parent_ids as $pid) {
    $current = wp_get_object_terms($pid, 'pa_kich-thuoc');
    if (count($current) < 3) {
        wp_set_object_terms($pid, $size_terms, 'pa_kich-thuoc');
        $fixed_terms++;
        echo "  ID {$pid}: đã gán terms\n";
    } else {
        echo "  ID {$pid}: đã có terms (OK)\n";
    }
}
echo "  => Đã sửa {$fixed_terms} products\n\n";

// ─── 3️⃣ Rebuild price cache ────────────────────────────────
echo "─── 3. REBUILD PRICE CACHE ───\n";
foreach ($parent_ids as $pid) {
    $product = wc_get_product($pid);
    if (!$product || !$product->is_type('variable')) continue;

    $prices = $product->get_variation_prices(true);
    $min = !empty($prices['regular_price']) ? min($prices['regular_price']) : 0;
    $max = !empty($prices['regular_price']) ? max($prices['regular_price']) : 0;

    if ($min > 0) {
        update_post_meta($pid, '_price', $min);
        update_post_meta($pid, '_min_variation_price', $min);
        update_post_meta($pid, '_min_regular_variation_price', $min);
    }
    if ($max > 0) {
        update_post_meta($pid, '_max_variation_price', $max);
        update_post_meta($pid, '_max_regular_variation_price', $max);
    }
    echo "  ID {$pid}: {$min} ~ {$max}\n";
}

// ─── Kết thúc ───────────────────────────────────────────────
echo "\n═══════════════════════════════════════════════════════\n";
echo "  ✅ FIX-ALL HOÀN TẤT!\n";
echo "═══════════════════════════════════════════════════════\n";
echo "  Đã sửa: {$fixed} simple prices\n";
echo "          {$fixed_v} variation prices\n";
echo "          {$fixed_terms} object terms\n\n";

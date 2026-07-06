<?php
/**
 * Fix Stock — Bật quản lý tồn kho cho tất cả sản phẩm
 *
 * Cách chạy:
 *   docker compose run --rm wpcli wp eval-file /var/www/html/fix-stock.php
 *
 * Script này sẽ:
 *   1. Bật manage_stock = true cho tất cả Simple Product
 *   2. Set stock quantity = 100 (mặc định) cho Simple Product
 *   3. Bật manage_stock = true cho tất cả Variation
 *   4. Set stock quantity = 50 (mặc định) cho từng variation
 *   5. Tắt manage_stock trên product cha (variable) — chỉ variations mới cần stock
 */

defined('ABSPATH') || exit;

if (!class_exists('WooCommerce')) {
    echo "[LOI] WooCommerce chưa được kích hoạt!\n";
    exit(1);
}

echo "\n═══════════════════════════════════════════════════════\n";
echo "  📦 FIX STOCK — Bật quản lý tồn kho\n";
echo "═══════════════════════════════════════════════════════\n\n";

$fixed_simple = 0;
$fixed_variation = 0;
$skipped = 0;

// ─── 1. Fix Simple Products ─────────────────────────────────
echo "─── Bước 1: Simple Products ───\n";

$simple_products = wc_get_products([
    'type'  => 'simple',
    'limit' => -1,
]);

foreach ($simple_products as $product) {
    $id = $product->get_id();

    if ($product->get_manage_stock()) {
        echo "  [SKIP] Product ID {$id} đã bật stock rồi.\n";
        $skipped++;
        continue;
    }

    $product->set_manage_stock(true);
    $product->set_stock_quantity(100);  // Mặc định 100 sản phẩm
    $product->set_stock_status('instock');
    $product->save();

    echo "  [FIX] Product ID {$id}: {$product->get_name()} → stock = 100\n";
    $fixed_simple++;
}

// ─── 2. Fix Variations ──────────────────────────────────────
echo "\n─── Bước 2: Variations ───\n";

$variations = wc_get_products([
    'type'  => 'variation',
    'limit' => -1,
]);

foreach ($variations as $variation) {
    $id = $variation->get_id();

    if ($variation->get_manage_stock()) {
        echo "  [SKIP] Variation ID {$id} đã bật stock rồi.\n";
        $skipped++;
        continue;
    }

    $variation->set_manage_stock(true);
    $variation->set_stock_quantity(50);  // Mặc định 50 cho mỗi biến thể
    $variation->set_stock_status('instock');
    $variation->save();

    $parent_id = $variation->get_parent_id();
    echo "  [FIX] Variation ID {$id} (Parent: {$parent_id}) → stock = 50\n";
    $fixed_variation++;
}

// ─── 3. Kiểm tra product cha (Variable) ─────────────────────
echo "\n─── Bước 3: Kiểm tra Variable Products (cha) ───\n";

$variable_products = wc_get_products([
    'type'  => 'variable',
    'limit' => -1,
]);

foreach ($variable_products as $product) {
    $id = $product->get_id();

    // Variable product KHÔNG cần manage_stock — chỉ variations mới cần
    if ($product->get_manage_stock()) {
        $product->set_manage_stock(false);
        $product->save();
        echo "  [FIX] Variable Product ID {$id}: tắt manage_stock (chỉ variations mới cần)\n";
    } else {
        echo "  [OK]  Variable Product ID {$id}: đã đúng (manage_stock = off)\n";
    }
}

// ─── Tổng kết ───────────────────────────────────────────────
echo "\n═══════════════════════════════════════════════════════\n";
echo "  📊 KẾT QUẢ:\n";
echo "  • Simple Products đã fix: {$fixed_simple}\n";
echo "  • Variations đã fix:      {$fixed_variation}\n";
echo "  • Đã bỏ qua (có sẵn):    {$skipped}\n";
echo "═══════════════════════════════════════════════════════\n\n";

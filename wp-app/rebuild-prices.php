<?php
/**
 * WP-CLI Fix — Rebuild price cache for variable products
 *
 * Chạy: docker compose run --rm wpcli wp eval-file /var/www/html/rebuild-prices.php
 */
defined('ABSPATH') || exit;

echo "Rebuilding price cache...\n\n";

global $wpdb;

// ─── 1️⃣ Simple products ───
echo "─── Simple Products ───\n";
$simple_ids = $wpdb->get_col(
    "SELECT p.ID FROM {$wpdb->posts} p
     INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_sku' AND pm.meta_value LIKE 'FLW-S%'
     WHERE p.post_type = 'product'"
);
foreach ($simple_ids as $pid) {
    $price = get_post_meta($pid, '_regular_price', true);
    if ($price) update_post_meta($pid, '_price', $price);
    echo "  ID {$pid}: _price = " . number_format((float)$price, 0, ',', '.') . "đ\n";
}

// ─── 2️⃣ Variable products ───
echo "\n─── Variable Products ───\n";
$variable_ids = $wpdb->get_col(
    "SELECT p.ID FROM {$wpdb->posts} p
     INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_sku' AND pm.meta_value LIKE 'FLW-V%'
     WHERE p.post_type = 'product'"
);
foreach ($variable_ids as $pid) {
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

echo "\n✅ Done!\n";
    }
}

echo "\nDone!\n";

<?php
/**
 * Single product price
 *
 * @package ThungLungHoa
 */

if (!defined('ABSPATH')) {
    exit;
}

global $product;
?>
<div class="pd-price">
  <?php echo $product->get_price_html(); ?>
</div>

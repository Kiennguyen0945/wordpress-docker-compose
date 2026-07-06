<?php
/**
 * Result count
 *
 * @package ThungLungHoa
 */

defined('ABSPATH') || exit;
?>
<span class="count">
  <?php
  if (1 === intval($total)) {
      _e('Hiển thị 1 sản phẩm', 'thunglung-hoa');
  } elseif ($total <= $per_page || -1 === $per_page) {
      printf(_x('Hiển thị tất cả %d sản phẩm', 'thunglung-hoa'), $total);
  } else {
      $first = ($per_page * $current) - $per_page + 1;
      $last  = min($total, $per_page * $current);
      printf(_x('Hiển thị %1$d–%2$d trong %3$d sản phẩm', 'thunglung-hoa'), $first, $last, $total);
  }
  ?>
</span>

<?php
/**
 * Single product tabs
 *
 * @package ThungLungHoa
 */

if (!defined('ABSPATH')) {
    exit;
}

$tabs = apply_filters('woocommerce_product_tabs', []);

if (empty($tabs)) {
    return;
}
?>

<div class="pd-tabs container">
  <div class="pd-tab-nav">
    <?php $first = true; ?>
    <?php foreach ($tabs as $key => $tab) : ?>
      <button class="pd-tab-btn <?php echo $first ? 'active' : ''; ?>" data-tab="<?php echo esc_attr($key); ?>">
        <?php echo wp_kses_post(apply_filters('woocommerce_product_' . $key . '_tab_title', $tab['title'], $key)); ?>
      </button>
      <?php $first = false; ?>
    <?php endforeach; ?>
  </div>
  <div class="pd-tab-content">
    <?php $first = true; ?>
    <?php foreach ($tabs as $key => $tab) : ?>
      <div class="pd-tab-pane <?php echo $first ? 'active' : ''; ?>" id="tab-<?php echo esc_attr($key); ?>">
        <?php
        if (isset($tab['callback'])) {
            call_user_func($tab['callback'], $key, $tab);
        }
        ?>
      </div>
      <?php $first = false; ?>
    <?php endforeach; ?>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.pd-tab-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
      document.querySelectorAll('.pd-tab-btn').forEach(function(b) { b.classList.remove('active'); });
      document.querySelectorAll('.pd-tab-pane').forEach(function(p) { p.classList.remove('active'); });
      btn.classList.add('active');
      var tab = btn.getAttribute('data-tab');
      document.getElementById('tab-' + tab).classList.add('active');
    });
  });
});
</script>

<?php
/**
 * Single product tabs — full-width layout
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

<section class="pd-tab-section">
  <div class="container">
    <nav class="pd-tab-nav">
      <?php $first = true; ?>
      <?php foreach ($tabs as $key => $tab) : ?>
        <button class="pd-tab-btn <?php echo $first ? 'active' : ''; ?>" data-tab="<?php echo esc_attr($key); ?>">
          <?php echo wp_kses_post(apply_filters('woocommerce_product_' . $key . '_tab_title', $tab['title'], $key)); ?>
        </button>
        <?php $first = false; ?>
      <?php endforeach; ?>
    </nav>
  </div>

  <div class="pd-tab-content">
    <div class="container">
      <?php $first = true; ?>
      <?php foreach ($tabs as $key => $tab) : ?>
        <div class="pd-tab-pane <?php echo $first ? 'active' : ''; ?>" id="tab-<?php echo esc_attr($key); ?>">
          <?php
          if (isset($tab['callback'])) {
              // Wrap output to hide the redundant H2 that duplicates tab button
              ob_start();
              call_user_func($tab['callback'], $key, $tab);
              $content = ob_get_clean();
              // Remove the auto-generated h2 (e.g. "Description") since tab button already shows it
              $content = preg_replace('/<h2[^>]*>.*?<\/h2>/i', '', $content, 1);
              echo $content;
          }
          ?>
        </div>
        <?php $first = false; ?>
      <?php endforeach; ?>
    </div>
  </div>
</section>

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

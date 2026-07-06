<?php
/**
 * Breadcrumb
 *
 * @package ThungLungHoa
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<?php if ($breadcrumb) : ?>
  <div class="breadcrumb container">
    <?php foreach ($breadcrumb as $key => $crumb) : ?>
      <?php if (!empty($crumb[1]) && sizeof($breadcrumb) !== $key + 1) : ?>
        <a href="<?php echo esc_url($crumb[1]); ?>"><?php echo esc_html($crumb[0]); ?></a>
        <span class="sep">/</span>
      <?php else : ?>
        <span class="current"><?php echo esc_html($crumb[0]); ?></span>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

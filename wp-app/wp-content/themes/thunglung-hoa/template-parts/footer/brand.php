<?php
/**
 * Footer: Brand column (logo + social)
 *
 * @package ThungLungHoa
 */
?>
<div class="footer-brand">
  <a href="<?php echo esc_url(home_url('/')); ?>" class="logo">
    <?php echo tlh_logo_svg(); ?>
    <?php bloginfo('name'); ?>
  </a>
  <p>Hoa tươi mỗi sớm mai — cắm bó theo từng câu chuyện riêng của bạn. Gửi trao yêu thương, tinh tế trong từng cánh hoa.</p>
  <div class="footer-social">
    <a href="#" aria-label="Facebook">f</a>
    <a href="#" aria-label="Instagram">ig</a>
    <a href="#" aria-label="Zalo">za</a>
  </div>
</div>

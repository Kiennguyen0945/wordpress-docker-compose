<!-- ============ FOOTER ============ -->
<footer class="site-footer" id="footer">
  <div class="container">
    <div class="footer-grid">
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
      <div class="footer-col">
        <h4>Khám phá</h4>
        <ul>
          <li><a href="<?php echo esc_url(home_url('/')); ?>">Trang chủ</a></li>
          <li><a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>">Cửa hàng</a></li>
          <li><a href="<?php echo esc_url(get_permalink(wc_get_page_id('cart'))); ?>">Giỏ hàng</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Danh mục</h4>
        <ul>
          <li><a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>">Hoa theo dịp</a></li>
          <li><a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>">Hoa theo loại</a></li>
          <li><a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>">Hoa theo thiết kế</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Nhận ưu đãi sớm nhất</h4>
        <p style="font-size:.88rem; color:#5b5049; margin-bottom:10px;">Đăng ký để nhận ưu đãi dịp lễ và mẫu hoa mới mỗi tuần.</p>
        <div class="newsletter-row">
          <input type="email" placeholder="Email của bạn">
          <button>Gửi</button>
        </div>
      </div>
    </div>
    <div class="footer-bottom">
      <span>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. Đã đăng ký bản quyền.</span>
      <span>123 Đường Hoa Lư, Ninh Kiều, Cần Thơ · Zalo/Hotline: 0909 xxx xxx</span>
    </div>
  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>

<!-- ============ FOOTER ============ -->
<footer class="site-footer" id="footer">
  <div class="container">
    <div class="footer-grid">
      <?php get_template_part('template-parts/footer/brand'); ?>
      <?php get_template_part('template-parts/footer/links'); ?>
      <?php get_template_part('template-parts/footer/newsletter'); ?>
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

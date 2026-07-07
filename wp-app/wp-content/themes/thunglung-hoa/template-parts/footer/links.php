<?php
/**
 * Footer: Menu links columns
 *
 * @package ThungLungHoa
 */
?>
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

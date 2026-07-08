<?php
/**
 * Product: Horizontal filter bar for the shop toolbar.
 *
 * Hiển thị: đếm sản phẩm + dropdown "Theo Dịp" (danh mục) + dropdown "Theo Kiểu Dáng" + sort select.
 * Render bên trong .shop-toolbar ở archive-product.php.
 *
 * @package ThungLungHoa
 */
?>
<span class="tlh-filter-count"><?php woocommerce_result_count(); ?></span>

<div class="tlh-filter-group">
  <div class="tlh-filter-dropdown">
    <button type="button" class="tlh-filter-btn" data-filter="category">
      Theo Dịp
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M6 9l6 6 6-6"/></svg>
    </button>
    <div class="tlh-filter-panel" data-panel="category">
      <?php
      $categories = get_terms( array(
          'taxonomy'   => 'product_cat',
          'hide_empty' => true,
          'orderby'    => 'name',
          'order'      => 'ASC',
      ) );
      if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) :
          foreach ( $categories as $cat ) :
      ?>
      <label>
        <input type="checkbox" name="category" value="<?php echo esc_attr( $cat->slug ); ?>">
        <?php echo esc_html( $cat->name ); ?>
      </label>
      <?php
          endforeach;
      else :
      ?>
      <p style="color:#888;font-size:13px;margin:0;">Chưa có danh mục</p>
      <?php endif; ?>
    </div>
  </div>

  <div class="tlh-filter-dropdown">
    <button type="button" class="tlh-filter-btn" data-filter="style">
      Theo Kiểu Dáng
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M6 9l6 6 6-6"/></svg>
    </button>
    <div class="tlh-filter-panel" data-panel="style">
      <p style="color:#888;font-size:13px;margin:0;">Chưa có phân loại</p>
    </div>
  </div>
</div>

<select class="tlh-sort-select">
  <option value="date-desc">Mới nhất</option>
  <option value="price-asc">Giá: Thấp đến cao</option>
  <option value="price-desc">Giá: Cao đến thấp</option>
  <option value="popularity">Phổ biến nhất</option>
</select>

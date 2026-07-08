<?php
/**
 * Product: Horizontal filter bar for the shop toolbar.
 *
 * Hiển thị: đếm sản phẩm + dropdown lọc danh mục (theo nhóm cha) + dropdown sort.
 *
 * 👉 Cách thêm nhóm danh mục mới (VD: "Loại hoa"):
 *    1. Vào WP Admin > Sản phẩm > Danh mục, tạo parent category "Loại hoa", slug "loai-hoa"
 *    2. Thêm các danh mục con: "Hoa hồng", "Hoa hướng dương",...
 *    3. Thêm 1 dòng vào mảng $filter_groups dưới đây.
 *
 * @package ThungLungHoa
 */

// ─── Cấu hình các nhóm filter ────────────────────────────────────
// Mỗi phần tử = 1 dropdown. Thêm phần tử để có thêm dropdown.
// 'parent_slug'  : slug của danh mục cha trong WP
// 'button_label' : text hiển thị trên nút dropdown
$filter_groups = array(
    array(
        'parent_slug'  => 'hoa-theo-dip',
        'button_label' => 'Theo Dịp',
    ),
    array(
        'parent_slug'  => 'hoa-theo-kieu',
        'button_label' => 'Theo Kiểu Dáng',
    ),
);
?>
<span class="tlh-filter-count"><?php woocommerce_result_count(); ?></span>

<div class="tlh-filter-group">
  <?php foreach ( $filter_groups as $group ) :
      $parent_term = get_term_by( 'slug', $group['parent_slug'], 'product_cat' );
      if ( ! $parent_term ) continue;

      $children = get_terms( array(
          'taxonomy'   => 'product_cat',
          'hide_empty' => true,
          'parent'     => $parent_term->term_id,
          'orderby'    => 'name',
          'order'      => 'ASC',
      ) );
      if ( empty( $children ) || is_wp_error( $children ) ) continue;
  ?>
  <div class="tlh-filter-dropdown">
    <button type="button" class="tlh-filter-btn" data-filter="<?php echo esc_attr( $group['parent_slug'] ); ?>">
      <?php echo esc_html( $group['button_label'] ); ?>
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M6 9l6 6 6-6"/></svg>
    </button>
    <div class="tlh-filter-panel" data-panel="<?php echo esc_attr( $group['parent_slug'] ); ?>">
      <?php foreach ( $children as $cat ) : ?>
      <label>
        <input type="checkbox" name="category" value="<?php echo esc_attr( $cat->slug ); ?>">
        <?php echo esc_html( $cat->name ); ?>
      </label>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<select class="tlh-sort-select">
  <option value="date-desc">Mới nhất</option>
  <option value="price-asc">Giá: Thấp đến cao</option>
  <option value="price-desc">Giá: Cao đến thấp</option>
</select>

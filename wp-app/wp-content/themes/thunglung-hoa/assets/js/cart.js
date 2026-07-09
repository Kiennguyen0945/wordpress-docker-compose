/**
 * Cart page enhancements — Cart Block overrides
 *
 * 1. Khi có sản phẩm: thêm dòng "Miễn phí vận chuyển — MIỄN PHÍ" vào tổng cộng
 * 2. Khi giỏ hàng trống: thay thế Cart Block bằng giao diện custom (kể cả xoá dynamic)
 *
 * @package ThungLungHoa
 * @since 1.0.0
 */

(function () {
  'use strict';

  /* ============================================================
   * HELPERS
   * ============================================================ */

  /** Custom empty cart HTML (giống cart-empty.php) */
  function getEmptyCartHTML() {
    return '<div class="empty-cart" style="grid-column:1/-1;">' +
      '<div class="empty-cart-icon">😢</div>' +
      '<h2 class="empty-cart-title">Giỏ hàng của bạn đang trống!</h2>' +
      '<p class="empty-cart-desc">Hãy khám phá bộ sưu tập hoa tươi và chọn cho mình những bó hoa yêu thích nhé.</p>' +
      '<a href="/shop/" class="btn btn-accent btn-lg">Mua sắm ngay</a>' +
      '<div class="empty-cart-divider">• &nbsp;• &nbsp;•</div>' +
      '<div class="empty-cart-suggestions">' +
        '<div class="section-head" style="text-align:center;">' +
          '<span class="eyebrow">Gợi ý cho bạn</span>' +
          '<h2>Sản phẩm mới</h2>' +
        '</div>' +
        '<div class="grid-4">' +
          '<p style="text-align:center;color:#8a7f75;grid-column:1/-1;">Đang tải sản phẩm…</p>' +
        '</div>' +
      '</div>' +
    '</div>';
  }

  /** Shipping row HTML — chèn vào danh sách tổng cộng */
  function shippingRowHTML() {
    return '<div class="wc-block-components-totals-item" data-tlh-shipping="1">' +
      '<span class="wc-block-components-totals-item__label">Miễn phí vận chuyển</span>' +
      '<span class="wc-block-components-totals-item__value"><strong>MIỄN PHÍ</strong></span>' +
    '</div>';
  }

  /* ============================================================
   * 1. Chèn dòng "Miễn phí vận chuyển" vào tổng cộng giỏ hàng
   * ============================================================ */
  function injectShippingRow() {
    // Đã chèn rồi thì thôi
    if (document.querySelector('[data-tlh-shipping="1"]')) return;

    // Tìm danh sách tổng cộng — các .wc-block-components-totals-item
    // Chèn vào sau dòng subtotal (hoặc đầu danh sách nếu chưa có)
    var items = document.querySelectorAll('.wc-block-components-totals-item');
    if (items.length === 0) return;

    // Chèn sau dòng cuối cùng, trước tổng tiền
    var target = items[items.length - 1];
    if (target) {
      target.insertAdjacentHTML('afterend', shippingRowHTML());

      // Nếu dòng cuối là tổng (total), đảm bảo shipping row ở trên nó
      var isTotalRow = target.querySelector('.wc-block-components-totals-item__label');
      if (isTotalRow && isTotalRow.textContent.trim() === 'Tổng') {
        // Di chuyển shipping row lên trước total
        var shippingRow = document.querySelector('[data-tlh-shipping="1"]');
        if (shippingRow) {
          target.parentNode.insertBefore(shippingRow, target);
        }
      }
    }
  }

  /* ============================================================
   * 2. Thay thế Cart Block khi giỏ hàng trống
   * ============================================================ */
  function replaceCartIfEmpty() {
    var cartWrapper = document.querySelector('.wp-block-woocommerce-cart');
    if (!cartWrapper || cartWrapper.getAttribute('data-tlh-empty') === '1') return;
    if (cartWrapper.querySelector('.empty-cart')) return; // PHP filter đã xử lý

    var filledBlock = cartWrapper.querySelector('.wp-block-woocommerce-filled-cart-block');
    if (!filledBlock) return;

    // Cart trống khi filled-block bị ẩn hoặc không có items
    var isEmpty = filledBlock.style.display === 'none' ||
                  filledBlock.offsetParent === null ||
                  cartWrapper.querySelector('.wc-block-cart__empty-cart-notice') !== null;

    if (!isEmpty) return;

    // Thay thế toàn bộ Cart Block
    var replacement = document.createElement('div');
    replacement.className = 'wp-block-woocommerce-cart';
    replacement.setAttribute('data-tlh-empty', '1');
    replacement.innerHTML = getEmptyCartHTML();
    cartWrapper.parentNode.replaceChild(replacement, cartWrapper);
  }

  /* ============================================================
   * 3. Polling — mỗi 300ms kiểm tra trạng thái
   * ============================================================ */
  setInterval(function () {
    var wrapper = document.querySelector('.wp-block-woocommerce-cart');
    if (!wrapper) return;

    // Đã thay thế empty rồi → bỏ qua
    if (wrapper.getAttribute('data-tlh-empty') === '1') return;

    var filledBlock = wrapper.querySelector('.wp-block-woocommerce-filled-cart-block');

    if (filledBlock && filledBlock.offsetParent !== null && filledBlock.style.display !== 'none') {
      // 🟢 Cart có sản phẩm — chèn shipping row
      injectShippingRow();
    } else {
      // 🔴 Cart trống — thay thế toàn bộ
      replaceCartIfEmpty();
    }
  }, 400);

  /* ============================================================
   * 4. Khởi tạo lần đầu
   * ============================================================ */
  function init() {
    replaceCartIfEmpty();
    injectShippingRow();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () { setTimeout(init, 1200); });
  } else {
    setTimeout(init, 1200);
  }
})();

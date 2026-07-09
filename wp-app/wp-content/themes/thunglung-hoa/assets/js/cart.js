/**
 * Cart page enhancements — Cart Block overrides
 *
 * 1. Thay "0₫" của phí vận chuyển thành "MIỄN PHÍ" (in đậm)
 * 2. Khi giỏ hàng trống (kể cả xoá dynamic), hiển thị giao diện custom
 *
 * @package ThungLungHoa
 * @since 1.0.0
 */

(function () {
  'use strict';

  // =========================================================
  // Custom empty cart HTML (giống cart-empty.php)
  // =========================================================
  function getEmptyCartHTML() {
    var shopUrl = typeof wc_store_data !== 'undefined' && wc_store_data.shop_url
      ? wc_store_data.shop_url
      : '/shop/';

    return (
      '<div class="empty-cart">' +
        '<div class="empty-cart-icon">😢</div>' +
        '<h2 class="empty-cart-title">Giỏ hàng của bạn đang trống!</h2>' +
        '<p class="empty-cart-desc">Hãy khám phá bộ sưu tập hoa tươi và chọn cho mình những bó hoa yêu thích nhé.</p>' +
        '<a href="' + shopUrl + '" class="btn btn-accent btn-lg">Mua sắm ngay</a>' +
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
      '</div>'
    );
  }

  // =========================================================
  // 1. "Miễn phí vận chuyển" — format fee row
  // =========================================================
  function formatFreeShipping() {
    document.querySelectorAll('.wc-block-components-totals-item').forEach(function (row) {
      var labelEl = row.querySelector('.wc-block-components-totals-item__label');
      if (!labelEl) return;
      var label = labelEl.textContent.trim();

      if (label.indexOf('Miễn phí vận chuyển') !== -1) {
        var valueEl = row.querySelector('.wc-block-components-totals-item__value');
        if (valueEl) {
          valueEl.innerHTML = '<strong>MIỄN PHÍ</strong>';
        }
      }
    });
  }

  // =========================================================
  // 2. Hiển thị custom empty cart khi Cart Block chuyển state
  // =========================================================
  function checkAndRenderEmptyCart() {
    var emptyBlock = document.querySelector('.wp-block-woocommerce-empty-cart-block');

    // Cart Block đang ở empty state
    if (emptyBlock) {
      // Kiểm tra xem đã có nội dung custom chưa (tránh chèn lại nhiều lần)
      if (!emptyBlock.querySelector('.empty-cart')) {
        emptyBlock.innerHTML = getEmptyCartHTML();
      }
    }
  }

  // =========================================================
  // 3. MutationObserver — phát hiện mọi thay đổi trong Cart Block
  // =========================================================
  var cartContainer = document.querySelector('.wp-block-woocommerce-cart') ||
                      document.querySelector('.wp-block-woocommerce-empty-cart-block') ||
                      document.querySelector('.wp-block-woocommerce-filled-cart-block');

  var observer = new MutationObserver(function () {
    // Luôn kiểm tra empty state
    checkAndRenderEmptyCart();

    // Nếu cart có sản phẩm, format fee
    var itemsBlock = document.querySelector('.wp-block-woocommerce-cart-items-block');
    if (itemsBlock) {
      formatFreeShipping();
    }
  });

  // Observe toàn bộ body để bắt khi Cart Block được React render
  var bodyObserver = new MutationObserver(function () {
    var container = document.querySelector('.wp-block-woocommerce-cart') ||
                    document.querySelector('.wp-block-woocommerce-empty-cart-block') ||
                    document.querySelector('.wp-block-woocommerce-filled-cart-block');
    if (container && !cartContainer) {
      cartContainer = container;
      observer.observe(container, {
        childList: true,
        subtree: true,
        attributes: true,
      });
      // Check sau khi kết nối
      setTimeout(function () {
        checkAndRenderEmptyCart();
        formatFreeShipping();
      }, 300);
    }
  });

  bodyObserver.observe(document.body, {
    childList: true,
    subtree: true,
  });

  // Observer cho Cart Block container nếu đã có
  if (cartContainer) {
    observer.observe(cartContainer, {
      childList: true,
      subtree: true,
      attributes: true,
    });
  }

  // =========================================================
  // 4. Khởi tạo lần đầu
  // =========================================================
  function init() {
    checkAndRenderEmptyCart();
    formatFreeShipping();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () {
      setTimeout(init, 800);
    });
  } else {
    setTimeout(init, 800);
  }
})();

/**
 * Royal Flower Studio — Product Filters (AJAX)
 *
 * - Dropdown toggle cho "Theo Dịp", "Theo Kiểu Dáng"
 * - Thu thập category checkbox + sort value → gửi AJAX
 * - Cập nhật grid sản phẩm, phân trang, đếm
 * - Bắt sự kiện click trên phân trang (AJAX-loaded)
 *
 * @package ThungLungHoa
 */
(function () {
  'use strict';

  /* ========== DOM refs ========== */
  var toolbar   = document.querySelector('.shop-toolbar');
  var grid      = document.querySelector('.grid-3, .grid-4, .grid-2');
  var paginationNav = document.querySelector('.pagination');

  if (!toolbar || !grid) return;

  var currentPage = 1;

  /* ========== Dropdown toggle ========== */
  toolbar.addEventListener('click', function (e) {
    var btn = e.target.closest('.tlh-filter-btn');
    if (!btn) return;

    var panel = btn.nextElementSibling;
    if (!panel || !panel.classList.contains('tlh-filter-panel')) return;

    var isOpen = panel.classList.contains('open');

    // Đóng tất cả dropdown trước
    document.querySelectorAll('.tlh-filter-panel.open').forEach(function (p) {
      p.classList.remove('open');
    });
    document.querySelectorAll('.tlh-filter-btn.active').forEach(function (b) {
      b.classList.remove('active');
    });

    if (!isOpen) {
      panel.classList.add('open');
      btn.classList.add('active');
    }
  });

  /* ========== Đóng dropdown khi click ra ngoài ========== */
  document.addEventListener('click', function (e) {
    if (!e.target.closest('.tlh-filter-dropdown')) {
      document.querySelectorAll('.tlh-filter-panel.open').forEach(function (p) {
        p.classList.remove('open');
      });
      document.querySelectorAll('.tlh-filter-btn.active').forEach(function (b) {
        b.classList.remove('active');
      });
    }
  });

  /* ========== Filter change → AJAX ========== */
  function onFilterChange() {
    currentPage = 1;
    fetchProducts();
  }

  toolbar.addEventListener('change', function (e) {
    if (e.target.closest('.tlh-filter-panel') || e.target.closest('.tlh-sort-select')) {
      onFilterChange();
    }
  });

  /* ========== AJAX: lọc sản phẩm ========== */
  function fetchProducts() {
    // Categories đang check
    var checked = document.querySelectorAll('.tlh-filter-panel input[name="category"]:checked');
    var categories = Array.prototype.map.call(checked, function (cb) { return cb.value; });

    // Sort value
    var sortSelect = document.querySelector('.tlh-sort-select');
    var sortValue = sortSelect ? sortSelect.value : 'date-desc';

    var orderby = 'date';
    var order   = 'DESC';
    switch (sortValue) {
      case 'price-asc':  orderby = 'price'; order = 'ASC';  break;
      case 'price-desc': orderby = 'price'; order = 'DESC'; break;
      case 'popularity': orderby = 'popularity'; order = 'DESC'; break;
    }

    var fd = new FormData();
    fd.append('action',  'tlh_filter_products');
    fd.append('nonce',   tlh_ajax.nonce);
    fd.append('orderby', orderby);
    fd.append('order',   order);
    fd.append('paged',   currentPage);

    categories.forEach(function (slug) {
      fd.append('category[]', slug);
    });

    grid.style.opacity = '0.4';

    fetch(tlh_ajax.ajax_url, { method: 'POST', body: fd })
      .then(function (r) { return r.json(); })
      .then(function (res) {
        if (res.success && res.data) {
          // Cập nhật grid sản phẩm
          grid.innerHTML = res.data.html;

          // Cập nhật text đếm: "Hiển thị X–Y trong Z sản phẩm"
          var countEl = document.querySelector('.tlh-filter-count');
          if (countEl && res.data.count !== undefined) {
            var perPage = 12;
            var from    = (currentPage - 1) * perPage + 1;
            var to      = Math.min(currentPage * perPage, res.data.count);
            countEl.textContent = 'Hiển thị ' + from + '\u2013' + to + ' trong ' + res.data.count + ' sản phẩm';
          }

          // Cập nhật phân trang
          if (paginationNav) {
            if (res.data.pagination) {
              paginationNav.outerHTML = res.data.pagination;
              paginationNav = document.querySelector('.pagination');
            } else {
              paginationNav.innerHTML = '';
            }
          }
        }
        grid.style.opacity = '1';
      })
      .catch(function () {
        grid.style.opacity = '1';
      });
  }

  /* ========== Bắt click phân trang (AJAX-loaded) ========== */
  document.addEventListener('click', function (e) {
    var link = e.target.closest('a.page-numbers');
    if (!link) return;
    if (!link.closest('.pagination')) return;
    if (link.classList.contains('current')) return;

    e.preventDefault();

    var href  = link.getAttribute('href');
    var pageNum = null;

    // Thử format #?page=N (AJAX-loaded)
    var match = href.match(/[?&]page=(\d+)/);
    if (match) {
      pageNum = parseInt(match[1], 10);
    }

    // Thử format /page/N/ (WordPress chuẩn)
    if (!pageNum) {
      match = href.match(/\/page\/(\d+)/);
      if (match) pageNum = parseInt(match[1], 10);
    }

    // Thử class next/prev
    if (!pageNum) {
      if (link.classList.contains('next')) {
        pageNum = currentPage + 1;
      } else if (link.classList.contains('prev')) {
        pageNum = Math.max(1, currentPage - 1);
      }
    }

    if (pageNum) {
      currentPage = pageNum;
      fetchProducts();
    }
  });
})();

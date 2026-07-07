/**
 * Royal Flower Studio — Product Filters (AJAX)
 * Người phụ trách: [Người B]
 */
(function() {
  'use strict';

  const filterForm = document.querySelector('.filter-list');
  const productGrid = document.querySelector('ul.products');

  if (!filterForm || !productGrid) return;

  filterForm.addEventListener('change', function(e) {
    const radio = e.target.closest('input[type="radio"]');
    if (!radio) return;

    const sortValue = radio.value;
    let orderby = 'date';
    let order = 'DESC';

    switch (sortValue) {
      case 'price-asc':
        orderby = 'price';
        order = 'ASC';
        break;
      case 'price-desc':
        orderby = 'price';
        order = 'DESC';
        break;
      case 'popularity':
        orderby = 'popularity';
        order = 'DESC';
        break;
      default:
        orderby = 'date';
        order = 'DESC';
    }

    // Gửi AJAX request
    const formData = new FormData();
    formData.append('action', 'tlh_filter_products');
    formData.append('nonce', tlh_ajax.nonce);
    formData.append('orderby', orderby);
    formData.append('order', order);

    productGrid.style.opacity = '0.5';

    fetch(tlh_ajax.ajax_url, {
      method: 'POST',
      body: formData,
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
      if (data.success && data.data.html) {
        productGrid.innerHTML = data.data.html;
      }
      productGrid.style.opacity = '1';
    })
    .catch(function() {
      productGrid.style.opacity = '1';
    });
  });
})();

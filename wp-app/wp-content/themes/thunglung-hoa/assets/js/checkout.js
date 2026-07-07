/**
 * Thung Lũng Hoa — Checkout interactions
 * Payment method selection, promo code, etc.
 */
(function() {
  'use strict';

  // Payment method selection
  const payMethods = document.querySelectorAll('.pay-method');
  payMethods.forEach(function(method) {
    method.addEventListener('click', function() {
      payMethods.forEach(function(m) { m.classList.remove('selected'); });
      method.classList.add('selected');
      const radio = method.querySelector('input[type="radio"]');
      if (radio) radio.checked = true;
    });
  });
})();

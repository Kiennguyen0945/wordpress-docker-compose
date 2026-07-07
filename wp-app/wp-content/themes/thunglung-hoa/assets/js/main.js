/**
 * Royal Flower Studio — Main JavaScript
 * Global interactions: header scroll, search toggle, mobile menu.
 */
(function() {
  'use strict';

  // Sticky header shadow on scroll
  const header = document.querySelector('.site-header');
  if (header) {
    window.addEventListener('scroll', function() {
      if (window.scrollY > 10) {
        header.style.boxShadow = '0 4px 20px rgba(45,38,34,.08)';
      } else {
        header.style.boxShadow = 'none';
      }
    });
  }

  // Mobile menu toggle (nếu có)
  console.log('Royal Flower Studio — JS loaded');
})();

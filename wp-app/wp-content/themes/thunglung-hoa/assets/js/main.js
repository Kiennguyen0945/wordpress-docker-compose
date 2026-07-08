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

  // Account popover
  const accountMenu = document.querySelector('.account-menu');
  const accountToggle = document.querySelector('.account-toggle');
  const accountPopover = document.querySelector('.account-popover');

  if (accountMenu && accountToggle && accountPopover) {
    const closeAccountPopover = function() {
      accountPopover.hidden = true;
      accountToggle.setAttribute('aria-expanded', 'false');
    };

    accountToggle.addEventListener('click', function(event) {
      event.preventDefault();
      const willOpen = accountPopover.hidden;
      accountPopover.hidden = !willOpen;
      accountToggle.setAttribute('aria-expanded', String(willOpen));
    });

    document.addEventListener('click', function(event) {
      if (!accountMenu.contains(event.target)) {
        closeAccountPopover();
      }
    });

    document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape') {
        closeAccountPopover();
      }
    });
  }

  // Login / Register modal
  const authModal = document.getElementById('auth-modal');
  const authOpenButtons = document.querySelectorAll('[data-auth-open]');
  const authCloseButtons = document.querySelectorAll('[data-auth-close]');
  const authSwitchButtons = document.querySelectorAll('[data-auth-switch]');
  const authPanels = document.querySelectorAll('[data-auth-panel]');

  const showAuthPanel = function(panelName) {
    authPanels.forEach(function(panel) {
      panel.hidden = panel.dataset.authPanel !== panelName;
    });
  };

  const openAuthModal = function(panelName) {
    if (!authModal) {
      return;
    }

    if (accountPopover && accountToggle) {
      accountPopover.hidden = true;
      accountToggle.setAttribute('aria-expanded', 'false');
    }

    showAuthPanel(panelName || 'login');
    authModal.hidden = false;
    document.body.classList.add('auth-modal-open');

    const firstInput = authModal.querySelector('[data-auth-panel]:not([hidden]) input');
    if (firstInput) {
      firstInput.focus();
    }
  };

  const closeAuthModal = function() {
    if (!authModal) {
      return;
    }

    authModal.hidden = true;
    document.body.classList.remove('auth-modal-open');
  };

  authOpenButtons.forEach(function(button) {
    button.addEventListener('click', function(event) {
      event.preventDefault();
      openAuthModal(button.dataset.authOpen);
    });
  });

  authCloseButtons.forEach(function(button) {
    button.addEventListener('click', function() {
      closeAuthModal();
    });
  });

  authSwitchButtons.forEach(function(button) {
    button.addEventListener('click', function(event) {
      event.preventDefault();
      showAuthPanel(button.dataset.authSwitch);
    });
  });

  document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
      closeAuthModal();
    }
  });

  const showFormMessage = function(form, message, type) {
    let messageEl = form.querySelector('.user-form-message');

    if (!messageEl) {
      messageEl = document.createElement('div');
      messageEl.className = 'user-form-message';
      form.prepend(messageEl);
    }

    messageEl.textContent = message;
    messageEl.dataset.type = type;
  };

  const bindAjaxForm = function(form, action, successRedirect) {
    if (!form || typeof tlh_ajax === 'undefined') {
      return;
    }

    form.addEventListener('submit', function(event) {
      event.preventDefault();

      const submitButton = form.querySelector('[type="submit"]');
      const formData = new FormData(form);
      formData.append('action', action);
      formData.append('nonce', tlh_ajax.nonce);

      if (submitButton) {
        submitButton.disabled = true;
        submitButton.dataset.originalText = submitButton.textContent;
        submitButton.textContent = action === 'tlh_update_profile'
          ? 'Đang cập nhật…'
          : action === 'tlh_update_password'
            ? 'Đang đổi mật khẩu…'
            : 'Đang xử lý...';
      }

      fetch(tlh_ajax.ajax_url, {
        method: 'POST',
        credentials: 'same-origin',
        body: formData
      })
        .then(function(response) {
          return response.json();
        })
        .then(function(result) {
          const message = result.data && result.data.message ? result.data.message : 'Có lỗi xảy ra. Vui lòng thử lại.';

          showFormMessage(form, message, result.success ? 'success' : 'error');

          if (result.success && action === 'tlh_update_profile') {
            form.scrollIntoView({ behavior: 'smooth', block: 'center' });
          }

          if (result.success && action === 'tlh_update_profile' && submitButton) {
            submitButton.textContent = 'Đã cập nhật';
          }

          if (result.success && successRedirect) {
            window.location.href = successRedirect;
          }
        })
        .catch(function() {
          showFormMessage(form, 'Không thể kết nối máy chủ. Vui lòng thử lại.', 'error');
        })
        .finally(function() {
          if (submitButton) {
            submitButton.disabled = false;
            submitButton.textContent = submitButton.dataset.originalText || 'Gửi';
          }
        });
    });
  };

  const bindAuthForm = function(form, action) {
    if (!form || typeof tlh_ajax === 'undefined') {
      return;
    }

    form.addEventListener('submit', function(event) {
      event.preventDefault();

      const submitButton = form.querySelector('[type="submit"]');
      const formData = new FormData(form);
      formData.append('action', action);
      formData.append('nonce', tlh_ajax.nonce);

      if (submitButton) {
        submitButton.disabled = true;
        submitButton.dataset.originalText = submitButton.textContent;
        submitButton.textContent = 'Đang xử lý...';
      }

      fetch(tlh_ajax.ajax_url, {
        method: 'POST',
        credentials: 'same-origin',
        body: formData
      })
        .then(function(response) {
          return response.json();
        })
        .then(function(result) {
          const message = result.data && result.data.message ? result.data.message : 'Có lỗi xảy ra. Vui lòng thử lại.';

          showFormMessage(form, message, result.success ? 'success' : 'error');

          if (result.success && result.data && result.data.redirect) {
            window.location.href = result.data.redirect;
          }
        })
        .catch(function() {
          showFormMessage(form, 'Không thể kết nối máy chủ. Vui lòng thử lại.', 'error');
        })
        .finally(function() {
          if (submitButton) {
            submitButton.disabled = false;
            submitButton.textContent = submitButton.dataset.originalText || 'Gửi';
          }
        });
    });
  };

  document.querySelectorAll('[data-auth-action]').forEach(function(form) {
    bindAuthForm(form, form.dataset.authAction);
  });

  bindAjaxForm(document.getElementById('tlh-profile-form'), 'tlh_update_profile');
  bindAjaxForm(document.getElementById('tlh-password-form'), 'tlh_update_password');

  // Mobile menu toggle (nếu có)
  console.log('Royal Flower Studio — JS loaded');
})();

/**
 * The Cochin - Responsive Header Navigation Script
 * Implements sticky header transitions, mobile toggle drawer, and keyboard accessibility.
 */
document.addEventListener('DOMContentLoaded', function () {
  const header = document.querySelector('#siteHeader');
  const toggle = document.querySelector('.menu-toggle');
  const navigation = document.querySelector('#primaryNavigation');
  const mobileQuery = window.matchMedia('(max-width: 980px)');

  if (!toggle || !navigation) {
    return;
  }

  function closeMenu() {
    toggle.setAttribute('aria-expanded', 'false');
    toggle.setAttribute('aria-label', 'Open menu');
    navigation.classList.remove('is-open');
    document.body.classList.remove('menu-open');
  }

  toggle.addEventListener('click', function () {
    const open = toggle.getAttribute('aria-expanded') === 'true';
    toggle.setAttribute('aria-expanded', String(!open));
    toggle.setAttribute('aria-label', open ? 'Open menu' : 'Close menu');
    navigation.classList.toggle('is-open', !open);
    document.body.classList.toggle('menu-open', !open);
  });

  navigation.addEventListener('click', function (event) {
    if (event.target.closest('a') && mobileQuery.matches) {
      closeMenu();
    }
  });

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
      closeMenu();
      toggle.focus();
    }
  });

  if (typeof mobileQuery.addEventListener === 'function') {
    mobileQuery.addEventListener('change', function (event) {
      if (!event.matches) {
        closeMenu();
      }
    });
  } else if (typeof mobileQuery.addListener === 'function') {
    mobileQuery.addListener(function (event) {
      if (!event.matches) {
        closeMenu();
      }
    });
  }

  if (header) {
    const handleScroll = function () {
      header.classList.toggle('is-scrolled', window.scrollY > 24);
    };
    window.addEventListener('scroll', handleScroll, { passive: true });
    handleScroll();
  }

  // ==========================================================================
  // Delivery & Takeaway Partner Modal Logic
  // ==========================================================================
  const orderModal = document.getElementById('cochinOrderModal');
  const orderModalClose = document.getElementById('cochinOrderModalClose');

  function openOrderModal(e) {
    if (e && typeof e.preventDefault === 'function') {
      e.preventDefault();
    }
    if (orderModal) {
      orderModal.style.display = 'flex';
      // Force repaint for transition
      orderModal.offsetHeight;
      orderModal.classList.add('is-active');
      document.body.style.overflow = 'hidden';
      if (orderModalClose) {
        orderModalClose.focus();
      }
    }
  }

  function closeOrderModal() {
    if (orderModal) {
      orderModal.classList.remove('is-active');
      setTimeout(function () {
        orderModal.style.display = 'none';
        document.body.style.overflow = '';
      }, 300);
    }
  }

  // Attach to all elements that open the order modal
  document.addEventListener('click', function (e) {
    const target = e.target;
    // Check if clicked element or its parent matches order trigger
    const trigger = target.closest('[data-open-order-modal], a[href$="#order"], a[href="#order"], .btn-delivery-order, .btn-order-online, .btn-dietary-order, .btn-banquet-order');
    if (trigger) {
      // If it's an internal link or button, open the modal
      openOrderModal(e);
    }

    // Close on backdrop click
    if (target === orderModal) {
      closeOrderModal();
    }
  });

  if (orderModalClose) {
    orderModalClose.addEventListener('click', closeOrderModal);
  }

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && orderModal && orderModal.classList.contains('is-active')) {
      closeOrderModal();
    }
  });
});


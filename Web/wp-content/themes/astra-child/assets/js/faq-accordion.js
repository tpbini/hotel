/**
 * The Cochin - FAQ Accordion Functionality
 *
 * Implements smooth height transitions, icon rotation (+ to ×),
 * and keyboard accessibility.
 */
document.addEventListener('DOMContentLoaded', function () {
  const accordion = document.querySelector('#cochinFaqAccordion');
  if (!accordion) return;

  const cards = accordion.querySelectorAll('.cochin-faq-card');

  // Initialize max-height for any pre-opened items
  cards.forEach((card) => {
    const wrap = card.querySelector('.cochin-faq-answer-wrap');
    if (card.classList.contains('is-active') && wrap) {
      wrap.style.maxHeight = wrap.scrollHeight + 'px';
      wrap.style.opacity = '1';
    }
  });

  accordion.addEventListener('click', function (e) {
    const btn = e.target.closest('.cochin-faq-question');
    if (!btn) return;

    const card = btn.closest('.cochin-faq-card');
    const wrap = card.querySelector('.cochin-faq-answer-wrap');
    if (!wrap) return;

    const isCurrentlyActive = card.classList.contains('is-active');

    if (isCurrentlyActive) {
      // Close this item
      card.classList.remove('is-active');
      btn.setAttribute('aria-expanded', 'false');
      wrap.style.maxHeight = '0px';
      wrap.style.opacity = '0';
    } else {
      // Open this item
      card.classList.add('is-active');
      btn.setAttribute('aria-expanded', 'true');
      wrap.style.maxHeight = wrap.scrollHeight + 'px';
      wrap.style.opacity = '1';
    }
  });

  // Recalculate open heights on window resize for responsive accuracy
  window.addEventListener('resize', function () {
    cards.forEach((card) => {
      if (card.classList.contains('is-active')) {
        const wrap = card.querySelector('.cochin-faq-answer-wrap');
        if (wrap) {
          wrap.style.maxHeight = wrap.scrollHeight + 'px';
        }
      }
    });
  });
});

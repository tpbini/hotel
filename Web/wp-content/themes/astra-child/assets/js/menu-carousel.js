/**
 * The Cochin - Homepage Menu Carousel
 *
 * Implements seamless continuous auto-scroll from right to left,
 * arrow navigation, touch swipe, and pause on mouse hover.
 */
document.addEventListener('DOMContentLoaded', function () {
  const track = document.querySelector('#menuCarouselTrack');
  const prevBtn = document.querySelector('.cochin-carousel-arrow.arrow-prev');
  const nextBtn = document.querySelector('.cochin-carousel-arrow.arrow-next');
  const section = document.querySelector('.cochin-menu-carousel-section');

  if (!track) return;

  // Clone children for seamless infinite loop
  const originalCards = Array.from(track.children);
  if (originalCards.length > 0) {
    originalCards.forEach((card) => {
      const clone = card.cloneNode(true);
      clone.setAttribute('aria-hidden', 'true');
      track.appendChild(clone);
    });
  }

  let isPaused = false;
  let isDragging = false;
  let startX = 0;
  let scrollLeft = 0;
  let autoScrollTimer = null;
  const scrollSpeed = 0.85; // Pixels per tick for smooth continuous movement

  function getStep() {
    const card = track.querySelector('.cochin-menu-card');
    if (card) {
      const style = window.getComputedStyle(track);
      const gap = parseFloat(style.gap) || 24;
      return card.offsetWidth + gap;
    }
    return 300;
  }

  function getHalfWidth() {
    return track.scrollWidth / 2;
  }

  // Smooth continuous auto-scroll
  function stepScroll() {
    if (!isPaused && !isDragging) {
      track.scrollLeft += scrollSpeed;
      const half = getHalfWidth();
      if (half > 0 && track.scrollLeft >= half) {
        track.scrollLeft -= half;
      }
    }
    autoScrollTimer = requestAnimationFrame(stepScroll);
  }

  autoScrollTimer = requestAnimationFrame(stepScroll);

  // Pause on hover
  if (section) {
    section.addEventListener('mouseenter', () => { isPaused = true; });
    section.addEventListener('mouseleave', () => { isPaused = false; });
  }

  // Next / Prev Arrow Navigation
  if (nextBtn) {
    nextBtn.addEventListener('click', (e) => {
      e.preventDefault();
      isPaused = true;
      const step = getStep();
      const half = getHalfWidth();
      track.scrollBy({ left: step, behavior: 'smooth' });
      setTimeout(() => {
        if (half > 0 && track.scrollLeft >= half) {
          track.scrollLeft -= half;
        }
        isPaused = false;
      }, 800);
    });
  }

  if (prevBtn) {
    prevBtn.addEventListener('click', (e) => {
      e.preventDefault();
      isPaused = true;
      const step = getStep();
      const half = getHalfWidth();
      if (track.scrollLeft <= 0 && half > 0) {
        track.scrollLeft = half;
      }
      track.scrollBy({ left: -step, behavior: 'smooth' });
      setTimeout(() => { isPaused = false; }, 800);
    });
  }

  // Touch & Mouse Drag Handlers
  track.addEventListener('mousedown', (e) => {
    isDragging = true;
    isPaused = true;
    startX = e.pageX - track.offsetLeft;
    scrollLeft = track.scrollLeft;
    track.style.cursor = 'grabbing';
  });

  window.addEventListener('mouseup', () => {
    if (isDragging) {
      isDragging = false;
      track.style.cursor = '';
      setTimeout(() => { isPaused = false; }, 1500);
    }
  });

  track.addEventListener('mousemove', (e) => {
    if (!isDragging) return;
    e.preventDefault();
    const x = e.pageX - track.offsetLeft;
    const walk = (x - startX) * 1.5;
    track.scrollLeft = scrollLeft - walk;
    const half = getHalfWidth();
    if (half > 0) {
      if (track.scrollLeft >= half) {
        track.scrollLeft -= half;
      } else if (track.scrollLeft <= 0) {
        track.scrollLeft += half;
      }
    }
  });

  track.addEventListener('touchstart', () => {
    isPaused = true;
  }, { passive: true });

  track.addEventListener('touchend', () => {
    setTimeout(() => { isPaused = false; }, 2000);
  }, { passive: true });
});

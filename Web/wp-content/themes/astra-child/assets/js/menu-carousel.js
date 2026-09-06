/**
 * The Cochin - Homepage Menu Carousel
 *
 * Implements smooth auto-scroll from right to left, arrow navigation,
 * touch swipe, and pause on mouse hover.
 */
document.addEventListener('DOMContentLoaded', function () {
  const track = document.querySelector('#menuCarouselTrack');
  const prevBtn = document.querySelector('.cochin-carousel-arrow.arrow-prev');
  const nextBtn = document.querySelector('.cochin-carousel-arrow.arrow-next');
  const section = document.querySelector('.cochin-menu-carousel-section');

  if (!track) return;

  let isPaused = false;
  let isDragging = false;
  let startX = 0;
  let scrollLeft = 0;
  let autoScrollTimer = null;
  const scrollSpeed = 1.0; // Pixels per tick for smooth continuous movement

  function getStep() {
    const card = track.querySelector('.cochin-menu-card');
    if (card) {
      return card.offsetWidth + 24; // card width + gap
    }
    return 300;
  }

  // Smooth continuous auto-scroll
  function stepScroll() {
    if (!isPaused && !isDragging) {
      track.scrollLeft += scrollSpeed;
      // Loop smoothly if scrolled to the very end
      if (track.scrollLeft >= track.scrollWidth - track.clientWidth - 2) {
        track.scrollLeft = 0;
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
      track.scrollBy({ left: step, behavior: 'smooth' });
      setTimeout(() => { isPaused = false; }, 2500);
    });
  }

  if (prevBtn) {
    prevBtn.addEventListener('click', (e) => {
      e.preventDefault();
      isPaused = true;
      const step = getStep();
      track.scrollBy({ left: -step, behavior: 'smooth' });
      setTimeout(() => { isPaused = false; }, 2500);
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
  });

  track.addEventListener('touchstart', () => {
    isPaused = true;
  }, { passive: true });

  track.addEventListener('touchend', () => {
    setTimeout(() => { isPaused = false; }, 2000);
  }, { passive: true });
});

/**
 * The Cochin - About Page Tabs
 * Handles interactive tabs for "Our Specialties" section with keyboard accessibility.
 */
document.addEventListener('DOMContentLoaded', function() {
    var tabButtons = document.querySelectorAll('.about-tab-btn');
    var tabPanes = document.querySelectorAll('.about-tab-pane');

    if (!tabButtons.length || !tabPanes.length) {
        return;
    }

    tabButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            var targetId = this.getAttribute('data-target');

            // Deactivate all buttons
            tabButtons.forEach(function(btn) {
                btn.classList.remove('is-active');
                btn.setAttribute('aria-selected', 'false');
            });

            // Hide all panes
            tabPanes.forEach(function(pane) {
                pane.classList.remove('is-active');
                pane.setAttribute('hidden', '');
            });

            // Activate clicked button
            this.classList.add('is-active');
            this.setAttribute('aria-selected', 'true');

            // Show target pane
            var targetPane = document.getElementById(targetId);
            if (targetPane) {
                targetPane.classList.add('is-active');
                targetPane.removeAttribute('hidden');
            }
        });

        // Keyboard navigation support
        button.addEventListener('keydown', function(e) {
            var buttonsArray = Array.from(tabButtons);
            var index = buttonsArray.indexOf(this);

            if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
                e.preventDefault();
                var nextIndex = (index + 1) % buttonsArray.length;
                buttonsArray[nextIndex].focus();
                buttonsArray[nextIndex].click();
            } else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
                e.preventDefault();
                var prevIndex = (index - 1 + buttonsArray.length) % buttonsArray.length;
                buttonsArray[prevIndex].focus();
                buttonsArray[prevIndex].click();
            }
        });
    });
});

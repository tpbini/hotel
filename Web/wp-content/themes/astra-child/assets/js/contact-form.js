/**
 * The Cochin - Contact & Inquiry Interactive Form Engine
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        initContactForm();
    });

    function initContactForm() {
        const form = document.getElementById('theCochinContactForm');
        const btn = document.getElementById('contactSubmitBtn');
        const feedback = document.getElementById('contactFormFeedback');

        if (!form || !btn || !feedback) return;

        // Auto format UK phone number input
        const phoneInput = document.getElementById('contact_phone');
        if (phoneInput) {
            phoneInput.addEventListener('input', function() {
                let val = this.value.replace(/[^0-9+]/g, '');
                if (val.startsWith('+447') && val.length > 4) {
                    val = '07' + val.substring(4);
                }
                if (val.length === 11 && (val.startsWith('07') || val.startsWith('01') || val.startsWith('02'))) {
                    this.value = val.substring(0, 5) + ' ' + val.substring(5);
                }
            });
        }

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            btn.disabled = true;
            const originalBtnHtml = btn.innerHTML;
            btn.innerHTML = '<span class="cochin-contact-spinner"></span> Sending Message...';

            feedback.style.display = 'none';
            feedback.className = 'cochin-contact-feedback';
            feedback.innerHTML = '';

            const formData = new FormData(form);

            fetch(window.cochinContactData ? window.cochinContactData.ajaxUrl : '/wp-admin/admin-ajax.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = originalBtnHtml;
                feedback.style.display = 'block';

                if (data && data.success) {
                    feedback.className = 'cochin-contact-feedback success';
                    feedback.innerHTML = `
                        <div class="feedback-icon">✓</div>
                        <div class="feedback-text">
                            <strong>Message Sent Successfully!</strong>
                            <p>${escapeHtml(data.data && data.data.message ? data.data.message : 'Thank you for reaching out to The Cochin. Our team will review your message and get back to you shortly.')}</p>
                        </div>
                    `;
                    form.reset();
                } else {
                    feedback.className = 'cochin-contact-feedback error';
                    feedback.innerHTML = `
                        <div class="feedback-icon">⚠️</div>
                        <div class="feedback-text">
                            <strong>Unable to Send Message:</strong>
                            <p>${escapeHtml(data.data && data.data.message ? data.data.message : 'Please check your inputs and try again, or telephone us on 01442 233777.')}</p>
                        </div>
                    `;
                }

                // Smooth scroll to feedback
                feedback.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = originalBtnHtml;
                feedback.style.display = 'block';
                feedback.className = 'cochin-contact-feedback error';
                feedback.innerHTML = `
                    <div class="feedback-icon">⚠️</div>
                    <div class="feedback-text">
                        <strong>Network Notice:</strong>
                        <p>Could not connect to the server. Please call our restaurant directly on <a href="tel:01442233777">01442 233777</a>.</p>
                    </div>
                `;
            });
        });
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

})(jQuery);

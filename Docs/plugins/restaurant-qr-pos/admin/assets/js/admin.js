/**
 * Restaurant QR POS Admin Helpers.
 */
(function($) {
    'use strict';
    $(document).ready(function() {
        // Simple QR Badge Print
        $('.ro-print-qr-btn').on('click', function(e) {
            e.preventDefault();
            const qrContainer = $(this).closest('.ro-qr-card').html();
            const printWin = window.open('', '', 'width=600,height=700');
            printWin.document.write('<html><head><title>Print Table QR</title><style>body{text-align:center;font-family:sans-serif;padding:40px;}</style></head><body>' + qrContainer + '</body></html>');
            printWin.document.close();
            printWin.focus();
            printWin.print();
            printWin.close();
        });
    });
})(jQuery);

// public/js/app.js
// Alpine.js sudah di-load via CDN di blade, tidak perlu import lagi.
// Tambahkan custom JavaScript kamu di sini jika diperlukan.

document.addEventListener('DOMContentLoaded', function () {
    // Contoh: auto-close alert setelah 5 detik (backup jika Alpine tidak jalan)
    const alerts = document.querySelectorAll('[data-auto-close]');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            alert.style.display = 'none';
        }, 5000);
    });
});
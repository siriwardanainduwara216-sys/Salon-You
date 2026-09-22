// frontend-java-script/theme-toggle.js
// Handles the site-wide dark/light mode toggle.
// Preference is saved in localStorage so it stays the same across pages and visits.

(function () {
    const STORAGE_KEY = 'salon-you-theme'; // 'dark' or 'light'

    // Apply saved theme as early as possible (before the page fully paints)
    function applySavedTheme() {
        const saved = localStorage.getItem(STORAGE_KEY);
        if (saved === 'dark') {
            document.body.classList.add('dark-mode');
        }
    }

    function updateToggleIcon() {
        const btn = document.getElementById('theme-toggle-btn');
        if (!btn) return;
        const isDark = document.body.classList.contains('dark-mode');
        btn.innerHTML = isDark
            ? '<i class="fas fa-sun"></i>'
            : '<i class="fas fa-moon"></i>';
        btn.setAttribute('aria-label', isDark ? 'Switch to light mode' : 'Switch to dark mode');
    }

    function toggleTheme() {
        const isDark = document.body.classList.toggle('dark-mode');
        localStorage.setItem(STORAGE_KEY, isDark ? 'dark' : 'light');
        updateToggleIcon();
    }

    document.addEventListener('DOMContentLoaded', function () {
        applySavedTheme();
        updateToggleIcon();

        const btn = document.getElementById('theme-toggle-btn');
        if (btn) {
            btn.addEventListener('click', toggleTheme);
        }
    });
})();
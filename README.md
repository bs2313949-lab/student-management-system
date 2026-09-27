document.addEventListener('DOMContentLoaded', () => {
    const themeToggle = document.getElementById('themeToggle');
    if (themeToggle) {
        const applyTheme = (theme) => {
            document.body.classList.toggle('light', theme === 'light');
            themeToggle.textContent = theme === 'light' ? '🌙' : '☀️';
        };

        const savedTheme = document.body.classList.contains('light') ? 'light' : 'dark';
        applyTheme(savedTheme);

        themeToggle.addEventListener('click', () => {
            const nextTheme = document.body.classList.contains('light') ? 'dark' : 'light';
            document.body.classList.toggle('light', nextTheme === 'light');
            themeToggle.textContent = nextTheme === 'light' ? '🌙' : '☀️';
            document.cookie = `theme=${nextTheme}; path=/; max-age=${60 * 60 * 24 * 365}`;
        });
    }
});

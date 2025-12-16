/**
 * Theme Toggle - Handles dark/light mode switching
 */

class ThemeToggle {
    constructor() {
        this.init();
    }

    init() {
        this.loadTheme();
        this.bindEventListeners();
    }

    loadTheme() {
        const isDarkMode = localStorage.getItem('dark-mode') === 'true' || 
            (!localStorage.getItem('dark-mode') && window.matchMedia('(prefers-color-scheme: dark)').matches);
        
        if (isDarkMode) {
            document.documentElement.classList.add('dark');
            localStorage.setItem('dark-mode', 'true');
        } else {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('dark-mode', 'false');
        }
    }

    bindEventListeners() {
        $('#theme-toggle').on('click', () => this.toggleTheme());
    }

    toggleTheme() {
        const html = document.documentElement;
        html.classList.toggle('dark');
        const isDark = html.classList.contains('dark');
        localStorage.setItem('dark-mode', isDark);
    }
}

export default ThemeToggle;


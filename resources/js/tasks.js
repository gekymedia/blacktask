/**
 * Tasks Page - Main entry point for task management
 */

import TaskManager from './components/taskManager.js';
import ThemeToggle from './components/themeToggle.js';

// Initialize when DOM is ready
$(document).ready(function() {
    // Get CSRF token from meta tag or create one
    const csrfToken = $('meta[name="csrf-token"]').attr('content') || 
                     $('input[name="_token"]').val();

    // Initialize theme toggle
    new ThemeToggle();

    // Initialize task manager
    new TaskManager(csrfToken);

    // Register service worker for PWA support
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js')
            .then(registration => console.log('SW registered:', registration))
            .catch(err => console.log('SW registration failed:', err));
    }
});


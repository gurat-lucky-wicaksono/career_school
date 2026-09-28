import './bootstrap';
import { createIcons, icons } from 'lucide';

// Helper to initialize or re-render Lucide SVG icons in dynamic DOM
window.lucide = {
    createIcons: () => createIcons({ icons }),
};

// Theme toggle helper according to docs/design.md Section 10
window.initTheme = function () {
    const savedTheme = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
};

window.toggleTheme = function () {
    const isDark = document.documentElement.classList.toggle('dark');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
};

// Run theme check immediately
window.initTheme();

// Choices.js helper for custom dropdowns
window.initChoiceSelect = function (el, customOptions = {}) {
    if (!window.Choices || !el || el._choicesInstance) return el?._choicesInstance || null;

    const isCompact = el.classList.contains('choices-compact') || (customOptions.compact === true);
    const defaultOptions = {
        searchEnabled: false,
        itemSelectText: '',
        shouldSort: false,
        placeholder: true,
        allowHTML: false,
        position: 'bottom',
        classNames: {
            containerOuter: isCompact ? 'choices choices--compact' : 'choices',
        },
    };

    const instance = new window.Choices(el, Object.assign({}, defaultOptions, customOptions));
    el._choicesInstance = instance;

    // Ensure event propagates smoothly to Alpine.js & vanilla listeners
    el.addEventListener('change', () => {
        el.dispatchEvent(new Event('input', { bubbles: true }));
    });

    return instance;
};

window.initAutoChoices = function () {
    if (!window.Choices) return;
    document.querySelectorAll('select.choices-select, select[data-choice]').forEach(el => {
        window.initChoiceSelect(el);
    });
};

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    createIcons({ icons });
    window.initAutoChoices();
});

// Re-initialize icons and choices after Livewire updates/navigations
document.addEventListener('livewire:navigated', () => {
    createIcons({ icons });
    window.initAutoChoices();
});


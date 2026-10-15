import './bootstrap';
import Alpine from 'alpinejs';
import ApexCharts from 'apexcharts';
import flatpickr from 'flatpickr';
import Swal from 'sweetalert2';
import { createIcons, icons } from 'lucide';

window.Alpine = Alpine;
window.ApexCharts = ApexCharts;
window.flatpickr = flatpickr;
window.Swal = Swal;

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
    document.documentElement.classList.add('theme-transitioning');
    const isDark = document.documentElement.classList.toggle('dark');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
    // Dispatch custom event so ApexCharts and other reactive elements update immediately
    window.dispatchEvent(new CustomEvent('theme-changed', { detail: { isDark } }));
    setTimeout(() => {
        document.documentElement.classList.remove('theme-transitioning');
    }, 250);
};

// Run theme check immediately
window.initTheme();

// Start Alpine.js
Alpine.start();

// Global Keyboard Shortcuts: Ctrl+B for Sidebar Collapse, Ctrl+K for Search Focus
document.addEventListener('keydown', (e) => {
    // Ctrl + B or Cmd + B -> Toggle Sidebar
    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'b') {
        e.preventDefault();
        window.dispatchEvent(new CustomEvent('toggle-sidebar'));
    }

    // Ctrl + K or Cmd + K -> Focus Global Search
    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
        e.preventDefault();
        const searchInput = document.getElementById('global-search-input');
        if (searchInput) {
            searchInput.focus();
            searchInput.select();
        }
    }
});

// Choices.js helper for custom dropdowns
window.initChoiceSelect = function (el, customOptions = {}) {
    if (!window.Choices || !el || el._choicesInstance) return el?._choicesInstance || null;

    const defaultOptions = {
        searchEnabled: false,
        itemSelectText: '',
        shouldSort: false,
        placeholder: true,
        allowHTML: false,
    };

    const instance = new window.Choices(el, Object.assign({}, defaultOptions, customOptions));
    el._choicesInstance = instance;

    if (el.classList.contains('choices-compact') || customOptions.compact) {
        instance.containerOuter?.element?.classList.add('choices--compact');
    }

    // Ensure event propagates smoothly to Alpine.js & vanilla listeners
    el.addEventListener('change', () => {
        el.dispatchEvent(new Event('input', { bubbles: true }));
    });

    return instance;
};

window.initAutoChoices = function (container = document) {
    if (!window.Choices || !container) return;
    const targets = container.querySelectorAll ? container.querySelectorAll('select.choices-select, select[data-choice]') : [];
    targets.forEach(el => {
        window.initChoiceSelect(el);
    });
};

// Helper to safely set value on Choices.js or standard Select elements
window.setSelectValue = function (elOrId, value) {
    const el = typeof elOrId === 'string' ? document.getElementById(elOrId) : elOrId;
    if (!el) return;
    if (el._choicesInstance) {
        el._choicesInstance.setChoiceByValue(String(value ?? ''));
    } else {
        el.value = value;
    }
};

// Flatpickr auto-initializer
window.initDatePicker = function (el, customOptions = {}) {
    if (!window.flatpickr || !el) return null;
    const defaultOptions = {
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'j F Y',
        locale: {
            firstDayOfWeek: 1,
        },
    };
    return window.flatpickr(el, Object.assign({}, defaultOptions, customOptions));
};

// Theme-Aware SweetAlert2 Configuration & Helpers (Zero White Popup in Dark Mode)
window.getSwalThemeConfig = function () {
    const isDark = document.documentElement.classList.contains('dark');
    return {
        background: isDark ? '#1E1E1E' : '#FFFFFF',
        color: isDark ? '#F7F8F3' : '#1E1E1E',
        customClass: {
            popup: 'rounded-2xl border border-slate-200/80 dark:border-white/10 shadow-2xl font-sans p-6 bg-surface-card',
            title: 'text-lg font-bold font-sans text-slate-900 dark:text-white',
            htmlContainer: 'text-sm text-slate-600 dark:text-slate-300 mt-2 leading-relaxed',
            actions: 'mt-6 gap-3 flex items-center justify-end',
            confirmButton: 'px-5 py-2.5 text-sm font-semibold rounded-xl bg-brand-primary text-neutral-900 hover:brightness-105 transition-all shadow-sm focus:outline-none',
            cancelButton: 'px-4 py-2.5 text-sm font-semibold rounded-xl border border-slate-200 dark:border-white/10 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-neutral-800 transition-colors focus:outline-none',
        },
        buttonsStyling: false,
    };
};

window.notifyAlert = function ({
    title = 'Informasi',
    text = '',
    icon = 'info',
    confirmButtonText = 'Mengerti',
}) {
    if (!window.Swal) return;
    const theme = window.getSwalThemeConfig();
    return Swal.fire({
        ...theme,
        title,
        text,
        icon,
        confirmButtonText,
    });
};

window.confirmAction = function ({
    title = 'Konfirmasi Tindakan',
    text = 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
    confirmButtonText = 'Ya, Lanjutkan',
    cancelButtonText = 'Batal',
    isDanger = false,
    onConfirm = () => {},
}) {
    if (!window.Swal) return;
    const theme = window.getSwalThemeConfig();
    if (isDanger) {
        theme.customClass.confirmButton = 'px-5 py-2.5 text-sm font-semibold rounded-xl bg-red-600 hover:bg-red-700 text-white transition-colors shadow-sm focus:outline-none';
    }
    return Swal.fire({
        ...theme,
        title,
        text,
        icon: isDanger ? 'warning' : 'question',
        showCancelButton: true,
        confirmButtonText,
        cancelButtonText,
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            onConfirm();
        }
    });
};

// ApexCharts helper matching iOS minimalist aesthetic (predominantly young lime green with clear contrast)
window.createSchoolChart = function (el, options = {}) {
    if (!el || !window.ApexCharts) return null;

    function getThemeSettings(isDark) {
        return {
            chart: {
                fontFamily: 'Plus Jakarta Sans, sans-serif',
                toolbar: { show: false },
                background: 'transparent',
                foreColor: isDark ? '#A0A0A0' : '#64748B',
            },
            grid: {
                borderColor: isDark ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 0, 0, 0.06)',
                strokeDashArray: 4,
                padding: { top: 10, right: 12, bottom: 0, left: 12 }
            },
            tooltip: {
                theme: isDark ? 'dark' : 'light',
                x: { show: true },
            },
        };
    }

    const isDark = document.documentElement.classList.contains('dark');
    const baseTheme = getThemeSettings(isDark);

    // Default palette: Signature Young Lime Green (#C2F970 / #A3E635), Contrasting Deep Pine (#166E4F), and Neutral Slate (#94A3B8)
    const defaultOptions = {
        chart: baseTheme.chart,
        colors: ['#A3E635', '#166E4F', '#94A3B8'],
        stroke: {
            curve: 'smooth',
            width: 2.5,
        },
        grid: baseTheme.grid,
        tooltip: baseTheme.tooltip,
    };

    const finalOptions = Object.assign({}, defaultOptions, options);
    if (options.chart) finalOptions.chart = Object.assign({}, defaultOptions.chart, options.chart);
    if (options.grid) finalOptions.grid = Object.assign({}, defaultOptions.grid, options.grid);
    if (options.tooltip) finalOptions.tooltip = Object.assign({}, defaultOptions.tooltip, options.tooltip);

    const chart = new ApexCharts(el, finalOptions);
    chart.render();

    // Listen to theme toggle event so chart updates dynamically between light & dark mode
    window.addEventListener('theme-changed', (e) => {
        const darkNow = e.detail?.isDark ?? document.documentElement.classList.contains('dark');
        const updated = getThemeSettings(darkNow);
        chart.updateOptions({
            chart: {
                foreColor: updated.chart.foreColor,
            },
            grid: {
                borderColor: updated.grid.borderColor,
            },
            tooltip: {
                theme: updated.tooltip.theme,
            }
        }, false, true);
    });

    return chart;
};

// Initialize on DOM ready or immediate if already interactive
const initAppComponents = () => {
    if (window.lucide && window.lucide.createIcons) {
        window.lucide.createIcons();
    }
    if (window.initAutoChoices) {
        window.initAutoChoices();
    }
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAppComponents);
} else {
    initAppComponents();
}

// Re-initialize icons and choices after Livewire updates/navigations
document.addEventListener('livewire:navigated', initAppComponents);

// Confirmation dialog for Sidebar Logout
window.confirmSidebarLogout = function () {
    if (window.confirmAction) {
        window.confirmAction({
            title: 'Konfirmasi Keluar?',
            text: 'Apakah Anda yakin ingin mengakhiri sesi kerja saat ini?',
            confirmButtonText: 'Ya, Keluar Akun',
            cancelButtonText: 'Batal',
            isDanger: true,
            onConfirm: function () {
                const form = document.getElementById('sidebar-logout-form');
                if (form) form.submit();
            }
        });
    } else {
        const form = document.getElementById('sidebar-logout-form');
        if (form && confirm('Apakah Anda yakin ingin keluar?')) {
            form.submit();
        }
    }
};

// Global Reusable Modal Helpers
window.openModal = function (id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
    if (window.lucide && window.lucide.createIcons) {
        window.lucide.createIcons();
    }
    // Automatically initialize Choices.js dropdowns inside this modal
    setTimeout(() => {
        if (window.initAutoChoices) {
            window.initAutoChoices(el);
        }
    }, 50);
};

window.closeModal = function (id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.classList.add('hidden');
    const openModals = document.querySelectorAll('[role="dialog"]:not(.hidden)');
    if (openModals.length === 0) {
        document.body.classList.remove('overflow-hidden');
    }
};

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        const openModals = document.querySelectorAll('[role="dialog"]:not(.hidden)');
        openModals.forEach((modal) => {
            window.closeModal(modal.id);
        });
    }
});

// Global SweetAlert2 Powered Delete Action
window.submitDeleteForm = function (actionUrl, entityTitle = 'data ini') {
    if (window.confirmAction) {
        window.confirmAction({
            title: 'Hapus ' + entityTitle + '?',
            text: 'Data ' + entityTitle + ' akan dihapus permanen dari sistem.',
            confirmButtonText: 'Ya, Hapus Data',
            cancelButtonText: 'Batal',
            isDanger: true,
            onConfirm: function () {
                let deleteForm = document.getElementById('global-delete-form');
                if (!deleteForm) {
                    deleteForm = document.createElement('form');
                    deleteForm.id = 'global-delete-form';
                    deleteForm.method = 'POST';
                    deleteForm.style.display = 'none';

                    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                    deleteForm.innerHTML = `
                        <input type="hidden" name="_token" value="${csrf}">
                        <input type="hidden" name="_method" value="DELETE">
                    `;
                    document.body.appendChild(deleteForm);
                }
                deleteForm.action = actionUrl;
                deleteForm.submit();
            }
        });
    } else {
        if (confirm('Apakah Anda yakin ingin menghapus ' + entityTitle + '?')) {
            let deleteForm = document.getElementById('global-delete-form');
            if (!deleteForm) {
                deleteForm = document.createElement('form');
                deleteForm.id = 'global-delete-form';
                deleteForm.method = 'POST';
                deleteForm.style.display = 'none';
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                deleteForm.innerHTML = `
                    <input type="hidden" name="_token" value="${csrf}">
                    <input type="hidden" name="_method" value="DELETE">
                `;
                document.body.appendChild(deleteForm);
            }
            deleteForm.action = actionUrl;
            deleteForm.submit();
        }
    }
};



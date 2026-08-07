import './bootstrap';

import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import focus from '@alpinejs/focus';
import persist from '@alpinejs/persist';

import menuTree from './components/menu-tree';
import permissionMatrix from './components/permission-matrix';
import iconPicker from './components/icon-picker';
import questionBuilder from './components/question-builder';
import lessonItemBuilder from './components/lesson-item-builder';
import { initCharts } from './components/charts';

Alpine.plugin(collapse);
Alpine.plugin(focus);
Alpine.plugin(persist);

/* Shared UI state -------------------------------------------------------- */

Alpine.store('ui', {
    sidebarOpen: false,
    theme: Alpine.$persist('system').as('ui.theme'),

    init() {
        this.applyTheme();

        window
            .matchMedia('(prefers-color-scheme: dark)')
            .addEventListener('change', () => this.theme === 'system' && this.applyTheme());
    },

    setTheme(theme) {
        this.theme = theme;
        this.applyTheme();
    },

    applyTheme() {
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const dark = this.theme === 'dark' || (this.theme === 'system' && prefersDark);

        document.documentElement.classList.toggle('dark', dark);
    },
});

/* Toast notifications ---------------------------------------------------- */

Alpine.store('toasts', {
    items: [],

    push(message, type = 'success', timeout = 4000) {
        const id = Date.now() + Math.random();

        this.items.push({ id, message, type });

        setTimeout(() => this.remove(id), timeout);
    },

    remove(id) {
        this.items = this.items.filter((toast) => toast.id !== id);
    },
});

/* Admin components ------------------------------------------------------- */

Alpine.data('menuTree', menuTree);
Alpine.data('permissionMatrix', permissionMatrix);
Alpine.data('iconPicker', iconPicker);
Alpine.data('questionBuilder', questionBuilder);
Alpine.data('lessonItemBuilder', lessonItemBuilder);

window.Alpine = Alpine;
Alpine.start();

document.addEventListener('DOMContentLoaded', initCharts);

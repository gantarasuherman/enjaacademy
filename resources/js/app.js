import './bootstrap';

import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import focus from '@alpinejs/focus';
import persist from '@alpinejs/persist';

import menuTree from './components/menu-tree';
import permissionMatrix from './components/permission-matrix';
import iconPicker from './components/icon-picker';
import questionBuilder from './components/question-builder';
import lessonBuilder from './components/lesson-builder';
import videoLessonBuilder from './components/video-lesson-builder';
import featureRoadmap from './components/feature-roadmap';
import { initCharts } from './components/charts';

Alpine.plugin(collapse);
Alpine.plugin(focus);
Alpine.plugin(persist);

/* Shared UI state -------------------------------------------------------- */

Alpine.store('ui', {
    sidebarOpen: false,
    // Desktop-only "minimize to icons" state — mobile always uses the
    // full-width off-canvas sidebar via `sidebarOpen` above, unaffected by this.
    sidebarCollapsed: Alpine.$persist(false).as('ui.sidebarCollapsed'),
    theme: Alpine.$persist('system').as('ui.theme'),

    init() {
        this.applyTheme();

        window
            .matchMedia('(prefers-color-scheme: dark)')
            .addEventListener('change', () => this.theme === 'system' && this.applyTheme());
    },

    toggleSidebarCollapsed() {
        this.sidebarCollapsed = !this.sidebarCollapsed;
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
Alpine.data('lessonBuilder', lessonBuilder);
Alpine.data('videoLessonBuilder', videoLessonBuilder);
Alpine.data('featureRoadmap', featureRoadmap);

window.Alpine = Alpine;
Alpine.start();

document.addEventListener('DOMContentLoaded', initCharts);

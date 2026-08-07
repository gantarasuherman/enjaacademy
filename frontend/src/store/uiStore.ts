import { create } from 'zustand';

export interface Toast {
    id: number;
    message: string;
    variant: 'success' | 'danger' | 'info' | 'warning';
    /** Optional headline shown above the message, e.g. "Level 16!". */
    title?: string;
}

interface UiState {
    sidebarOpen: boolean;
    sidebarCollapsed: boolean;
    searchOpen: boolean;
    toasts: Toast[];

    setSidebarOpen: (open: boolean) => void;
    toggleSidebar: () => void;
    toggleCollapsed: () => void;
    setSearchOpen: (open: boolean) => void;

    toast: (message: string, variant?: Toast['variant'], title?: string) => void;
    dismissToast: (id: number) => void;
}

let toastId = 0;

export const useUiStore = create<UiState>((set, get) => ({
    sidebarOpen: false,
    sidebarCollapsed: localStorage.getItem('ea.sidebarCollapsed') === '1',
    searchOpen: false,
    toasts: [],

    setSidebarOpen: (sidebarOpen) => set({ sidebarOpen }),

    toggleSidebar: () => set((state) => ({ sidebarOpen: !state.sidebarOpen })),

    toggleCollapsed: () => {
        const sidebarCollapsed = !get().sidebarCollapsed;
        localStorage.setItem('ea.sidebarCollapsed', sidebarCollapsed ? '1' : '0');
        set({ sidebarCollapsed });
    },

    setSearchOpen: (searchOpen) => set({ searchOpen }),

    toast: (message, variant = 'success', title) => {
        const id = ++toastId;

        set((state) => ({ toasts: [...state.toasts, { id, message, variant, title }] }));

        setTimeout(() => get().dismissToast(id), 4200);
    },

    dismissToast: (id) => set((state) => ({ toasts: state.toasts.filter((t) => t.id !== id) })),
}));

import { create } from 'zustand';
import { persist } from 'zustand/middleware';

export type Theme = 'light' | 'dark' | 'system';

interface ThemeState {
    theme: Theme;
    setTheme: (theme: Theme) => void;
    toggle: () => void;
    apply: () => void;
}

function resolve(theme: Theme): boolean {
    if (theme === 'system') {
        return window.matchMedia('(prefers-color-scheme: dark)').matches;
    }

    return theme === 'dark';
}

export const useThemeStore = create<ThemeState>()(
    persist(
        (set, get) => ({
            theme: 'system',

            setTheme: (theme) => {
                set({ theme });
                document.documentElement.classList.toggle('dark', resolve(theme));
            },

            toggle: () => {
                const next = resolve(get().theme) ? 'light' : 'dark';
                get().setTheme(next);
            },

            apply: () => {
                document.documentElement.classList.toggle('dark', resolve(get().theme));
            },
        }),
        {
            name: 'ea.theme',
            // The pre-paint script in index.html reads this key directly, so
            // it stores the bare value rather than zustand's wrapper shape.
            storage: {
                getItem: (name) => {
                    const value = localStorage.getItem(name);
                    return value ? { state: { theme: value as Theme }, version: 0 } : null;
                },
                setItem: (name, value) => localStorage.setItem(name, value.state.theme),
                removeItem: (name) => localStorage.removeItem(name),
            },
        },
    ),
);

/** Keeps `system` in sync when the OS preference changes mid-session. */
export function watchSystemTheme(): () => void {
    const media = window.matchMedia('(prefers-color-scheme: dark)');

    const handler = () => {
        if (useThemeStore.getState().theme === 'system') {
            useThemeStore.getState().apply();
        }
    };

    media.addEventListener('change', handler);

    return () => media.removeEventListener('change', handler);
}

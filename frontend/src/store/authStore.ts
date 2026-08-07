import { create } from 'zustand';
import { persist } from 'zustand/middleware';
import type { User } from '@/types';
import { userService } from '@/services/api';

interface AuthState {
    user: User | null;
    token: string | null;
    status: 'idle' | 'loading' | 'authenticated' | 'error';
    error: string | null;

    login: (email: string, password: string) => Promise<boolean>;
    register: (name: string, email: string, password: string) => Promise<boolean>;
    logout: () => Promise<void>;
    refresh: () => Promise<void>;
    patchUser: (patch: Partial<User>) => void;
    clearError: () => void;
}

export const useAuthStore = create<AuthState>()(
    persist(
        (set, get) => ({
            user: null,
            token: null,
            status: 'idle',
            error: null,

            login: async (email, password) => {
                set({ status: 'loading', error: null });

                try {
                    const { user, token } = await userService.login(email, password);
                    set({ user, token, status: 'authenticated' });
                    return true;
                } catch (error) {
                    set({
                        status: 'error',
                        error: error instanceof Error ? error.message : 'Gagal masuk.',
                    });
                    return false;
                }
            },

            register: async (name, email, password) => {
                set({ status: 'loading', error: null });

                try {
                    const { user, token } = await userService.register(name, email, password);
                    set({ user, token, status: 'authenticated' });
                    return true;
                } catch (error) {
                    set({
                        status: 'error',
                        error: error instanceof Error ? error.message : 'Gagal mendaftar.',
                    });
                    return false;
                }
            },

            logout: async () => {
                // Clear locally first so the UI never appears stuck if the
                // network call is slow or the token is already dead.
                set({ user: null, token: null, status: 'idle', error: null });

                try {
                    await userService.logout();
                } catch {
                    /* already signed out locally */
                }
            },

            refresh: async () => {
                if (!get().token) return;

                try {
                    set({ user: await userService.me(), status: 'authenticated' });
                } catch {
                    set({ user: null, token: null, status: 'idle' });
                }
            },

            patchUser: (patch) => {
                const user = get().user;
                if (user) set({ user: { ...user, ...patch } });
            },

            clearError: () => set({ error: null }),
        }),
        {
            name: 'ea.auth',
            partialize: (state) => ({ user: state.user, token: state.token, status: state.status }),
        },
    ),
);

export const selectIsAuthenticated = (state: AuthState) => state.status === 'authenticated' && !!state.user;

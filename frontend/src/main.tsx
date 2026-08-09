import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';
import { RouterProvider } from 'react-router-dom';
import { router } from './app/routes';
import { useThemeStore, watchSystemTheme } from './store/themeStore';
import { useAuthStore } from './store/authStore';
import { tokenStorage } from './services/api/client';
import { ToastViewport } from './components/ui/Feedback';
import './styles/index.css';

// The pre-paint script already set the class; this re-syncs the store with it
// and keeps `system` following the OS for the rest of the session.
useThemeStore.getState().apply();
watchSystemTheme();

/**
 * Consumes the one-shot `?sso=<token>` query param SpaController appends
 * when an already web-session-authenticated admin/teacher/superadmin lands
 * here from the admin panel's "Belajar" link — lets them into the SPA as
 * themselves instead of showing a second login screen. Stripped from the
 * address bar immediately so it never ends up bookmarked or in the browser's
 * visible history.
 */
function consumeSsoToken(): void {
    const params = new URLSearchParams(window.location.search);
    const ssoToken = params.get('sso');

    if (!ssoToken) return;

    tokenStorage.set(ssoToken);
    useAuthStore.setState({ token: ssoToken });

    params.delete('sso');
    const query = params.toString();
    const url = window.location.pathname + (query ? `?${query}` : '') + window.location.hash;
    window.history.replaceState(null, '', url);
}

consumeSsoToken();

// A session persisted from an earlier run (or from mock-mode login, whose
// token never touches the real backend) can look "authenticated" locally
// while every API call 401s. Validate it once against `/me` on boot so a
// dead session gets cleared instead of silently failing every request. Also
// what turns the token `consumeSsoToken()` just stored into an actual
// `user`/`status: 'authenticated'` in the store.
void useAuthStore.getState().refresh();

createRoot(document.getElementById('root')!).render(
    <StrictMode>
        <RouterProvider router={router} />
        <ToastViewport />
    </StrictMode>,
);

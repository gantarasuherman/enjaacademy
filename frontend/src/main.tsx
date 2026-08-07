import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';
import { RouterProvider } from 'react-router-dom';
import { router } from './app/routes';
import { useThemeStore, watchSystemTheme } from './store/themeStore';
import { ToastViewport } from './components/ui/Feedback';
import './styles/index.css';

// The pre-paint script already set the class; this re-syncs the store with it
// and keeps `system` following the OS for the rest of the session.
useThemeStore.getState().apply();
watchSystemTheme();

createRoot(document.getElementById('root')!).render(
    <StrictMode>
        <RouterProvider router={router} />
        <ToastViewport />
    </StrictMode>,
);

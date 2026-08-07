import type { ReactNode } from 'react';
import { Navigate, useLocation } from 'react-router-dom';
import { selectIsAuthenticated, useAuthStore } from '@/store/authStore';

/**
 * Gate for everything under /app. The attempted path is carried in location
 * state so login can send the user straight back to where they were headed.
 */
export function ProtectedRoute({ children }: { children: ReactNode }) {
    const isAuthenticated = useAuthStore(selectIsAuthenticated);
    const location = useLocation();

    if (!isAuthenticated) {
        return <Navigate to="/login" replace state={{ from: location.pathname + location.search }} />;
    }

    return <>{children}</>;
}

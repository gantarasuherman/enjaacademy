import axios, { AxiosError, type AxiosInstance } from 'axios';

/**
 * Single axios instance for the Laravel backend.
 *
 * `withCredentials` is on so Sanctum's cookie-based SPA auth works when the
 * frontend is served from a stateful domain; the bearer-token path works too
 * because the interceptor attaches a token when one is stored.
 */
export const http: AxiosInstance = axios.create({
    baseURL: import.meta.env.VITE_API_BASE_URL ?? '/api',
    withCredentials: true,
    headers: {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    },
    timeout: 15_000,
});

const TOKEN_KEY = 'ea.token';

export const tokenStorage = {
    get: () => localStorage.getItem(TOKEN_KEY),
    set: (token: string) => localStorage.setItem(TOKEN_KEY, token),
    clear: () => localStorage.removeItem(TOKEN_KEY),
};

http.interceptors.request.use((config) => {
    const token = tokenStorage.get();

    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }

    return config;
});

export class ApiError extends Error {
    constructor(
        message: string,
        readonly status: number,
        readonly errors: Record<string, string[]> = {},
    ) {
        super(message);
        this.name = 'ApiError';
    }

    /** First message for a field, for inline form errors. */
    fieldError(field: string): string | undefined {
        return this.errors[field]?.[0];
    }
}

http.interceptors.response.use(
    (response) => response,
    (error: AxiosError<{ message?: string; errors?: Record<string, string[]> }>) => {
        // A 401 means the session or token is gone; drop it so the app can
        // redirect to login instead of retrying with a dead credential.
        if (error.response?.status === 401) {
            tokenStorage.clear();
        }

        throw new ApiError(
            error.response?.data?.message ?? error.message ?? 'Terjadi kesalahan jaringan.',
            error.response?.status ?? 0,
            error.response?.data?.errors ?? {},
        );
    },
);

/** Laravel wraps resources in `{ data: ... }`; unwrap it transparently. */
export function unwrap<T>(payload: T | { data: T }): T {
    if (payload && typeof payload === 'object' && 'data' in payload) {
        return (payload as { data: T }).data;
    }

    return payload as T;
}

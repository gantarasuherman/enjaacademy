import { useCallback, useEffect, useRef, useState } from 'react';

interface AsyncState<T> {
    data: T | null;
    loading: boolean;
    error: string | null;
}

/**
 * Runs an async loader on mount (and whenever `deps` change), guarding against
 * setting state after unmount or after a newer request has superseded this one.
 */
export function useAsync<T>(loader: () => Promise<T>, deps: unknown[] = []) {
    const [state, setState] = useState<AsyncState<T>>({ data: null, loading: true, error: null });
    const requestId = useRef(0);

    const run = useCallback(async () => {
        const id = ++requestId.current;

        setState((prev) => ({ ...prev, loading: true, error: null }));

        try {
            const data = await loader();

            if (id === requestId.current) {
                setState({ data, loading: false, error: null });
            }
        } catch (error) {
            if (id === requestId.current) {
                setState({
                    data: null,
                    loading: false,
                    error: error instanceof Error ? error.message : 'Gagal memuat data.',
                });
            }
        }
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, deps);

    useEffect(() => {
        void run();

        return () => {
            // Invalidate any in-flight request belonging to this effect run.
            requestId.current++;
        };
    }, [run]);

    return { ...state, reload: run };
}

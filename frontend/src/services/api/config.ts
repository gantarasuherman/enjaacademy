/**
 * Data-source switch.
 *
 * Every service exports the same signature under both modes, so flipping
 * `VITE_DATA_SOURCE` to `api` is the only change needed to move from the
 * bundled JSON to the live Laravel backend.
 */
export type DataSource = 'mock' | 'api';

export const DATA_SOURCE: DataSource =
    (import.meta.env.VITE_DATA_SOURCE as DataSource | undefined) ?? 'mock';

export const isMock = DATA_SOURCE === 'mock';

/** Simulated latency so loading states are exercised during development. */
const MOCK_LATENCY_MS = 220;

export function delay<T>(value: T, ms: number = MOCK_LATENCY_MS): Promise<T> {
    return new Promise((resolve) => setTimeout(() => resolve(value), ms));
}

/**
 * Picks between the mock implementation and the HTTP one at call time, so a
 * service method reads as a single expression:
 *
 *   export const list = () => source(() => delay(mockList()), () => get('/x'));
 */
export function source<T>(mock: () => Promise<T>, api: () => Promise<T>): Promise<T> {
    return isMock ? mock() : api();
}

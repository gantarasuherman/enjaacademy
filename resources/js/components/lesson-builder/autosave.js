/**
 * localStorage-backed draft persistence — no backend endpoint. Keyed by
 * lesson slug (or `'new'` for a not-yet-created lesson) so drafts across
 * different lessons never collide.
 */
const PREFIX = 'lesson-draft:';

export function draftKeyFor(slugOrNew) {
    return `${PREFIX}${slugOrNew || 'new'}`;
}

export function readDraft(key) {
    try {
        const raw = localStorage.getItem(key);

        return raw ? JSON.parse(raw) : null;
    } catch {
        return null;
    }
}

export function writeDraft(key, state) {
    try {
        localStorage.setItem(key, JSON.stringify({ state, savedAt: Date.now() }));
    } catch {
        // Storage full/unavailable (private browsing) — autosave is a nicety, fail silently.
    }
}

export function clearDraft(key) {
    try {
        localStorage.removeItem(key);
    } catch {
        // ignore
    }
}

export function debounce(fn, wait) {
    let timer = null;

    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => fn(...args), wait);
    };
}

import { formatDistanceToNow, format, parseISO } from 'date-fns';
import { id as localeId } from 'date-fns/locale';

export function formatDate(value: string | Date, pattern = 'd MMM yyyy'): string {
    const date = typeof value === 'string' ? parseISO(value) : value;

    return format(date, pattern, { locale: localeId });
}

export function formatRelative(value: string | Date): string {
    const date = typeof value === 'string' ? parseISO(value) : value;

    return formatDistanceToNow(date, { addSuffix: true, locale: localeId });
}

/** 95 → "1m 35s", used by the quiz timer and duration labels. */
export function formatDuration(totalSeconds: number): string {
    const minutes = Math.floor(totalSeconds / 60);
    const seconds = totalSeconds % 60;

    if (minutes === 0) return `${seconds}s`;

    return `${minutes}m ${String(seconds).padStart(2, '0')}s`;
}

/** mm:ss for the countdown display. */
export function formatClock(totalSeconds: number): string {
    const safe = Math.max(0, totalSeconds);
    const minutes = Math.floor(safe / 60);
    const seconds = safe % 60;

    return `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
}

export function formatNumber(value: number): string {
    return new Intl.NumberFormat('id-ID').format(value);
}

/** 1200 → "1,2rb" so XP counters stay narrow on mobile. */
export function formatCompact(value: number): string {
    return new Intl.NumberFormat('id-ID', { notation: 'compact', maximumFractionDigits: 1 }).format(value);
}

export function pluralise(count: number, singular: string, plural?: string): string {
    return `${formatNumber(count)} ${count === 1 ? singular : (plural ?? singular)}`;
}

export function initials(name: string): string {
    return name
        .split(' ')
        .slice(0, 2)
        .map((part) => part.charAt(0).toUpperCase())
        .join('');
}

/** Renders the **bold** markers used inside the grammar example sentences. */
export function splitHighlight(sentence: string): { text: string; bold: boolean }[] {
    return sentence.split(/(\*\*[^*]+\*\*)/g).filter(Boolean).map((chunk) => {
        const bold = chunk.startsWith('**') && chunk.endsWith('**');

        return { text: bold ? chunk.slice(2, -2) : chunk, bold };
    });
}

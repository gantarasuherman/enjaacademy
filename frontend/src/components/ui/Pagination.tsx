import { ChevronLeft, ChevronRight } from 'lucide-react';
import { cn } from '@/utils/cn';

/** 1,2,3,…,current-1,current,current+1,…,total — collapsed once there are too many pages to list in full. */
function pageRange(current: number, total: number): (number | '…')[] {
    if (total <= 7) return Array.from({ length: total }, (_, i) => i + 1);

    const pages = new Set<number>([1, total, current - 1, current, current + 1]);
    const sorted = [...pages].filter((p) => p >= 1 && p <= total).sort((a, b) => a - b);

    const result: (number | '…')[] = [];
    let previous = 0;

    for (const p of sorted) {
        if (previous && p - previous > 1) result.push('…');
        result.push(p);
        previous = p;
    }

    return result;
}

export function Pagination({
    page,
    totalPages,
    onChange,
    className,
}: {
    page: number;
    totalPages: number;
    onChange: (page: number) => void;
    className?: string;
}) {
    if (totalPages <= 1) return null;

    return (
        <nav className={cn('flex items-center justify-center gap-1', className)} aria-label="Pagination">
            <button
                type="button"
                disabled={page === 1}
                onClick={() => onChange(page - 1)}
                aria-label="Halaman sebelumnya"
                className="grid size-8 shrink-0 place-items-center rounded-sm border border-[var(--surface-border)] text-fg-muted transition hover:bg-surface-sunken disabled:cursor-not-allowed disabled:opacity-40"
            >
                <ChevronLeft className="size-4" />
            </button>

            {pageRange(page, totalPages).map((p, i) =>
                p === '…' ? (
                    <span key={`ellipsis-${i}`} className="grid size-8 shrink-0 place-items-center text-sm text-fg-muted">
                        …
                    </span>
                ) : (
                    <button
                        key={p}
                        type="button"
                        onClick={() => onChange(p)}
                        aria-current={p === page ? 'page' : undefined}
                        className={cn(
                            'grid size-8 shrink-0 place-items-center rounded-sm text-sm font-semibold transition',
                            p === page
                                ? 'bg-primary text-white'
                                : 'border border-[var(--surface-border)] text-fg-muted hover:bg-surface-sunken',
                        )}
                    >
                        {p}
                    </button>
                ),
            )}

            <button
                type="button"
                disabled={page === totalPages}
                onClick={() => onChange(page + 1)}
                aria-label="Halaman berikutnya"
                className="grid size-8 shrink-0 place-items-center rounded-sm border border-[var(--surface-border)] text-fg-muted transition hover:bg-surface-sunken disabled:cursor-not-allowed disabled:opacity-40"
            >
                <ChevronRight className="size-4" />
            </button>
        </nav>
    );
}

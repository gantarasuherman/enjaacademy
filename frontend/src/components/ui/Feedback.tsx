import type { ReactNode } from 'react';
import { AnimatePresence, motion } from 'framer-motion';
import { AlertTriangle, CheckCircle2, Info, Loader2, X, XCircle } from 'lucide-react';
import { cn } from '@/utils/cn';
import { useUiStore, type Toast } from '@/store/uiStore';

/* ------------------------------------------------------------------ Spinner */

export function Spinner({ className, label = 'Memuat' }: { className?: string; label?: string }) {
    return <Loader2 role="status" aria-label={label} className={cn('size-5 animate-spin text-primary', className)} />;
}

export function PageLoader({ label = 'Memuat…' }: { label?: string }) {
    return (
        <div className="grid min-h-64 place-items-center gap-3 py-16 text-center">
            <Spinner className="mx-auto size-7" />
            <p className="text-sm text-fg-muted">{label}</p>
        </div>
    );
}

/** Shimmering placeholder that matches the eventual content's shape. */
export function Skeleton({ className }: { className?: string }) {
    return <div className={cn('animate-pulse rounded-sm bg-surface-sunken', className)} />;
}

/* -------------------------------------------------------------- Empty state */

export function EmptyState({
    icon,
    title,
    description,
    action,
}: {
    icon?: ReactNode;
    title: string;
    description?: string;
    action?: ReactNode;
}) {
    return (
        <div className="grid place-items-center gap-3 rounded-md border border-dashed border-[var(--surface-border)] px-6 py-14 text-center">
            {icon && <div className="grid size-12 place-items-center rounded-full bg-surface-sunken text-fg-muted">{icon}</div>}
            <div>
                <p className="font-display text-base font-bold">{title}</p>
                {description && <p className="mx-auto mt-1 max-w-sm text-sm text-fg-muted">{description}</p>}
            </div>
            {action}
        </div>
    );
}

/* ------------------------------------------------------------------- Alert */

const ALERT_TONES = {
    success: { cls: 'bg-success/10 text-emerald-800 dark:text-emerald-300', Icon: CheckCircle2 },
    danger: { cls: 'bg-danger/10 text-red-800 dark:text-red-300', Icon: XCircle },
    warning: { cls: 'bg-warning/12 text-amber-800 dark:text-amber-300', Icon: AlertTriangle },
    info: { cls: 'bg-info/10 text-sky-800 dark:text-sky-300', Icon: Info },
};

export function Alert({
    tone = 'info',
    title,
    children,
    className,
}: {
    tone?: keyof typeof ALERT_TONES;
    title?: string;
    children?: ReactNode;
    className?: string;
}) {
    const { cls, Icon } = ALERT_TONES[tone];

    return (
        <div role="alert" className={cn('flex gap-3 rounded-sm px-4 py-3 text-sm', cls, className)}>
            <Icon className="mt-0.5 size-4 shrink-0" aria-hidden />
            <div className="min-w-0">
                {title && <p className="font-semibold">{title}</p>}
                {children && <div className={cn(title && 'mt-0.5')}>{children}</div>}
            </div>
        </div>
    );
}

/* ------------------------------------------------------------------ Toasts */

const TOAST_TONES: Record<Toast['variant'], { cls: string; Icon: typeof CheckCircle2 }> = {
    success: { cls: 'border-success/40 bg-surface', Icon: CheckCircle2 },
    danger: { cls: 'border-danger/40 bg-surface', Icon: XCircle },
    warning: { cls: 'border-warning/40 bg-surface', Icon: AlertTriangle },
    info: { cls: 'border-info/40 bg-surface', Icon: Info },
};

const TOAST_ICON_COLOR: Record<Toast['variant'], string> = {
    success: 'text-success',
    danger: 'text-danger',
    warning: 'text-warning',
    info: 'text-info',
};

export function ToastViewport() {
    const toasts = useUiStore((state) => state.toasts);
    const dismiss = useUiStore((state) => state.dismissToast);

    return (
        <div
            aria-live="polite"
            aria-atomic="false"
            className="pointer-events-none fixed bottom-4 right-4 z-[60] flex w-[min(22rem,calc(100vw-2rem))] flex-col gap-2"
        >
            <AnimatePresence initial={false}>
                {toasts.map((toast) => {
                    const { cls, Icon } = TOAST_TONES[toast.variant];

                    return (
                        <motion.div
                            key={toast.id}
                            layout
                            initial={{ opacity: 0, x: 24, scale: 0.97 }}
                            animate={{ opacity: 1, x: 0, scale: 1 }}
                            exit={{ opacity: 0, x: 24, scale: 0.97 }}
                            transition={{ duration: 0.2, ease: [0.16, 1, 0.3, 1] }}
                            className={cn(
                                'pointer-events-auto flex items-start gap-3 rounded-sm border px-4 py-3 shadow-[var(--shadow-pop)]',
                                cls,
                            )}
                        >
                            <Icon className={cn('mt-0.5 size-4 shrink-0', TOAST_ICON_COLOR[toast.variant])} aria-hidden />
                            <div className="min-w-0 flex-1">
                                {toast.title && <p className="text-sm font-bold">{toast.title}</p>}
                                <p className="text-sm text-fg-muted">{toast.message}</p>
                            </div>
                            <button
                                type="button"
                                onClick={() => dismiss(toast.id)}
                                aria-label="Tutup notifikasi"
                                className="text-fg-muted transition hover:text-fg"
                            >
                                <X className="size-4" />
                            </button>
                        </motion.div>
                    );
                })}
            </AnimatePresence>
        </div>
    );
}

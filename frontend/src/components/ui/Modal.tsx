import { useEffect, type ReactNode } from 'react';
import { createPortal } from 'react-dom';
import { AnimatePresence, motion } from 'framer-motion';
import { X } from 'lucide-react';
import { cn } from '@/utils/cn';
import { IconButton } from './Button';

const SIZES = {
    sm: 'max-w-sm',
    md: 'max-w-lg',
    lg: 'max-w-2xl',
    xl: 'max-w-4xl',
};

export function Modal({
    open,
    onClose,
    title,
    description,
    size = 'md',
    footer,
    children,
}: {
    open: boolean;
    onClose: () => void;
    title?: ReactNode;
    description?: ReactNode;
    size?: keyof typeof SIZES;
    footer?: ReactNode;
    children: ReactNode;
}) {
    // Escape closes, and the body stops scrolling behind the overlay.
    useEffect(() => {
        if (!open) return;

        const onKey = (event: KeyboardEvent) => event.key === 'Escape' && onClose();
        const previousOverflow = document.body.style.overflow;

        document.addEventListener('keydown', onKey);
        document.body.style.overflow = 'hidden';

        return () => {
            document.removeEventListener('keydown', onKey);
            document.body.style.overflow = previousOverflow;
        };
    }, [open, onClose]);

    return createPortal(
        <AnimatePresence>
            {open && (
                <div className="fixed inset-0 z-50 grid place-items-center p-4">
                    <motion.div
                        initial={{ opacity: 0 }}
                        animate={{ opacity: 1 }}
                        exit={{ opacity: 0 }}
                        transition={{ duration: 0.15 }}
                        onClick={onClose}
                        className="absolute inset-0 bg-slate-950/50 backdrop-blur-sm"
                    />

                    <motion.div
                        role="dialog"
                        aria-modal="true"
                        initial={{ opacity: 0, y: 8, scale: 0.98 }}
                        animate={{ opacity: 1, y: 0, scale: 1 }}
                        exit={{ opacity: 0, y: 8, scale: 0.98 }}
                        transition={{ duration: 0.2, ease: [0.16, 1, 0.3, 1] }}
                        className={cn(
                            'relative z-10 w-full overflow-hidden rounded-lg bg-surface shadow-[var(--shadow-pop)]',
                            SIZES[size],
                        )}
                    >
                        {(title || description) && (
                            <div className="flex items-start justify-between gap-4 border-b border-[var(--surface-border)] px-5 py-4">
                                <div className="min-w-0">
                                    {title && <h2 className="text-lg font-bold">{title}</h2>}
                                    {description && <p className="mt-0.5 text-sm text-fg-muted">{description}</p>}
                                </div>
                                <IconButton label="Tutup" variant="ghost" size="sm" onClick={onClose}>
                                    <X className="size-4" />
                                </IconButton>
                            </div>
                        )}

                        <div className="max-h-[70vh] overflow-y-auto px-5 py-4 scrollbar-thin">{children}</div>

                        {footer && (
                            <div className="flex justify-end gap-2 border-t border-[var(--surface-border)] bg-surface-sunken px-5 py-3">
                                {footer}
                            </div>
                        )}
                    </motion.div>
                </div>
            )}
        </AnimatePresence>,
        document.body,
    );
}

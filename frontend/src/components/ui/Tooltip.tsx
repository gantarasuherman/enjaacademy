import { useState, type ReactNode } from 'react';
import { AnimatePresence, motion } from 'framer-motion';
import { cn } from '@/utils/cn';

/**
 * CSS-positioned tooltip. Opens on hover *and* focus so it is reachable by
 * keyboard, and is purely decorative for screen readers (the trigger itself
 * carries the accessible name).
 */
export function Tooltip({
    content,
    side = 'top',
    children,
    className,
}: {
    content: ReactNode;
    side?: 'top' | 'bottom' | 'left' | 'right';
    children: ReactNode;
    className?: string;
}) {
    const [open, setOpen] = useState(false);

    const position = {
        top: 'bottom-full left-1/2 -translate-x-1/2 mb-2',
        bottom: 'top-full left-1/2 -translate-x-1/2 mt-2',
        left: 'right-full top-1/2 -translate-y-1/2 mr-2',
        right: 'left-full top-1/2 -translate-y-1/2 ml-2',
    }[side];

    return (
        <span
            className={cn('relative inline-flex', className)}
            onMouseEnter={() => setOpen(true)}
            onMouseLeave={() => setOpen(false)}
            onFocus={() => setOpen(true)}
            onBlur={() => setOpen(false)}
        >
            {children}
            <AnimatePresence>
                {open && (
                    <motion.span
                        role="tooltip"
                        initial={{ opacity: 0, scale: 0.96 }}
                        animate={{ opacity: 1, scale: 1 }}
                        exit={{ opacity: 0, scale: 0.96 }}
                        transition={{ duration: 0.12 }}
                        className={cn(
                            'pointer-events-none absolute z-50 whitespace-nowrap rounded-sm bg-ink px-2.5 py-1.5',
                            'text-xs font-medium text-white shadow-[var(--shadow-pop)] dark:bg-surface-raised',
                            position,
                        )}
                    >
                        {content}
                    </motion.span>
                )}
            </AnimatePresence>
        </span>
    );
}

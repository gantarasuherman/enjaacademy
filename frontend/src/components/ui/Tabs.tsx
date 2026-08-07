import type { ReactNode } from 'react';
import { motion } from 'framer-motion';
import { cn } from '@/utils/cn';

export interface TabItem {
    id: string;
    label: string;
    icon?: ReactNode;
    count?: number;
}

/**
 * Underlined tabs with a shared layout indicator, so the underline slides
 * between tabs instead of jumping.
 */
export function Tabs({
    items,
    active,
    onChange,
    className,
    layoutId = 'tab-indicator',
}: {
    items: TabItem[];
    active: string;
    onChange: (id: string) => void;
    className?: string;
    layoutId?: string;
}) {
    return (
        <div
            role="tablist"
            className={cn(
                'flex gap-1 overflow-x-auto border-b border-[var(--surface-border)] no-scrollbar',
                className,
            )}
        >
            {items.map((item) => {
                const isActive = item.id === active;

                return (
                    <button
                        key={item.id}
                        role="tab"
                        aria-selected={isActive}
                        onClick={() => onChange(item.id)}
                        className={cn(
                            'relative flex shrink-0 items-center gap-2 px-4 py-2.5 text-sm font-medium transition-colors',
                            isActive ? 'text-primary' : 'text-fg-muted hover:text-fg',
                        )}
                    >
                        {item.icon}
                        {item.label}
                        {item.count !== undefined && (
                            <span
                                className={cn(
                                    'rounded-full px-1.5 py-0.5 text-[11px] font-semibold',
                                    isActive ? 'bg-primary-100 text-primary-700' : 'bg-surface-sunken text-fg-muted',
                                )}
                            >
                                {item.count}
                            </span>
                        )}
                        {isActive && (
                            <motion.span
                                layoutId={layoutId}
                                className="absolute inset-x-2 -bottom-px h-0.5 rounded-full bg-primary"
                                transition={{ duration: 0.2, ease: [0.16, 1, 0.3, 1] }}
                            />
                        )}
                    </button>
                );
            })}
        </div>
    );
}

export function TabPanel({ id, active, children }: { id: string; active: string; children: ReactNode }) {
    if (id !== active) return null;

    return (
        <motion.div
            role="tabpanel"
            initial={{ opacity: 0, y: 4 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.2, ease: [0.16, 1, 0.3, 1] }}
        >
            {children}
        </motion.div>
    );
}

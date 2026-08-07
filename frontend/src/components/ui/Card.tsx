import type { HTMLAttributes, ReactNode } from 'react';
import { cn } from '@/utils/cn';

interface CardProps extends HTMLAttributes<HTMLDivElement> {
    /** Adds hover lift — use only when the whole card is clickable. */
    interactive?: boolean;
    padded?: boolean;
}

export function Card({ interactive, padded = true, className, children, ...props }: CardProps) {
    return (
        <div
            className={cn(
                'rounded-md border border-[var(--surface-border)] bg-surface shadow-[var(--shadow-card)]',
                padded && 'p-5',
                interactive &&
                    'cursor-pointer transition duration-200 ease-[var(--ease-out-soft)] hover:-translate-y-0.5 hover:shadow-[var(--shadow-pop)]',
                className,
            )}
            {...props}
        >
            {children}
        </div>
    );
}

export function CardHeader({
    title,
    subtitle,
    action,
    className,
}: {
    title: ReactNode;
    subtitle?: ReactNode;
    action?: ReactNode;
    className?: string;
}) {
    return (
        <div className={cn('mb-4 flex items-start justify-between gap-3', className)}>
            <div className="min-w-0">
                <h3 className="truncate text-base font-bold">{title}</h3>
                {subtitle && <p className="mt-0.5 text-sm text-fg-muted">{subtitle}</p>}
            </div>
            {action && <div className="shrink-0">{action}</div>}
        </div>
    );
}

export function CardSection({ className, children }: { className?: string; children: ReactNode }) {
    return <div className={cn('border-t border-[var(--surface-border)] pt-4', className)}>{children}</div>;
}

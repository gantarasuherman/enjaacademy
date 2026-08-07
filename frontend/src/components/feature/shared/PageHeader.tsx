import type { ReactNode } from 'react';
import { Link } from 'react-router-dom';
import { ChevronLeft } from 'lucide-react';
import { cn } from '@/utils/cn';

export function PageHeader({
    title,
    description,
    backTo,
    backLabel = 'Kembali',
    badge,
    action,
    className,
}: {
    title: ReactNode;
    description?: ReactNode;
    backTo?: string;
    backLabel?: string;
    badge?: ReactNode;
    action?: ReactNode;
    className?: string;
}) {
    return (
        <div className={cn('mb-6', className)}>
            {backTo && (
                <Link
                    to={backTo}
                    className="mb-3 inline-flex items-center gap-1 text-sm font-medium text-fg-muted transition hover:text-primary"
                >
                    <ChevronLeft className="size-4" />
                    {backLabel}
                </Link>
            )}

            <div className="flex flex-wrap items-start justify-between gap-4">
                <div className="min-w-0">
                    <div className="flex flex-wrap items-center gap-2">
                        <h1 className="font-display text-2xl font-extrabold tracking-tight">{title}</h1>
                        {badge}
                    </div>
                    {description && <p className="mt-1.5 max-w-2xl text-sm text-fg-muted">{description}</p>}
                </div>

                {action && <div className="shrink-0">{action}</div>}
            </div>
        </div>
    );
}

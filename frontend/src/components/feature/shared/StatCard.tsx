import type { LucideIcon } from 'lucide-react';
import { cn } from '@/utils/cn';
import { Card } from '@/components/ui/Card';

const TONES = {
    primary: 'bg-primary-100 text-primary dark:bg-primary/20 dark:text-primary-300',
    secondary: 'bg-secondary-100 text-secondary-700 dark:bg-secondary/20 dark:text-secondary-300',
    success: 'bg-success/12 text-emerald-600 dark:text-emerald-400',
    danger: 'bg-danger/12 text-red-600 dark:text-red-400',
    info: 'bg-info/12 text-sky-600 dark:text-sky-400',
    warning: 'bg-warning/15 text-amber-600 dark:text-amber-400',
};

export function StatCard({
    icon: Icon,
    label,
    value,
    hint,
    tone = 'primary',
    className,
}: {
    icon: LucideIcon;
    label: string;
    value: string | number;
    hint?: string;
    tone?: keyof typeof TONES;
    className?: string;
}) {
    return (
        <Card className={cn('flex items-center gap-4', className)}>
            <span className={cn('grid size-11 shrink-0 place-items-center rounded-sm', TONES[tone])}>
                <Icon className="size-5" aria-hidden />
            </span>
            <div className="min-w-0">
                <p className="truncate text-xs font-medium text-fg-muted">{label}</p>
                <p className="font-display text-xl font-extrabold leading-tight">{value}</p>
                {hint && <p className="truncate text-[11px] text-fg-muted">{hint}</p>}
            </div>
        </Card>
    );
}

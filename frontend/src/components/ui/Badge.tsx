import type { ReactNode } from 'react';
import { cn } from '@/utils/cn';

export type BadgeTone =
    | 'primary'
    | 'secondary'
    | 'success'
    | 'danger'
    | 'warning'
    | 'info'
    | 'neutral';

const TONES: Record<BadgeTone, string> = {
    primary: 'bg-primary-100 text-primary-700 dark:bg-primary/20 dark:text-primary-300',
    secondary: 'bg-secondary-100 text-secondary-700 dark:bg-secondary/20 dark:text-secondary-300',
    success: 'bg-success/12 text-emerald-700 dark:text-emerald-400',
    danger: 'bg-danger/12 text-red-700 dark:text-red-400',
    warning: 'bg-warning/15 text-amber-700 dark:text-amber-400',
    info: 'bg-info/12 text-sky-700 dark:text-sky-400',
    neutral: 'bg-surface-sunken text-fg-muted',
};

export function Badge({
    tone = 'neutral',
    icon,
    children,
    className,
}: {
    tone?: BadgeTone;
    icon?: ReactNode;
    children: ReactNode;
    className?: string;
}) {
    return (
        <span
            className={cn(
                'inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-semibold whitespace-nowrap',
                TONES[tone],
                className,
            )}
        >
            {icon}
            {children}
        </span>
    );
}

/** Proficiency levels get a fixed colour so learners recognise them at a glance — same 5-position ramp for both scales. */
const CEFR_TONE: Record<string, BadgeTone> = {
    Beginner: 'success',
    Elementary: 'info',
    Intermediate: 'warning',
    'Upper-Intermediate': 'secondary',
    Advanced: 'danger',
    N5: 'success',
    N4: 'info',
    N3: 'warning',
    N2: 'secondary',
    N1: 'danger',
};

export function CefrBadge({ level, className }: { level: string; className?: string }) {
    return (
        <Badge tone={CEFR_TONE[level] ?? 'neutral'} className={cn('font-mono', className)}>
            {level}
        </Badge>
    );
}

const DIFFICULTY_TONE: Record<string, BadgeTone> = {
    beginner: 'success',
    intermediate: 'warning',
    advanced: 'danger',
    easy: 'success',
    medium: 'warning',
    hard: 'danger',
};

const DIFFICULTY_LABEL: Record<string, string> = {
    beginner: 'Pemula',
    intermediate: 'Menengah',
    advanced: 'Mahir',
    easy: 'Mudah',
    medium: 'Sedang',
    hard: 'Sulit',
};

export function DifficultyBadge({ level, className }: { level: string; className?: string }) {
    return (
        <Badge tone={DIFFICULTY_TONE[level] ?? 'neutral'} className={className}>
            {DIFFICULTY_LABEL[level] ?? level}
        </Badge>
    );
}

export function Chip({
    active,
    onClick,
    icon,
    children,
    className,
}: {
    active?: boolean;
    onClick?: () => void;
    icon?: ReactNode;
    children: ReactNode;
    className?: string;
}) {
    return (
        <button
            type="button"
            onClick={onClick}
            aria-pressed={active}
            className={cn(
                'inline-flex items-center gap-1.5 rounded-full border px-3 py-1.5 text-sm font-medium transition',
                'duration-150 ease-[var(--ease-out-soft)] active:scale-[0.97]',
                active
                    ? 'border-primary bg-primary text-white shadow-sm'
                    : 'border-[var(--surface-border)] bg-surface text-fg-muted hover:border-primary-300 hover:text-fg',
                className,
            )}
        >
            {icon}
            {children}
        </button>
    );
}

import type { ReactNode } from 'react';
import { cn } from '@/utils/cn';

export type ProgressTone = 'primary' | 'secondary' | 'success' | 'danger' | 'info';

const FILL: Record<ProgressTone, string> = {
    primary: 'bg-primary',
    secondary: 'bg-secondary',
    success: 'bg-success',
    danger: 'bg-danger',
    info: 'bg-info',
};

export function ProgressBar({
    value,
    tone = 'secondary',
    size = 'md',
    label,
    showValue,
    className,
}: {
    value: number;
    tone?: ProgressTone;
    size?: 'sm' | 'md' | 'lg';
    label?: string;
    showValue?: boolean;
    className?: string;
}) {
    const clamped = Math.max(0, Math.min(100, Math.round(value)));

    return (
        <div className={className}>
            {(label || showValue) && (
                <div className="mb-1.5 flex items-center justify-between text-xs">
                    {label && <span className="font-medium text-fg-muted">{label}</span>}
                    {showValue && <span className="font-mono font-semibold text-fg">{clamped}%</span>}
                </div>
            )}
            <div
                role="progressbar"
                aria-valuenow={clamped}
                aria-valuemin={0}
                aria-valuemax={100}
                aria-label={label ?? 'Progres'}
                className={cn(
                    'w-full overflow-hidden rounded-full bg-surface-sunken',
                    size === 'sm' ? 'h-1.5' : size === 'lg' ? 'h-3' : 'h-2',
                )}
            >
                <div
                    className={cn('h-full rounded-full transition-[width] duration-500 ease-[var(--ease-out-soft)]', FILL[tone])}
                    style={{ width: `${clamped}%` }}
                />
            </div>
        </div>
    );
}

/**
 * Circular meter used for the daily goal and level rings.
 */
export function ProgressRing({
    value,
    size = 96,
    stroke = 8,
    tone = 'secondary',
    children,
    className,
}: {
    value: number;
    size?: number;
    stroke?: number;
    tone?: ProgressTone;
    children?: ReactNode;
    className?: string;
}) {
    const clamped = Math.max(0, Math.min(100, value));
    const radius = (size - stroke) / 2;
    const circumference = 2 * Math.PI * radius;
    const offset = circumference - (clamped / 100) * circumference;

    const strokeColor = {
        primary: 'var(--color-primary)',
        secondary: 'var(--color-secondary)',
        success: 'var(--color-success)',
        danger: 'var(--color-danger)',
        info: 'var(--color-info)',
    }[tone];

    return (
        <div className={cn('relative inline-grid place-items-center', className)} style={{ width: size, height: size }}>
            <svg width={size} height={size} className="-rotate-90" aria-hidden>
                <circle
                    cx={size / 2}
                    cy={size / 2}
                    r={radius}
                    fill="none"
                    strokeWidth={stroke}
                    className="stroke-[var(--surface-sunken)]"
                />
                <circle
                    cx={size / 2}
                    cy={size / 2}
                    r={radius}
                    fill="none"
                    strokeWidth={stroke}
                    stroke={strokeColor}
                    strokeLinecap="round"
                    strokeDasharray={circumference}
                    strokeDashoffset={offset}
                    style={{ transition: 'stroke-dashoffset 600ms cubic-bezier(0.16,1,0.3,1)' }}
                />
            </svg>
            <div className="absolute inset-0 grid place-items-center text-center">{children}</div>
        </div>
    );
}

/** Segmented step indicator for the quiz header. */
export function StepDots({
    total,
    current,
    states,
    onSelect,
}: {
    total: number;
    current: number;
    states?: ('correct' | 'wrong' | 'answered' | 'empty')[];
    onSelect?: (index: number) => void;
}) {
    return (
        <div className="flex flex-wrap gap-1.5">
            {Array.from({ length: total }, (_, i) => {
                const state = states?.[i] ?? 'empty';

                return (
                    <button
                        key={i}
                        type="button"
                        onClick={() => onSelect?.(i)}
                        aria-label={`Soal ${i + 1}`}
                        aria-current={i === current}
                        className={cn(
                            'size-2.5 rounded-full transition duration-150',
                            onSelect && 'cursor-pointer hover:scale-125',
                            i === current && 'ring-2 ring-primary ring-offset-2 ring-offset-[var(--surface)]',
                            state === 'correct' && 'bg-success',
                            state === 'wrong' && 'bg-danger',
                            state === 'answered' && 'bg-primary',
                            state === 'empty' && 'bg-surface-sunken ring-1 ring-[var(--surface-border)]',
                        )}
                    />
                );
            })}
        </div>
    );
}

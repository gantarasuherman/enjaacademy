import { cn } from '@/utils/cn';
import { initials } from '@/utils/format';

const SIZES = {
    xs: 'size-6 text-[10px]',
    sm: 'size-8 text-xs',
    md: 'size-10 text-sm',
    lg: 'size-14 text-lg',
    xl: 'size-20 text-2xl',
};

export function Avatar({
    name,
    src,
    size = 'md',
    ring,
    className,
}: {
    name: string;
    src?: string | null;
    size?: keyof typeof SIZES;
    ring?: boolean;
    className?: string;
}) {
    if (src) {
        return (
            <img
                src={src}
                alt={name}
                className={cn(
                    'shrink-0 rounded-full object-cover',
                    SIZES[size],
                    ring && 'ring-2 ring-primary ring-offset-2 ring-offset-[var(--surface)]',
                    className,
                )}
            />
        );
    }

    return (
        <span
            aria-label={name}
            title={name}
            className={cn(
                'grid shrink-0 place-items-center rounded-full bg-primary-100 font-display font-bold text-primary-700',
                'dark:bg-primary/25 dark:text-primary-200',
                SIZES[size],
                ring && 'ring-2 ring-primary ring-offset-2 ring-offset-[var(--surface)]',
                className,
            )}
        >
            {initials(name)}
        </span>
    );
}

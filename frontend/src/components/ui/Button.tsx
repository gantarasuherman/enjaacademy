import { forwardRef, type ButtonHTMLAttributes, type ReactNode } from 'react';
import { Link } from 'react-router-dom';
import { Loader2 } from 'lucide-react';
import { cn } from '@/utils/cn';

export type ButtonVariant = 'primary' | 'secondary' | 'ghost' | 'danger' | 'success' | 'outline';
export type ButtonSize = 'sm' | 'md' | 'lg';

const VARIANTS: Record<ButtonVariant, string> = {
    primary: 'bg-primary text-white shadow-sm hover:bg-primary-600 active:bg-primary-700',
    secondary: 'bg-secondary text-white shadow-sm hover:bg-secondary-600 active:bg-secondary-700',
    outline:
        'border border-[var(--surface-border)] bg-surface text-fg hover:bg-surface-sunken active:bg-surface-sunken',
    ghost: 'text-fg-muted hover:bg-surface-sunken hover:text-fg',
    danger: 'bg-danger text-white shadow-sm hover:brightness-95 active:brightness-90',
    success: 'bg-success text-white shadow-sm hover:brightness-95 active:brightness-90',
};

const SIZES: Record<ButtonSize, string> = {
    sm: 'h-8 gap-1.5 px-3 text-xs',
    md: 'h-10 gap-2 px-4 text-sm',
    lg: 'h-12 gap-2.5 px-6 text-base',
};

interface BaseProps {
    variant?: ButtonVariant;
    size?: ButtonSize;
    loading?: boolean;
    icon?: ReactNode;
    iconRight?: ReactNode;
    fullWidth?: boolean;
    children?: ReactNode;
    className?: string;
}

export interface ButtonProps extends BaseProps, Omit<ButtonHTMLAttributes<HTMLButtonElement>, keyof BaseProps> {
    /** Renders a react-router <Link> that looks identical to the button. */
    to?: string;
    href?: string;
}

export const Button = forwardRef<HTMLButtonElement, ButtonProps>(function Button(
    {
        variant = 'primary',
        size = 'md',
        loading = false,
        icon,
        iconRight,
        fullWidth,
        className,
        children,
        to,
        href,
        disabled,
        ...props
    },
    ref,
) {
    const classes = cn(
        'inline-flex items-center justify-center rounded-sm font-medium transition-[background-color,transform,box-shadow]',
        'duration-150 ease-[var(--ease-out-soft)] active:scale-[0.98]',
        'disabled:pointer-events-none disabled:opacity-55',
        VARIANTS[variant],
        SIZES[size],
        fullWidth && 'w-full',
        className,
    );

    const content = (
        <>
            {loading ? <Loader2 className="size-4 animate-spin" aria-hidden /> : icon}
            {children}
            {!loading && iconRight}
        </>
    );

    if (to) {
        return (
            <Link to={to} className={classes} aria-disabled={disabled}>
                {content}
            </Link>
        );
    }

    if (href) {
        return (
            <a href={href} target="_blank" rel="noreferrer noopener" className={classes}>
                {content}
            </a>
        );
    }

    return (
        <button ref={ref} className={classes} disabled={disabled || loading} {...props}>
            {content}
        </button>
    );
});

export interface IconButtonProps extends Omit<ButtonProps, 'children' | 'icon' | 'iconRight'> {
    /** Required — an icon-only control needs an accessible name. */
    label: string;
    children: ReactNode;
}

export function IconButton({ label, children, size = 'md', className, ...props }: IconButtonProps) {
    return (
        <Button
            aria-label={label}
            title={label}
            size={size}
            className={cn('aspect-square p-0', size === 'sm' ? 'w-8' : size === 'lg' ? 'w-12' : 'w-10', className)}
            {...props}
        >
            {children}
        </Button>
    );
}

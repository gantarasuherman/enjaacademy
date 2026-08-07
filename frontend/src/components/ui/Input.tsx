import {
    forwardRef,
    useId,
    type InputHTMLAttributes,
    type ReactNode,
    type SelectHTMLAttributes,
    type TextareaHTMLAttributes,
} from 'react';
import { cn } from '@/utils/cn';

const FIELD =
    'w-full rounded-sm border border-[var(--surface-border)] bg-surface px-3 py-2.5 text-sm text-fg ' +
    'placeholder:text-fg-muted/70 transition duration-150 ' +
    'focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/25 ' +
    'disabled:cursor-not-allowed disabled:opacity-60';

function FieldShell({
    id,
    label,
    hint,
    error,
    required,
    children,
    className,
}: {
    id: string;
    label?: string;
    hint?: string;
    error?: string;
    required?: boolean;
    children: ReactNode;
    className?: string;
}) {
    return (
        <div className={cn('space-y-1.5', className)}>
            {label && (
                <label htmlFor={id} className="block text-sm font-medium text-fg">
                    {label}
                    {required && <span className="ml-0.5 text-danger">*</span>}
                </label>
            )}
            {children}
            {error ? (
                <p id={`${id}-error`} role="alert" className="text-xs text-danger">
                    {error}
                </p>
            ) : (
                hint && <p className="text-xs text-fg-muted">{hint}</p>
            )}
        </div>
    );
}

interface InputProps extends InputHTMLAttributes<HTMLInputElement> {
    label?: string;
    hint?: string;
    error?: string;
    icon?: ReactNode;
    trailing?: ReactNode;
    wrapperClassName?: string;
}

export const Input = forwardRef<HTMLInputElement, InputProps>(function Input(
    { label, hint, error, icon, trailing, className, wrapperClassName, id, ...props },
    ref,
) {
    const generatedId = useId();
    const fieldId = id ?? generatedId;

    return (
        <FieldShell
            id={fieldId}
            label={label}
            hint={hint}
            error={error}
            required={props.required}
            className={wrapperClassName}
        >
            <div className="relative">
                {icon && (
                    <span className="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-fg-muted">
                        {icon}
                    </span>
                )}
                <input
                    ref={ref}
                    id={fieldId}
                    aria-invalid={!!error}
                    aria-describedby={error ? `${fieldId}-error` : undefined}
                    className={cn(
                        FIELD,
                        icon && 'pl-10',
                        trailing && 'pr-10',
                        error && 'border-danger focus:border-danger focus:ring-danger/25',
                        className,
                    )}
                    {...props}
                />
                {trailing && <span className="absolute right-3 top-1/2 -translate-y-1/2">{trailing}</span>}
            </div>
        </FieldShell>
    );
});

interface TextareaProps extends TextareaHTMLAttributes<HTMLTextAreaElement> {
    label?: string;
    hint?: string;
    error?: string;
}

export const Textarea = forwardRef<HTMLTextAreaElement, TextareaProps>(function Textarea(
    { label, hint, error, className, id, ...props },
    ref,
) {
    const generatedId = useId();
    const fieldId = id ?? generatedId;

    return (
        <FieldShell id={fieldId} label={label} hint={hint} error={error} required={props.required}>
            <textarea
                ref={ref}
                id={fieldId}
                aria-invalid={!!error}
                aria-describedby={error ? `${fieldId}-error` : undefined}
                className={cn(FIELD, 'min-h-28 resize-y leading-relaxed', error && 'border-danger', className)}
                {...props}
            />
        </FieldShell>
    );
});

interface SelectProps extends SelectHTMLAttributes<HTMLSelectElement> {
    label?: string;
    hint?: string;
    error?: string;
    options: { value: string; label: string }[];
    placeholder?: string;
}

export const Select = forwardRef<HTMLSelectElement, SelectProps>(function Select(
    { label, hint, error, options, placeholder, className, id, ...props },
    ref,
) {
    const generatedId = useId();
    const fieldId = id ?? generatedId;

    return (
        <FieldShell id={fieldId} label={label} hint={hint} error={error} required={props.required}>
            <select
                ref={ref}
                id={fieldId}
                aria-invalid={!!error}
                className={cn(FIELD, 'cursor-pointer pr-9', error && 'border-danger', className)}
                {...props}
            >
                {placeholder && <option value="">{placeholder}</option>}
                {options.map((option) => (
                    <option key={option.value} value={option.value}>
                        {option.label}
                    </option>
                ))}
            </select>
        </FieldShell>
    );
});

export function Checkbox({
    label,
    description,
    className,
    ...props
}: InputHTMLAttributes<HTMLInputElement> & { label: string; description?: string }) {
    const id = useId();

    return (
        <label htmlFor={id} className={cn('flex cursor-pointer items-start gap-3', className)}>
            <input
                id={id}
                type="checkbox"
                className="mt-0.5 size-4 shrink-0 cursor-pointer rounded border-[var(--surface-border)] text-primary focus:ring-2 focus:ring-primary/30"
                {...props}
            />
            <span className="min-w-0">
                <span className="block text-sm font-medium text-fg">{label}</span>
                {description && <span className="block text-xs text-fg-muted">{description}</span>}
            </span>
        </label>
    );
}

export function Toggle({
    checked,
    onChange,
    label,
    description,
}: {
    checked: boolean;
    onChange: (value: boolean) => void;
    label: string;
    description?: string;
}) {
    return (
        <div className="flex items-center justify-between gap-4">
            <div className="min-w-0">
                <p className="text-sm font-medium text-fg">{label}</p>
                {description && <p className="text-xs text-fg-muted">{description}</p>}
            </div>
            <button
                type="button"
                role="switch"
                aria-checked={checked}
                aria-label={label}
                onClick={() => onChange(!checked)}
                className={cn(
                    'relative h-6 w-11 shrink-0 rounded-full transition-colors duration-200',
                    checked ? 'bg-primary' : 'bg-surface-sunken ring-1 ring-[var(--surface-border)]',
                )}
            >
                <span
                    className={cn(
                        'absolute top-0.5 size-5 rounded-full bg-white shadow transition-transform duration-200 ease-[var(--ease-out-soft)]',
                        checked ? 'translate-x-5.5' : 'translate-x-0.5',
                    )}
                />
            </button>
        </div>
    );
}

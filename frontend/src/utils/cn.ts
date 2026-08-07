import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';

/**
 * Conditional classes with Tailwind conflict resolution, so a `className`
 * prop can always override a component's own utilities.
 */
export function cn(...inputs: ClassValue[]): string {
    return twMerge(clsx(inputs));
}

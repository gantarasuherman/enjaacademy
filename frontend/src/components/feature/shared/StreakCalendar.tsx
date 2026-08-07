import { useMemo } from 'react';
import { cn } from '@/utils/cn';
import type { DailyActivity } from '@/types';

const DAY_LABELS = ['S', 'S', 'R', 'K', 'J', 'S', 'M'];

/**
 * GitHub-style contribution grid for the last 12 weeks. Intensity is bucketed
 * rather than continuous, so the difference between an 8-minute and a
 * 40-minute day is actually readable.
 */
export function StreakCalendar({ activity, weeks = 12 }: { activity: DailyActivity[]; weeks?: number }) {
    const { grid, byDate } = useMemo(() => {
        const map = new Map(activity.map((day) => [day.date, day]));

        const days: string[] = [];
        const cursor = new Date();

        // Walk back to the most recent Sunday so columns line up as weeks.
        cursor.setDate(cursor.getDate() - cursor.getDay());
        cursor.setDate(cursor.getDate() + 6);

        for (let i = weeks * 7 - 1; i >= 0; i--) {
            const date = new Date(cursor);
            date.setDate(cursor.getDate() - i);
            days.push(date.toISOString().slice(0, 10));
        }

        const columns: string[][] = [];
        for (let i = 0; i < days.length; i += 7) {
            columns.push(days.slice(i, i + 7));
        }

        return { grid: columns, byDate: map };
    }, [activity, weeks]);

    function level(minutes: number): string {
        if (minutes === 0) return 'bg-surface-sunken';
        if (minutes < 10) return 'bg-secondary/25';
        if (minutes < 20) return 'bg-secondary/50';
        if (minutes < 35) return 'bg-secondary/75';
        return 'bg-secondary';
    }

    const today = new Date().toISOString().slice(0, 10);

    return (
        <div>
            <div className="flex gap-1 overflow-x-auto no-scrollbar">
                <div className="mr-1 grid shrink-0 grid-rows-7 gap-1 pt-0.5">
                    {DAY_LABELS.map((label, i) => (
                        <span key={i} className="h-3 text-[9px] leading-3 text-fg-muted">
                            {i % 2 === 1 ? label : ''}
                        </span>
                    ))}
                </div>

                {grid.map((week, weekIndex) => (
                    <div key={weekIndex} className="grid shrink-0 grid-rows-7 gap-1">
                        {week.map((date) => {
                            const day = byDate.get(date);
                            const minutes = day?.minutes ?? 0;
                            const future = date > today;

                            return (
                                <span
                                    key={date}
                                    title={
                                        future
                                            ? date
                                            : `${date}: ${minutes} menit${day?.xp ? `, ${day.xp} XP` : ''}`
                                    }
                                    className={cn(
                                        'size-3 rounded-[3px] transition',
                                        future ? 'bg-transparent' : level(minutes),
                                        date === today && 'ring-1 ring-primary ring-offset-1 ring-offset-[var(--surface)]',
                                    )}
                                />
                            );
                        })}
                    </div>
                ))}
            </div>

            <div className="mt-3 flex items-center justify-end gap-1.5 text-[10px] text-fg-muted">
                <span>Sedikit</span>
                {['bg-surface-sunken', 'bg-secondary/25', 'bg-secondary/50', 'bg-secondary/75', 'bg-secondary'].map(
                    (cls) => (
                        <span key={cls} className={cn('size-3 rounded-[3px]', cls)} />
                    ),
                )}
                <span>Banyak</span>
            </div>
        </div>
    );
}

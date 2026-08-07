import { useState } from 'react';
import { Crown, Flame, Medal, Trophy } from 'lucide-react';
import { useAsync } from '@/hooks/useAsync';
import { userService } from '@/services/api';
import { cn } from '@/utils/cn';
import { formatNumber } from '@/utils/format';
import { Card, CardHeader } from '@/components/ui/Card';
import { Avatar } from '@/components/ui/Avatar';
import { Badge, Chip } from '@/components/ui/Badge';
import { Skeleton } from '@/components/ui/Feedback';
import { PageHeader } from '@/components/feature/shared/PageHeader';

const SCOPES = [
    { id: 'weekly' as const, label: 'Minggu ini' },
    { id: 'monthly' as const, label: 'Bulan ini' },
    { id: 'all' as const, label: 'Sepanjang waktu' },
];

const PODIUM_STYLES = [
    { ring: 'ring-yellow-400', bg: 'bg-yellow-400/10', text: 'text-yellow-600', icon: Crown, order: 'sm:order-2', size: 'sm:scale-110' },
    { ring: 'ring-slate-400', bg: 'bg-slate-400/10', text: 'text-slate-500', icon: Medal, order: 'sm:order-1', size: '' },
    { ring: 'ring-amber-700', bg: 'bg-amber-700/10', text: 'text-amber-700', icon: Medal, order: 'sm:order-3', size: '' },
];

export default function LeaderboardPage() {
    const [scope, setScope] = useState<'weekly' | 'monthly' | 'all'>('all');
    const { data: entries, loading } = useAsync(() => userService.getLeaderboard(scope), [scope]);

    const podium = (entries ?? []).slice(0, 3);
    const rest = (entries ?? []).slice(3);
    const me = (entries ?? []).find((entry) => entry.isCurrentUser);

    return (
        <>
            <PageHeader
                title="Leaderboard"
                description="Peringkat berdasarkan total XP. Konsistensi harian mengalahkan sesi maraton sekali sebulan."
            />

            <div className="mb-6 flex flex-wrap gap-2">
                {SCOPES.map((item) => (
                    <Chip key={item.id} active={scope === item.id} onClick={() => setScope(item.id)}>
                        {item.label}
                    </Chip>
                ))}
            </div>

            {loading ? (
                <div className="space-y-3">
                    {Array.from({ length: 8 }, (_, i) => (
                        <Skeleton key={i} className="h-16 w-full" />
                    ))}
                </div>
            ) : (
                <>
                    {/* Podium */}
                    {podium.length === 3 && (
                        <div className="mb-6 grid gap-4 sm:grid-cols-3 sm:items-end">
                            {podium.map((entry, index) => {
                                const style = PODIUM_STYLES[index];
                                const Icon = style.icon;

                                return (
                                    <Card
                                        key={entry.userId}
                                        className={cn(
                                            'flex flex-col items-center text-center transition',
                                            style.order,
                                            style.size,
                                            entry.isCurrentUser && 'ring-2 ring-primary',
                                        )}
                                    >
                                        <span className={cn('grid size-9 place-items-center rounded-full', style.bg, style.text)}>
                                            <Icon className="size-5" />
                                        </span>

                                        <Avatar
                                            name={entry.name}
                                            src={entry.avatar}
                                            size="lg"
                                            className={cn('mt-3 ring-2 ring-offset-2 ring-offset-[var(--surface)]', style.ring)}
                                        />

                                        <p className="mt-3 truncate font-display font-bold">{entry.name}</p>
                                        <p className="mt-0.5 font-mono text-lg font-extrabold text-primary">
                                            {formatNumber(entry.xp)}
                                        </p>
                                        <p className="text-xs text-fg-muted">XP</p>

                                        <div className="mt-3 flex gap-1.5">
                                            <Badge tone="primary">Lv {entry.level}</Badge>
                                            <Badge tone="secondary" icon={<Flame className="size-3" />}>
                                                {entry.streak}
                                            </Badge>
                                        </div>
                                    </Card>
                                );
                            })}
                        </div>
                    )}

                    {/* Rest of the table */}
                    <Card padded={false}>
                        <CardHeader title="Peringkat lengkap" className="px-5 pt-5" />

                        <ul className="divide-y divide-[var(--surface-border)]">
                            {rest.map((entry) => (
                                <li
                                    key={entry.userId}
                                    className={cn(
                                        'flex items-center gap-4 px-5 py-3.5 transition',
                                        entry.isCurrentUser ? 'bg-primary-50 dark:bg-primary/12' : 'hover:bg-surface-sunken',
                                    )}
                                >
                                    <span className="w-8 shrink-0 text-center font-mono text-sm font-bold text-fg-muted">
                                        {entry.rank}
                                    </span>

                                    <Avatar name={entry.name} src={entry.avatar} size="sm" />

                                    <div className="min-w-0 flex-1">
                                        <p className="truncate text-sm font-semibold">
                                            {entry.name}
                                            {entry.isCurrentUser && (
                                                <Badge tone="primary" className="ml-2">
                                                    Kamu
                                                </Badge>
                                            )}
                                        </p>
                                        <p className="text-xs text-fg-muted">Level {entry.level}</p>
                                    </div>

                                    <span className="hidden items-center gap-1 text-xs text-fg-muted sm:flex">
                                        <Flame className="size-3.5 text-secondary" />
                                        {entry.streak}
                                    </span>

                                    <span className="shrink-0 font-mono text-sm font-bold">
                                        {formatNumber(entry.xp)}
                                    </span>
                                </li>
                            ))}
                        </ul>
                    </Card>

                    {/* Sticky "your rank" summary */}
                    {me && me.rank > 3 && (
                        <Card className="mt-5 flex items-center gap-4 border-primary bg-primary-50 dark:bg-primary/12">
                            <Trophy className="size-6 shrink-0 text-primary" />
                            <div className="min-w-0 flex-1">
                                <p className="font-display font-bold">Peringkatmu: #{me.rank}</p>
                                <p className="text-sm text-fg-muted">
                                    {entries && entries[me.rank - 2]
                                        ? `${formatNumber(entries[me.rank - 2].xp - me.xp)} XP lagi untuk menyalip ${entries[me.rank - 2].name}.`
                                        : 'Terus kumpulkan XP untuk naik peringkat.'}
                                </p>
                            </div>
                            <span className="shrink-0 font-mono text-lg font-extrabold text-primary">
                                {formatNumber(me.xp)}
                            </span>
                        </Card>
                    )}
                </>
            )}
        </>
    );
}

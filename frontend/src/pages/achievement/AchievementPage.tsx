import { useState } from 'react';
import { Award, Lock, Trophy } from 'lucide-react';
import { useAsync } from '@/hooks/useAsync';
import { userService } from '@/services/api';
import { useProgressStore } from '@/store/progressStore';
import { achievementPercent, TIER_STYLES } from '@/utils/gamification';
import { formatDate } from '@/utils/format';
import { cn } from '@/utils/cn';
import { Card } from '@/components/ui/Card';
import { Badge, Chip } from '@/components/ui/Badge';
import { ProgressBar } from '@/components/ui/Progress';
import { EmptyState, Skeleton } from '@/components/ui/Feedback';
import { DynamicIcon } from '@/components/ui/DynamicIcon';
import { PageHeader } from '@/components/feature/shared/PageHeader';

const TIER_LABEL: Record<string, string> = {
    bronze: 'Perunggu',
    silver: 'Perak',
    gold: 'Emas',
    platinum: 'Platinum',
};

export default function AchievementPage() {
    const [filter, setFilter] = useState<'all' | 'unlocked' | 'locked'>('all');

    const { data: achievements, loading } = useAsync(() => userService.listAchievements(), []);
    const unlocked = useProgressStore((state) => state.unlocked);

    const rows = (achievements ?? []).map((achievement) => {
        const state = unlocked.find((item) => item.achievementId === achievement.id);

        return {
            achievement,
            progress: state?.progress ?? 0,
            unlockedAt: state?.unlockedAt ?? null,
        };
    });

    const filtered = rows.filter((row) =>
        filter === 'unlocked' ? row.unlockedAt : filter === 'locked' ? !row.unlockedAt : true,
    );

    const unlockedCount = rows.filter((row) => row.unlockedAt).length;
    const totalXpEarned = rows
        .filter((row) => row.unlockedAt)
        .reduce((sum, row) => sum + row.achievement.xpReward, 0);

    return (
        <>
            <PageHeader
                title="Achievement"
                description="Lencana yang terbuka otomatis begitu kamu memenuhi kriterianya. Beberapa memberi bonus XP."
                action={
                    <div className="rounded-sm bg-surface-sunken px-4 py-2 text-center">
                        <p className="font-display text-lg font-extrabold">
                            {unlockedCount}/{rows.length}
                        </p>
                        <p className="text-[11px] text-fg-muted">terbuka</p>
                    </div>
                }
            />

            <div className="mb-5 grid gap-4 sm:grid-cols-3">
                <Card className="text-center">
                    <p className="font-display text-3xl font-extrabold text-secondary">{unlockedCount}</p>
                    <p className="mt-1 text-sm text-fg-muted">lencana terbuka</p>
                </Card>
                <Card className="text-center">
                    <p className="font-display text-3xl font-extrabold text-primary">{rows.length - unlockedCount}</p>
                    <p className="mt-1 text-sm text-fg-muted">masih terkunci</p>
                </Card>
                <Card className="text-center">
                    <p className="font-display text-3xl font-extrabold text-success">+{totalXpEarned}</p>
                    <p className="mt-1 text-sm text-fg-muted">XP bonus dari lencana</p>
                </Card>
            </div>

            <div className="mb-5 flex flex-wrap gap-2">
                <Chip active={filter === 'all'} onClick={() => setFilter('all')}>
                    Semua ({rows.length})
                </Chip>
                <Chip active={filter === 'unlocked'} onClick={() => setFilter('unlocked')}>
                    Terbuka ({unlockedCount})
                </Chip>
                <Chip active={filter === 'locked'} onClick={() => setFilter('locked')}>
                    Terkunci ({rows.length - unlockedCount})
                </Chip>
            </div>

            {loading ? (
                <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    {Array.from({ length: 9 }, (_, i) => (
                        <Skeleton key={i} className="h-40 w-full" />
                    ))}
                </div>
            ) : filtered.length === 0 ? (
                <EmptyState icon={<Award className="size-6" />} title="Tidak ada lencana di filter ini" />
            ) : (
                <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    {filtered.map(({ achievement, progress, unlockedAt }) => {
                        const tier = TIER_STYLES[achievement.tier] ?? TIER_STYLES.bronze;
                        const percent = achievementPercent(progress, achievement.threshold);
                        const isUnlocked = !!unlockedAt;

                        return (
                            <Card
                                key={achievement.id}
                                className={cn('flex h-full flex-col', !isUnlocked && 'opacity-80')}
                            >
                                <div className="flex items-start justify-between gap-3">
                                    <span
                                        className={cn(
                                            'grid size-12 shrink-0 place-items-center rounded-full ring-2',
                                            isUnlocked ? `${tier.bg} ${tier.text} ${tier.ring}` : 'bg-surface-sunken text-fg-muted ring-transparent',
                                        )}
                                    >
                                        {isUnlocked ? (
                                            <DynamicIcon
                                                name={achievement.icon}
                                                fallback={Trophy}
                                                className="size-6"
                                            />
                                        ) : (
                                            <Lock className="size-5" />
                                        )}
                                    </span>

                                    <div className="flex flex-col items-end gap-1.5">
                                        <Badge tone={isUnlocked ? 'secondary' : 'neutral'}>
                                            {TIER_LABEL[achievement.tier]}
                                        </Badge>
                                        {achievement.xpReward > 0 && (
                                            <span className="font-mono text-[11px] text-fg-muted">
                                                +{achievement.xpReward} XP
                                            </span>
                                        )}
                                    </div>
                                </div>

                                <h3 className="mt-4 font-display text-base font-bold">{achievement.title}</h3>
                                <p className="mt-1 flex-1 text-sm text-fg-muted">{achievement.description}</p>

                                {isUnlocked ? (
                                    <p className="mt-4 text-xs font-medium text-success">
                                        ✓ Terbuka {formatDate(unlockedAt!)}
                                    </p>
                                ) : (
                                    <div className="mt-4">
                                        <div className="mb-1.5 flex justify-between text-xs">
                                            <span className="text-fg-muted">Progres</span>
                                            <span className="font-mono">
                                                {progress}/{achievement.threshold}
                                            </span>
                                        </div>
                                        <ProgressBar value={percent} size="sm" tone="primary" />
                                    </div>
                                )}
                            </Card>
                        );
                    })}
                </div>
            )}
        </>
    );
}

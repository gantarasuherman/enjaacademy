import { useState } from 'react';
import { BookOpen, Clock, Flame, Target, TrendingUp, Zap } from 'lucide-react';
import { useAsync } from '@/hooks/useAsync';
import { learningService } from '@/services/api';
import { useProgressStore } from '@/store/progressStore';
import { useAuthStore } from '@/store/authStore';
import { levelInfo } from '@/utils/gamification';
import { formatNumber } from '@/utils/format';
import { Card, CardHeader } from '@/components/ui/Card';
import { Chip } from '@/components/ui/Badge';
import { ProgressBar, ProgressRing } from '@/components/ui/Progress';
import { Skeleton } from '@/components/ui/Feedback';
import { PageHeader } from '@/components/feature/shared/PageHeader';
import { StatCard } from '@/components/feature/shared/StatCard';
import { StreakCalendar } from '@/components/feature/shared/StreakCalendar';
import { ActivityAreaChart, SkillPieChart } from '@/components/feature/shared/charts';

const RANGES = [
    { id: 7, label: '7 hari' },
    { id: 14, label: '14 hari' },
    { id: 30, label: '30 hari' },
];

export default function ProgressPage() {
    const [range, setRange] = useState(30);
    const [metric, setMetric] = useState<'xp' | 'minutes'>('xp');

    const user = useAuthStore((state) => state.user);
    const { xp, activity, progress, modulePercent, streakDays, lessonsCompleted } = useProgressStore();
    const { data: modules, loading } = useAsync(() => learningService.listModules(), []);

    const info = levelInfo(xp);
    const streak = streakDays;

    const windowed = activity.slice(-range);
    const totalMinutes = windowed.reduce((sum, day) => sum + day.minutes, 0);
    const totalXp = windowed.reduce((sum, day) => sum + day.xp, 0);
    const activeDays = windowed.filter((day) => day.minutes > 0).length;
    const completedLessons = lessonsCompleted;

    // Time split per skill, derived from each module's completion.
    const skillSplit = (modules ?? [])
        .map((module) => ({ name: module.name, value: modulePercent(String(module.id)) }))
        .filter((row) => row.value > 0);

    return (
        <>
            <PageHeader
                title="Progress"
                description="Rekap perjalanan belajarmu — XP, waktu belajar, konsistensi, dan penyelesaian per modul."
            />

            <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <StatCard icon={Zap} label="Total XP" value={formatNumber(xp)} hint={`Level ${info.level}`} tone="secondary" />
                <StatCard
                    icon={Clock}
                    label={`Waktu ${range} hari`}
                    value={`${Math.floor(totalMinutes / 60)}j ${totalMinutes % 60}m`}
                    hint={`${activeDays} hari aktif`}
                    tone="info"
                />
                <StatCard icon={Flame} label="Streak" value={`${streak} hari`} hint="Berturut-turut" tone="danger" />
                <StatCard
                    icon={BookOpen}
                    label="Materi selesai"
                    value={completedLessons}
                    hint={`${progress.length} total tercatat`}
                    tone="success"
                />
            </div>

            <div className="mt-6 grid gap-6 lg:grid-cols-3">
                <div className="space-y-6 lg:col-span-2">
                    <Card>
                        <CardHeader
                            title="Grafik aktivitas"
                            subtitle={`${metric === 'xp' ? formatNumber(totalXp) + ' XP' : totalMinutes + ' menit'} dalam ${range} hari terakhir`}
                            action={
                                <div className="flex flex-wrap gap-1.5">
                                    {RANGES.map((item) => (
                                        <Chip key={item.id} active={range === item.id} onClick={() => setRange(item.id)}>
                                            {item.label}
                                        </Chip>
                                    ))}
                                </div>
                            }
                        />

                        <div className="mb-4 flex gap-1.5">
                            <Chip active={metric === 'xp'} onClick={() => setMetric('xp')}>
                                XP
                            </Chip>
                            <Chip active={metric === 'minutes'} onClick={() => setMetric('minutes')}>
                                Menit
                            </Chip>
                        </div>

                        {windowed.length > 0 ? (
                            <ActivityAreaChart
                                data={windowed}
                                dataKey={metric}
                                suffix={metric === 'xp' ? ' XP' : ' menit'}
                            />
                        ) : (
                            <Skeleton className="h-60 w-full" />
                        )}
                    </Card>

                    <Card>
                        <CardHeader title="Penyelesaian per modul" />

                        {loading ? (
                            <div className="space-y-4">
                                {Array.from({ length: 5 }, (_, i) => (
                                    <Skeleton key={i} className="h-12 w-full" />
                                ))}
                            </div>
                        ) : (
                            <div className="space-y-4">
                                {(modules ?? []).map((module) => {
                                    const percent = modulePercent(String(module.id));

                                    return (
                                        <div key={module.id}>
                                            <div className="mb-1.5 flex items-center justify-between gap-3">
                                                <span className="truncate text-sm font-medium">{module.name}</span>
                                                <span className="shrink-0 font-mono text-xs text-fg-muted">{percent}%</span>
                                            </div>
                                            <ProgressBar
                                                value={percent}
                                                size="sm"
                                                tone={percent === 100 ? 'success' : percent > 0 ? 'secondary' : 'primary'}
                                            />
                                        </div>
                                    );
                                })}
                            </div>
                        )}
                    </Card>

                    <Card>
                        <CardHeader title="Kalender konsistensi" subtitle="12 minggu terakhir" />
                        <StreakCalendar activity={activity} />
                    </Card>
                </div>

                <div className="space-y-6">
                    <Card className="text-center">
                        <CardHeader title="Level saat ini" className="justify-center text-center" />
                        <ProgressRing value={info.percent} size={140} stroke={11} tone="secondary">
                            <div>
                                <p className="font-display text-3xl font-extrabold">{info.level}</p>
                                <p className="text-[11px] text-fg-muted">level</p>
                            </div>
                        </ProgressRing>
                        <p className="mt-4 text-sm text-fg-muted">
                            {formatNumber(info.xpRemaining)} XP lagi ke level {info.level + 1}
                        </p>
                    </Card>

                    <Card>
                        <CardHeader title="Target harian" />
                        <div className="space-y-3">
                            {windowed.slice(-7).map((day) => {
                                const goal = user?.dailyGoalMinutes ?? 20;
                                const percent = Math.min(100, Math.round((day.minutes / goal) * 100));

                                return (
                                    <div key={day.date}>
                                        <div className="mb-1 flex justify-between text-xs">
                                            <span className="text-fg-muted">{day.date.slice(5)}</span>
                                            <span className="font-mono">{day.minutes}m</span>
                                        </div>
                                        <ProgressBar value={percent} size="sm" tone={percent >= 100 ? 'success' : 'secondary'} />
                                    </div>
                                );
                            })}
                        </div>
                    </Card>

                    {skillSplit.length > 0 && (
                        <Card>
                            <CardHeader title="Sebaran skill" subtitle="Persentase penyelesaian tiap modul" />
                            <SkillPieChart data={skillSplit} />
                        </Card>
                    )}

                    <Card className="bg-primary-50 dark:bg-primary/10">
                        <div className="flex items-start gap-3">
                            <TrendingUp className="size-5 shrink-0 text-primary" />
                            <div>
                                <p className="text-sm font-bold">Rata-rata harianmu</p>
                                <p className="mt-1 text-sm text-fg-muted">
                                    {activeDays > 0
                                        ? `${Math.round(totalMinutes / activeDays)} menit per hari aktif — pertahankan ritmenya.`
                                        : 'Belum ada data. Mulai satu materi hari ini.'}
                                </p>
                            </div>
                        </div>
                    </Card>

                    <Card>
                        <div className="flex items-center gap-3">
                            <Target className="size-5 text-secondary" />
                            <div>
                                <p className="text-sm font-bold">Target level</p>
                                <p className="text-sm text-fg-muted">{user?.targetLevel ?? 'B2'} — terus jalan.</p>
                            </div>
                        </div>
                    </Card>
                </div>
            </div>
        </>
    );
}

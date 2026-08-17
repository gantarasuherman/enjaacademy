import { Link } from 'react-router-dom';
import {
    ArrowRight,
    Award,
    BookMarked,
    Flame,
    Layers,
    Play,
    Target,
    TrendingUp,
    Trophy,
    Zap,
} from 'lucide-react';
import { useAuthStore } from '@/store/authStore';
import { useProgressStore } from '@/store/progressStore';
import { useFlashcardStore } from '@/store/flashcardStore';
import { useAsync } from '@/hooks/useAsync';
import { learningService, userService, weakWordService } from '@/services/api';
import { levelInfo, achievementPercent } from '@/utils/gamification';
import { formatCompact, formatNumber } from '@/utils/format';
import { Card, CardHeader } from '@/components/ui/Card';
import { Button } from '@/components/ui/Button';
import { Badge } from '@/components/ui/Badge';
import { ProgressBar, ProgressRing } from '@/components/ui/Progress';
import { Avatar } from '@/components/ui/Avatar';
import { Alert, Skeleton } from '@/components/ui/Feedback';
import { StatCard } from '@/components/feature/shared/StatCard';
import { StreakCalendar } from '@/components/feature/shared/StreakCalendar';
import { ActivityAreaChart } from '@/components/feature/shared/charts';

export default function DashboardPage() {
    const user = useAuthStore((state) => state.user);
    const { xp, activity, unlocked, todayMinutes, modulePercent, streakDays } = useProgressStore();
    const dueCount = useFlashcardStore((state) => state.dueCount());

    const { data: modules, loading: modulesLoading } = useAsync(() => learningService.listModules(), []);
    const { data: achievements } = useAsync(() => userService.listAchievements(), []);
    const { data: leaderboard } = useAsync(() => userService.getLeaderboard(), []);
    const { data: weakWords } = useAsync(() => weakWordService.list(), []);
    const weakCount = weakWords?.length ?? 0;

    const info = levelInfo(xp);
    const streak = streakDays;
    const goal = user?.dailyGoalMinutes ?? 20;
    const minutesToday = todayMinutes();
    const goalPercent = Math.min(100, Math.round((minutesToday / goal) * 100));

    // "Continue learning" = the module with the most progress that isn't done.
    const inProgress = (modules ?? [])
        .map((module) => ({ module, percent: modulePercent(String(module.id)) }))
        .filter((row) => row.percent > 0 && row.percent < 100)
        .sort((a, b) => b.percent - a.percent);

    const nextUp = inProgress[0] ?? { module: modules?.[0], percent: 0 };

    const recentUnlocks = (achievements ?? [])
        .map((achievement) => ({
            achievement,
            state: unlocked.find((u) => u.achievementId === achievement.id),
        }))
        .filter((row) => row.state?.unlockedAt)
        .slice(0, 4);

    const nearestLocked = (achievements ?? [])
        .map((achievement) => ({
            achievement,
            state: unlocked.find((u) => u.achievementId === achievement.id),
        }))
        .filter((row) => row.state && !row.state.unlockedAt)
        .sort(
            (a, b) =>
                achievementPercent(b.state!.progress, b.achievement.threshold) -
                achievementPercent(a.state!.progress, a.achievement.threshold),
        )
        .slice(0, 3);

    return (
        <>
            {/* ------------------------------------------------------ Welcome */}
            <div className="mb-6 flex flex-wrap items-center justify-between gap-4">
                <div className="flex items-center gap-4">
                    <Avatar name={user?.name ?? 'Tamu'} src={user?.avatar} size="lg" ring />
                    <div>
                        <h1 className="font-display text-2xl font-extrabold tracking-tight">
                            Halo, {user?.name?.split(' ')[0] ?? 'Tamu'} 👋
                        </h1>
                        <p className="mt-0.5 text-sm text-fg-muted">
                            {minutesToday >= goal
                                ? 'Target hari ini sudah tercapai. Mantap!'
                                : `Tinggal ${goal - minutesToday} menit lagi untuk target hari ini.`}
                        </p>
                    </div>
                </div>

                <Badge tone="secondary" icon={<Flame className="size-3.5" />} className="px-3 py-1.5 text-sm">
                    Streak {streak} hari
                </Badge>
            </div>

            {/* --------------------------------------------- Weak words callout */}
            {weakCount > 0 && (
                <Alert tone="warning" className="mb-6">
                    <span className="flex flex-wrap items-center justify-between gap-3">
                        <span>
                            <strong>{weakCount}</strong> kata butuh direview — sering salah di Kuis Harian.
                        </span>
                        <Link to="/app/weak-words" className="font-semibold text-primary hover:underline">
                            Review sekarang →
                        </Link>
                    </span>
                </Alert>
            )}

            {/* -------------------------------------------------------- Stats */}
            <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <StatCard icon={Zap} label="Total XP" value={formatNumber(xp)} hint={`Level ${info.level}`} tone="secondary" />
                <StatCard icon={Flame} label="Streak" value={`${streak} hari`} hint="Jangan sampai putus" tone="danger" />
                <StatCard
                    icon={Layers}
                    label="Kartu jatuh tempo"
                    value={dueCount}
                    hint={dueCount > 0 ? 'Siap direview' : 'Semua sudah direview'}
                    tone="info"
                />
                <StatCard
                    icon={Award}
                    label="Pencapaian"
                    value={unlocked.filter((u) => u.unlockedAt).length}
                    hint={`dari ${achievements?.length ?? 0} lencana`}
                    tone="success"
                />
            </div>

            <div className="mt-6 grid gap-6 lg:grid-cols-3">
                {/* ------------------------------------------------ Main column */}
                <div className="space-y-6 lg:col-span-2">
                    {/* Continue learning */}
                    <Card className="relative overflow-hidden bg-gradient-to-br from-primary to-primary-700 text-white">
                        <div aria-hidden className="absolute -right-12 -top-12 size-48 rounded-full bg-white/8" />

                        <div className="relative">
                            <p className="text-sm font-medium text-white/80">Lanjutkan belajar</p>

                            {modulesLoading ? (
                                <Skeleton className="mt-2 h-8 w-56 bg-white/20" />
                            ) : nextUp.module ? (
                                <>
                                    <h2 className="mt-1 font-display text-2xl font-extrabold">{nextUp.module.name}</h2>
                                    <p className="mt-1.5 max-w-lg text-sm text-white/80">{nextUp.module.description}</p>

                                    <div className="mt-5 max-w-sm">
                                        <div className="mb-1.5 flex justify-between text-xs text-white/80">
                                            <span>Progres modul</span>
                                            <span className="font-mono font-semibold">{nextUp.percent}%</span>
                                        </div>
                                        <div className="h-2 overflow-hidden rounded-full bg-white/20">
                                            <div
                                                className="h-full rounded-full bg-secondary transition-[width] duration-500"
                                                style={{ width: `${nextUp.percent}%` }}
                                            />
                                        </div>
                                    </div>

                                    <Button
                                        to={`/app/learning/${nextUp.module.slug}`}
                                        variant="secondary"
                                        className="mt-5"
                                        icon={<Play className="size-4" />}
                                    >
                                        Lanjutkan
                                    </Button>
                                </>
                            ) : (
                                <p className="mt-2 text-white/80">Belum ada modul. Mulai dari katalog belajar.</p>
                            )}
                        </div>
                    </Card>

                    {/* Weekly activity */}
                    <Card>
                        <CardHeader
                            title="Aktivitas 30 hari terakhir"
                            subtitle="XP yang kamu kumpulkan setiap hari"
                            action={
                                <Button to="/app/progress" variant="ghost" size="sm" iconRight={<ArrowRight className="size-3.5" />}>
                                    Detail
                                </Button>
                            }
                        />
                        {activity.length > 0 ? (
                            <ActivityAreaChart data={activity} />
                        ) : (
                            <Skeleton className="h-60 w-full" />
                        )}
                    </Card>

                    {/* Module progress */}
                    <Card>
                        <CardHeader
                            title="Progres per modul"
                            action={
                                <Button to="/app/learning" variant="ghost" size="sm" iconRight={<ArrowRight className="size-3.5" />}>
                                    Semua modul
                                </Button>
                            }
                        />

                        <div className="space-y-4">
                            {modulesLoading
                                ? Array.from({ length: 4 }, (_, i) => <Skeleton key={i} className="h-12 w-full" />)
                                : (modules ?? []).slice(0, 5).map((module) => {
                                      const percent = modulePercent(String(module.id));

                                      return (
                                          <Link
                                              key={module.id}
                                              to={`/app/learning/${module.slug}`}
                                              className="block rounded-sm p-2 transition hover:bg-surface-sunken"
                                          >
                                              <div className="mb-1.5 flex items-center justify-between gap-3">
                                                  <span className="truncate text-sm font-semibold">{module.name}</span>
                                                  <span className="shrink-0 font-mono text-xs text-fg-muted">{percent}%</span>
                                              </div>
                                              <ProgressBar
                                                  value={percent}
                                                  size="sm"
                                                  tone={percent === 100 ? 'success' : 'secondary'}
                                              />
                                          </Link>
                                      );
                                  })}
                        </div>
                    </Card>
                </div>

                {/* ----------------------------------------------- Side column */}
                <div className="space-y-6">
                    {/* Daily goal */}
                    <Card className="text-center">
                        <CardHeader title="Target harian" className="justify-center text-center" />
                        <ProgressRing value={goalPercent} size={132} stroke={10} tone={goalPercent >= 100 ? 'success' : 'secondary'}>
                            <div>
                                <p className="font-display text-2xl font-extrabold">{minutesToday}</p>
                                <p className="text-xs text-fg-muted">dari {goal} menit</p>
                            </div>
                        </ProgressRing>
                        <p className="mt-4 text-sm text-fg-muted">
                            {goalPercent >= 100 ? '🎉 Target tercapai hari ini!' : 'Sedikit lagi, ayo lanjut.'}
                        </p>
                    </Card>

                    {/* Level */}
                    <Card>
                        <CardHeader title={`Level ${info.level}`} subtitle={`${formatCompact(xp)} XP total`} />
                        <ProgressBar value={info.percent} tone="secondary" showValue label="Menuju level berikutnya" />
                        <p className="mt-2 text-xs text-fg-muted">
                            {formatNumber(info.xpRemaining)} XP lagi untuk mencapai level {info.level + 1}.
                        </p>
                    </Card>

                    {/* Streak calendar */}
                    <Card>
                        <CardHeader title="Kalender belajar" subtitle="12 minggu terakhir" />
                        <StreakCalendar activity={activity} />
                    </Card>

                    {/* Achievements */}
                    <Card>
                        <CardHeader
                            title="Pencapaian terbaru"
                            action={
                                <Button to="/app/achievement" variant="ghost" size="sm">
                                    Semua
                                </Button>
                            }
                        />

                        {recentUnlocks.length > 0 ? (
                            <ul className="space-y-3">
                                {recentUnlocks.map(({ achievement }) => (
                                    <li key={achievement.id} className="flex items-center gap-3">
                                        <span className="grid size-9 shrink-0 place-items-center rounded-full bg-secondary-100 text-secondary-700 dark:bg-secondary/20 dark:text-secondary-300">
                                            <Trophy className="size-4" />
                                        </span>
                                        <div className="min-w-0">
                                            <p className="truncate text-sm font-semibold">{achievement.title}</p>
                                            <p className="truncate text-xs text-fg-muted">{achievement.description}</p>
                                        </div>
                                    </li>
                                ))}
                            </ul>
                        ) : (
                            <p className="text-sm text-fg-muted">Belum ada lencana. Selesaikan satu materi untuk memulai.</p>
                        )}

                        {nearestLocked.length > 0 && (
                            <div className="mt-5 border-t border-[var(--surface-border)] pt-4">
                                <p className="mb-3 text-xs font-bold uppercase tracking-wide text-fg-muted">
                                    Hampir terbuka
                                </p>
                                <div className="space-y-3">
                                    {nearestLocked.map(({ achievement, state }) => (
                                        <div key={achievement.id}>
                                            <div className="mb-1 flex justify-between text-xs">
                                                <span className="truncate font-medium">{achievement.title}</span>
                                                <span className="shrink-0 font-mono text-fg-muted">
                                                    {state!.progress}/{achievement.threshold}
                                                </span>
                                            </div>
                                            <ProgressBar
                                                value={achievementPercent(state!.progress, achievement.threshold)}
                                                size="sm"
                                                tone="primary"
                                            />
                                        </div>
                                    ))}
                                </div>
                            </div>
                        )}
                    </Card>

                    {/* Leaderboard preview */}
                    <Card>
                        <CardHeader
                            title="Papan peringkat"
                            action={
                                <Button to="/app/leaderboard" variant="ghost" size="sm">
                                    Semua
                                </Button>
                            }
                        />
                        <ol className="space-y-2.5">
                            {(leaderboard ?? []).slice(0, 5).map((entry) => (
                                <li
                                    key={entry.userId}
                                    className={`flex items-center gap-3 rounded-sm px-2 py-1.5 ${
                                        entry.isCurrentUser ? 'bg-primary-100 dark:bg-primary/15' : ''
                                    }`}
                                >
                                    <span className="w-5 shrink-0 text-center font-mono text-xs font-bold text-fg-muted">
                                        {entry.rank}
                                    </span>
                                    <Avatar name={entry.name} size="xs" />
                                    <span className="min-w-0 flex-1 truncate text-sm font-medium">{entry.name}</span>
                                    <span className="shrink-0 font-mono text-xs text-fg-muted">
                                        {formatCompact(entry.xp)}
                                    </span>
                                </li>
                            ))}
                        </ol>
                    </Card>

                    {/* Quick links */}
                    <div className="grid grid-cols-2 gap-3">
                        <Button to="/app/flashcard" variant="outline" icon={<Layers className="size-4" />}>
                            Flashcard
                        </Button>
                        <Button to="/app/quiz" variant="outline" icon={<Target className="size-4" />}>
                            Quiz
                        </Button>
                        <Button to="/app/vocabulary" variant="outline" icon={<BookMarked className="size-4" />}>
                            Kosakata
                        </Button>
                        <Button to="/app/progress" variant="outline" icon={<TrendingUp className="size-4" />}>
                            Progres
                        </Button>
                        <Button to="/app/weak-words" variant="outline" icon={<Flame className="size-4" />} className="col-span-2">
                            Review Kata Lemah
                        </Button>
                    </div>
                </div>
            </div>
        </>
    );
}

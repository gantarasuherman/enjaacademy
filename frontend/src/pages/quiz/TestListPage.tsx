import { Link } from 'react-router-dom';
import { Clock, ListChecks, Play, Timer, Zap } from 'lucide-react';
import { useAsync } from '@/hooks/useAsync';
import { quizService } from '@/services/api';
import { formatDuration } from '@/utils/format';
import { Card, CardHeader } from '@/components/ui/Card';
import { Badge, CefrBadge } from '@/components/ui/Badge';
import { EmptyState, Skeleton } from '@/components/ui/Feedback';
import { PageHeader } from '@/components/feature/shared/PageHeader';

export default function TestListPage() {
    const { data: tests, loading } = useAsync(() => quizService.list({ category: 'test' }), []);

    return (
        <>
            <PageHeader
                title="Tes"
                description="Simulasi ujian komprehensif — puluhan soal dari level dasar sampai mahir dalam satu sesi berwaktu, seperti placement test sungguhan."
            />

            {loading ? (
                <div className="grid gap-4 sm:grid-cols-2">
                    {Array.from({ length: 2 }, (_, i) => (
                        <Skeleton key={i} className="h-56 w-full" />
                    ))}
                </div>
            ) : !tests || tests.length === 0 ? (
                <EmptyState
                    icon={<Timer className="size-6" />}
                    title="Belum ada tes tersedia"
                    description="Tes komprehensif sedang disiapkan — nantikan segera."
                />
            ) : (
                <div className="grid gap-4 sm:grid-cols-2">
                    {tests.map((test) => (
                        <Link key={test.id} to={`/app/quiz/${test.id}`} className="block h-full">
                            <Card interactive className="flex h-full flex-col">
                                <div className="flex items-start justify-between gap-3">
                                    <span className="grid size-11 shrink-0 place-items-center rounded-sm bg-primary-100 text-primary dark:bg-primary/20">
                                        <Timer className="size-5" />
                                    </span>
                                    {test.cefr && <CefrBadge level={test.cefr} />}
                                </div>

                                <h3 className="mt-4 font-display text-base font-bold leading-snug">{test.title}</h3>
                                <p className="mt-1.5 line-clamp-2 flex-1 text-sm text-fg-muted">{test.description}</p>

                                <div className="mt-4 grid grid-cols-3 gap-2 text-center text-xs">
                                    <div className="rounded-sm bg-surface-sunken py-2">
                                        <p className="font-display text-sm font-extrabold">{test.questionIds.length}</p>
                                        <p className="text-fg-muted">soal</p>
                                    </div>
                                    <div className="rounded-sm bg-surface-sunken py-2">
                                        <p className="font-display text-sm font-extrabold">
                                            {test.timeLimitSeconds ? formatDuration(test.timeLimitSeconds) : '∞'}
                                        </p>
                                        <p className="text-fg-muted">waktu</p>
                                    </div>
                                    <div className="rounded-sm bg-surface-sunken py-2">
                                        <p className="font-display text-sm font-extrabold">{test.passScore}%</p>
                                        <p className="text-fg-muted">lulus</p>
                                    </div>
                                </div>

                                <div className="mt-4 flex items-center justify-between">
                                    <Badge tone="secondary" icon={<Zap className="size-3" />}>
                                        +{test.xpReward} XP
                                    </Badge>
                                    <span className="inline-flex items-center gap-1.5 text-sm font-semibold text-primary">
                                        <Play className="size-3.5" /> Mulai tes
                                    </span>
                                </div>
                            </Card>
                        </Link>
                    ))}
                </div>
            )}

            <Card className="mt-8 flex flex-wrap items-center gap-4">
                <ListChecks className="size-6 shrink-0 text-secondary" />
                <div className="min-w-0 flex-1">
                    <p className="font-display font-bold">Berbeda dari kuis biasa</p>
                    <p className="mt-0.5 text-sm text-fg-muted">
                        Tes mencakup banyak level sekaligus dalam satu sesi panjang berwaktu — begitu timer berjalan,
                        tidak bisa dijeda. Pastikan kamu punya waktu luang sebelum memulai.
                    </p>
                </div>
                <Clock className="hidden size-5 text-fg-muted sm:block" />
            </Card>

            <Card className="mt-6">
                <CardHeader title="Belum siap?" subtitle="Pemanasan dulu dengan kuis singkat per topik." />
                <Link to="/app/quiz" className="inline-flex items-center gap-1.5 text-sm font-semibold text-primary hover:underline">
                    Lihat semua kuis <Play className="size-3.5" />
                </Link>
            </Card>
        </>
    );
}

import { useState } from 'react';
import { Link } from 'react-router-dom';
import { Clock, GraduationCap, Play, Target, Zap } from 'lucide-react';
import { useAsync } from '@/hooks/useAsync';
import { quizService } from '@/services/api';
import { formatDuration } from '@/utils/format';
import { Card } from '@/components/ui/Card';
import { CefrBadge, Chip, Badge } from '@/components/ui/Badge';
import { EmptyState, Skeleton } from '@/components/ui/Feedback';
import { PageHeader } from '@/components/feature/shared/PageHeader';

const LEVELS = ['all', 'A1', 'A2', 'B1', 'B2', 'C1'];

export default function QuizListPage() {
    const [cefr, setCefr] = useState('all');
    const { data: quizzes, loading } = useAsync(() => quizService.list(), []);

    const filtered = (quizzes ?? []).filter((quiz) => cefr === 'all' || quiz.cefr === cefr);

    return (
        <>
            <PageHeader
                title="Quiz"
                description="Tujuh tipe soal: pilihan ganda, benar-salah, isian, ketik, cocokkan, susun kata, dan menyimak — semuanya dengan pembahasan."
            />

            <div className="mb-5 flex flex-wrap gap-2">
                {LEVELS.map((level) => (
                    <Chip key={level} active={cefr === level} onClick={() => setCefr(level)}>
                        {level === 'all' ? 'Semua level' : level}
                    </Chip>
                ))}
            </div>

            {loading ? (
                <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    {Array.from({ length: 6 }, (_, i) => (
                        <Skeleton key={i} className="h-52 w-full" />
                    ))}
                </div>
            ) : filtered.length === 0 ? (
                <EmptyState
                    icon={<GraduationCap className="size-6" />}
                    title="Belum ada kuis di level ini"
                    description="Coba pilih level lain."
                />
            ) : (
                <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    {filtered.map((quiz) => (
                        <Link key={quiz.id} to={`/app/quiz/${quiz.id}`} className="block h-full">
                            <Card interactive className="flex h-full flex-col">
                                <div className="flex items-start justify-between gap-3">
                                    <span className="grid size-11 shrink-0 place-items-center rounded-sm bg-success/12 text-emerald-600 dark:text-emerald-400">
                                        <GraduationCap className="size-5" />
                                    </span>
                                    <CefrBadge level={quiz.cefr} />
                                </div>

                                <h3 className="mt-4 font-display text-base font-bold leading-snug">{quiz.title}</h3>
                                <p className="mt-1.5 line-clamp-2 flex-1 text-sm text-fg-muted">{quiz.description}</p>

                                <div className="mt-4 grid grid-cols-3 gap-2 text-center text-xs">
                                    <div className="rounded-sm bg-surface-sunken py-2">
                                        <p className="font-display text-sm font-extrabold">{quiz.questionIds.length}</p>
                                        <p className="text-fg-muted">soal</p>
                                    </div>
                                    <div className="rounded-sm bg-surface-sunken py-2">
                                        <p className="font-display text-sm font-extrabold">
                                            {quiz.timeLimitSeconds ? formatDuration(quiz.timeLimitSeconds) : '∞'}
                                        </p>
                                        <p className="text-fg-muted">waktu</p>
                                    </div>
                                    <div className="rounded-sm bg-surface-sunken py-2">
                                        <p className="font-display text-sm font-extrabold">{quiz.passScore}%</p>
                                        <p className="text-fg-muted">lulus</p>
                                    </div>
                                </div>

                                <div className="mt-4 flex items-center justify-between">
                                    <Badge tone="secondary" icon={<Zap className="size-3" />}>
                                        +{quiz.xpReward} XP
                                    </Badge>
                                    <span className="inline-flex items-center gap-1.5 text-sm font-semibold text-primary">
                                        <Play className="size-3.5" /> Mulai
                                    </span>
                                </div>
                            </Card>
                        </Link>
                    ))}
                </div>
            )}

            <Card className="mt-8 flex flex-wrap items-center gap-4">
                <Target className="size-6 shrink-0 text-secondary" />
                <div className="min-w-0 flex-1">
                    <p className="font-display font-bold">Kenapa nilai kuis bisa turun?</p>
                    <p className="mt-0.5 text-sm text-fg-muted">
                        Soal diacak setiap percobaan, jadi kamu tidak bisa menghafal urutannya — hanya pemahaman yang
                        terbawa.
                    </p>
                </div>
                <Clock className="hidden size-5 text-fg-muted sm:block" />
            </Card>
        </>
    );
}

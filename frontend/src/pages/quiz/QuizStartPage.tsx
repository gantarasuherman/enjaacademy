import { useEffect } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { motion } from 'framer-motion';
import { CheckCircle2, GraduationCap, History, Play, RotateCcw, Target, Trophy, XCircle } from 'lucide-react';
import { useAsync } from '@/hooks/useAsync';
import { useQuizStore } from '@/store/quizStore';
import { quizService } from '@/services/api';
import { formatDate, formatDuration } from '@/utils/format';
import { Card, CardHeader } from '@/components/ui/Card';
import { Button } from '@/components/ui/Button';
import { Badge } from '@/components/ui/Badge';
import { Alert, EmptyState, PageLoader } from '@/components/ui/Feedback';
import { StatCard } from '@/components/feature/shared/StatCard';

export default function QuizStartPage() {
    const { quizId = '' } = useParams();
    const navigate = useNavigate();

    const { overview, overviewLoading, loadOverview } = useQuizStore();
    const { data: quiz, loading: quizLoading } = useAsync(() => quizService.get(quizId), [quizId]);

    useEffect(() => {
        void loadOverview(quizId);
    }, [quizId, loadOverview]);

    if (quizLoading || overviewLoading || !overview) {
        return <PageLoader label="Memuat informasi kuis…" />;
    }

    if (!quiz) {
        return (
            <EmptyState
                icon={<GraduationCap className="size-6" />}
                title="Kuis tidak ditemukan"
                description="Kuis ini mungkin sudah dihapus atau belum diterbitkan."
                action={
                    <Button to="/app/quiz" variant="outline">
                        Kembali ke daftar kuis
                    </Button>
                }
            />
        );
    }

    const attempted = overview.attemptsUsed > 0;
    const limitLabel = overview.attemptsLimit === null ? '∞ Tidak terbatas' : `Maks. ${overview.attemptsLimit}`;

    return (
        <div className="mx-auto max-w-2xl">
            <motion.div
                initial={{ opacity: 0, y: 8 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.3, ease: [0.16, 1, 0.3, 1] }}
            >
                <Card>
                    <div className="flex items-start justify-between gap-3">
                        <div className="min-w-0">
                            <h1 className="font-display text-2xl font-extrabold leading-snug">{quiz.title}</h1>
                            {quiz.description && <p className="mt-1.5 text-sm text-fg-muted">{quiz.description}</p>}
                        </div>
                        <Badge tone={attempted ? 'success' : 'neutral'}>
                            {attempted ? '✓ Sudah dikerjakan' : 'Belum dikerjakan'}
                        </Badge>
                    </div>

                    <div className="mt-5 grid grid-cols-3 gap-2 text-center text-xs">
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

                    {attempted && (
                        <div className="mt-5 grid gap-3 sm:grid-cols-4">
                            <StatCard icon={Trophy} label="Terakhir" value={overview.latest ?? '—'} tone="info" />
                            <StatCard icon={Target} label="Terbaik" value={overview.best ?? '—'} tone="success" />
                            <StatCard icon={XCircle} label="Terendah" value={overview.worst ?? '—'} tone="warning" />
                            <StatCard icon={History} label="Rata-rata" value={overview.average ?? '—'} tone="primary" />
                        </div>
                    )}

                    <div className="mt-5 flex items-center justify-between rounded-sm bg-surface-sunken px-4 py-3">
                        <div>
                            <p className="text-xs text-fg-muted">Percobaan</p>
                            <p className="font-display text-sm font-bold">
                                {overview.attemptsUsed}
                                {overview.attemptsLimit !== null && ` / ${overview.attemptsLimit}`}
                                {overview.attemptsLeft !== null && (
                                    <span className="ml-2 font-normal text-fg-muted">
                                        Sisa kesempatan: {overview.attemptsLeft}
                                    </span>
                                )}
                            </p>
                        </div>
                        <Badge tone={overview.canAttempt ? 'secondary' : 'danger'}>{limitLabel}</Badge>
                    </div>

                    {!overview.canAttempt && (
                        <Alert tone="warning" title="Kesempatan kuis telah habis" className="mt-5">
                            Kamu telah menggunakan seluruh {overview.attemptsLimit} kesempatan untuk kuis ini.
                            {overview.best !== null && ` Nilai terbaikmu: ${overview.best}/100.`}
                        </Alert>
                    )}

                    <div className="mt-6 flex flex-wrap justify-center gap-2">
                        {overview.canAttempt ? (
                            <Button
                                onClick={() => navigate(`/app/quiz/${quizId}/play`)}
                                icon={attempted ? <RotateCcw className="size-4" /> : <Play className="size-4" />}
                            >
                                {attempted ? 'Mulai Quiz Lagi' : 'Mulai Quiz'}
                            </Button>
                        ) : null}
                        <Button to="/app/quiz" variant="outline">
                            Kembali ke Materi
                        </Button>
                    </div>
                </Card>
            </motion.div>

            {overview.history.length > 0 && (
                <Card className="mt-5">
                    <CardHeader
                        title="Riwayat Nilai"
                        subtitle={`${overview.history.length} percobaan · rata-rata ${overview.average}`}
                        action={<History className="size-5 text-fg-muted" />}
                    />

                    <ol className="space-y-2">
                        {overview.history.map((attempt) => (
                            <li
                                key={attempt.attemptNumber}
                                className="flex items-center justify-between rounded-sm bg-surface-sunken px-3 py-2 text-sm"
                            >
                                <div className="flex items-center gap-3">
                                    <span className="grid size-7 shrink-0 place-items-center rounded-full bg-surface text-xs font-bold text-fg-muted">
                                        #{attempt.attemptNumber}
                                    </span>
                                    <div>
                                        <p className="font-semibold">{attempt.score} / 100</p>
                                        <p className="text-xs text-fg-muted">
                                            {attempt.correctCount}/{attempt.totalCount} benar ·{' '}
                                            {formatDate(attempt.completedAt, 'd MMMM yyyy • HH:mm')}
                                        </p>
                                    </div>
                                </div>
                                {attempt.passed ? (
                                    <CheckCircle2 className="size-4 shrink-0 text-success" />
                                ) : (
                                    <XCircle className="size-4 shrink-0 text-danger" />
                                )}
                            </li>
                        ))}
                    </ol>
                </Card>
            )}
        </div>
    );
}

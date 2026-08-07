import { useEffect } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { motion } from 'framer-motion';
import { CheckCircle2, Clock, Lightbulb, RotateCcw, Target, XCircle, Zap } from 'lucide-react';
import { useQuizStore } from '@/store/quizStore';
import { cn } from '@/utils/cn';
import { formatDuration } from '@/utils/format';
import { Card, CardHeader } from '@/components/ui/Card';
import { Button } from '@/components/ui/Button';
import { Badge } from '@/components/ui/Badge';
import { ProgressRing } from '@/components/ui/Progress';
import { EmptyState } from '@/components/ui/Feedback';
import { StatCard } from '@/components/feature/shared/StatCard';

export default function QuizResultPage() {
    const { quizId = '' } = useParams();
    const navigate = useNavigate();

    const { quiz, questions, result, records, start } = useQuizStore();

    // A direct visit (or a refresh) has no result in memory to show.
    useEffect(() => {
        if (!result) navigate(`/app/quiz/${quizId}`, { replace: true });
    }, [result, quizId, navigate]);

    if (!result || !quiz) {
        return (
            <EmptyState
                title="Hasil tidak tersedia"
                description="Mulai kuisnya lagi untuk melihat hasil."
                action={<Button to={`/app/quiz/${quizId}`}>Mulai kuis</Button>}
            />
        );
    }

    const answerMap = new Map(records.map((record) => [record.questionId, record]));

    return (
        <div className="mx-auto max-w-3xl">
            {/* Score */}
            <motion.div
                initial={{ opacity: 0, scale: 0.96 }}
                animate={{ opacity: 1, scale: 1 }}
                transition={{ duration: 0.35, ease: [0.16, 1, 0.3, 1] }}
            >
                <Card className="text-center">
                    <ProgressRing
                        value={result.score}
                        size={148}
                        stroke={12}
                        tone={result.passed ? 'success' : 'danger'}
                        className="mx-auto"
                    >
                        <div>
                            <p className="font-display text-3xl font-extrabold">{result.score}%</p>
                            <p className="text-xs text-fg-muted">skor</p>
                        </div>
                    </ProgressRing>

                    <h1 className="mt-5 font-display text-2xl font-extrabold">
                        {result.passed ? '🎉 Lulus!' : 'Belum lulus'}
                    </h1>

                    <p className="mt-1.5 text-sm text-fg-muted">
                        {result.passed
                            ? `Kamu menjawab ${result.correctCount} dari ${result.totalCount} soal dengan benar.`
                            : `Butuh ${quiz.passScore}% untuk lulus. Pelajari pembahasannya, lalu coba lagi.`}
                    </p>

                    {result.earnedXp > 0 && (
                        <Badge tone="secondary" icon={<Zap className="size-3.5" />} className="mt-4 px-3 py-1.5 text-sm">
                            +{result.earnedXp} XP
                        </Badge>
                    )}

                    <div className="mt-6 flex flex-wrap justify-center gap-2">
                        <Button
                            onClick={() => {
                                void start(quizId);
                                navigate(`/app/quiz/${quizId}`);
                            }}
                            icon={<RotateCcw className="size-4" />}
                        >
                            Coba lagi
                        </Button>
                        <Button to="/app/quiz" variant="outline">
                            Kuis lain
                        </Button>
                        <Button to="/app/dashboard" variant="ghost">
                            Ke dashboard
                        </Button>
                    </div>
                </Card>
            </motion.div>

            {/* Summary */}
            <div className="mt-5 grid gap-4 sm:grid-cols-3">
                <StatCard
                    icon={CheckCircle2}
                    label="Benar"
                    value={result.correctCount}
                    hint={`dari ${result.totalCount} soal`}
                    tone="success"
                />
                <StatCard
                    icon={XCircle}
                    label="Salah"
                    value={result.totalCount - result.correctCount}
                    hint="Lihat pembahasan"
                    tone="danger"
                />
                <StatCard
                    icon={Clock}
                    label="Waktu"
                    value={formatDuration(result.durationSeconds)}
                    hint={quiz.timeLimitSeconds ? `batas ${formatDuration(quiz.timeLimitSeconds)}` : 'tanpa batas'}
                    tone="info"
                />
            </div>

            {/* Review */}
            <Card className="mt-5">
                <CardHeader
                    title="Pembahasan"
                    subtitle="Bagian paling berharga dari sebuah kuis — baca yang salah dulu."
                    action={<Target className="size-5 text-fg-muted" />}
                />

                <ol className="space-y-4">
                    {questions.map((question, index) => {
                        const record = answerMap.get(question.id);
                        const correct = record?.correct ?? false;

                        const given = Array.isArray(record?.given)
                            ? record!.given.join(', ')
                            : String(record?.given ?? '');

                        // Multiple-choice answers are stored as option ids, so
                        // map them back to labels for the summary line.
                        const givenLabel =
                            question.options?.find((option) => option.id === given)?.label ?? given;

                        const correctLabel = Array.isArray(question.correctAnswer)
                            ? question.correctAnswer.join(', ')
                            : (question.options?.find((option) => option.id === question.correctAnswer)?.label ??
                              String(question.correctAnswer));

                        return (
                            <li
                                key={question.id}
                                className={cn(
                                    'rounded-sm border-l-4 bg-surface-sunken p-4',
                                    correct ? 'border-l-success' : 'border-l-danger',
                                )}
                            >
                                <div className="flex items-start gap-3">
                                    <span
                                        className={cn(
                                            'grid size-6 shrink-0 place-items-center rounded-full text-white',
                                            correct ? 'bg-success' : 'bg-danger',
                                        )}
                                    >
                                        {correct ? <CheckCircle2 className="size-4" /> : <XCircle className="size-4" />}
                                    </span>

                                    <div className="min-w-0 flex-1">
                                        <p className="text-sm font-semibold">
                                            {index + 1}. {question.prompt}
                                        </p>

                                        <div className="mt-2 space-y-1 text-sm">
                                            <p>
                                                <span className="text-fg-muted">Jawabanmu: </span>
                                                <span className={correct ? 'text-success' : 'text-danger'}>
                                                    {givenLabel || <em className="text-fg-muted">kosong</em>}
                                                </span>
                                            </p>

                                            {!correct && (
                                                <p>
                                                    <span className="text-fg-muted">Jawaban benar: </span>
                                                    <span className="font-semibold text-success">{correctLabel}</span>
                                                </p>
                                            )}
                                        </div>

                                        {question.explanation && (
                                            <p className="mt-2.5 flex gap-2 rounded-sm bg-surface p-2.5 text-xs text-fg-muted">
                                                <Lightbulb className="size-3.5 shrink-0 text-warning" />
                                                {question.explanation}
                                            </p>
                                        )}
                                    </div>
                                </div>
                            </li>
                        );
                    })}
                </ol>
            </Card>
        </div>
    );
}

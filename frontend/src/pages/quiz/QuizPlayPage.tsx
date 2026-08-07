import { useEffect, useState } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { AlertTriangle, ChevronLeft, ChevronRight, Clock, Flag } from 'lucide-react';
import { useQuizStore } from '@/store/quizStore';
import { useProgressStore } from '@/store/progressStore';
import { useUiStore } from '@/store/uiStore';
import { useCountdown } from '@/hooks/useCountdown';
import { cn } from '@/utils/cn';
import { formatClock } from '@/utils/format';
import { Card } from '@/components/ui/Card';
import { Button } from '@/components/ui/Button';
import { Badge } from '@/components/ui/Badge';
import { ProgressBar, StepDots } from '@/components/ui/Progress';
import { Modal } from '@/components/ui/Modal';
import { Alert, PageLoader } from '@/components/ui/Feedback';
import { QuestionRenderer } from '@/components/feature/quiz/QuestionRenderer';

const TYPE_LABEL: Record<string, string> = {
    'multiple-choice': 'Pilihan ganda',
    'true-false': 'Benar / Salah',
    'fill-blank': 'Isian',
    typing: 'Ketik jawaban',
    matching: 'Cocokkan',
    'drag-drop': 'Susun kata',
    listening: 'Menyimak',
    speaking: 'Ucapkan',
};

export default function QuizPlayPage() {
    const { quizId = '' } = useParams();
    const navigate = useNavigate();
    const toast = useUiStore((state) => state.toast);
    const awardXp = useProgressStore((state) => state.awardXp);

    const {
        quiz,
        questions,
        index,
        answers,
        phase,
        secondsLeft,
        error,
        start,
        setAnswer,
        next,
        previous,
        goTo,
        tick,
        submit,
        reset,
        current,
        answeredCount,
    } = useQuizStore();

    const [confirmOpen, setConfirmOpen] = useState(false);

    useEffect(() => {
        void start(quizId);

        return () => reset();
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [quizId]);

    useCountdown(phase === 'active', tick);

    // Once the store finishes grading, hand off to the result screen.
    useEffect(() => {
        if (phase !== 'finished') return;

        const result = useQuizStore.getState().result;

        if (result) {
            if (result.earnedXp > 0) {
                const { levelUp, level } = awardXp(result.earnedXp);
                if (levelUp) toast(`Naik ke level ${level}!`, 'success', 'Level up 🎉');
            }

            navigate(`/app/quiz/${quizId}/result`, { replace: true });
        }
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [phase]);

    if (phase === 'loading' || phase === 'idle') {
        return error ? (
            <Alert tone="danger" title="Kuis tidak dapat dimuat">
                {error}
            </Alert>
        ) : (
            <PageLoader label="Menyiapkan soal…" />
        );
    }

    const question = current();

    if (!quiz || !question) return <PageLoader />;

    const isLast = index === questions.length - 1;
    const answered = answeredCount();
    const unanswered = questions.length - answered;
    const lowTime = secondsLeft !== null && secondsLeft <= 30;

    const dotStates = questions.map((q) => {
        const value = answers[q.id];
        const filled = Array.isArray(value) ? value.length > 0 : String(value ?? '').trim() !== '';

        return filled ? ('answered' as const) : ('empty' as const);
    });

    return (
        <div className="mx-auto max-w-3xl">
            {/* Header */}
            <div className="mb-5">
                <div className="mb-3 flex flex-wrap items-center justify-between gap-3">
                    <div className="min-w-0">
                        <h1 className="truncate font-display text-xl font-extrabold">{quiz.title}</h1>
                        <p className="mt-0.5 text-sm text-fg-muted">
                            Soal {index + 1} dari {questions.length} · {answered} terjawab
                        </p>
                    </div>

                    {secondsLeft !== null && (
                        <span
                            className={cn(
                                'flex items-center gap-2 rounded-sm px-3 py-2 font-mono text-sm font-bold tabular-nums',
                                lowTime ? 'bg-danger/12 text-danger' : 'bg-surface-sunken text-fg',
                            )}
                        >
                            <Clock className={cn('size-4', lowTime && 'animate-pulse')} />
                            {formatClock(secondsLeft)}
                        </span>
                    )}
                </div>

                <ProgressBar value={((index + 1) / questions.length) * 100} tone="secondary" />

                <div className="mt-3">
                    <StepDots total={questions.length} current={index} states={dotStates} onSelect={goTo} />
                </div>
            </div>

            {lowTime && (
                <Alert tone="warning" className="mb-4">
                    Waktu hampir habis — jawaban akan dikirim otomatis saat timer mencapai nol.
                </Alert>
            )}

            {/* Question */}
            <Card>
                <div className="mb-4 flex flex-wrap items-center gap-2">
                    <Badge tone="primary">{TYPE_LABEL[question.type] ?? question.type}</Badge>
                    <Badge tone="neutral">{question.points} poin</Badge>
                </div>

                <h2 className="mb-5 font-display text-lg font-bold leading-snug">{question.prompt}</h2>

                <QuestionRenderer
                    question={question}
                    value={answers[question.id] ?? (question.type === 'matching' || question.type === 'drag-drop' ? [] : '')}
                    onChange={(value) => setAnswer(question.id, value)}
                />
            </Card>

            {/* Navigation */}
            <div className="mt-5 flex flex-wrap items-center justify-between gap-3">
                <Button
                    variant="outline"
                    disabled={index === 0}
                    onClick={previous}
                    icon={<ChevronLeft className="size-4" />}
                >
                    Sebelumnya
                </Button>

                <div className="flex gap-2">
                    {isLast ? (
                        <Button
                            onClick={() => setConfirmOpen(true)}
                            loading={phase === 'submitting'}
                            icon={<Flag className="size-4" />}
                        >
                            Selesai & kirim
                        </Button>
                    ) : (
                        <Button onClick={next} iconRight={<ChevronRight className="size-4" />}>
                            Berikutnya
                        </Button>
                    )}
                </div>
            </div>

            {/* Submit confirmation */}
            <Modal
                open={confirmOpen}
                onClose={() => setConfirmOpen(false)}
                title="Kirim jawaban?"
                description="Setelah dikirim, jawaban tidak bisa diubah."
                footer={
                    <>
                        <Button variant="outline" onClick={() => setConfirmOpen(false)}>
                            Periksa lagi
                        </Button>
                        <Button
                            loading={phase === 'submitting'}
                            onClick={() => {
                                setConfirmOpen(false);
                                void submit();
                            }}
                        >
                            Ya, kirim
                        </Button>
                    </>
                }
            >
                {unanswered > 0 ? (
                    <Alert tone="warning" title={`${unanswered} soal belum dijawab`}>
                        Soal yang kosong akan dihitung salah. Ketuk titik di bagian atas untuk melompat ke soal
                        tersebut.
                    </Alert>
                ) : (
                    <Alert tone="success" title="Semua soal sudah dijawab">
                        Siap untuk dikirim.
                    </Alert>
                )}

                <div className="mt-4 flex flex-wrap gap-1.5">
                    {questions.map((q, i) => {
                        const filled = dotStates[i] === 'answered';

                        return (
                            <button
                                key={q.id}
                                type="button"
                                onClick={() => {
                                    goTo(i);
                                    setConfirmOpen(false);
                                }}
                                className={cn(
                                    'size-8 rounded-sm text-xs font-bold transition',
                                    filled
                                        ? 'bg-primary text-white'
                                        : 'bg-surface-sunken text-fg-muted ring-1 ring-[var(--surface-border)] hover:ring-danger',
                                )}
                            >
                                {i + 1}
                            </button>
                        );
                    })}
                </div>
            </Modal>

            {error && (
                <Alert tone="danger" className="mt-4">
                    <span className="flex items-center gap-2">
                        <AlertTriangle className="size-4" /> {error}
                    </span>
                </Alert>
            )}
        </div>
    );
}

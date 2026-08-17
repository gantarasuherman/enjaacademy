import { useMemo, useRef, useState } from 'react';
import { CheckCircle2, ChevronLeft, ChevronRight, Flag, SkipForward, XCircle, Zap } from 'lucide-react';
import { dailyQuizService } from '@/services/api';
import type { DailyQuizQuestionItem, DailyQuizResult } from '@/types';
import { useAsync } from '@/hooks/useAsync';
import { useUiStore } from '@/store/uiStore';
import { cn } from '@/utils/cn';
import { Card } from '@/components/ui/Card';
import { Button } from '@/components/ui/Button';
import { Badge } from '@/components/ui/Badge';
import { ProgressBar, StepDots } from '@/components/ui/Progress';
import { Modal } from '@/components/ui/Modal';
import { Input } from '@/components/ui/Input';
import { Alert, PageLoader } from '@/components/ui/Feedback';

const TYPE_LABEL: Record<string, string> = {
    multiple_choice: 'Pilihan ganda',
    fill_blank: 'Isian',
    matching: 'Cocokkan',
    true_false: 'Benar / Salah',
    context: 'Konteks',
};

/** Pulls the quoted target word out of prompts like `Cocokkan kata "run" dengan artinya.` */
function extractQuotedWord(prompt: string): string | null {
    return prompt.match(/"([^"]+)"/)?.[1] ?? null;
}

/**
 * Hangman-style answer boxes for `fill_blank`: revealed letters (from the
 * server's `hint` pattern, e.g. "p__________e") render as static boxes,
 * underscores render as single-letter inputs, spaces render as gaps. The
 * reconstructed full string (revealed + typed letters) is the answer value.
 */
function FillBlankLetters({ hint, value, onChange }: { hint: string; value: string; onChange: (value: string) => void }) {
    const cells = useMemo(() => hint.split(''), [hint]);
    const inputRefs = useRef<Array<HTMLInputElement | null>>([]);

    // Untyped editable positions must keep a placeholder character (not '') —
    // `.join('')` on an array containing empty strings collapses those slots,
    // shifting every later position out of alignment with `hint`.
    function reconstruct(overrideIndex: number, overrideChar: string): string {
        return cells
            .map((c, i) => {
                if (c === ' ') return ' ';
                if (c !== '_') return c;
                if (i === overrideIndex) return overrideChar || '_';
                return value[i] && value[i] !== '_' ? value[i] : '_';
            })
            .join('');
    }

    function focusEditableCell(from: number, direction: 1 | -1) {
        for (let i = from; i >= 0 && i < cells.length; i += direction) {
            if (cells[i] === '_') {
                inputRefs.current[i]?.focus();
                return;
            }
        }
    }

    return (
        <div className="flex flex-wrap items-center gap-1.5">
            {cells.map((c, i) => {
                if (c === ' ') return <span key={i} className="w-3" />;

                if (c !== '_') {
                    return (
                        <span
                            key={i}
                            className="grid size-10 place-items-center rounded-sm border border-[var(--surface-border)] bg-surface-sunken font-mono text-lg font-bold uppercase text-fg-muted"
                        >
                            {c}
                        </span>
                    );
                }

                const letter = value[i] && value[i] !== '_' ? value[i] : '';

                return (
                    <input
                        key={i}
                        ref={(el) => {
                            inputRefs.current[i] = el;
                        }}
                        value={letter}
                        maxLength={1}
                        autoFocus={i === cells.indexOf('_')}
                        onChange={(e) => {
                            const char = e.target.value.slice(-1);
                            onChange(reconstruct(i, char));
                            if (char) focusEditableCell(i + 1, 1);
                        }}
                        onKeyDown={(e) => {
                            if (e.key === 'Backspace' && !letter) {
                                focusEditableCell(i - 1, -1);
                            }
                        }}
                        className={cn(
                            'size-10 rounded-sm border border-[var(--surface-border)] bg-surface text-center font-mono text-lg font-bold uppercase',
                            'focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/25',
                        )}
                    />
                );
            })}
        </div>
    );
}

function QuestionBody({
    question,
    value,
    onChange,
}: {
    question: DailyQuizQuestionItem;
    value: string;
    onChange: (value: string) => void;
}) {
    const { type, payload } = question;

    if (type === 'fill_blank') {
        // Older in-progress attempts generated before the letter-hint feature
        // shipped won't have `hint` in their stored payload — fall back to a
        // plain text field rather than breaking on those.
        if (payload.hint) {
            return <FillBlankLetters hint={payload.hint} value={value} onChange={onChange} />;
        }

        return (
            <Input
                label="Jawaban kamu"
                placeholder="Ketik kata yang tepat…"
                value={value}
                onChange={(e) => onChange(e.target.value)}
                autoFocus
            />
        );
    }

    // multiple_choice / true_false / context all reduce to picking one option.
    const options = type === 'matching' ? (payload.pairs ?? []).map((p) => p.meaning) : (payload.options ?? []);

    return (
        <div className="grid gap-2.5">
            {options.map((option) => {
                const active = value === option;

                return (
                    <button
                        key={option}
                        type="button"
                        onClick={() => onChange(option)}
                        className={cn(
                            'rounded-sm border px-4 py-3 text-left text-sm font-medium transition',
                            'duration-150 ease-[var(--ease-out-soft)] active:scale-[0.99]',
                            active
                                ? 'border-primary bg-primary/10 text-fg ring-2 ring-primary/25'
                                : 'border-[var(--surface-border)] bg-surface text-fg hover:border-primary-300',
                        )}
                    >
                        {option}
                    </button>
                );
            })}
        </div>
    );
}

function ReviewList({ result }: { result: DailyQuizResult }) {
    return (
        <div className="grid gap-3">
            {result.review.map((item) => (
                <Card key={item.questionId} padded className={cn('border-l-4', item.isCorrect ? 'border-l-success' : 'border-l-danger')}>
                    <div className="flex items-start gap-3">
                        {item.isCorrect ? (
                            <CheckCircle2 className="mt-0.5 size-5 shrink-0 text-success" />
                        ) : (
                            <XCircle className="mt-0.5 size-5 shrink-0 text-danger" />
                        )}
                        <div className="min-w-0 flex-1">
                            <div className="mb-1.5 flex flex-wrap items-center gap-2">
                                <Badge tone="neutral">{TYPE_LABEL[item.type] ?? item.type}</Badge>
                                <span className="font-mono text-xs font-bold text-fg-muted">{item.word}</span>
                            </div>
                            <p className="text-sm font-medium text-fg">{item.prompt}</p>
                            <p className="mt-2 text-sm">
                                <span className="text-fg-muted">Jawabanmu: </span>
                                <span className={item.isCorrect ? 'font-semibold text-success' : 'font-semibold text-danger'}>
                                    {item.givenAnswer || '(tidak dijawab)'}
                                </span>
                            </p>
                            {!item.isCorrect && (
                                <p className="text-sm">
                                    <span className="text-fg-muted">Jawaban benar: </span>
                                    <span className="font-semibold text-success">{item.correctAnswer}</span>
                                </p>
                            )}
                        </div>
                    </div>
                </Card>
            ))}
        </div>
    );
}

export function DailyQuizPage({ onFinished }: { onFinished: () => void }) {
    const toast = useUiStore((state) => state.toast);
    const { data: attempt, loading, error, reload } = useAsync(() => dailyQuizService.getToday());

    const [index, setIndex] = useState(0);
    const [answers, setAnswers] = useState<Record<string, string>>({});
    const [confirmOpen, setConfirmOpen] = useState(false);
    const [skipOpen, setSkipOpen] = useState(false);
    const [submitting, setSubmitting] = useState(false);
    const [skipping, setSkipping] = useState(false);
    const [result, setResult] = useState<DailyQuizResult | null>(null);

    const questions = useMemo(() => attempt?.questions ?? [], [attempt]);
    const question = questions[index];

    if (loading) return <PageLoader label="Menyiapkan kuis harian…" />;

    if (error || !attempt) {
        return (
            <div className="mx-auto max-w-xl py-16">
                <Alert tone="danger" title="Kuis tidak dapat dimuat">
                    {error ?? 'Terjadi kesalahan.'}
                </Alert>
                <Button className="mt-4" onClick={() => reload()}>
                    Coba lagi
                </Button>
            </div>
        );
    }

    if (result) {
        const passed = result.score >= 60;

        return (
            <div className="mx-auto max-w-2xl py-8">
                <Card className="mb-6 text-center">
                    <p className="text-sm font-medium text-fg-muted">Kuis harian selesai</p>
                    <p className="mt-2 font-display text-5xl font-extrabold text-primary">{result.score}</p>
                    <p className="mt-1 text-sm text-fg-muted">
                        {result.correctCount} dari {result.totalQuestions} jawaban benar
                    </p>
                    <Badge tone={passed ? 'success' : 'warning'} className="mt-3">
                        {passed ? 'Kerja bagus!' : 'Terus berlatih!'}
                    </Badge>
                    {result.earnedXp > 0 && (
                        <p className="mt-3 flex items-center justify-center gap-1.5 text-sm font-semibold text-primary">
                            <Zap className="size-4" />+{result.earnedXp} XP
                        </p>
                    )}
                </Card>

                <h2 className="mb-3 font-display text-lg font-bold">Ulasan jawaban</h2>
                <ReviewList result={result} />

                <div className="mt-6 flex justify-end">
                    <Button size="lg" onClick={onFinished}>
                        Lanjut ke Dashboard
                    </Button>
                </div>
            </div>
        );
    }

    if (!question) return <PageLoader />;

    const isLast = index === questions.length - 1;
    const answeredCount = questions.filter((q) => (answers[q.id] ?? '').trim() !== '').length;
    const unanswered = questions.length - answeredCount;

    const dotStates = questions.map((q) => ((answers[q.id] ?? '').trim() !== '' ? ('answered' as const) : ('empty' as const)));

    const currentValue = answers[question.id] ?? '';
    const targetWord = question.type === 'matching' ? extractQuotedWord(question.payload.prompt) : null;

    async function handleSubmit() {
        if (!attempt) return;

        setSubmitting(true);

        try {
            const payload = questions.map((q) => ({ questionId: q.id, answer: answers[q.id] ?? '' }));
            const res = await dailyQuizService.submit(attempt.id, payload);
            setResult(res);
        } catch (err) {
            toast(err instanceof Error ? err.message : 'Gagal mengirim jawaban.', 'danger');
        } finally {
            setSubmitting(false);
        }
    }

    async function handleSkip() {
        setSkipping(true);

        try {
            await dailyQuizService.skip();
            toast('Kuis harian dilewati. Sampai jumpa besok!', 'info');
            onFinished();
        } catch (err) {
            toast(err instanceof Error ? err.message : 'Gagal melewati kuis.', 'danger');
            setSkipping(false);
            setSkipOpen(false);
        }
    }

    return (
        <div className="mx-auto max-w-2xl py-6">
            <div className="mb-5">
                <div className="mb-3 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h1 className="font-display text-xl font-extrabold">Kuis Harian</h1>
                        <p className="mt-0.5 text-sm text-fg-muted">
                            Soal {index + 1} dari {questions.length} · {answeredCount} terjawab
                        </p>
                    </div>
                    <Button variant="ghost" size="sm" icon={<SkipForward className="size-4" />} onClick={() => setSkipOpen(true)}>
                        Lewati hari ini
                    </Button>
                </div>

                <ProgressBar value={((index + 1) / questions.length) * 100} tone="secondary" />

                <div className="mt-3">
                    <StepDots total={questions.length} current={index} states={dotStates} onSelect={setIndex} />
                </div>
            </div>

            <Card>
                <div className="mb-4 flex flex-wrap items-center gap-2">
                    <Badge tone="primary">{TYPE_LABEL[question.type] ?? question.type}</Badge>
                    {targetWord && <Badge tone="neutral">{targetWord}</Badge>}
                </div>

                <h2 className="mb-5 font-display text-lg font-bold leading-snug">{question.payload.prompt}</h2>

                <QuestionBody question={question} value={currentValue} onChange={(v) => setAnswers((prev) => ({ ...prev, [question.id]: v }))} />
            </Card>

            <div className="mt-5 flex flex-wrap items-center justify-between gap-3">
                <Button variant="outline" disabled={index === 0} onClick={() => setIndex((i) => i - 1)} icon={<ChevronLeft className="size-4" />}>
                    Sebelumnya
                </Button>

                {isLast ? (
                    <Button onClick={() => setConfirmOpen(true)} loading={submitting} icon={<Flag className="size-4" />}>
                        Selesai & kirim
                    </Button>
                ) : (
                    <Button onClick={() => setIndex((i) => i + 1)} iconRight={<ChevronRight className="size-4" />}>
                        Berikutnya
                    </Button>
                )}
            </div>

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
                            loading={submitting}
                            onClick={() => {
                                setConfirmOpen(false);
                                void handleSubmit();
                            }}
                        >
                            Ya, kirim
                        </Button>
                    </>
                }
            >
                {unanswered > 0 ? (
                    <Alert tone="warning" title={`${unanswered} soal belum dijawab`}>
                        Soal yang kosong akan dihitung salah.
                    </Alert>
                ) : (
                    <Alert tone="success" title="Semua soal sudah dijawab">
                        Siap untuk dikirim.
                    </Alert>
                )}
            </Modal>

            <Modal
                open={skipOpen}
                onClose={() => setSkipOpen(false)}
                title="Lewati kuis hari ini?"
                description="Kamu hanya bisa melewati kuis harian satu kali per hari dan tidak akan mendapat progres dari kuis ini."
                footer={
                    <>
                        <Button variant="outline" onClick={() => setSkipOpen(false)}>
                            Batal
                        </Button>
                        <Button variant="danger" loading={skipping} onClick={() => void handleSkip()}>
                            Ya, lewati
                        </Button>
                    </>
                }
            >
                <p className="text-sm text-fg-muted">Kamu bisa mengerjakan kuis harian ini lagi besok.</p>
            </Modal>
        </div>
    );
}

export default DailyQuizPage;

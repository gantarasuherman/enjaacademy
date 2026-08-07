import { useEffect, useMemo, useState } from 'react';
import { Check, GripVertical, Volume2, X } from 'lucide-react';
import { useSpeechSynthesis } from '@/hooks/useSpeechSynthesis';
import { cn } from '@/utils/cn';
import type { Question } from '@/types';
import { Button, IconButton } from '@/components/ui/Button';
import { Input } from '@/components/ui/Input';

interface RendererProps {
    question: Question;
    value: string | string[];
    onChange: (value: string | string[]) => void;
    /** Set after submission so the component can show the answer key. */
    reveal?: boolean;
}

/* ------------------------------------------------- multiple choice / t-f */

function OptionList({ question, value, onChange, reveal }: RendererProps) {
    return (
        <div className="grid gap-2.5 sm:grid-cols-2">
            {(question.options ?? []).map((option, index) => {
                const selected = value === option.id;
                const correct = question.correctAnswer === option.id;

                return (
                    <button
                        key={option.id}
                        type="button"
                        disabled={reveal}
                        onClick={() => onChange(option.id)}
                        className={cn(
                            'flex items-center gap-3 rounded-sm border-2 p-4 text-left text-sm transition',
                            'duration-150 ease-[var(--ease-out-soft)] disabled:cursor-default',
                            reveal && correct && 'border-success bg-success/10',
                            reveal && selected && !correct && 'border-danger bg-danger/10',
                            !reveal && selected && 'border-primary bg-primary-50 dark:bg-primary/12',
                            !reveal && !selected && 'border-[var(--surface-border)] hover:border-primary-300 hover:bg-surface-sunken',
                            reveal && !correct && !selected && 'border-[var(--surface-border)] opacity-60',
                        )}
                    >
                        <span
                            className={cn(
                                'grid size-7 shrink-0 place-items-center rounded-full text-xs font-bold',
                                reveal && correct
                                    ? 'bg-success text-white'
                                    : reveal && selected
                                      ? 'bg-danger text-white'
                                      : selected
                                        ? 'bg-primary text-white'
                                        : 'bg-surface-sunken text-fg-muted',
                            )}
                        >
                            {reveal && correct ? (
                                <Check className="size-4" />
                            ) : reveal && selected ? (
                                <X className="size-4" />
                            ) : (
                                String.fromCharCode(65 + index)
                            )}
                        </span>

                        <span className="min-w-0 flex-1">{option.label}</span>
                    </button>
                );
            })}
        </div>
    );
}

/* ------------------------------------------------------------ fill blank */

function FillBlank({ question, value, onChange, reveal }: RendererProps) {
    const [before, after] = (question.template ?? '___').split('___');
    const correct = reveal && String(value).trim().toLowerCase() === String(question.correctAnswer).toLowerCase();

    return (
        <div className="rounded-sm bg-surface-sunken p-5">
            <p className="flex flex-wrap items-center gap-x-1.5 gap-y-3 text-base leading-loose">
                <span>{before}</span>

                <input
                    type="text"
                    disabled={reveal}
                    value={typeof value === 'string' ? value : ''}
                    onChange={(event) => onChange(event.target.value)}
                    placeholder="…"
                    aria-label="Jawaban isian"
                    className={cn(
                        'w-40 border-b-2 bg-transparent px-2 py-1 text-center font-semibold outline-none transition',
                        reveal
                            ? correct
                                ? 'border-success text-success'
                                : 'border-danger text-danger'
                            : 'border-primary focus:border-primary-600',
                    )}
                />

                <span>{after}</span>
            </p>

            {reveal && !correct && (
                <p className="mt-3 text-sm">
                    Jawaban benar: <strong className="text-success">{String(question.correctAnswer)}</strong>
                </p>
            )}
        </div>
    );
}

/* ---------------------------------------------------------------- typing */

function Typing({ question, value, onChange, reveal }: RendererProps) {
    const correct =
        reveal && String(value).trim().toLowerCase() === String(question.correctAnswer).toLowerCase();

    return (
        <div>
            <Input
                disabled={reveal}
                value={typeof value === 'string' ? value : ''}
                onChange={(event) => onChange(event.target.value)}
                placeholder="Ketik jawabanmu…"
                className={cn(
                    'text-base',
                    reveal && (correct ? 'border-success text-success' : 'border-danger text-danger'),
                )}
            />

            {reveal && !correct && (
                <p className="mt-2 text-sm">
                    Jawaban benar: <strong className="text-success">{String(question.correctAnswer)}</strong>
                </p>
            )}
        </div>
    );
}

/* -------------------------------------------------------------- matching */

function Matching({ question, value, onChange, reveal }: RendererProps) {
    const pairs = question.pairs ?? [];

    // Right-hand options are shuffled once per mount, not on every render.
    const shuffled = useMemo(
        () => [...pairs.map((pair) => pair.right)].sort(() => Math.random() - 0.5),
        [question.id],
    );

    const answers = Array.isArray(value) ? value : Array(pairs.length).fill('');

    function setAt(index: number, right: string) {
        const next = [...answers];
        next[index] = right;
        onChange(next);
    }

    return (
        <div className="space-y-2.5">
            {pairs.map((pair, index) => {
                const chosen = answers[index] ?? '';
                const correct = reveal && chosen === pair.right;

                return (
                    <div
                        key={pair.left}
                        className={cn(
                            'grid items-center gap-3 rounded-sm border p-3 sm:grid-cols-[1fr_auto_1fr]',
                            reveal
                                ? correct
                                    ? 'border-success bg-success/8'
                                    : 'border-danger bg-danger/8'
                                : 'border-[var(--surface-border)]',
                        )}
                    >
                        <span className="text-sm font-semibold">{pair.left}</span>

                        <span className="hidden text-fg-muted sm:block">→</span>

                        <select
                            disabled={reveal}
                            value={chosen}
                            onChange={(event) => setAt(index, event.target.value)}
                            aria-label={`Pasangan untuk ${pair.left}`}
                            className="w-full rounded-sm border border-[var(--surface-border)] bg-surface px-3 py-2 text-sm focus:border-primary focus:outline-none"
                        >
                            <option value="">Pilih pasangan…</option>
                            {shuffled.map((right) => (
                                <option key={right} value={right}>
                                    {right}
                                </option>
                            ))}
                        </select>

                        {reveal && !correct && (
                            <p className="text-xs text-success sm:col-span-3">Seharusnya: {pair.right}</p>
                        )}
                    </div>
                );
            })}
        </div>
    );
}

/* ------------------------------------------------------------- drag drop */

function DragDrop({ question, value, onChange, reveal }: RendererProps) {
    const tokens = question.tokens ?? [];
    const arranged = Array.isArray(value) ? value : [];

    // Tokens still in the pool = all tokens minus those already placed,
    // counting duplicates correctly.
    const pool = useMemo(() => {
        const remaining = [...tokens];

        arranged.forEach((token) => {
            const index = remaining.indexOf(token);
            if (index >= 0) remaining.splice(index, 1);
        });

        return remaining;
    }, [tokens, arranged]);

    const [shuffledPool, setShuffledPool] = useState<string[]>([]);

    useEffect(() => {
        setShuffledPool([...tokens].sort(() => Math.random() - 0.5));
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [question.id]);

    const orderedPool = shuffledPool.filter((token) => pool.includes(token));
    const correct = reveal && JSON.stringify(arranged) === JSON.stringify(question.correctAnswer);

    return (
        <div className="space-y-4">
            {/* Answer tray */}
            <div
                className={cn(
                    'flex min-h-16 flex-wrap items-start gap-2 rounded-sm border-2 border-dashed p-3',
                    reveal
                        ? correct
                            ? 'border-success bg-success/8'
                            : 'border-danger bg-danger/8'
                        : 'border-[var(--surface-border)] bg-surface-sunken',
                )}
            >
                {arranged.length === 0 && (
                    <p className="py-2 text-sm text-fg-muted">Ketuk kata di bawah untuk menyusun kalimat.</p>
                )}

                {arranged.map((token, index) => (
                    <button
                        key={`${token}-${index}`}
                        type="button"
                        disabled={reveal}
                        onClick={() => onChange(arranged.filter((_, i) => i !== index))}
                        className="inline-flex items-center gap-1.5 rounded-sm bg-primary px-3 py-1.5 text-sm font-medium text-white transition hover:bg-primary-600 disabled:opacity-90"
                    >
                        <GripVertical className="size-3 opacity-60" />
                        {token}
                    </button>
                ))}
            </div>

            {/* Token pool */}
            {!reveal && (
                <div className="flex flex-wrap gap-2">
                    {orderedPool.map((token, index) => (
                        <button
                            key={`${token}-${index}`}
                            type="button"
                            onClick={() => onChange([...arranged, token])}
                            className="rounded-sm border border-[var(--surface-border)] bg-surface px-3 py-1.5 text-sm font-medium transition hover:border-primary-300 hover:bg-surface-sunken"
                        >
                            {token}
                        </button>
                    ))}
                </div>
            )}

            {reveal && !correct && (
                <p className="text-sm">
                    Susunan benar:{' '}
                    <strong className="text-success">
                        {(question.correctAnswer as string[]).join(' ')}
                    </strong>
                </p>
            )}
        </div>
    );
}

/* -------------------------------------------------------------- speaking */

function SpeakingQuestion({ question, value, onChange, reveal }: RendererProps) {
    const { speak, supported } = useSpeechSynthesis();
    const target = question.template ?? String(question.correctAnswer);

    return (
        <div className="space-y-4">
            <div className="rounded-sm bg-surface-sunken p-5 text-center">
                <p className="font-display text-lg font-bold">{target}</p>
                {supported && (
                    <Button
                        variant="outline"
                        size="sm"
                        className="mt-3"
                        icon={<Volume2 className="size-4" />}
                        onClick={() => speak(target, { rate: 0.85 })}
                    >
                        Dengarkan
                    </Button>
                )}
            </div>

            <Input
                label="Ketik ulang kalimatnya setelah kamu ucapkan"
                hint="Latihan lisan penuh dengan skor otomatis ada di menu Speaking."
                disabled={reveal}
                value={typeof value === 'string' ? value : ''}
                onChange={(event) => onChange(event.target.value)}
                placeholder="Ketik kalimat yang kamu ucapkan…"
            />
        </div>
    );
}

/* ------------------------------------------------------------------ root */

export function QuestionRenderer(props: RendererProps) {
    const { question } = props;
    const { speak, supported } = useSpeechSynthesis();

    return (
        <div className="space-y-5">
            {/* Audio prompt for listening questions */}
            {question.type === 'listening' && (
                <div className="flex items-center gap-3 rounded-sm bg-info/10 p-4">
                    <IconButton
                        label="Putar audio soal"
                        onClick={() => supported && speak(question.prompt)}
                        disabled={!supported}
                    >
                        <Volume2 className="size-5" />
                    </IconButton>
                    <p className="text-sm text-fg-muted">
                        Putar audio, lalu jawab. Kamu boleh mendengarkan berkali-kali.
                    </p>
                </div>
            )}

            {(() => {
                switch (question.type) {
                    case 'multiple-choice':
                    case 'true-false':
                    case 'listening':
                        return <OptionList {...props} />;
                    case 'fill-blank':
                        return <FillBlank {...props} />;
                    case 'typing':
                        return <Typing {...props} />;
                    case 'matching':
                        return <Matching {...props} />;
                    case 'drag-drop':
                        return <DragDrop {...props} />;
                    case 'speaking':
                        return <SpeakingQuestion {...props} />;
                    default:
                        return <Typing {...props} />;
                }
            })()}
        </div>
    );
}

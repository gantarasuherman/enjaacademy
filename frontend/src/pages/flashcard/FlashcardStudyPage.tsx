import { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import { AnimatePresence, motion } from 'framer-motion';
import { CheckCircle2, Lightbulb, RotateCcw, Volume2 } from 'lucide-react';
import { useAsync } from '@/hooks/useAsync';
import { useSpeechSynthesis } from '@/hooks/useSpeechSynthesis';
import { flashcardService } from '@/services/api';
import { useFlashcardStore } from '@/store/flashcardStore';
import { useProgressStore } from '@/store/progressStore';
import { useUiStore } from '@/store/uiStore';
import { cn } from '@/utils/cn';
import type { ReviewGrade } from '@/types';
import { Card, CardHeader } from '@/components/ui/Card';
import { Button, IconButton } from '@/components/ui/Button';
import { Badge } from '@/components/ui/Badge';
import { ProgressBar } from '@/components/ui/Progress';
import { EmptyState, PageLoader } from '@/components/ui/Feedback';
import { PageHeader } from '@/components/feature/shared/PageHeader';

const GRADES: { id: ReviewGrade; label: string; hint: string; className: string }[] = [
    { id: 'again', label: 'Ulangi', hint: 'Lupa total', className: 'bg-danger text-white hover:brightness-95' },
    { id: 'hard', label: 'Sulit', hint: 'Ingat dengan susah payah', className: 'bg-warning text-white hover:brightness-95' },
    { id: 'good', label: 'Bagus', hint: 'Ingat setelah berpikir', className: 'bg-primary text-white hover:bg-primary-600' },
    { id: 'easy', label: 'Mudah', hint: 'Langsung ingat', className: 'bg-success text-white hover:brightness-95' },
];

export default function FlashcardStudyPage() {
    const { deckId = '' } = useParams();
    const isAllDecks = deckId === 'all';

    const { data: deck } = useAsync(
        () => (isAllDecks ? Promise.resolve(null) : flashcardService.getDeck(deckId)),
        [deckId],
    );

    const { queue, index, flipped, sessionStats, startSession, flip, grade, currentCard, remaining } =
        useFlashcardStore();

    const { speak, supported } = useSpeechSynthesis();
    const recordFlashcardXp = useProgressStore((state) => state.awardXp);
    const toast = useUiStore((state) => state.toast);

    const [ready, setReady] = useState(false);

    useEffect(() => {
        void startSession(isAllDecks ? null : deckId, 20).then(() => setReady(true));
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [deckId]);

    const card = currentCard();
    const done = ready && !card;

    // Award XP once at the end rather than per card, so the toast isn't spammy.
    useEffect(() => {
        if (!done || sessionStats.reviewed === 0) return;

        const xp = sessionStats.reviewed * 2;
        recordFlashcardXp(xp);
        toast(`+${xp} XP — ${sessionStats.reviewed} kartu direview.`, 'success', 'Sesi selesai');
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [done]);

    // Keyboard shortcuts: space flips, 1–4 grades.
    useEffect(() => {
        function onKey(event: KeyboardEvent) {
            if (!card) return;

            if (event.code === 'Space') {
                event.preventDefault();
                flip();
                return;
            }

            if (!flipped) return;

            const gradeIndex = Number(event.key) - 1;
            if (gradeIndex >= 0 && gradeIndex < GRADES.length) {
                grade(GRADES[gradeIndex].id);
            }
        }

        window.addEventListener('keydown', onKey);
        return () => window.removeEventListener('keydown', onKey);
    }, [card, flipped, flip, grade]);

    if (!ready) return <PageLoader label="Menyiapkan kartu…" />;

    if (done) {
        return (
            <div className="mx-auto max-w-md">
                <Card className="text-center">
                    <CheckCircle2 className="mx-auto size-14 text-success" />
                    <h1 className="mt-4 font-display text-2xl font-extrabold">Sesi selesai!</h1>
                    <p className="mt-1.5 text-sm text-fg-muted">
                        {sessionStats.reviewed === 0
                            ? 'Tidak ada kartu yang jatuh tempo. Kembali lagi besok.'
                            : `Kamu mereview ${sessionStats.reviewed} kartu.`}
                    </p>

                    {sessionStats.reviewed > 0 && (
                        <div className="mt-5 grid grid-cols-2 gap-3">
                            <div className="rounded-sm bg-success/10 p-3">
                                <p className="font-display text-2xl font-extrabold text-success">{sessionStats.good}</p>
                                <p className="text-xs text-fg-muted">dikuasai</p>
                            </div>
                            <div className="rounded-sm bg-danger/10 p-3">
                                <p className="font-display text-2xl font-extrabold text-danger">{sessionStats.again}</p>
                                <p className="text-xs text-fg-muted">perlu diulang</p>
                            </div>
                        </div>
                    )}

                    <div className="mt-6 flex flex-wrap justify-center gap-2">
                        <Button
                            onClick={() => {
                                setReady(false);
                                void startSession(isAllDecks ? null : deckId, 20).then(() => setReady(true));
                            }}
                            icon={<RotateCcw className="size-4" />}
                        >
                            Sesi baru
                        </Button>
                        <Button to="/app/flashcard" variant="outline">
                            Semua dek
                        </Button>
                    </div>
                </Card>
            </div>
        );
    }

    if (!card) {
        return <EmptyState title="Tidak ada kartu untuk dipelajari" action={<Button to="/app/flashcard">Kembali</Button>} />;
    }

    const total = queue.length;
    const percent = total === 0 ? 0 : Math.round((index / total) * 100);

    return (
        <div className="mx-auto max-w-2xl">
            <PageHeader
                backTo="/app/flashcard"
                backLabel="Semua dek"
                title={isAllDecks ? 'Semua dek' : (deck?.title ?? 'Flashcard')}
                badge={<Badge tone="secondary">{remaining()} tersisa</Badge>}
            />

            <ProgressBar value={percent} tone="secondary" className="mb-6" />

            {/* Card */}
            <div className="perspective-1000">
                <motion.button
                    type="button"
                    onClick={flip}
                    aria-label={flipped ? 'Balik ke depan' : 'Balik untuk melihat jawaban'}
                    className="relative block min-h-72 w-full cursor-pointer"
                    animate={{ rotateY: flipped ? 180 : 0 }}
                    transition={{ duration: 0.45, ease: [0.16, 1, 0.3, 1] }}
                    style={{ transformStyle: 'preserve-3d' }}
                >
                    {/* Front */}
                    <span
                        className="absolute inset-0 grid place-content-center rounded-lg border border-[var(--surface-border)] bg-surface p-8 text-center shadow-[var(--shadow-card)]"
                        style={{ backfaceVisibility: 'hidden' }}
                    >
                        <span className="block font-display text-3xl font-extrabold">{card.front}</span>
                        {card.ipa && <span className="ipa mt-2 block text-sm text-fg-muted">{card.ipa}</span>}
                        <span className="mt-6 block text-xs text-fg-muted">Ketuk kartu atau tekan Spasi</span>
                    </span>

                    {/* Back */}
                    <span
                        className="absolute inset-0 grid place-content-center rounded-lg border-2 border-primary bg-primary-50 p-8 text-center shadow-[var(--shadow-card)] dark:bg-primary/12"
                        style={{ backfaceVisibility: 'hidden', transform: 'rotateY(180deg)' }}
                    >
                        <span className="block font-display text-2xl font-bold text-primary">{card.back}</span>
                        {card.hint && (
                            <span className="mt-4 flex items-center justify-center gap-2 text-sm text-fg-muted">
                                <Lightbulb className="size-4 text-warning" />
                                {card.hint}
                            </span>
                        )}
                    </span>
                </motion.button>
            </div>

            {/* Audio */}
            {supported && (
                <div className="mt-4 flex justify-center">
                    <IconButton
                        label={`Dengarkan ${card.front}`}
                        variant="outline"
                        onClick={(event) => {
                            event.stopPropagation();
                            speak(card.front);
                        }}
                    >
                        <Volume2 className="size-5" />
                    </IconButton>
                </div>
            )}

            {/* Grade buttons */}
            <AnimatePresence mode="wait">
                {flipped ? (
                    <motion.div
                        key="grades"
                        initial={{ opacity: 0, y: 8 }}
                        animate={{ opacity: 1, y: 0 }}
                        exit={{ opacity: 0 }}
                        transition={{ duration: 0.18 }}
                        className="mt-6 grid grid-cols-2 gap-2.5 sm:grid-cols-4"
                    >
                        {GRADES.map((item, i) => (
                            <button
                                key={item.id}
                                type="button"
                                onClick={() => grade(item.id)}
                                className={cn(
                                    'rounded-sm px-3 py-3 text-center transition active:scale-[0.97]',
                                    item.className,
                                )}
                            >
                                <span className="block text-sm font-bold">{item.label}</span>
                                <span className="mt-0.5 block text-[11px] opacity-85">{item.hint}</span>
                                <span className="mt-1 block font-mono text-[10px] opacity-70">{i + 1}</span>
                            </button>
                        ))}
                    </motion.div>
                ) : (
                    <motion.p
                        key="hint"
                        initial={{ opacity: 0 }}
                        animate={{ opacity: 1 }}
                        exit={{ opacity: 0 }}
                        className="mt-6 text-center text-sm text-fg-muted"
                    >
                        Coba ingat dulu jawabannya, baru balik kartunya.
                    </motion.p>
                )}
            </AnimatePresence>

            <Card className="mt-8">
                <CardHeader title="Sesi ini" />
                <div className="grid grid-cols-3 gap-3 text-center">
                    <div>
                        <p className="font-display text-xl font-extrabold">{sessionStats.reviewed}</p>
                        <p className="text-xs text-fg-muted">direview</p>
                    </div>
                    <div>
                        <p className="font-display text-xl font-extrabold text-success">{sessionStats.good}</p>
                        <p className="text-xs text-fg-muted">dikuasai</p>
                    </div>
                    <div>
                        <p className="font-display text-xl font-extrabold text-danger">{sessionStats.again}</p>
                        <p className="text-xs text-fg-muted">diulang</p>
                    </div>
                </div>
            </Card>
        </div>
    );
}

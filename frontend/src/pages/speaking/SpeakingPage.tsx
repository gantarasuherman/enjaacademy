import { useEffect, useState } from 'react';
import { ChevronLeft, ChevronRight, Mic, MicOff, RotateCcw, Volume2 } from 'lucide-react';
import { useAsync } from '@/hooks/useAsync';
import { scorePronunciation, useSpeechRecognition } from '@/hooks/useSpeechRecognition';
import { useSpeechSynthesis } from '@/hooks/useSpeechSynthesis';
import { speakingService } from '@/services/api';
import { useProgressStore } from '@/store/progressStore';
import { useUiStore } from '@/store/uiStore';
import { cn } from '@/utils/cn';
import { Card, CardHeader } from '@/components/ui/Card';
import { Button, IconButton } from '@/components/ui/Button';
import { Badge, CefrBadge } from '@/components/ui/Badge';
import { ProgressRing } from '@/components/ui/Progress';
import { Alert, EmptyState, PageLoader } from '@/components/ui/Feedback';
import { PageHeader } from '@/components/feature/shared/PageHeader';

export default function SpeakingPage() {
    const { data: exercises, loading } = useAsync(() => speakingService.list(), []);
    const [index, setIndex] = useState(0);
    const [result, setResult] = useState<ReturnType<typeof scorePronunciation> | null>(null);

    const recognition = useSpeechRecognition({ lang: 'en-US' });
    const { speak, speaking, supported: ttsSupported } = useSpeechSynthesis();

    const awardXp = useProgressStore((state) => state.awardXp);
    const toast = useUiStore((state) => state.toast);

    const exercise = exercises?.[index] ?? null;

    // Score as soon as a final transcript arrives and listening has stopped.
    useEffect(() => {
        if (!exercise || recognition.listening || !recognition.transcript) return;

        const scored = scorePronunciation(exercise.targetSentence, recognition.transcript);
        setResult(scored);

        if (scored.score >= 70) {
            // Server decides the real XP (and withholds it on a repeat pass
            // of an exercise already rewarded) — see SkillPracticeController.
            void speakingService.complete(exercise.itemId, scored.score).then(({ earnedXp }) => {
                if (earnedXp > 0) {
                    awardXp(earnedXp);
                    toast(`+${earnedXp} XP — skor ${scored.score}%`, 'success');
                }
            });
        }
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [recognition.listening, recognition.transcript]);

    function goTo(next: number) {
        if (!exercises || next < 0 || next >= exercises.length) return;

        setIndex(next);
        setResult(null);
        recognition.reset();
    }

    if (loading) return <PageLoader />;

    if (!exercise) {
        return <EmptyState icon={<Mic className="size-6" />} title="Belum ada latihan speaking" />;
    }

    return (
        <>
            <PageHeader
                title="Speaking"
                description="Ucapkan kalimat targetnya, lalu sistem membandingkan hasil pengenalan suara dengan teks aslinya kata per kata."
                badge={<CefrBadge level={exercise.cefr} />}
            />

            {!recognition.supported && (
                <Alert tone="warning" title="Browser belum mendukung pengenalan suara" className="mb-5">
                    Fitur ini butuh Web Speech API. Gunakan Chrome, Edge, atau Safari terbaru. Kamu tetap bisa
                    mendengarkan contoh dan berlatih secara mandiri.
                </Alert>
            )}

            {recognition.error && (
                <Alert tone="danger" className="mb-5">
                    {recognition.error}
                </Alert>
            )}

            <div className="grid gap-6 lg:grid-cols-[1fr_18rem]">
                <div className="space-y-6">
                    {/* Target sentence */}
                    <Card>
                        <CardHeader
                            title={`Latihan ${index + 1} dari ${exercises!.length}`}
                            action={
                                <div className="flex gap-1">
                                    <IconButton
                                        label="Sebelumnya"
                                        variant="outline"
                                        size="sm"
                                        disabled={index === 0}
                                        onClick={() => goTo(index - 1)}
                                    >
                                        <ChevronLeft className="size-4" />
                                    </IconButton>
                                    <IconButton
                                        label="Berikutnya"
                                        variant="outline"
                                        size="sm"
                                        disabled={index === exercises!.length - 1}
                                        onClick={() => goTo(index + 1)}
                                    >
                                        <ChevronRight className="size-4" />
                                    </IconButton>
                                </div>
                            }
                        />

                        <div className="rounded-md bg-surface-sunken p-6 text-center">
                            {/* Word-level feedback replaces the plain sentence once scored. */}
                            <p className="font-display text-2xl font-bold leading-relaxed">
                                {result
                                    ? result.wordScores.map((word, i) => (
                                          <span
                                              key={i}
                                              className={cn(
                                                  'mx-0.5 rounded px-1',
                                                  word.matched
                                                      ? 'bg-success/15 text-emerald-700 dark:text-emerald-400'
                                                      : 'bg-danger/15 text-red-700 dark:text-red-400',
                                              )}
                                          >
                                              {word.word}
                                          </span>
                                      ))
                                    : exercise.targetSentence}
                            </p>

                            <p className="ipa mt-3 text-sm text-fg-muted">{exercise.ipa}</p>
                            <p className="mt-2 text-sm text-fg-muted">{exercise.meaning}</p>

                            {ttsSupported && (
                                <Button
                                    variant="outline"
                                    size="sm"
                                    className="mt-4"
                                    icon={<Volume2 className="size-4" />}
                                    onClick={() => speak(exercise.targetSentence, { rate: 0.85 })}
                                    disabled={speaking}
                                >
                                    Dengarkan contoh
                                </Button>
                            )}
                        </div>

                        {/* Record control */}
                        <div className="mt-6 flex flex-col items-center gap-3">
                            <button
                                type="button"
                                onClick={recognition.listening ? recognition.stop : recognition.start}
                                disabled={!recognition.supported}
                                aria-label={recognition.listening ? 'Hentikan rekaman' : 'Mulai merekam'}
                                className={cn(
                                    'relative grid size-20 place-items-center rounded-full text-white transition',
                                    'duration-200 ease-[var(--ease-out-soft)] active:scale-95 disabled:opacity-50',
                                    recognition.listening ? 'bg-danger' : 'bg-primary hover:bg-primary-600',
                                )}
                            >
                                {recognition.listening && (
                                    <span className="absolute inset-0 animate-ping rounded-full bg-danger/40" />
                                )}
                                {recognition.listening ? <MicOff className="size-8" /> : <Mic className="size-8" />}
                            </button>

                            <p className="text-sm text-fg-muted">
                                {recognition.listening ? 'Mendengarkan… bicara sekarang' : 'Ketuk untuk mulai merekam'}
                            </p>

                            {recognition.interim && (
                                <p className="text-sm italic text-fg-muted">"{recognition.interim}"</p>
                            )}
                        </div>
                    </Card>

                    {/* Result */}
                    {result && (
                        <Card>
                            <CardHeader title="Hasil pelafalan" />

                            <div className="flex flex-col items-center gap-5 sm:flex-row">
                                <ProgressRing
                                    value={result.score}
                                    size={112}
                                    tone={result.score >= 70 ? 'success' : result.score >= 40 ? 'secondary' : 'danger'}
                                >
                                    <div>
                                        <p className="font-display text-2xl font-extrabold">{result.score}</p>
                                        <p className="text-[11px] text-fg-muted">skor</p>
                                    </div>
                                </ProgressRing>

                                <div className="min-w-0 flex-1">
                                    <p className="text-sm font-medium">{result.feedback}</p>

                                    <div className="mt-3 rounded-sm bg-surface-sunken p-3">
                                        <p className="text-xs font-bold uppercase tracking-wide text-fg-muted">
                                            Yang terdengar
                                        </p>
                                        <p className="mt-1 text-sm italic">"{recognition.transcript}"</p>
                                    </div>

                                    <Button
                                        variant="outline"
                                        size="sm"
                                        className="mt-3"
                                        icon={<RotateCcw className="size-4" />}
                                        onClick={() => {
                                            setResult(null);
                                            recognition.reset();
                                        }}
                                    >
                                        Coba lagi
                                    </Button>
                                </div>
                            </div>
                        </Card>
                    )}
                </div>

                <div className="space-y-6 lg:sticky lg:top-24 lg:self-start">
                    <Card>
                        <CardHeader title="Fokus bunyi" subtitle="Bunyi yang jadi sasaran latihan ini." />
                        <div className="flex flex-wrap gap-2">
                            {exercise.focusSounds.map((sound) => (
                                <Badge key={sound} tone="secondary" className="ipa px-3 py-1 text-sm">
                                    {sound}
                                </Badge>
                            ))}
                        </div>
                    </Card>

                    <Card>
                        <CardHeader title="Tips" />
                        <ul className="space-y-2.5 text-sm">
                            {[
                                'Bicara 15–20 cm dari mikrofon.',
                                'Pelankan tempo — kejelasan mengalahkan kecepatan.',
                                'Dengarkan contoh dulu, tiru iramanya.',
                                'Ulangi kalimat yang sama tiga kali sebelum lanjut.',
                            ].map((tip) => (
                                <li key={tip} className="flex gap-2.5">
                                    <span className="mt-1.5 size-1.5 shrink-0 rounded-full bg-primary" />
                                    {tip}
                                </li>
                            ))}
                        </ul>
                    </Card>

                    <Card>
                        <CardHeader title="Semua latihan" />
                        <div className="space-y-1.5">
                            {exercises!.map((item, i) => (
                                <button
                                    key={item.id}
                                    type="button"
                                    onClick={() => goTo(i)}
                                    className={cn(
                                        'w-full truncate rounded-sm px-3 py-2 text-left text-sm transition',
                                        i === index
                                            ? 'bg-primary-100 font-semibold text-primary-700 dark:bg-primary/20 dark:text-primary-200'
                                            : 'text-fg-muted hover:bg-surface-sunken',
                                    )}
                                >
                                    {i + 1}. {item.targetSentence}
                                </button>
                            ))}
                        </div>
                    </Card>
                </div>
            </div>
        </>
    );
}

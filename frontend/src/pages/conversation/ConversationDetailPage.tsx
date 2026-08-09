import { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import { CheckCircle2, Mic, MicOff, RotateCcw, Volume2 } from 'lucide-react';
import { useAsync } from '@/hooks/useAsync';
import { scorePronunciation, useSpeechRecognition } from '@/hooks/useSpeechRecognition';
import { useSpeechSynthesis } from '@/hooks/useSpeechSynthesis';
import { conversationService } from '@/services/api';
import { useProgressStore } from '@/store/progressStore';
import { useUiStore } from '@/store/uiStore';
import { cn } from '@/utils/cn';
import { Card, CardHeader } from '@/components/ui/Card';
import { Button, IconButton } from '@/components/ui/Button';
import { Badge, CefrBadge } from '@/components/ui/Badge';
import { ProgressBar } from '@/components/ui/Progress';
import { Alert, EmptyState, PageLoader } from '@/components/ui/Feedback';
import { PageHeader } from '@/components/feature/shared/PageHeader';

export default function ConversationDetailPage() {
    const { scenarioId = '' } = useParams();
    const { data: scenario, loading } = useAsync(() => conversationService.get(scenarioId), [scenarioId]);

    const recognition = useSpeechRecognition({ lang: 'en-US' });
    const { speak, speaking, supported: ttsSupported } = useSpeechSynthesis();

    const awardXp = useProgressStore((state) => state.awardXp);
    const toast = useUiStore((state) => state.toast);

    const [step, setStep] = useState(0);
    const [revealedLines, setRevealedLines] = useState<Set<string>>(new Set());
    const [scores, setScores] = useState<Record<string, number>>({});
    const [finished, setFinished] = useState(false);

    const visibleLines = scenario?.lines.slice(0, step + 1) ?? [];
    const currentLine = scenario?.lines[step] ?? null;
    const isUserTurn = currentLine?.userTurn === true;

    // Auto-play the other speaker's line so the learner hears it first.
    useEffect(() => {
        if (!currentLine || currentLine.userTurn || !ttsSupported) return;

        speak(currentLine.text, { rate: 0.92 });
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [currentLine?.id]);

    // Grade the learner's turn once recognition settles.
    useEffect(() => {
        if (!currentLine || !isUserTurn || recognition.listening || !recognition.transcript) return;

        const { score } = scorePronunciation(currentLine.text, recognition.transcript);

        setScores((prev) => ({ ...prev, [currentLine.id]: score }));

        if (score >= 60) {
            awardXp(8);
            toast(`+8 XP — skor ${score}%`, 'success');
        }
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [recognition.listening, recognition.transcript]);

    function advance() {
        if (!scenario) return;

        recognition.reset();

        if (step < scenario.lines.length - 1) {
            setStep(step + 1);
        } else {
            setFinished(true);
            awardXp(25);
            toast('+25 XP — percakapan selesai!', 'success', 'Mantap');
        }
    }

    function toggleReveal(lineId: string) {
        setRevealedLines((prev) => {
            const next = new Set(prev);
            if (next.has(lineId)) next.delete(lineId);
            else next.add(lineId);
            return next;
        });
    }

    function restart() {
        setStep(0);
        setScores({});
        setFinished(false);
        recognition.reset();
    }

    if (loading) return <PageLoader />;

    if (!scenario) {
        return <EmptyState title="Skenario tidak ditemukan" action={<Button to="/app/conversation">Kembali</Button>} />;
    }

    const userTurns = scenario.lines.filter((line) => line.userTurn);
    const scoredTurns = userTurns.filter((line) => scores[line.id] !== undefined);
    const averageScore =
        scoredTurns.length === 0
            ? 0
            : Math.round(scoredTurns.reduce((sum, line) => sum + scores[line.id], 0) / scoredTurns.length);

    return (
        <>
            <PageHeader
                backTo="/app/conversation"
                backLabel="Semua skenario"
                title={scenario.title}
                description={scenario.context}
                badge={<CefrBadge level={scenario.cefr} />}
                action={
                    <Button variant="ghost" icon={<RotateCcw className="size-4" />} onClick={restart}>
                        Ulangi
                    </Button>
                }
            />

            <ProgressBar
                value={((step + 1) / scenario.lines.length) * 100}
                tone="secondary"
                label={`Baris ${step + 1} dari ${scenario.lines.length}`}
                showValue
                className="mb-5"
            />

            {!recognition.supported && (
                <Alert tone="warning" className="mb-5">
                    Browser ini belum mendukung pengenalan suara. Kamu tetap bisa membaca dialog dan mendengarkan
                    contohnya.
                </Alert>
            )}

            <div className="grid gap-6 lg:grid-cols-[1fr_18rem]">
                <div className="space-y-6">
                    {/* Dialogue thread */}
                    <Card>
                        <p className="mb-4 text-xs text-fg-muted">
                            Cara baca (romaji) selalu tampil. Arahkan kursor atau ketuk kalimat untuk melihat
                            terjemahan Bahasa Indonesia.
                        </p>
                        <div className="space-y-4">
                            {visibleLines.map((line) => {
                                const mine = line.role === 'b';
                                const score = scores[line.id];

                                return (
                                    <div
                                        key={line.id}
                                        className={cn('flex gap-3', mine ? 'flex-row-reverse text-right' : '')}
                                    >
                                        <span
                                            className={cn(
                                                'grid size-9 shrink-0 place-items-center rounded-full text-xs font-bold',
                                                mine
                                                    ? 'bg-primary text-white'
                                                    : 'bg-surface-sunken text-fg-muted',
                                            )}
                                        >
                                            {line.speaker.slice(0, 2).toUpperCase()}
                                        </span>

                                        <div className={cn('group/bubble min-w-0 max-w-[80%]', mine && 'items-end')}>
                                            <p className="mb-1 text-xs font-medium text-fg-muted">{line.speaker}</p>

                                            <button
                                                type="button"
                                                onClick={() => toggleReveal(line.id)}
                                                className={cn(
                                                    'block w-full rounded-md px-4 py-2.5 text-left text-sm transition',
                                                    mine
                                                        ? 'bg-primary text-white'
                                                        : 'bg-surface-sunken text-fg',
                                                )}
                                            >
                                                {line.text}
                                            </button>

                                            {line.romaji && (
                                                <p className={cn('mt-1 text-xs italic text-fg-muted', mine && 'text-right')}>
                                                    {line.romaji}
                                                </p>
                                            )}

                                            <p
                                                className={cn(
                                                    'mt-1 text-xs text-primary',
                                                    mine && 'text-right',
                                                    revealedLines.has(line.id) ? 'block' : 'hidden group-hover/bubble:block',
                                                )}
                                            >
                                                {line.translation}
                                            </p>

                                            <div className={cn('mt-1.5 flex items-center gap-2', mine && 'justify-end')}>
                                                {ttsSupported && !mine && (
                                                    <button
                                                        type="button"
                                                        onClick={() => speak(line.text, { rate: 0.92 })}
                                                        className="inline-flex items-center gap-1 text-xs text-fg-muted hover:text-primary"
                                                    >
                                                        <Volume2 className="size-3" /> Ulangi audio
                                                    </button>
                                                )}
                                                {score !== undefined && (
                                                    <Badge tone={score >= 70 ? 'success' : score >= 40 ? 'warning' : 'danger'}>
                                                        {score}%
                                                    </Badge>
                                                )}
                                            </div>
                                        </div>
                                    </div>
                                );
                            })}
                        </div>
                    </Card>

                    {/* Turn control */}
                    {!finished ? (
                        <Card>
                            {isUserTurn ? (
                                <>
                                    <CardHeader
                                        title="Giliranmu"
                                        subtitle="Ucapkan kalimat berikut sejelas mungkin."
                                    />

                                    <button
                                        type="button"
                                        onClick={() => toggleReveal(currentLine!.id)}
                                        className="group/turn w-full rounded-sm bg-surface-sunken p-4 text-center"
                                    >
                                        <p className="font-display text-lg font-bold">{currentLine!.text}</p>
                                        {currentLine!.romaji && (
                                            <p className="mt-1 text-sm italic text-fg-muted">{currentLine!.romaji}</p>
                                        )}
                                        <p
                                            className={cn(
                                                'mt-1 text-sm text-primary',
                                                revealedLines.has(currentLine!.id) ? 'block' : 'hidden group-hover/turn:block',
                                            )}
                                        >
                                            {currentLine!.translation}
                                        </p>
                                    </button>

                                    <div className="mt-5 flex flex-col items-center gap-3">
                                        <button
                                            type="button"
                                            onClick={recognition.listening ? recognition.stop : recognition.start}
                                            disabled={!recognition.supported}
                                            aria-label={recognition.listening ? 'Hentikan rekaman' : 'Mulai merekam'}
                                            className={cn(
                                                'relative grid size-16 place-items-center rounded-full text-white transition',
                                                'active:scale-95 disabled:opacity-50',
                                                recognition.listening ? 'bg-danger' : 'bg-primary hover:bg-primary-600',
                                            )}
                                        >
                                            {recognition.listening && (
                                                <span className="absolute inset-0 animate-ping rounded-full bg-danger/40" />
                                            )}
                                            {recognition.listening ? <MicOff className="size-6" /> : <Mic className="size-6" />}
                                        </button>

                                        {recognition.interim && (
                                            <p className="text-sm italic text-fg-muted">"{recognition.interim}"</p>
                                        )}

                                        <Button onClick={advance} variant={scores[currentLine!.id] ? 'primary' : 'outline'}>
                                            {scores[currentLine!.id] ? 'Lanjut' : 'Lewati giliran ini'}
                                        </Button>
                                    </div>
                                </>
                            ) : (
                                <div className="flex flex-wrap items-center justify-between gap-3">
                                    <div className="flex items-center gap-3">
                                        <Volume2 className={cn('size-5 text-primary', speaking && 'animate-pulse')} />
                                        <p className="text-sm text-fg-muted">
                                            {speaking ? 'Sedang berbicara…' : 'Dengarkan lawan bicaramu.'}
                                        </p>
                                    </div>
                                    <Button onClick={advance}>Lanjut</Button>
                                </div>
                            )}
                        </Card>
                    ) : (
                        <Card className="text-center">
                            <CheckCircle2 className="mx-auto size-12 text-success" />
                            <h2 className="mt-3 font-display text-xl font-extrabold">Percakapan selesai!</h2>
                            <p className="mt-1 text-sm text-fg-muted">
                                {scoredTurns.length > 0
                                    ? `Rata-rata skor pelafalanmu ${averageScore}% dari ${scoredTurns.length} giliran.`
                                    : 'Coba lagi dan gunakan mikrofon untuk mendapatkan skor pelafalan.'}
                            </p>

                            <div className="mt-5 flex flex-wrap justify-center gap-2">
                                <Button onClick={restart} icon={<RotateCcw className="size-4" />}>
                                    Ulangi percakapan
                                </Button>
                                <Button to="/app/conversation" variant="outline">
                                    Skenario lain
                                </Button>
                            </div>
                        </Card>
                    )}
                </div>

                <div className="space-y-6 lg:sticky lg:top-24 lg:self-start">
                    <Card>
                        <CardHeader title="Frasa kunci" subtitle="Hafalkan ini sebelum mulai." />
                        <ul className="space-y-3">
                            {scenario.keyPhrases.map((phrase) => (
                                <li key={phrase.phrase}>
                                    <div className="flex items-start justify-between gap-2">
                                        <p className="text-sm font-semibold">{phrase.phrase}</p>
                                        {ttsSupported && (
                                            <IconButton
                                                label="Dengarkan"
                                                variant="ghost"
                                                size="sm"
                                                onClick={() => speak(phrase.phrase)}
                                            >
                                                <Volume2 className="size-3.5" />
                                            </IconButton>
                                        )}
                                    </div>
                                    <p className="text-xs text-fg-muted">{phrase.meaning}</p>
                                </li>
                            ))}
                        </ul>
                    </Card>

                    {scoredTurns.length > 0 && (
                        <Card>
                            <CardHeader title="Skor giliranmu" />
                            <p className="font-display text-3xl font-extrabold">{averageScore}%</p>
                            <p className="mt-1 text-xs text-fg-muted">
                                rata-rata dari {scoredTurns.length}/{userTurns.length} giliran
                            </p>
                            <ProgressBar
                                value={averageScore}
                                tone={averageScore >= 70 ? 'success' : 'secondary'}
                                className="mt-3"
                            />
                        </Card>
                    )}
                </div>
            </div>
        </>
    );
}

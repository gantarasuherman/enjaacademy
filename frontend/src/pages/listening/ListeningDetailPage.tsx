import { useMemo, useState } from 'react';
import { useParams } from 'react-router-dom';
import {
    Bookmark,
    BookmarkCheck,
    GraduationCap,
    Gauge,
    Eye,
    EyeOff,
    Pause,
    Play,
    RotateCcw,
    RotateCw,
} from 'lucide-react';
import { useAsync } from '@/hooks/useAsync';
import { useAudioPlayer } from '@/hooks/useAudioPlayer';
import { useSpeechSynthesis } from '@/hooks/useSpeechSynthesis';
import { listeningService } from '@/services/api';
import { useProgressStore } from '@/store/progressStore';
import { cn } from '@/utils/cn';
import { formatClock } from '@/utils/format';
import { Card, CardHeader } from '@/components/ui/Card';
import { Button, IconButton } from '@/components/ui/Button';
import { Badge, CefrBadge, Chip } from '@/components/ui/Badge';
import { Alert, EmptyState, PageLoader } from '@/components/ui/Feedback';
import { PageHeader } from '@/components/feature/shared/PageHeader';

const RATES = [0.6, 0.75, 1, 1.25];

export default function ListeningDetailPage() {
    const { lessonId = '' } = useParams();
    const { data: track, loading } = useAsync(() => listeningService.get(lessonId), [lessonId]);

    const player = useAudioPlayer(track?.audioUrl ?? null);
    const { speak, stop, speaking, supported: ttsSupported } = useSpeechSynthesis();
    const { isBookmarked, toggleBookmark } = useProgressStore();

    const [showTranscript, setShowTranscript] = useState(false);
    const [showTranslation, setShowTranslation] = useState(true);

    // Which subtitle cue is on screen right now.
    const activeCue = useMemo(() => {
        if (!track) return -1;

        return track.subtitles.findIndex(
            (cue) => player.currentTime >= cue.start && player.currentTime < cue.end,
        );
    }, [track, player.currentTime]);

    if (loading) return <PageLoader />;

    if (!track) {
        return <EmptyState title="Materi tidak ditemukan" action={<Button to="/app/listening">Kembali</Button>} />;
    }

    const bookmarked = isBookmarked('listening', track.id);
    const progress = player.duration > 0 ? (player.currentTime / player.duration) * 100 : 0;

    return (
        <>
            <PageHeader
                backTo="/app/listening"
                backLabel="Semua track"
                title={track.title}
                description={track.description}
                badge={<CefrBadge level={track.cefr} />}
                action={
                    <Button
                        variant={bookmarked ? 'primary' : 'outline'}
                        icon={bookmarked ? <BookmarkCheck className="size-4" /> : <Bookmark className="size-4" />}
                        onClick={() =>
                            toggleBookmark({
                                kind: 'listening',
                                refId: track.id,
                                title: track.title,
                                subtitle: `${track.accent} accent · ${track.cefr}`,
                            })
                        }
                    >
                        {bookmarked ? 'Tersimpan' : 'Simpan'}
                    </Button>
                }
            />

            <div className="grid gap-6 lg:grid-cols-[1fr_20rem]">
                <div className="space-y-6">
                    {/* Player */}
                    <Card>
                        {player.error && (
                            <Alert tone="warning" className="mb-4">
                                {player.error} Gunakan tombol "Bacakan transkrip" di bawah sebagai gantinya.
                            </Alert>
                        )}

                        <div className="flex items-center gap-4">
                            <IconButton
                                label={player.playing ? 'Jeda' : 'Putar'}
                                size="lg"
                                onClick={player.toggle}
                                disabled={!!player.error}
                            >
                                {player.playing ? <Pause className="size-5" /> : <Play className="size-5" />}
                            </IconButton>

                            <div className="min-w-0 flex-1">
                                <input
                                    type="range"
                                    min={0}
                                    max={player.duration || track.durationSeconds}
                                    value={player.currentTime}
                                    onChange={(event) => player.seek(Number(event.target.value))}
                                    aria-label="Posisi audio"
                                    className="w-full accent-[var(--color-primary)]"
                                    style={{
                                        background: `linear-gradient(to right, var(--color-primary) ${progress}%, var(--surface-sunken) ${progress}%)`,
                                    }}
                                />
                                <div className="mt-1 flex justify-between font-mono text-xs text-fg-muted">
                                    <span>{formatClock(Math.floor(player.currentTime))}</span>
                                    <span>{formatClock(Math.floor(player.duration || track.durationSeconds))}</span>
                                </div>
                            </div>
                        </div>

                        <div className="mt-4 flex flex-wrap items-center gap-2">
                            <IconButton label="Mundur 5 detik" variant="outline" size="sm" onClick={() => player.skip(-5)}>
                                <RotateCcw className="size-4" />
                            </IconButton>
                            <IconButton label="Maju 5 detik" variant="outline" size="sm" onClick={() => player.skip(5)}>
                                <RotateCw className="size-4" />
                            </IconButton>

                            <span className="ml-2 flex items-center gap-1.5">
                                <Gauge className="size-4 text-fg-muted" />
                                {RATES.map((rate) => (
                                    <Chip key={rate} active={player.rate === rate} onClick={() => player.changeRate(rate)}>
                                        {rate}×
                                    </Chip>
                                ))}
                            </span>

                            {ttsSupported && (
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    className="ml-auto"
                                    onClick={() => (speaking ? stop() : speak(track.transcript, { rate: 0.9 }))}
                                >
                                    {speaking ? 'Hentikan' : 'Bacakan transkrip'}
                                </Button>
                            )}
                        </div>
                    </Card>

                    {/* Subtitles */}
                    <Card>
                        <CardHeader
                            title="Subtitle"
                            action={
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    icon={showTranslation ? <EyeOff className="size-4" /> : <Eye className="size-4" />}
                                    onClick={() => setShowTranslation((v) => !v)}
                                >
                                    {showTranslation ? 'Sembunyikan terjemahan' : 'Tampilkan terjemahan'}
                                </Button>
                            }
                        />

                        <ul className="space-y-2">
                            {track.subtitles.map((cue, index) => (
                                <li key={index}>
                                    <button
                                        type="button"
                                        onClick={() => player.seek(cue.start)}
                                        className={cn(
                                            'w-full rounded-sm border-l-4 p-3 text-left transition',
                                            index === activeCue
                                                ? 'border-l-primary bg-primary-50 dark:bg-primary/12'
                                                : 'border-l-transparent hover:bg-surface-sunken',
                                        )}
                                    >
                                        <div className="flex items-start gap-3">
                                            <span className="shrink-0 font-mono text-[11px] text-fg-muted">
                                                {formatClock(Math.floor(cue.start))}
                                            </span>
                                            <div className="min-w-0">
                                                <p className={cn('text-sm', index === activeCue && 'font-semibold')}>
                                                    {cue.text}
                                                </p>
                                                {showTranslation && (
                                                    <p className="mt-0.5 text-sm text-fg-muted">{cue.translation}</p>
                                                )}
                                            </div>
                                        </div>
                                    </button>
                                </li>
                            ))}
                        </ul>
                    </Card>
                </div>

                <div className="space-y-6 lg:sticky lg:top-24 lg:self-start">
                    <Card>
                        <CardHeader title="Cara belajar" />
                        <ol className="space-y-3 text-sm">
                            {[
                                'Dengarkan sekali penuh tanpa membaca apa pun.',
                                'Dengarkan lagi di kecepatan 0,75× sambil melihat subtitle Inggris.',
                                'Buka terjemahan hanya untuk bagian yang masih gelap.',
                                'Dengarkan terakhir kali di 1× tanpa teks.',
                            ].map((step, index) => (
                                <li key={step} className="flex gap-3">
                                    <span className="grid size-5 shrink-0 place-items-center rounded-full bg-primary-100 text-[11px] font-bold text-primary dark:bg-primary/20">
                                        {index + 1}
                                    </span>
                                    {step}
                                </li>
                            ))}
                        </ol>
                    </Card>

                    <Card>
                        <CardHeader
                            title="Transkrip lengkap"
                            action={
                                <Button variant="ghost" size="sm" onClick={() => setShowTranscript((v) => !v)}>
                                    {showTranscript ? 'Tutup' : 'Buka'}
                                </Button>
                            }
                        />
                        {showTranscript ? (
                            <div className="space-y-2 text-sm leading-relaxed">
                                {track.transcript.split('\n').map((line, index) => (
                                    <p key={index}>{line}</p>
                                ))}
                            </div>
                        ) : (
                            <p className="text-sm text-fg-muted">
                                Coba dengarkan dulu tanpa transkrip — bukalah hanya kalau benar-benar buntu.
                            </p>
                        )}
                    </Card>

                    {track.quizId && (
                        <Card className="bg-primary-50 dark:bg-primary/10">
                            <div className="flex items-center gap-3">
                                <GraduationCap className="size-6 shrink-0 text-primary" />
                                <div className="min-w-0">
                                    <p className="font-display font-bold">Kuis menyimak</p>
                                    <p className="text-xs text-fg-muted">Soal berbasis audio ini.</p>
                                </div>
                            </div>
                            <Button to={`/app/quiz/${track.quizId}`} fullWidth className="mt-4">
                                Kerjakan kuis
                            </Button>
                        </Card>
                    )}

                    <Badge tone="info" className="w-full justify-center py-2">
                        Aksen: {track.accent}
                    </Badge>
                </div>
            </div>
        </>
    );
}

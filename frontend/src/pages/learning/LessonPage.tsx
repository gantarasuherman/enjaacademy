import { useEffect, useState } from 'react';
import { Link, useNavigate, useParams } from 'react-router-dom';
import { ChevronLeft, ChevronRight, CheckCircle2, Clock, GraduationCap, Volume2, Zap } from 'lucide-react';
import { useAsync } from '@/hooks/useAsync';
import { useSpeechSynthesis } from '@/hooks/useSpeechSynthesis';
import { learningService, vocabularyService, grammarService, readingService, listeningService, conversationService } from '@/services/api';
import { useProgressStore } from '@/store/progressStore';
import { useUiStore } from '@/store/uiStore';
import { splitHighlight } from '@/utils/format';
import type { Lesson } from '@/types';
import { Card, CardHeader } from '@/components/ui/Card';
import { Button, IconButton } from '@/components/ui/Button';
import { Badge, CefrBadge } from '@/components/ui/Badge';
import { EmptyState, PageLoader } from '@/components/ui/Feedback';
import { PageHeader } from '@/components/feature/shared/PageHeader';

/** Renders the lesson body according to its skill type. */
function LessonBody({ lesson }: { lesson: Lesson }) {
    const { speak } = useSpeechSynthesis();

    const { data: words } = useAsync(
        () => (lesson.vocabularyIds ? vocabularyService.listByIds(lesson.vocabularyIds) : Promise.resolve([])),
        [lesson.id],
    );

    const { data: topic } = useAsync(
        () => (lesson.grammarTopicId ? grammarService.getTopic(lesson.grammarTopicId) : Promise.resolve(null)),
        [lesson.id],
    );

    const { data: text } = useAsync(
        () => (lesson.readingTextId ? readingService.get(lesson.readingTextId) : Promise.resolve(null)),
        [lesson.id],
    );

    const { data: track } = useAsync(
        () => (lesson.listeningTrackId ? listeningService.get(lesson.listeningTrackId) : Promise.resolve(null)),
        [lesson.id],
    );

    const { data: scenario } = useAsync(
        () => (lesson.conversationId ? conversationService.get(lesson.conversationId) : Promise.resolve(null)),
        [lesson.id],
    );

    if (lesson.type === 'vocabulary' && words) {
        return (
            <div className="space-y-3">
                {words.map((word) => (
                    <Card key={word.id} className="flex items-start gap-4">
                        <IconButton
                            label={`Dengarkan ${word.word}`}
                            variant="outline"
                            size="sm"
                            onClick={() => speak(word.word)}
                        >
                            <Volume2 className="size-4" />
                        </IconButton>

                        <div className="min-w-0 flex-1">
                            <div className="flex flex-wrap items-baseline gap-2">
                                <p className="font-display text-lg font-bold">{word.word}</p>
                                <span className="ipa text-sm text-fg-muted">{word.ipa}</span>
                                <Badge tone="neutral">{word.partOfSpeech}</Badge>
                                <CefrBadge level={word.cefr} />
                            </div>
                            <p className="mt-1 text-sm font-medium text-primary">{word.meaning}</p>
                            <p className="mt-2 text-sm italic text-fg-muted">"{word.example}"</p>
                            <p className="text-sm text-fg-muted">{word.exampleMeaning}</p>
                        </div>
                    </Card>
                ))}
            </div>
        );
    }

    if (lesson.type === 'grammar' && topic) {
        return (
            <div className="space-y-5">
                <Card>
                    <CardHeader title={topic.title} subtitle={topic.explanation} />
                    {topic.formula && (
                        <div className="rounded-sm bg-primary-50 p-4 dark:bg-primary/10">
                            <p className="mb-1 text-xs font-bold uppercase tracking-wide text-primary">Rumus</p>
                            <p className="font-mono text-sm">{topic.formula}</p>
                        </div>
                    )}
                </Card>

                <Card>
                    <CardHeader title="Contoh kalimat" />
                    <ul className="space-y-3">
                        {topic.examples.map((example, index) => (
                            <li key={index} className="rounded-sm bg-surface-sunken p-3">
                                <p className="text-sm">
                                    {splitHighlight(example.sentence).map((chunk, i) =>
                                        chunk.bold ? (
                                            <strong key={i} className="rounded bg-secondary/20 px-1 text-secondary-700 dark:text-secondary-300">
                                                {chunk.text}
                                            </strong>
                                        ) : (
                                            <span key={i}>{chunk.text}</span>
                                        ),
                                    )}
                                </p>
                                <p className="mt-1 text-sm text-fg-muted">{example.meaning}</p>
                            </li>
                        ))}
                    </ul>
                </Card>

                {topic.commonMistakes.length > 0 && (
                    <Card>
                        <CardHeader title="Kesalahan umum" />
                        <ul className="space-y-3">
                            {topic.commonMistakes.map((mistake, index) => (
                                <li key={index} className="rounded-sm border border-[var(--surface-border)] p-3">
                                    <p className="text-sm text-danger line-through">{mistake.wrong}</p>
                                    <p className="text-sm font-semibold text-success">{mistake.right}</p>
                                    <p className="mt-1 text-xs text-fg-muted">{mistake.why}</p>
                                </li>
                            ))}
                        </ul>
                    </Card>
                )}
            </div>
        );
    }

    if (lesson.type === 'reading' && text) {
        return (
            <Card>
                <CardHeader title={text.title} subtitle={`${text.wordCount} kata · ${text.readingMinutes} menit baca`} />
                <div className="prose-sm max-w-none space-y-4 leading-relaxed">
                    {text.body.split('\n\n').map((paragraph, index) => (
                        <p key={index} className="text-sm leading-7">
                            {paragraph}
                        </p>
                    ))}
                </div>
                <Button to={`/app/reading/${text.id}`} variant="outline" className="mt-5">
                    Buka dengan glosarium
                </Button>
            </Card>
        );
    }

    if (lesson.type === 'listening' && track) {
        return (
            <Card>
                <CardHeader title={track.title} subtitle={track.description} />
                <Button to={`/app/listening/${track.id}`} icon={<Volume2 className="size-4" />}>
                    Buka pemutar audio
                </Button>
            </Card>
        );
    }

    if (lesson.type === 'conversation' && scenario) {
        return (
            <Card>
                <CardHeader title={scenario.title} subtitle={scenario.context} />
                <Button to={`/app/conversation/${scenario.id}`}>Mulai percakapan</Button>
            </Card>
        );
    }

    if (lesson.type === 'speaking') {
        return (
            <Card>
                <CardHeader title="Latihan pelafalan" subtitle="Rekam suaramu dan dapatkan skor otomatis." />
                <Button to="/app/speaking">Buka latihan speaking</Button>
            </Card>
        );
    }

    if (lesson.type === 'writing') {
        return (
            <Card>
                <CardHeader title="Latihan menulis" subtitle="Kerjakan prompt dengan rubrik penilaian." />
                <Button to="/app/writing">Buka latihan writing</Button>
            </Card>
        );
    }

    return <EmptyState title="Materi belum tersedia" description="Konten untuk materi ini sedang disiapkan." />;
}

export default function LessonPage() {
    const { lessonId = '' } = useParams();
    const navigate = useNavigate();
    const toast = useUiStore((state) => state.toast);

    const { data: lesson, loading } = useAsync(() => learningService.getLesson(lessonId), [lessonId]);
    const { data: neighbours } = useAsync(() => learningService.getNeighbours(lessonId), [lessonId]);

    const { lessonStatus, completeLesson, recordMinutes } = useProgressStore();
    const [submitting, setSubmitting] = useState(false);

    // Count the time actually spent on the page towards the daily goal.
    useEffect(() => {
        const startedAt = Date.now();

        return () => {
            const minutes = Math.round((Date.now() - startedAt) / 60000);
            if (minutes >= 1) recordMinutes(minutes);
        };
    }, [lessonId, recordMinutes]);

    if (loading) return <PageLoader />;

    if (!lesson) {
        return (
            <EmptyState
                title="Materi tidak ditemukan"
                description="Materi ini mungkin sudah dipindahkan."
                action={<Button to="/app/learning">Kembali</Button>}
            />
        );
    }

    const done = lessonStatus(lesson.id) === 'completed';

    async function handleComplete() {
        if (!lesson) return;

        setSubmitting(true);

        const xp = await completeLesson(lesson.id, lesson.moduleId);

        toast(`+${xp} XP — materi selesai!`, 'success', 'Kerja bagus');
        setSubmitting(false);

        const next = neighbours?.next;
        navigate(next ? `/app/learning/lesson/${next.id}` : `/app/learning/${lesson.moduleId}`);
    }

    return (
        <>
            <PageHeader
                backTo={`/app/learning/${lesson.moduleId}`}
                backLabel="Kembali ke modul"
                title={lesson.title}
                description={lesson.description}
                badge={
                    done ? (
                        <Badge tone="success" icon={<CheckCircle2 className="size-3" />}>
                            Selesai
                        </Badge>
                    ) : undefined
                }
            />

            <div className="mb-5 flex flex-wrap gap-4 text-sm text-fg-muted">
                <span className="flex items-center gap-1.5">
                    <Clock className="size-4" />
                    {lesson.durationMinutes} menit
                </span>
                <span className="flex items-center gap-1.5">
                    <Zap className="size-4" />+{lesson.xpReward} XP
                </span>
            </div>

            <LessonBody lesson={lesson} />

            {lesson.quizId && (
                <Card className="mt-6 flex flex-wrap items-center justify-between gap-4 bg-primary-50 dark:bg-primary/10">
                    <div className="flex items-center gap-3">
                        <GraduationCap className="size-6 text-primary" />
                        <div>
                            <p className="font-display font-bold">Kuis penutup materi</p>
                            <p className="text-sm text-fg-muted">Uji pemahamanmu sebelum lanjut.</p>
                        </div>
                    </div>
                    <Button to={`/app/quiz/${lesson.quizId}`}>Kerjakan kuis</Button>
                </Card>
            )}

            {/* Bottom navigation */}
            <div className="mt-8 flex flex-wrap items-center justify-between gap-3 border-t border-[var(--surface-border)] pt-6">
                {neighbours?.previous ? (
                    <Link
                        to={`/app/learning/lesson/${neighbours.previous.id}`}
                        className="inline-flex items-center gap-1.5 text-sm font-medium text-fg-muted transition hover:text-primary"
                    >
                        <ChevronLeft className="size-4" />
                        {neighbours.previous.title}
                    </Link>
                ) : (
                    <span />
                )}

                <Button
                    onClick={handleComplete}
                    loading={submitting}
                    variant={done ? 'outline' : 'primary'}
                    icon={<CheckCircle2 className="size-4" />}
                >
                    {done ? 'Tandai ulang selesai' : 'Tandai selesai'}
                </Button>

                {neighbours?.next ? (
                    <Link
                        to={`/app/learning/lesson/${neighbours.next.id}`}
                        className="inline-flex items-center gap-1.5 text-sm font-medium text-fg-muted transition hover:text-primary"
                    >
                        {neighbours.next.title}
                        <ChevronRight className="size-4" />
                    </Link>
                ) : (
                    <span />
                )}
            </div>
        </>
    );
}

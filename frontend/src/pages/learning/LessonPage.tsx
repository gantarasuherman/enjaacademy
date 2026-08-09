import { useEffect, useState } from 'react';
import { Link, useNavigate, useParams } from 'react-router-dom';
import { ChevronLeft, ChevronRight, CheckCircle2, Clock, Volume2, Zap } from 'lucide-react';
import { useAsync } from '@/hooks/useAsync';
import { useSpeechSynthesis } from '@/hooks/useSpeechSynthesis';
import { learningService } from '@/services/api';
import { useProgressStore } from '@/store/progressStore';
import { useUiStore } from '@/store/uiStore';
import type { Lesson } from '@/types';
import { Card } from '@/components/ui/Card';
import { Button, IconButton } from '@/components/ui/Button';
import { Badge } from '@/components/ui/Badge';
import { EmptyState, PageLoader } from '@/components/ui/Feedback';
import { PageHeader } from '@/components/feature/shared/PageHeader';

/**
 * Generic body: the lesson's own HTML `content` plus its flat `items` list.
 * A lesson's content shape doesn't vary by skill — that richer, per-skill
 * presentation lives in the dedicated Vocabulary/Grammar/Conversation pages,
 * which read the same underlying data.
 */
function LessonBody({ lesson }: { lesson: Lesson }) {
    const { speak } = useSpeechSynthesis();
    const items = lesson.items ?? [];

    return (
        <div className="space-y-5">
            {lesson.content && (
                <Card>
                    <div
                        className="prose-sm max-w-none text-sm leading-relaxed"
                        dangerouslySetInnerHTML={{ __html: lesson.content }}
                    />
                </Card>
            )}

            {items.length > 0 && (
                <div className="space-y-3">
                    {items.map((item) => (
                        <Card key={item.id} className="flex items-start gap-4">
                            <IconButton
                                label={`Dengarkan ${item.term}`}
                                variant="outline"
                                size="sm"
                                onClick={() => speak(item.term)}
                            >
                                <Volume2 className="size-4" />
                            </IconButton>

                            <div className="min-w-0 flex-1">
                                <div className="flex flex-wrap items-baseline gap-2">
                                    <p className="font-display text-lg font-bold">{item.term}</p>
                                    {item.reading && item.reading !== item.term && (
                                        <span className="text-sm text-fg-muted">{item.reading}</span>
                                    )}
                                    {item.romaji && <span className="text-sm text-fg-muted">{item.romaji}</span>}
                                </div>
                                {item.meaning && <p className="mt-1 text-sm font-medium text-primary">{item.meaning}</p>}
                                {item.example && <p className="mt-2 text-sm italic text-fg-muted">"{item.example}"</p>}
                                {item.example_meaning && (
                                    <p className="text-sm text-fg-muted">{item.example_meaning}</p>
                                )}
                            </div>
                        </Card>
                    ))}
                </div>
            )}

            {!lesson.content && items.length === 0 && (
                <EmptyState title="Materi belum tersedia" description="Konten untuk materi ini sedang disiapkan." />
            )}
        </div>
    );
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

    const moduleSlug = lesson.module?.slug;
    const done = lessonStatus(lesson.slug) === 'completed';

    async function handleComplete() {
        if (!lesson) return;

        setSubmitting(true);

        const xp = await completeLesson(lesson.slug, String(lesson.module?.id ?? ''), lesson.xp_reward);

        toast(`+${xp} XP — materi selesai!`, 'success', 'Kerja bagus');
        setSubmitting(false);

        const next = neighbours?.next;
        navigate(next ? `/app/learning/lesson/${next.slug}` : moduleSlug ? `/app/learning/${moduleSlug}` : '/app/learning');
    }

    return (
        <>
            <PageHeader
                backTo={moduleSlug ? `/app/learning/${moduleSlug}` : '/app/learning'}
                backLabel="Kembali ke modul"
                title={lesson.title}
                description={lesson.summary ?? undefined}
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
                    {lesson.estimated_minutes} menit
                </span>
                <span className="flex items-center gap-1.5">
                    <Zap className="size-4" />+{lesson.xp_reward} XP
                </span>
            </div>

            <LessonBody lesson={lesson} />

            {/* Bottom navigation */}
            <div className="mt-8 flex flex-wrap items-center justify-between gap-3 border-t border-[var(--surface-border)] pt-6">
                {neighbours?.previous ? (
                    <Link
                        to={`/app/learning/lesson/${neighbours.previous.slug}`}
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
                        to={`/app/learning/lesson/${neighbours.next.slug}`}
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

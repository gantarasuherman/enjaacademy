import { useEffect, useRef, useState } from 'react';
import { Link, useNavigate, useParams } from 'react-router-dom';
import { ChevronLeft, ChevronRight, CheckCircle2, Clock, ClipboardList, Volume2, Zap } from 'lucide-react';
import { useAsync } from '@/hooks/useAsync';
import { useSpeechSynthesis } from '@/hooks/useSpeechSynthesis';
import { learningService, quizService } from '@/services/api';
import { useProgressStore } from '@/store/progressStore';
import { useUiStore } from '@/store/uiStore';
import { cn } from '@/utils/cn';
import type { Lesson, LessonItem, Progress } from '@/types';
import { Card, CardHeader } from '@/components/ui/Card';
import { Button, IconButton } from '@/components/ui/Button';
import { Badge } from '@/components/ui/Badge';
import { EmptyState, PageLoader } from '@/components/ui/Feedback';
import { PageHeader } from '@/components/feature/shared/PageHeader';
import { VideoEmbed, type VideoEmbedHandle } from '@/components/feature/learning/VideoEmbed';

/** Chapters are stored as `mm:ss` in `extra.timestamp` — parsed only when seeking. */
function timestampToSeconds(value: string): number {
    const parts = value.split(':').map((part) => parseInt(part, 10));
    if (parts.some(Number.isNaN)) return 0;

    return parts.reduce((total, part) => total * 60 + part, 0);
}

function isVideoChapter(item: LessonItem): boolean {
    return item.extra?.type === 'video_chapter';
}

/**
 * Generic body: the lesson's own HTML `content` plus its flat `items` list.
 * A lesson's content shape doesn't vary by skill — that richer, per-skill
 * presentation lives in the dedicated Vocabulary/Grammar/Conversation pages,
 * which read the same underlying data. Video lessons additionally render an
 * embedded player plus any `video_chapter` items as a clickable chapter list.
 */
function LessonBody({ lesson, onVideoEnded }: { lesson: Lesson; onVideoEnded?: () => void }) {
    const { speak } = useSpeechSynthesis();
    const videoRef = useRef<VideoEmbedHandle>(null);
    const items = lesson.items ?? [];
    const chapters = items.filter(isVideoChapter);
    const regularItems = items.filter((item) => !isVideoChapter(item));

    return (
        <div className="space-y-5">
            {lesson.video_url && (
                <>
                    <Card padded={false} className="overflow-hidden">
                        <VideoEmbed ref={videoRef} url={lesson.video_url} onEnded={onVideoEnded} />
                    </Card>

                    {chapters.length > 0 && (
                        <Card>
                            <p className="mb-2 text-xs font-semibold uppercase tracking-wide text-fg-muted">Bab video</p>
                            <div className="space-y-1">
                                {chapters.map((chapter) => (
                                    <button
                                        key={chapter.id}
                                        type="button"
                                        onClick={() =>
                                            videoRef.current?.seekTo(timestampToSeconds(String(chapter.extra?.timestamp ?? '0:00')))
                                        }
                                        className="flex w-full items-center justify-between rounded-sm px-2 py-1.5 text-left text-sm transition hover:bg-surface-sunken"
                                    >
                                        <span className="font-medium">{chapter.term}</span>
                                        <span className="text-fg-muted">{String(chapter.extra?.timestamp ?? '')}</span>
                                    </button>
                                ))}
                            </div>
                        </Card>
                    )}
                </>
            )}

            {lesson.content && (
                <Card>
                    <div
                        className="prose-sm max-w-none text-sm leading-relaxed"
                        dangerouslySetInnerHTML={{ __html: lesson.content }}
                    />
                </Card>
            )}

            {regularItems.length > 0 && (
                <div className="space-y-3">
                    {regularItems.map((item) => (
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

            {!lesson.content && !lesson.video_url && regularItems.length === 0 && (
                <EmptyState title="Materi belum tersedia" description="Konten untuk materi ini sedang disiapkan." />
            )}
        </div>
    );
}

/** Udemy-style "up next" playlist for video modules — the whole module's lesson list, current one highlighted. */
function CurriculumSidebar({
    lessons,
    currentSlug,
    lessonStatus,
}: {
    lessons: Lesson[];
    currentSlug: string;
    lessonStatus: (slug: string) => Progress['status'];
}) {
    const completedCount = lessons.filter((lesson) => lessonStatus(lesson.slug) === 'completed').length;

    return (
        <Card padded={false} className="h-fit overflow-hidden lg:sticky lg:top-6">
            <div className="border-b border-[var(--surface-border)] p-4">
                <p className="font-display text-sm font-bold">Kurikulum</p>
                <p className="mt-0.5 text-xs text-fg-muted">
                    {completedCount}/{lessons.length} video selesai
                </p>
            </div>

            <div className="max-h-[70vh] space-y-0.5 overflow-y-auto p-2">
                {lessons.map((lesson, index) => {
                    const status = lessonStatus(lesson.slug);
                    const active = lesson.slug === currentSlug;

                    return (
                        <Link
                            key={lesson.id}
                            to={`/app/learning/lesson/${lesson.slug}`}
                            className={cn(
                                'flex items-start gap-3 rounded-sm px-3 py-2.5 text-sm transition',
                                active ? 'bg-primary-100 dark:bg-primary/15' : 'hover:bg-surface-sunken',
                            )}
                        >
                            <span
                                className={cn(
                                    'mt-0.5 grid size-6 shrink-0 place-items-center rounded-full text-[11px] font-bold',
                                    status === 'completed'
                                        ? 'bg-success/15 text-success'
                                        : active
                                          ? 'bg-primary text-white'
                                          : 'bg-surface-sunken text-fg-muted',
                                )}
                            >
                                {status === 'completed' ? <CheckCircle2 className="size-3.5" /> : index + 1}
                            </span>

                            <span className="min-w-0 flex-1">
                                <span className={cn('block truncate font-medium', active && 'text-primary')}>{lesson.title}</span>
                                <span className="mt-0.5 flex items-center gap-1 text-xs text-fg-muted">
                                    <Clock className="size-3" />
                                    {lesson.estimated_minutes}m
                                </span>
                            </span>
                        </Link>
                    );
                })}
            </div>
        </Card>
    );
}

export default function LessonPage() {
    const { lessonId = '' } = useParams();
    const navigate = useNavigate();
    const toast = useUiStore((state) => state.toast);

    const { data: lesson, loading } = useAsync(() => learningService.getLesson(lessonId), [lessonId]);
    const { data: neighbours } = useAsync(() => learningService.getNeighbours(lessonId), [lessonId]);
    const { data: lessonQuizzes } = useAsync(
        () => (lesson ? quizService.list({ lessonId: String(lesson.id) }) : Promise.resolve([])),
        [lesson?.id],
    );

    const isVideoModule = lesson?.module?.content_type === 'video';
    const moduleSlug = lesson?.module?.slug;
    const { data: moduleLessons } = useAsync(
        () => (isVideoModule && moduleSlug ? learningService.listLessons(moduleSlug) : Promise.resolve([])),
        [isVideoModule, moduleSlug],
    );

    const { lessonStatus, completeLesson, recordMinutes } = useProgressStore();
    const [submitting, setSubmitting] = useState(false);

    // Guards the autoplay-next redirect against a player firing "ended" more than once for the same lesson.
    const autoplayFiredRef = useRef(false);
    useEffect(() => {
        autoplayFiredRef.current = false;
    }, [lessonId]);

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

    function handleVideoEnded() {
        if (autoplayFiredRef.current) return;

        const next = neighbours?.next;
        if (!next) return;

        autoplayFiredRef.current = true;
        toast('Video selesai — lanjut ke video berikutnya…', 'info');
        setTimeout(() => navigate(`/app/learning/lesson/${next.slug}`), 2000);
    }

    const mainContent = (
        <>
            <LessonBody lesson={lesson} onVideoEnded={isVideoModule ? handleVideoEnded : undefined} />

            {lessonQuizzes && lessonQuizzes.length > 0 && (
                <div className="mt-5 space-y-3">
                    {lessonQuizzes.map((quiz) => (
                        <Card key={quiz.id}>
                            <CardHeader
                                title="Quiz materi ini"
                                subtitle={`${quiz.title} · ${quiz.questionIds.length} soal · opsional, tidak menghalangi "Tandai selesai"`}
                                action={
                                    <Button to={`/app/quiz/${quiz.id}`} variant="outline" icon={<ClipboardList className="size-4" />}>
                                        Kerjakan
                                    </Button>
                                }
                            />
                        </Card>
                    ))}
                </div>
            )}

            {/* Bottom navigation — for video modules the curriculum sidebar already covers jumping between lessons. */}
            <div
                className={cn(
                    'mt-8 flex flex-wrap items-center gap-3 border-t border-[var(--surface-border)] pt-6',
                    isVideoModule ? 'justify-center' : 'justify-between',
                )}
            >
                {!isVideoModule &&
                    (neighbours?.previous ? (
                        <Link
                            to={`/app/learning/lesson/${neighbours.previous.slug}`}
                            className="inline-flex items-center gap-1.5 text-sm font-medium text-fg-muted transition hover:text-primary"
                        >
                            <ChevronLeft className="size-4" />
                            {neighbours.previous.title}
                        </Link>
                    ) : (
                        <span />
                    ))}

                <Button
                    onClick={handleComplete}
                    loading={submitting}
                    variant={done ? 'outline' : 'primary'}
                    icon={<CheckCircle2 className="size-4" />}
                >
                    {done ? 'Tandai ulang selesai' : 'Tandai selesai'}
                </Button>

                {!isVideoModule &&
                    (neighbours?.next ? (
                        <Link
                            to={`/app/learning/lesson/${neighbours.next.slug}`}
                            className="inline-flex items-center gap-1.5 text-sm font-medium text-fg-muted transition hover:text-primary"
                        >
                            {neighbours.next.title}
                            <ChevronRight className="size-4" />
                        </Link>
                    ) : (
                        <span />
                    ))}
            </div>
        </>
    );

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

            {isVideoModule && moduleLessons && moduleLessons.length > 0 ? (
                <div className="grid items-start gap-6 lg:grid-cols-[1fr_320px]">
                    <div className="min-w-0">{mainContent}</div>
                    <CurriculumSidebar lessons={moduleLessons} currentSlug={lesson.slug} lessonStatus={lessonStatus} />
                </div>
            ) : (
                mainContent
            )}
        </>
    );
}

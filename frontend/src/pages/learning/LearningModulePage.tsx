import { Link, useParams } from 'react-router-dom';
import { BookOpen, CheckCircle2, Circle, Clock, Lock, PlayCircle, Zap } from 'lucide-react';
import { useAsync } from '@/hooks/useAsync';
import { learningService } from '@/services/api';
import { useProgressStore } from '@/store/progressStore';
import { cn } from '@/utils/cn';
import type { Lesson } from '@/types';
import { Card, CardHeader } from '@/components/ui/Card';
import { Button } from '@/components/ui/Button';
import { CefrBadge, DifficultyBadge } from '@/components/ui/Badge';
import { ProgressBar } from '@/components/ui/Progress';
import { EmptyState, PageLoader } from '@/components/ui/Feedback';
import { PageHeader } from '@/components/feature/shared/PageHeader';

const TYPE_LABEL: Record<string, string> = {
    vocabulary: 'Kosakata',
    grammar: 'Tata bahasa',
    listening: 'Menyimak',
    speaking: 'Berbicara',
    reading: 'Membaca',
    writing: 'Menulis',
    conversation: 'Percakapan',
};

function LessonRow({
    lesson,
    index,
    status,
    locked,
}: {
    lesson: Lesson;
    index: number;
    status: 'completed' | 'in-progress' | 'not-started';
    locked: boolean;
}) {
    const Icon = status === 'completed' ? CheckCircle2 : status === 'in-progress' ? PlayCircle : Circle;

    const body = (
        <div
            className={cn(
                'flex items-center gap-4 rounded-sm border border-[var(--surface-border)] p-4 transition',
                locked ? 'opacity-55' : 'hover:border-primary-300 hover:bg-surface-sunken',
            )}
        >
            <span
                className={cn(
                    'grid size-9 shrink-0 place-items-center rounded-full text-sm font-bold',
                    status === 'completed'
                        ? 'bg-success/15 text-success'
                        : status === 'in-progress'
                          ? 'bg-primary-100 text-primary dark:bg-primary/20'
                          : 'bg-surface-sunken text-fg-muted',
                )}
            >
                {locked ? <Lock className="size-4" /> : status === 'not-started' ? index + 1 : <Icon className="size-4.5" />}
            </span>

            <div className="min-w-0 flex-1">
                <div className="flex flex-wrap items-center gap-2">
                    <p className="truncate font-semibold">{lesson.title}</p>
                    <span className="rounded-full bg-surface-sunken px-2 py-0.5 text-[11px] font-medium text-fg-muted">
                        {TYPE_LABEL[lesson.type] ?? lesson.type}
                    </span>
                </div>
                <p className="mt-0.5 truncate text-sm text-fg-muted">{lesson.description}</p>
            </div>

            <div className="hidden shrink-0 items-center gap-4 text-xs text-fg-muted sm:flex">
                <span className="flex items-center gap-1">
                    <Clock className="size-3.5" />
                    {lesson.durationMinutes}m
                </span>
                <span className="flex items-center gap-1">
                    <Zap className="size-3.5" />+{lesson.xpReward}
                </span>
            </div>
        </div>
    );

    return locked ? (
        <div title="Selesaikan materi sebelumnya untuk membukanya">{body}</div>
    ) : (
        <Link to={`/app/learning/lesson/${lesson.id}`}>{body}</Link>
    );
}

export default function LearningModulePage() {
    const { moduleId = '' } = useParams();

    const { data: module, loading } = useAsync(() => learningService.getModule(moduleId), [moduleId]);
    const { data: lessons } = useAsync(() => learningService.listLessons(moduleId), [moduleId]);

    const { lessonStatus, modulePercent } = useProgressStore();

    if (loading) return <PageLoader />;

    if (!module) {
        return (
            <EmptyState
                icon={<BookOpen className="size-6" />}
                title="Modul tidak ditemukan"
                description="Modul ini mungkin sudah dipindahkan."
                action={<Button to="/app/learning">Kembali ke katalog</Button>}
            />
        );
    }

    const rows = lessons ?? [];
    const percent = modulePercent(module.id);
    const completedCount = rows.filter((lesson) => lessonStatus(lesson.id) === 'completed').length;

    // The next unfinished lesson is the one the big CTA jumps to.
    const nextLesson = rows.find((lesson) => lessonStatus(lesson.id) !== 'completed') ?? rows[0];

    return (
        <>
            <PageHeader
                backTo="/app/learning"
                backLabel="Semua modul"
                title={module.title}
                description={module.description}
                badge={
                    <span className="flex gap-1.5">
                        <CefrBadge level={module.cefr} />
                        <DifficultyBadge level={module.level} />
                    </span>
                }
                action={
                    nextLesson && (
                        <Button
                            to={`/app/learning/lesson/${nextLesson.id}`}
                            icon={<PlayCircle className="size-4" />}
                        >
                            {percent === 0 ? 'Mulai belajar' : percent === 100 ? 'Ulangi materi' : 'Lanjutkan'}
                        </Button>
                    )
                }
            />

            <div className="grid gap-6 lg:grid-cols-3">
                <div className="lg:col-span-2">
                    <Card>
                        <CardHeader
                            title="Daftar materi"
                            subtitle={`${completedCount} dari ${rows.length} materi selesai`}
                        />

                        {rows.length === 0 ? (
                            <EmptyState title="Belum ada materi" description="Materi untuk modul ini sedang disiapkan." />
                        ) : (
                            <div className="space-y-2.5">
                                {rows.map((lesson, index) => {
                                    const status = lessonStatus(lesson.id);

                                    // A lesson unlocks once the one before it is
                                    // done — the first is always open.
                                    const previousDone =
                                        index === 0 || lessonStatus(rows[index - 1].id) === 'completed';

                                    return (
                                        <LessonRow
                                            key={lesson.id}
                                            lesson={lesson}
                                            index={index}
                                            status={status}
                                            locked={!previousDone && status === 'not-started'}
                                        />
                                    );
                                })}
                            </div>
                        )}
                    </Card>
                </div>

                <div className="space-y-6">
                    <Card>
                        <CardHeader title="Progres modul" />
                        <ProgressBar value={percent} tone={percent === 100 ? 'success' : 'secondary'} showValue size="lg" />

                        <dl className="mt-5 space-y-3 text-sm">
                            <div className="flex justify-between">
                                <dt className="text-fg-muted">Materi</dt>
                                <dd className="font-semibold">
                                    {completedCount}/{rows.length}
                                </dd>
                            </div>
                            <div className="flex justify-between">
                                <dt className="text-fg-muted">Estimasi waktu</dt>
                                <dd className="font-semibold">{Math.round(module.durationMinutes / 60)} jam</dd>
                            </div>
                            <div className="flex justify-between">
                                <dt className="text-fg-muted">Total XP</dt>
                                <dd className="font-semibold">
                                    {rows.reduce((total, lesson) => total + lesson.xpReward, 0)} XP
                                </dd>
                            </div>
                            <div className="flex justify-between">
                                <dt className="text-fg-muted">Level</dt>
                                <dd>
                                    <CefrBadge level={module.cefr} />
                                </dd>
                            </div>
                        </dl>
                    </Card>

                    <Card>
                        <CardHeader title="Latihan terkait" />
                        <div className="space-y-2">
                            <Button to="/app/quiz" variant="outline" fullWidth>
                                Kerjakan kuis modul
                            </Button>
                            <Button to="/app/flashcard" variant="outline" fullWidth>
                                Latihan flashcard
                            </Button>
                        </div>
                    </Card>
                </div>
            </div>
        </>
    );
}

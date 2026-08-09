import { useEffect, useState } from 'react';
import { Link, useParams } from 'react-router-dom';
import { BookOpen, CheckCircle2, Circle, Clock, Lock, PlayCircle, Zap } from 'lucide-react';
import { useAsync } from '@/hooks/useAsync';
import { learningService } from '@/services/api';
import { useProgressStore } from '@/store/progressStore';
import { cn } from '@/utils/cn';
import type { Lesson } from '@/types';
import { Card, CardHeader } from '@/components/ui/Card';
import { Button } from '@/components/ui/Button';
import { ProgressBar } from '@/components/ui/Progress';
import { Pagination } from '@/components/ui/Pagination';
import { EmptyState, PageLoader } from '@/components/ui/Feedback';
import { PageHeader } from '@/components/feature/shared/PageHeader';

const LESSONS_PER_PAGE = 10;

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
                <p className="truncate font-semibold">{lesson.title}</p>
                {lesson.summary && <p className="mt-0.5 truncate text-sm text-fg-muted">{lesson.summary}</p>}
            </div>

            <div className="hidden shrink-0 items-center gap-4 text-xs text-fg-muted sm:flex">
                <span className="flex items-center gap-1">
                    <Clock className="size-3.5" />
                    {lesson.estimated_minutes}m
                </span>
                <span className="flex items-center gap-1">
                    <Zap className="size-3.5" />+{lesson.xp_reward}
                </span>
            </div>
        </div>
    );

    return locked ? (
        <div title="Selesaikan materi sebelumnya untuk membukanya">{body}</div>
    ) : (
        <Link to={`/app/learning/lesson/${lesson.slug}`}>{body}</Link>
    );
}

export default function LearningModulePage() {
    const { moduleId: moduleSlug = '' } = useParams();

    const { data: module, loading } = useAsync(() => learningService.getModule(moduleSlug), [moduleSlug]);
    const { data: lessons } = useAsync(() => learningService.listLessons(moduleSlug), [moduleSlug]);

    const { lessonStatus, modulePercent } = useProgressStore();

    const [page, setPage] = useState(1);

    // A different module means a different (shorter or longer) lesson list — start over at page 1.
    useEffect(() => setPage(1), [moduleSlug]);

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
    const percent = modulePercent(String(module.id));
    const completedCount = rows.filter((lesson) => lessonStatus(lesson.slug) === 'completed').length;

    // The next unfinished lesson is the one the big CTA jumps to.
    const nextLesson = rows.find((lesson) => lessonStatus(lesson.slug) !== 'completed') ?? rows[0];

    const totalPages = Math.max(1, Math.ceil(rows.length / LESSONS_PER_PAGE));
    const pageStart = (page - 1) * LESSONS_PER_PAGE;
    // Keep each row's index relative to the full list (not the page) — the
    // sequential unlock check below compares a lesson against the one right
    // before it in `rows`, which breaks if `index` resets every page.
    const pageRows = rows.slice(pageStart, pageStart + LESSONS_PER_PAGE).map((lesson, i) => ({
        lesson,
        index: pageStart + i,
    }));

    return (
        <>
            <PageHeader
                backTo="/app/learning"
                backLabel="Semua modul"
                title={module.name}
                description={module.description ?? undefined}
                action={
                    nextLesson && (
                        <Button
                            to={`/app/learning/lesson/${nextLesson.slug}`}
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
                            <>
                                <div className="space-y-2.5">
                                    {pageRows.map(({ lesson, index }) => {
                                        const status = lessonStatus(lesson.slug);

                                        // A lesson unlocks once the one before it is
                                        // done — the first is always open.
                                        const previousDone =
                                            index === 0 || lessonStatus(rows[index - 1].slug) === 'completed';

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

                                {totalPages > 1 && (
                                    <div className="mt-5 flex flex-col items-center gap-2 border-t border-[var(--surface-border)] pt-4">
                                        <p className="text-xs text-fg-muted">
                                            {pageStart + 1}–{Math.min(pageStart + LESSONS_PER_PAGE, rows.length)} dari{' '}
                                            {rows.length} materi
                                        </p>
                                        <Pagination page={page} totalPages={totalPages} onChange={setPage} />
                                    </div>
                                )}
                            </>
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
                                <dt className="text-fg-muted">Total XP</dt>
                                <dd className="font-semibold">
                                    {rows.reduce((total, lesson) => total + lesson.xp_reward, 0)} XP
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

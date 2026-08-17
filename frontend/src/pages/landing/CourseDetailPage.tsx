import { useState } from 'react';
import { useParams } from 'react-router-dom';
import { ArrowRight, BookOpen, CheckCircle2, Clock, GraduationCap, Layers, Wallet } from 'lucide-react';
import { useAsync } from '@/hooks/useAsync';
import { catalogService } from '@/services/api';
import { useAuthStore } from '@/store/authStore';
import { useProgressStore } from '@/store/progressStore';
import { formatCurrency } from '@/utils/format';
import { Button } from '@/components/ui/Button';
import { Card } from '@/components/ui/Card';
import { CefrBadge } from '@/components/ui/Badge';
import { EmptyState, PageLoader } from '@/components/ui/Feedback';
import { CourseCheckoutModal } from '@/components/feature/learning/CourseCheckoutModal';

/** Public syllabus page — no lesson content is exposed, only titles/levels/summaries (see `PublicCatalogController::module()`). */
export default function CourseDetailPage() {
    const { moduleSlug = '' } = useParams();
    const { data, loading } = useAsync(() => catalogService.getModule(moduleSlug), [moduleSlug]);
    const user = useAuthStore((state) => state.user);
    const markEnrolled = useProgressStore((state) => state.markEnrolled);
    const [checkoutOpen, setCheckoutOpen] = useState(false);

    if (loading) return <PageLoader />;

    if (!data) {
        return (
            <div className="mx-auto max-w-3xl px-4 py-16 lg:px-6">
                <EmptyState
                    icon={<BookOpen className="size-6" />}
                    title="Kursus tidak ditemukan"
                    description="Kursus ini mungkin sudah tidak tersedia."
                    action={<Button to="/#kursus">Kembali ke katalog</Button>}
                />
            </div>
        );
    }

    const { module, lessons } = data;

    return (
        <div className="mx-auto max-w-3xl px-4 py-12 lg:px-6">
            <div className="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <span className="inline-flex items-center gap-1.5 rounded-full bg-surface-sunken px-3 py-1 text-xs font-medium text-fg-muted">
                        <GraduationCap className="size-3.5" />
                        {module.language ? `${module.language.flag} ${module.language.name}` : 'Kursus'}
                    </span>
                    <h1 className="mt-3 font-display text-3xl font-extrabold tracking-tight">{module.name}</h1>
                    <p className="mt-2 max-w-xl text-fg-muted">{module.description}</p>
                </div>
            </div>

            <div className="mt-6 flex flex-wrap gap-4 text-sm text-fg-muted">
                <span className="flex items-center gap-1.5">
                    <Layers className="size-4" />
                    {lessons.length} materi
                </span>
            </div>

            <div className="mt-8">
                <h2 className="mb-3 font-display text-lg font-bold">Silabus</h2>

                {lessons.length === 0 ? (
                    <EmptyState title="Silabus belum tersedia" description="Materi untuk kursus ini sedang disiapkan." />
                ) : (
                    <ol className="space-y-2.5">
                        {lessons.map((lesson, index) => (
                            <li key={lesson.id}>
                                <Card className="flex items-center gap-4">
                                    <span className="grid size-8 shrink-0 place-items-center rounded-full bg-primary-100 text-sm font-bold text-primary dark:bg-primary/20">
                                        {index + 1}
                                    </span>
                                    <div className="min-w-0 flex-1">
                                        <p className="truncate font-semibold">{lesson.title}</p>
                                        {lesson.summary && <p className="truncate text-sm text-fg-muted">{lesson.summary}</p>}
                                    </div>
                                    <div className="flex shrink-0 items-center gap-3">
                                        {lesson.level && <CefrBadge level={lesson.level} />}
                                        <span className="flex items-center gap-1 text-xs text-fg-muted">
                                            <Clock className="size-3.5" />
                                            {lesson.estimated_minutes}m
                                        </span>
                                    </div>
                                </Card>
                            </li>
                        ))}
                    </ol>
                )}
            </div>

            <Card className="mt-8 bg-primary-50 dark:bg-primary/10">
                <div className="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <p className="flex items-center gap-2 font-display font-bold">
                            {module.is_paid ? (
                                <>
                                    <Wallet className="size-4 text-primary" />
                                    {formatCurrency(module.price ?? 0)}
                                </>
                            ) : (
                                <>
                                    <CheckCircle2 className="size-4 text-success" />
                                    Gratis, tanpa kartu kredit
                                </>
                            )}
                        </p>
                        <p className="mt-1 text-sm text-fg-muted">
                            {user
                                ? 'Ambil kursus ini dan mulai belajar.'
                                : 'Daftar untuk mengambil kursus ini dan mulai belajar.'}
                        </p>
                    </div>
                    {user ? (
                        <Button size="lg" iconRight={<ArrowRight className="size-4" />} onClick={() => setCheckoutOpen(true)}>
                            Ambil Kursus
                        </Button>
                    ) : (
                        <Button to="/register" size="lg" iconRight={<ArrowRight className="size-4" />}>
                            Daftar & Mulai
                        </Button>
                    )}
                </div>
            </Card>

            <CourseCheckoutModal
                module={module}
                open={checkoutOpen}
                onClose={() => setCheckoutOpen(false)}
                onEnrolled={() => {
                    markEnrolled(module.slug);
                    setCheckoutOpen(false);
                }}
            />
        </div>
    );
}

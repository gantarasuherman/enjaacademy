import { GraduationCap } from 'lucide-react';
import { useAsync } from '@/hooks/useAsync';
import { learningService } from '@/services/api';
import { useProgressStore } from '@/store/progressStore';
import { ModuleCard } from './LearningListPage';
import { EmptyState, Skeleton } from '@/components/ui/Feedback';
import { Button } from '@/components/ui/Button';
import { PageHeader } from '@/components/feature/shared/PageHeader';

export default function MyCoursesPage() {
    const { data: modules, loading } = useAsync(() => learningService.listModules(), []);
    const modulePercent = useProgressStore((state) => state.modulePercent);
    const isEnrolled = useProgressStore((state) => state.isEnrolled);
    const isActiveModule = useProgressStore((state) => state.isActiveModule);
    const toggleEnrollment = useProgressStore((state) => state.toggleEnrollment);
    const setActiveModule = useProgressStore((state) => state.setActiveModule);

    const myCourses = (modules ?? []).filter((m) => isEnrolled(m.slug));

    return (
        <>
            <PageHeader
                title="Kursus Saya"
                description="Kursus yang sedang kamu ambil. Kursus aktif menentukan bahasa soal Kuis Harian."
                action={
                    <Button to="/app/learning" variant="outline" icon={<GraduationCap className="size-4" />}>
                        Jelajahi kursus lain
                    </Button>
                }
            />

            {loading ? (
                <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    {Array.from({ length: 3 }, (_, i) => (
                        <Skeleton key={i} className="h-64 w-full" />
                    ))}
                </div>
            ) : myCourses.length === 0 ? (
                <EmptyState
                    icon={<GraduationCap className="size-6" />}
                    title="Belum ada kursus yang diambil"
                    description="Jelajahi katalog kursus dan ambil kursus pertamamu untuk memulai."
                    action={
                        <Button to="/app/learning" icon={<GraduationCap className="size-4" />}>
                            Jelajahi Kursus
                        </Button>
                    }
                />
            ) : (
                <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    {myCourses.map((module) => (
                        <ModuleCard
                            key={module.id}
                            module={module}
                            percent={modulePercent(String(module.id))}
                            enrolled
                            onToggleEnroll={toggleEnrollment}
                            isActive={isActiveModule(String(module.id))}
                            onSetActive={(slug) => setActiveModule(slug, String(module.id))}
                        />
                    ))}
                </div>
            )}
        </>
    );
}

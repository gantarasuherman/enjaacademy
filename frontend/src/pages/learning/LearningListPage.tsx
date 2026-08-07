import { useState } from 'react';
import { Link } from 'react-router-dom';
import { ArrowRight, CheckCircle2, Clock, Layers } from 'lucide-react';
import { useAsync } from '@/hooks/useAsync';
import { learningService } from '@/services/api';
import { useProgressStore } from '@/store/progressStore';
import type { LearningModule, SkillType } from '@/types';
import { Card } from '@/components/ui/Card';
import { Chip, CefrBadge, DifficultyBadge } from '@/components/ui/Badge';
import { ProgressBar } from '@/components/ui/Progress';
import { EmptyState, Skeleton } from '@/components/ui/Feedback';
import { DynamicIcon } from '@/components/ui/DynamicIcon';
import { PageHeader } from '@/components/feature/shared/PageHeader';

const FILTERS: { id: SkillType | 'all'; label: string }[] = [
    { id: 'all', label: 'Semua' },
    { id: 'vocabulary', label: 'Vocabulary' },
    { id: 'grammar', label: 'Grammar' },
    { id: 'listening', label: 'Listening' },
    { id: 'speaking', label: 'Speaking' },
    { id: 'reading', label: 'Reading' },
    { id: 'writing', label: 'Writing' },
    { id: 'conversation', label: 'Conversation' },
];

const LEVELS = [
    { id: 'all', label: 'Semua level' },
    { id: 'beginner', label: 'Pemula' },
    { id: 'intermediate', label: 'Menengah' },
    { id: 'advanced', label: 'Mahir' },
];

const COLOR_CLASSES: Record<string, string> = {
    primary: 'bg-primary-100 text-primary dark:bg-primary/20 dark:text-primary-300',
    secondary: 'bg-secondary-100 text-secondary-700 dark:bg-secondary/20 dark:text-secondary-300',
    success: 'bg-success/12 text-emerald-600 dark:text-emerald-400',
    info: 'bg-info/12 text-sky-600 dark:text-sky-400',
};

export function ModuleCard({ module, percent }: { module: LearningModule; percent: number }) {
    const done = percent === 100;

    return (
        <Link to={`/app/learning/${module.id}`} className="block h-full">
            <Card interactive className="flex h-full flex-col">
                <div className="flex items-start justify-between gap-3">
                    <span
                        className={`grid size-11 shrink-0 place-items-center rounded-sm ${
                            COLOR_CLASSES[module.color] ?? COLOR_CLASSES.primary
                        }`}
                    >
                        <DynamicIcon name={module.icon} className="size-5" />
                    </span>

                    <div className="flex flex-wrap justify-end gap-1.5">
                        <CefrBadge level={module.cefr} />
                        <DifficultyBadge level={module.level} />
                    </div>
                </div>

                <h3 className="mt-4 font-display text-base font-bold">{module.title}</h3>
                <p className="mt-1 line-clamp-2 flex-1 text-sm text-fg-muted">{module.description}</p>

                <div className="mt-4 flex items-center gap-4 text-xs text-fg-muted">
                    <span className="flex items-center gap-1">
                        <Layers className="size-3.5" />
                        {module.lessonIds.length} materi
                    </span>
                    <span className="flex items-center gap-1">
                        <Clock className="size-3.5" />
                        {Math.round(module.durationMinutes / 60)} jam
                    </span>
                </div>

                <div className="mt-4">
                    <div className="mb-1.5 flex items-center justify-between text-xs">
                        <span className="font-medium text-fg-muted">
                            {done ? (
                                <span className="flex items-center gap-1 text-success">
                                    <CheckCircle2 className="size-3.5" /> Selesai
                                </span>
                            ) : percent > 0 ? (
                                'Sedang berjalan'
                            ) : (
                                'Belum dimulai'
                            )}
                        </span>
                        <span className="font-mono font-semibold">{percent}%</span>
                    </div>
                    <ProgressBar value={percent} size="sm" tone={done ? 'success' : 'secondary'} />
                </div>
            </Card>
        </Link>
    );
}

export default function LearningListPage() {
    const [skill, setSkill] = useState<SkillType | 'all'>('all');
    const [level, setLevel] = useState('all');

    const { data: modules, loading } = useAsync(() => learningService.listModules(), []);
    const modulePercent = useProgressStore((state) => state.modulePercent);

    const filtered = (modules ?? [])
        .filter((module) => skill === 'all' || module.category === skill)
        .filter((module) => level === 'all' || module.level === level);

    const completed = (modules ?? []).filter((m) => modulePercent(m.id) === 100).length;

    return (
        <>
            <PageHeader
                title="Learning Path"
                description="Jalur belajar terstruktur dari pemula sampai mahir. Selesaikan satu modul sebelum melangkah ke berikutnya."
                action={
                    <div className="rounded-sm bg-surface-sunken px-4 py-2 text-center">
                        <p className="font-display text-lg font-extrabold">
                            {completed}/{modules?.length ?? 0}
                        </p>
                        <p className="text-[11px] text-fg-muted">modul selesai</p>
                    </div>
                }
            />

            <div className="mb-5 space-y-3">
                <div className="flex flex-wrap gap-2">
                    {FILTERS.map((filter) => (
                        <Chip key={filter.id} active={skill === filter.id} onClick={() => setSkill(filter.id)}>
                            {filter.label}
                        </Chip>
                    ))}
                </div>

                <div className="flex flex-wrap gap-2">
                    {LEVELS.map((item) => (
                        <Chip key={item.id} active={level === item.id} onClick={() => setLevel(item.id)}>
                            {item.label}
                        </Chip>
                    ))}
                </div>
            </div>

            {loading ? (
                <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    {Array.from({ length: 6 }, (_, i) => (
                        <Skeleton key={i} className="h-56 w-full" />
                    ))}
                </div>
            ) : filtered.length === 0 ? (
                <EmptyState
                    icon={<Layers className="size-6" />}
                    title="Tidak ada modul yang cocok"
                    description="Coba longgarkan filter skill atau level."
                />
            ) : (
                <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    {filtered.map((module) => (
                        <ModuleCard key={module.id} module={module} percent={modulePercent(module.id)} />
                    ))}
                </div>
            )}

            <Card className="mt-8 flex flex-wrap items-center justify-between gap-4 bg-primary-50 dark:bg-primary/10">
                <div>
                    <p className="font-display font-bold">Belum yakin mulai dari mana?</p>
                    <p className="mt-0.5 text-sm text-fg-muted">
                        Mulai dari Core Vocabulary — fondasi yang dipakai semua modul lain.
                    </p>
                </div>
                <Link
                    to="/app/learning/mod-vocab-core"
                    className="inline-flex items-center gap-1.5 text-sm font-semibold text-primary hover:underline"
                >
                    Mulai dari sini <ArrowRight className="size-4" />
                </Link>
            </Card>
        </>
    );
}

import { useState } from 'react';
import { Link } from 'react-router-dom';
import { ArrowRight, CheckCircle2, GraduationCap, Layers } from 'lucide-react';
import { useAsync } from '@/hooks/useAsync';
import { learningService } from '@/services/api';
import { useProgressStore } from '@/store/progressStore';
import type { LearningModule, SkillType } from '@/types';
import { Card } from '@/components/ui/Card';
import { Chip } from '@/components/ui/Badge';
import { ProgressBar } from '@/components/ui/Progress';
import { Alert, EmptyState, Skeleton } from '@/components/ui/Feedback';
import { DynamicIcon } from '@/components/ui/DynamicIcon';
import { PageHeader } from '@/components/feature/shared/PageHeader';

const FILTERS: { id: SkillType | 'kana' | 'kanji' | 'exam' | 'all'; label: string }[] = [
    { id: 'all', label: 'Semua' },
    { id: 'kana', label: 'Kana' },
    { id: 'kanji', label: 'Kanji' },
    { id: 'vocabulary', label: 'Vocabulary' },
    { id: 'grammar', label: 'Grammar' },
    { id: 'listening', label: 'Listening' },
    { id: 'speaking', label: 'Speaking' },
    { id: 'reading', label: 'Reading' },
    { id: 'writing', label: 'Writing' },
    { id: 'conversation', label: 'Conversation' },
    { id: 'exam', label: 'JLPT' },
];

const LANGUAGES: { id: string; label: string }[] = [
    { id: 'all', label: 'Semua Bahasa' },
    { id: 'japanese', label: '🇯🇵 Bahasa Jepang' },
    { id: 'english', label: '🇬🇧 Bahasa Inggris' },
];

const COLOR_CLASSES: Record<string, string> = {
    primary: 'bg-primary-100 text-primary dark:bg-primary/20 dark:text-primary-300',
    secondary: 'bg-secondary-100 text-secondary-700 dark:bg-secondary/20 dark:text-secondary-300',
    success: 'bg-success/12 text-emerald-600 dark:text-emerald-400',
    info: 'bg-info/12 text-sky-600 dark:text-sky-400',
};

export function ModuleCard({
    module,
    percent,
    enrolled,
    onToggleEnroll,
}: {
    module: LearningModule;
    percent: number;
    enrolled: boolean;
    onToggleEnroll: (moduleSlug: string) => void;
}) {
    const done = percent === 100;

    return (
        <Link to={`/app/learning/${module.slug}`} className="block h-full">
            <Card interactive className="flex h-full flex-col">
                <div className="flex items-start justify-between gap-3">
                    <span
                        className={`grid size-11 shrink-0 place-items-center rounded-sm ${
                            COLOR_CLASSES[module.color] ?? COLOR_CLASSES.primary
                        }`}
                    >
                        <DynamicIcon name={module.icon} className="size-5" />
                    </span>

                    {module.language && (
                        <span className="rounded-full bg-surface-sunken px-2.5 py-1 text-[11px] font-medium text-fg-muted">
                            {module.language.flag} {module.language.name}
                        </span>
                    )}
                </div>

                <h3 className="mt-4 font-display text-base font-bold">{module.name}</h3>
                <p className="mt-1 line-clamp-2 flex-1 text-sm text-fg-muted">{module.description}</p>

                <div className="mt-4 flex items-center gap-4 text-xs text-fg-muted">
                    <span className="flex items-center gap-1">
                        <Layers className="size-3.5" />
                        {module.lessons_count ?? 0} materi
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

                <button
                    type="button"
                    onClick={(e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        onToggleEnroll(module.slug);
                    }}
                    className={`mt-3 flex items-center justify-center gap-1.5 rounded-sm px-3 py-2 text-xs font-semibold transition ${
                        enrolled
                            ? 'bg-success/12 text-emerald-600 dark:text-emerald-400'
                            : 'bg-primary text-white hover:opacity-90'
                    }`}
                >
                    {enrolled ? (
                        <>
                            <CheckCircle2 className="size-3.5" /> Kelas Saya
                        </>
                    ) : (
                        <>
                            <GraduationCap className="size-3.5" /> Ambil Kelas
                        </>
                    )}
                </button>
            </Card>
        </Link>
    );
}

export default function LearningListPage() {
    const [skill, setSkill] = useState<SkillType | 'kana' | 'kanji' | 'exam' | 'all'>('all');
    const [lang, setLang] = useState<string>('all');

    const { data: modules, loading, error } = useAsync(() => learningService.listModules(), []);
    const modulePercent = useProgressStore((state) => state.modulePercent);
    const isEnrolled = useProgressStore((state) => state.isEnrolled);
    const toggleEnrollment = useProgressStore((state) => state.toggleEnrollment);

    const filtered = (modules ?? [])
        .filter((module) => skill === 'all' || module.content_type === skill)
        .filter((module) => lang === 'all' || module.language?.slug === lang);

    const myClasses = filtered.filter((m) => isEnrolled(m.slug));
    const otherClasses = filtered.filter((m) => !isEnrolled(m.slug));

    const completed = (modules ?? []).filter((m) => modulePercent(String(m.id)) === 100).length;

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

            <div className="mb-3 flex flex-wrap gap-2">
                {LANGUAGES.map((item) => (
                    <Chip key={item.id} active={lang === item.id} onClick={() => setLang(item.id)}>
                        {item.label}
                    </Chip>
                ))}
            </div>

            {error && (
                <Alert tone="danger" className="mb-5">
                    Gagal memuat modul: {error}. Coba muat ulang halaman — jika masih gagal, sesi login mungkin
                    perlu di-refresh (keluar lalu masuk lagi).
                </Alert>
            )}

            <div className="mb-5 flex flex-wrap gap-2">
                {FILTERS.map((filter) => (
                    <Chip key={filter.id} active={skill === filter.id} onClick={() => setSkill(filter.id)}>
                        {filter.label}
                    </Chip>
                ))}
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
                    description="Coba longgarkan filter skill."
                />
            ) : (
                <div className="space-y-8">
                    {myClasses.length > 0 && (
                        <div>
                            <h2 className="mb-3 font-display text-sm font-bold uppercase tracking-wide text-fg-muted">
                                Kelas Saya ({myClasses.length})
                            </h2>
                            <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                {myClasses.map((module) => (
                                    <ModuleCard
                                        key={module.id}
                                        module={module}
                                        percent={modulePercent(String(module.id))}
                                        enrolled
                                        onToggleEnroll={toggleEnrollment}
                                    />
                                ))}
                            </div>
                        </div>
                    )}

                    {otherClasses.length > 0 && (
                        <div>
                            <h2 className="mb-3 font-display text-sm font-bold uppercase tracking-wide text-fg-muted">
                                {myClasses.length > 0 ? 'Penawaran Kelas Lainnya' : 'Semua Modul'} ({otherClasses.length})
                            </h2>
                            <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                {otherClasses.map((module) => (
                                    <ModuleCard
                                        key={module.id}
                                        module={module}
                                        percent={modulePercent(String(module.id))}
                                        enrolled={false}
                                        onToggleEnroll={toggleEnrollment}
                                    />
                                ))}
                            </div>
                        </div>
                    )}
                </div>
            )}

            <Card className="mt-8 flex flex-wrap items-center justify-between gap-4 bg-primary-50 dark:bg-primary/10">
                <div>
                    <p className="font-display font-bold">Belum yakin mulai dari mana?</p>
                    <p className="mt-0.5 text-sm text-fg-muted">
                        Baru di Bahasa Jepang? Mulai dari Hiragana — fondasi yang dipakai semua modul Jepang lain.
                    </p>
                </div>
                <Link
                    to="/app/learning/hiragana"
                    className="inline-flex items-center gap-1.5 text-sm font-semibold text-primary hover:underline"
                >
                    Mulai dari sini <ArrowRight className="size-4" />
                </Link>
            </Card>
        </>
    );
}

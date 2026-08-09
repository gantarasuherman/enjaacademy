import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { ArrowRight, Clock, Sigma, Waypoints } from 'lucide-react';
import { useAsync } from '@/hooks/useAsync';
import { grammarService } from '@/services/api';
import type { LanguageSlug, Tense } from '@/types';
import { Card, CardHeader } from '@/components/ui/Card';
import { Badge, CefrBadge, Chip } from '@/components/ui/Badge';
import { Tabs, TabPanel } from '@/components/ui/Tabs';
import { Pagination } from '@/components/ui/Pagination';
import { EmptyState, Skeleton } from '@/components/ui/Feedback';
import { PageHeader } from '@/components/feature/shared/PageHeader';

const PER_PAGE = 10;

/** Slices `list` to the current page and clamps `page` back in range if the list shrank (e.g. a filter changed). */
function usePage<T>(list: T[]): { page: number; setPage: (p: number) => void; pageRows: T[]; totalPages: number } {
    const [page, setPage] = useState(1);
    const totalPages = Math.max(1, Math.ceil(list.length / PER_PAGE));
    const safePage = Math.min(page, totalPages);
    const start = (safePage - 1) * PER_PAGE;

    useEffect(() => {
        if (page > totalPages) setPage(totalPages);
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [totalPages]);

    return { page: safePage, setPage, pageRows: list.slice(start, start + PER_PAGE), totalPages };
}

const GROUP_LABEL: Record<Tense['group'], string> = {
    present: 'Present',
    past: 'Past',
    future: 'Future',
    perfect: 'Perfect',
};

const GROUP_TONE: Record<Tense['group'], 'success' | 'info' | 'secondary' | 'primary'> = {
    present: 'success',
    past: 'info',
    future: 'secondary',
    perfect: 'primary',
};

const HAS_TENSES = import.meta.env.VITE_DATA_SOURCE !== 'api';

const LANGUAGES: { id: LanguageSlug | 'all'; label: string }[] = [
    { id: 'all', label: 'Semua Bahasa' },
    { id: 'japanese', label: '🇯🇵 Bahasa Jepang' },
    { id: 'english', label: '🇬🇧 Bahasa Inggris' },
];

export default function GrammarPage() {
    const [tab, setTab] = useState('tenses');
    const [group, setGroup] = useState<string>('all');
    const [lang, setLang] = useState<LanguageSlug | 'all'>('all');

    const { data: allTopics, loading: topicsLoading } = useAsync(() => grammarService.listTopics(), []);
    const { data: tenses, loading: tensesLoading } = useAsync(() => grammarService.listTenses(), []);

    const topics = (allTopics ?? []).filter((topic) => lang === 'all' || topic.language === lang);
    const partsOfSpeech = topics.filter((topic) => topic.kind === 'parts-of-speech');
    const structures = topics.filter((topic) => topic.kind === 'structure');
    // The dedicated `tenses`/`Tense[]` system below (`HAS_TENSES`) has no
    // backend at all and hides itself in `api` mode — this is a second,
    // parallel way to reach tense content that *does* have a real backend
    // (an ordinary `GrammarTopic` with `kind: 'tense'`), so tenses stay
    // reachable in both modes.
    const tenseTopics = topics.filter((topic) => topic.kind === 'tense');

    const filteredTenses = (tenses ?? []).filter((tense) => group === 'all' || tense.group === group);

    const tenseTopicsPage = usePage(tenseTopics);
    const tensesPage = usePage(filteredTenses);
    const partsPage = usePage(partsOfSpeech);
    const structuresPage = usePage(structures);

    return (
        <>
            <PageHeader
                title="Grammar"
                description="Fondasi tata bahasa: parts of speech, delapan tense inti, dan struktur kalimat tingkat menengah — semuanya dengan rumus, contoh, dan kesalahan umum."
            />

            <div className="mb-4 flex flex-wrap gap-2">
                {LANGUAGES.map((item) => (
                    <Chip key={item.id} active={lang === item.id} onClick={() => setLang(item.id)}>
                        {item.label}
                    </Chip>
                ))}
            </div>

            <Tabs
                items={[
                    {
                        id: 'tenses',
                        label: 'Tenses',
                        icon: <Clock className="size-4" />,
                        count: HAS_TENSES ? tenses?.length : tenseTopics.length,
                    },
                    { id: 'parts', label: 'Parts of Speech', icon: <Sigma className="size-4" />, count: partsOfSpeech.length },
                    { id: 'structures', label: 'Struktur', icon: <Waypoints className="size-4" />, count: structures.length },
                ]}
                active={tab}
                onChange={setTab}
                className="mb-6"
            />

            {/* ------------------------------------------------------- Tenses */}
            {!HAS_TENSES && (
                <TabPanel id="tenses" active={tab}>
                    {topicsLoading ? (
                        <div className="grid gap-4 sm:grid-cols-2">
                            {Array.from({ length: 4 }, (_, i) => (
                                <Skeleton key={i} className="h-40 w-full" />
                            ))}
                        </div>
                    ) : tenseTopics.length === 0 ? (
                        <EmptyState title="Belum ada materi tense" />
                    ) : (
                        <>
                            <div className="grid gap-4 sm:grid-cols-2">
                                {tenseTopicsPage.pageRows.map((topic) => (
                                    <Link key={topic.id} to={`/app/grammar/${topic.id}`} className="block h-full">
                                        <Card interactive className="flex h-full flex-col">
                                            <div className="flex items-start justify-between gap-3">
                                                <h3 className="font-display text-base font-bold">{topic.title}</h3>
                                                <CefrBadge level={topic.cefr} />
                                            </div>
                                            <p className="mt-2 line-clamp-3 flex-1 text-sm text-fg-muted">{topic.explanation}</p>
                                            {topic.formula && (
                                                <div className="mt-3 rounded-sm bg-surface-sunken p-3">
                                                    <p className="font-mono text-[11px] leading-relaxed">{topic.formula}</p>
                                                </div>
                                            )}
                                        </Card>
                                    </Link>
                                ))}
                            </div>
                            <Pagination
                                page={tenseTopicsPage.page}
                                totalPages={tenseTopicsPage.totalPages}
                                onChange={tenseTopicsPage.setPage}
                                className="mt-6"
                            />
                        </>
                    )}
                </TabPanel>
            )}
            {HAS_TENSES && (
            <TabPanel id="tenses" active={tab}>
                <div className="mb-5 flex flex-wrap gap-2">
                    <Chip active={group === 'all'} onClick={() => setGroup('all')}>
                        Semua
                    </Chip>
                    {(['present', 'past', 'future', 'perfect'] as const).map((item) => (
                        <Chip key={item} active={group === item} onClick={() => setGroup(item)}>
                            {GROUP_LABEL[item]}
                        </Chip>
                    ))}
                </div>

                {tensesLoading ? (
                    <div className="grid gap-4 sm:grid-cols-2">
                        {Array.from({ length: 4 }, (_, i) => (
                            <Skeleton key={i} className="h-48 w-full" />
                        ))}
                    </div>
                ) : filteredTenses.length === 0 ? (
                    <EmptyState title="Tidak ada tense di grup ini" />
                ) : (
                    <>
                    <div className="grid gap-4 sm:grid-cols-2">
                        {tensesPage.pageRows.map((tense) => (
                            <Link key={tense.id} to={`/app/grammar/tenses/${tense.id}`} className="block h-full">
                                <Card interactive className="flex h-full flex-col">
                                    <div className="flex items-start justify-between gap-3">
                                        <h3 className="font-display text-base font-bold">{tense.name}</h3>
                                        <Badge tone={GROUP_TONE[tense.group]}>{GROUP_LABEL[tense.group]}</Badge>
                                    </div>

                                    <div className="mt-3 rounded-sm bg-surface-sunken p-3">
                                        <p className="font-mono text-xs leading-relaxed">{tense.formula.positive}</p>
                                    </div>

                                    <p className="mt-3 flex-1 text-sm text-fg-muted">{tense.usage[0]}</p>

                                    <div className="mt-3 flex flex-wrap gap-1">
                                        {tense.signalWords.slice(0, 4).map((word) => (
                                            <span
                                                key={word}
                                                className="rounded-full bg-primary-100 px-2 py-0.5 text-[11px] font-medium text-primary-700 dark:bg-primary/20 dark:text-primary-300"
                                            >
                                                {word}
                                            </span>
                                        ))}
                                    </div>

                                    <span className="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-primary">
                                        Pelajari <ArrowRight className="size-3.5" />
                                    </span>
                                </Card>
                            </Link>
                        ))}
                    </div>
                    <Pagination page={tensesPage.page} totalPages={tensesPage.totalPages} onChange={tensesPage.setPage} className="mt-6" />
                    </>
                )}
            </TabPanel>
            )}

            {/* ----------------------------------------------- Parts of speech */}
            <TabPanel id="parts" active={tab}>
                {topicsLoading ? (
                    <div className="space-y-3">
                        {Array.from({ length: 3 }, (_, i) => (
                            <Skeleton key={i} className="h-28 w-full" />
                        ))}
                    </div>
                ) : (
                    <>
                    <div className="grid gap-4 sm:grid-cols-2">
                        {partsPage.pageRows.map((topic) => (
                            <Link key={topic.id} to={`/app/grammar/${topic.id}`} className="block h-full">
                                <Card interactive className="flex h-full flex-col">
                                    <div className="flex items-start justify-between gap-3">
                                        <h3 className="font-display text-base font-bold">{topic.title}</h3>
                                        <CefrBadge level={topic.cefr} />
                                    </div>
                                    <p className="mt-2 line-clamp-3 flex-1 text-sm text-fg-muted">{topic.explanation}</p>
                                    <p className="mt-3 text-xs text-fg-muted">
                                        {topic.examples.length} contoh · {topic.commonMistakes.length} kesalahan umum
                                    </p>
                                </Card>
                            </Link>
                        ))}
                    </div>
                    <Pagination page={partsPage.page} totalPages={partsPage.totalPages} onChange={partsPage.setPage} className="mt-6" />
                    </>
                )}
            </TabPanel>

            {/* --------------------------------------------------- Structures */}
            <TabPanel id="structures" active={tab}>
                {topicsLoading ? (
                    <Skeleton className="h-40 w-full" />
                ) : (
                    <>
                    <div className="grid gap-4 sm:grid-cols-2">
                        {structuresPage.pageRows.map((topic) => (
                            <Link key={topic.id} to={`/app/grammar/${topic.id}`} className="block h-full">
                                <Card interactive className="flex h-full flex-col">
                                    <div className="flex items-start justify-between gap-3">
                                        <h3 className="font-display text-base font-bold">{topic.title}</h3>
                                        <CefrBadge level={topic.cefr} />
                                    </div>
                                    <p className="mt-2 line-clamp-3 flex-1 text-sm text-fg-muted">{topic.explanation}</p>
                                    {topic.formula && (
                                        <div className="mt-3 rounded-sm bg-surface-sunken p-3">
                                            <p className="font-mono text-[11px] leading-relaxed">{topic.formula}</p>
                                        </div>
                                    )}
                                </Card>
                            </Link>
                        ))}
                    </div>
                    <Pagination page={structuresPage.page} totalPages={structuresPage.totalPages} onChange={structuresPage.setPage} className="mt-6" />
                    </>
                )}
            </TabPanel>

            <Card className="mt-8">
                <CardHeader
                    title="Sudah paham teorinya?"
                    subtitle="Uji pemahamanmu dengan kuis grammar bertingkat."
                    action={
                        <Link
                            to="/app/quiz"
                            className="inline-flex items-center gap-1.5 text-sm font-semibold text-primary hover:underline"
                        >
                            Kerjakan kuis <ArrowRight className="size-4" />
                        </Link>
                    }
                />
            </Card>
        </>
    );
}

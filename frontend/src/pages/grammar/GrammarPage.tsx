import { useState } from 'react';
import { Link } from 'react-router-dom';
import { ArrowRight, Clock, Sigma, Waypoints } from 'lucide-react';
import { useAsync } from '@/hooks/useAsync';
import { grammarService } from '@/services/api';
import type { Tense } from '@/types';
import { Card, CardHeader } from '@/components/ui/Card';
import { Badge, CefrBadge, Chip } from '@/components/ui/Badge';
import { Tabs, TabPanel } from '@/components/ui/Tabs';
import { EmptyState, Skeleton } from '@/components/ui/Feedback';
import { PageHeader } from '@/components/feature/shared/PageHeader';

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

export default function GrammarPage() {
    const [tab, setTab] = useState('tenses');
    const [group, setGroup] = useState<string>('all');

    const { data: topics, loading: topicsLoading } = useAsync(() => grammarService.listTopics(), []);
    const { data: tenses, loading: tensesLoading } = useAsync(() => grammarService.listTenses(), []);

    const partsOfSpeech = (topics ?? []).filter((topic) => topic.kind === 'parts-of-speech');
    const structures = (topics ?? []).filter((topic) => topic.kind === 'structure');

    const filteredTenses = (tenses ?? []).filter((tense) => group === 'all' || tense.group === group);

    return (
        <>
            <PageHeader
                title="Grammar"
                description="Fondasi tata bahasa: parts of speech, delapan tense inti, dan struktur kalimat tingkat menengah — semuanya dengan rumus, contoh, dan kesalahan umum."
            />

            <Tabs
                items={[
                    { id: 'tenses', label: 'Tenses', icon: <Clock className="size-4" />, count: tenses?.length },
                    { id: 'parts', label: 'Parts of Speech', icon: <Sigma className="size-4" />, count: partsOfSpeech.length },
                    { id: 'structures', label: 'Struktur', icon: <Waypoints className="size-4" />, count: structures.length },
                ]}
                active={tab}
                onChange={setTab}
                className="mb-6"
            />

            {/* ------------------------------------------------------- Tenses */}
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
                    <div className="grid gap-4 sm:grid-cols-2">
                        {filteredTenses.map((tense) => (
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
                )}
            </TabPanel>

            {/* ----------------------------------------------- Parts of speech */}
            <TabPanel id="parts" active={tab}>
                {topicsLoading ? (
                    <div className="space-y-3">
                        {Array.from({ length: 3 }, (_, i) => (
                            <Skeleton key={i} className="h-28 w-full" />
                        ))}
                    </div>
                ) : (
                    <div className="grid gap-4 sm:grid-cols-2">
                        {partsOfSpeech.map((topic) => (
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
                )}
            </TabPanel>

            {/* --------------------------------------------------- Structures */}
            <TabPanel id="structures" active={tab}>
                {topicsLoading ? (
                    <Skeleton className="h-40 w-full" />
                ) : (
                    <div className="grid gap-4 sm:grid-cols-2">
                        {structures.map((topic) => (
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

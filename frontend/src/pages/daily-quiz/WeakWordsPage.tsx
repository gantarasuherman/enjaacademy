import { useEffect, useState } from 'react';
import { AlertTriangle, BookOpen, Flame, Search } from 'lucide-react';
import { vocabularyBankService, weakWordService } from '@/services/api';
import { useAsync } from '@/hooks/useAsync';
import { PageHeader } from '@/components/feature/shared/PageHeader';
import { Card } from '@/components/ui/Card';
import { CefrBadge, Chip } from '@/components/ui/Badge';
import { Badge } from '@/components/ui/Badge';
import { Input } from '@/components/ui/Input';
import { Pagination } from '@/components/ui/Pagination';
import { Tabs } from '@/components/ui/Tabs';
import { Alert, EmptyState, Skeleton } from '@/components/ui/Feedback';

type BankLanguage = 'english' | 'japanese';

const LEVELS_BY_LANGUAGE: Record<BankLanguage, string[]> = {
    english: ['Beginner', 'Elementary', 'Intermediate', 'Upper-Intermediate', 'Advanced'],
    japanese: ['N5', 'N4', 'N3', 'N2', 'N1'],
};

function WeakWordsTab() {
    const { data: words, loading, error } = useAsync(() => weakWordService.list());

    if (loading) {
        return (
            <div className="grid gap-3 sm:grid-cols-2">
                {Array.from({ length: 6 }, (_, i) => (
                    <Skeleton key={i} className="h-24 w-full" />
                ))}
            </div>
        );
    }

    if (error) {
        return (
            <Alert tone="danger" title="Gagal memuat kata lemah">
                {error}
            </Alert>
        );
    }

    if (!words || words.length === 0) {
        return (
            <EmptyState
                icon={<Flame className="size-6" />}
                title="Belum ada kata lemah"
                description="Kata yang kamu jawab salah di Kuis Harian akan muncul di sini sampai kamu menguasainya."
            />
        );
    }

    return (
        <div className="grid gap-3 sm:grid-cols-2">
            {words.map((w) => (
                <Card key={w.id} padded>
                    <div className="flex items-start justify-between gap-3">
                        <div className="min-w-0">
                            <p className="truncate font-display text-base font-bold">{w.word}</p>
                            <p className="mt-0.5 truncate text-sm text-fg-muted">{w.meaningId}</p>
                        </div>
                        <CefrBadge level={w.level} />
                    </div>
                    <div className="mt-3 flex flex-wrap items-center gap-2 text-xs text-fg-muted">
                        <Badge tone="danger" icon={<AlertTriangle className="size-3" />}>
                            {w.wrongCount}× salah
                        </Badge>
                        {w.correctStreak > 0 && (
                            <Badge tone="success" icon={<Flame className="size-3" />}>
                                {w.correctStreak}/3 benar berturut
                            </Badge>
                        )}
                    </div>
                </Card>
            ))}
        </div>
    );
}

function VocabularyBrowseTab() {
    const [language, setLanguage] = useState<BankLanguage>('english');
    const [level, setLevel] = useState<string | null>(null);
    const [search, setSearch] = useState('');
    const [debouncedSearch, setDebouncedSearch] = useState('');
    const [page, setPage] = useState(1);

    useEffect(() => {
        const t = setTimeout(() => setDebouncedSearch(search), 350);
        return () => clearTimeout(t);
    }, [search]);

    useEffect(() => {
        setPage(1);
    }, [language, level, debouncedSearch]);

    // Switching language invalidates any level picked from the other scale (CEFR vs JLPT).
    useEffect(() => setLevel(null), [language]);

    const { data, loading, error } = useAsync(
        () => vocabularyBankService.list({ language, level: level ?? undefined, search: debouncedSearch || undefined, page }),
        [language, level, debouncedSearch, page],
    );

    return (
        <div>
            <div className="mb-4 flex flex-wrap items-center gap-3">
                <Input
                    icon={<Search className="size-4" />}
                    placeholder="Cari kata…"
                    value={search}
                    onChange={(e) => setSearch(e.target.value)}
                    wrapperClassName="max-w-xs"
                />
                <div className="flex flex-wrap gap-1.5">
                    <Chip active={language === 'english'} onClick={() => setLanguage('english')}>
                        🇬🇧 Inggris
                    </Chip>
                    <Chip active={language === 'japanese'} onClick={() => setLanguage('japanese')}>
                        🇯🇵 Jepang
                    </Chip>
                </div>
            </div>

            <div className="mb-4 flex flex-wrap gap-1.5">
                <Chip active={level === null} onClick={() => setLevel(null)}>
                    Semua level
                </Chip>
                {LEVELS_BY_LANGUAGE[language].map((l) => (
                    <Chip key={l} active={level === l} onClick={() => setLevel(l)}>
                        {l}
                    </Chip>
                ))}
            </div>

            {loading ? (
                <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    {Array.from({ length: 9 }, (_, i) => (
                        <Skeleton key={i} className="h-28 w-full" />
                    ))}
                </div>
            ) : error ? (
                <Alert tone="danger" title="Gagal memuat kosakata">
                    {error}
                </Alert>
            ) : !data || data.words.length === 0 ? (
                <EmptyState icon={<BookOpen className="size-6" />} title="Tidak ada kata ditemukan" description="Coba ubah kata kunci atau filter level." />
            ) : (
                <>
                    <p className="mb-3 text-xs text-fg-muted">{data.total.toLocaleString('id-ID')} kata ditemukan</p>
                    <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        {data.words.map((w) => (
                            <Card key={w.id} padded>
                                <div className="flex items-start justify-between gap-2">
                                    <div className="min-w-0">
                                        <p className="truncate font-display text-base font-bold">{w.word}</p>
                                        {w.phonetic && <p className="truncate font-mono text-xs text-fg-muted">{w.phonetic}</p>}
                                    </div>
                                    <CefrBadge level={w.level} />
                                </div>
                                {w.partOfSpeech && (
                                    <p className="mt-1 text-[11px] font-semibold uppercase tracking-wide text-fg-muted">{w.partOfSpeech}</p>
                                )}
                                <p className="mt-2 text-sm">{w.meaningId}</p>
                                {w.meaningEn && <p className="text-xs text-fg-muted">{w.meaningEn}</p>}
                            </Card>
                        ))}
                    </div>
                    <Pagination page={data.page} totalPages={data.lastPage} onChange={setPage} className="mt-6" />
                </>
            )}
        </div>
    );
}

export default function WeakWordsPage() {
    const [tab, setTab] = useState('weak');

    return (
        <>
            <PageHeader
                title="Kata Lemah & Kosakata"
                description="Pantau kata yang perlu kamu latih lagi, atau jelajahi seluruh bank kosakata berdasarkan level."
            />

            <Card padded={false}>
                <Tabs
                    className="px-5 pt-2"
                    items={[
                        { id: 'weak', label: 'Kata Lemah' },
                        { id: 'browse', label: 'Jelajahi Kosakata' },
                    ]}
                    active={tab}
                    onChange={setTab}
                />
                <div className="p-5">
                    {tab === 'weak' ? <WeakWordsTab /> : <VocabularyBrowseTab />}
                </div>
            </Card>
        </>
    );
}

import { useEffect, useMemo, useState } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { BookMarked, Bookmark, BookmarkCheck, Search, Volume2, X } from 'lucide-react';
import { useAsync } from '@/hooks/useAsync';
import { useDebounce } from '@/hooks/useDebounce';
import { useSpeechSynthesis } from '@/hooks/useSpeechSynthesis';
import { vocabularyService } from '@/services/api';
import { useProgressStore } from '@/store/progressStore';
import { useUiStore } from '@/store/uiStore';
import { cn } from '@/utils/cn';
import type { LanguageSlug, VocabularyItem } from '@/types';
import { Card, CardHeader } from '@/components/ui/Card';
import { Button, IconButton } from '@/components/ui/Button';
import { Badge, CefrBadge, Chip } from '@/components/ui/Badge';
import { Input } from '@/components/ui/Input';
import { EmptyState, Skeleton } from '@/components/ui/Feedback';
import { DynamicIcon } from '@/components/ui/DynamicIcon';
import { PageHeader } from '@/components/feature/shared/PageHeader';

const CEFR_LEVELS = ['A1', 'A2', 'B1', 'B2', 'C1', 'C2'];

const LANGUAGES: { id: LanguageSlug | 'all'; label: string }[] = [
    { id: 'all', label: 'Semua Bahasa' },
    { id: 'japanese', label: '🇯🇵 Bahasa Jepang' },
    { id: 'english', label: '🇬🇧 Bahasa Inggris' },
];

/** Right-hand detail panel — becomes a bottom sheet on mobile. */
function WordDetail({ word, onClose }: { word: VocabularyItem | null; onClose: () => void }) {
    const { speak, supported } = useSpeechSynthesis();
    const { isBookmarked, toggleBookmark } = useProgressStore();
    const toast = useUiStore((state) => state.toast);

    if (!word) {
        return (
            <Card className="hidden h-full place-content-center text-center lg:grid">
                <BookMarked className="mx-auto size-8 text-fg-muted/50" />
                <p className="mt-3 text-sm text-fg-muted">Pilih sebuah kata untuk melihat detailnya.</p>
            </Card>
        );
    }

    const bookmarked = isBookmarked('vocabulary', word.id);

    return (
        <Card className="h-full">
            <div className="flex items-start justify-between gap-3">
                <div className="min-w-0">
                    <h2 className="font-display text-2xl font-extrabold">{word.word}</h2>
                    <p className="ipa mt-0.5 text-sm text-fg-muted">{word.ipa}</p>
                </div>
                <IconButton label="Tutup" variant="ghost" size="sm" className="lg:hidden" onClick={onClose}>
                    <X className="size-4" />
                </IconButton>
            </div>

            <div className="mt-3 flex flex-wrap gap-1.5">
                <Badge tone="neutral">{word.partOfSpeech}</Badge>
                <CefrBadge level={word.cefr} />
            </div>

            <div className="mt-4 flex gap-2">
                {supported && (
                    <Button size="sm" variant="outline" icon={<Volume2 className="size-4" />} onClick={() => speak(word.word)}>
                        Dengarkan
                    </Button>
                )}
                <Button
                    size="sm"
                    variant={bookmarked ? 'primary' : 'outline'}
                    icon={bookmarked ? <BookmarkCheck className="size-4" /> : <Bookmark className="size-4" />}
                    onClick={() => {
                        toggleBookmark({
                            kind: 'vocabulary',
                            refId: word.id,
                            title: word.word,
                            subtitle: `${word.meaning} · ${word.cefr}`,
                        });
                        toast(bookmarked ? 'Bookmark dihapus.' : 'Ditambahkan ke bookmark.', 'success');
                    }}
                >
                    {bookmarked ? 'Tersimpan' : 'Simpan'}
                </Button>
            </div>

            <dl className="mt-5 space-y-4 text-sm">
                <div>
                    <dt className="text-xs font-bold uppercase tracking-wide text-fg-muted">Arti</dt>
                    <dd className="mt-1 font-medium text-primary">{word.meaning}</dd>
                </div>

                <div>
                    <dt className="text-xs font-bold uppercase tracking-wide text-fg-muted">Contoh</dt>
                    <dd className="mt-1">
                        <p className="italic">"{word.example}"</p>
                        <p className="mt-0.5 text-fg-muted">{word.exampleMeaning}</p>
                        {supported && (
                            <button
                                type="button"
                                onClick={() => speak(word.example)}
                                className="mt-1.5 inline-flex items-center gap-1 text-xs font-medium text-primary hover:underline"
                            >
                                <Volume2 className="size-3" /> Dengarkan contoh
                            </button>
                        )}
                    </dd>
                </div>

                {word.synonyms.length > 0 && (
                    <div>
                        <dt className="text-xs font-bold uppercase tracking-wide text-fg-muted">Sinonim</dt>
                        <dd className="mt-1 flex flex-wrap gap-1.5">
                            {word.synonyms.map((item) => (
                                <Badge key={item} tone="success">
                                    {item}
                                </Badge>
                            ))}
                        </dd>
                    </div>
                )}

                {word.antonyms.length > 0 && (
                    <div>
                        <dt className="text-xs font-bold uppercase tracking-wide text-fg-muted">Antonim</dt>
                        <dd className="mt-1 flex flex-wrap gap-1.5">
                            {word.antonyms.map((item) => (
                                <Badge key={item} tone="danger">
                                    {item}
                                </Badge>
                            ))}
                        </dd>
                    </div>
                )}
            </dl>

            <Button to="/app/flashcard" variant="outline" fullWidth className="mt-6">
                Latih dengan flashcard
            </Button>
        </Card>
    );
}

export default function VocabularyPage() {
    const { categoryId } = useParams();
    const navigate = useNavigate();

    const [search, setSearch] = useState('');
    const [cefr, setCefr] = useState<string>('');
    const [lang, setLang] = useState<LanguageSlug | 'all'>('all');
    const [selected, setSelected] = useState<VocabularyItem | null>(null);

    const debounced = useDebounce(search, 250);

    const { data: categories } = useAsync(() => vocabularyService.listCategories(), []);
    const { data: words, loading } = useAsync(
        () =>
            vocabularyService.list({
                categoryId,
                search: debounced,
                cefr: cefr || undefined,
                language: lang === 'all' ? undefined : lang,
            }),
        [categoryId, debounced, cefr, lang],
    );

    const { isBookmarked } = useProgressStore();

    // Changing category should clear a stale selection from the previous list.
    useEffect(() => setSelected(null), [categoryId]);

    const activeCategory = useMemo(
        () => categories?.find((category) => category.id === categoryId),
        [categories, categoryId],
    );

    const visibleCategories = (categories ?? []).filter(
        (category) => lang === 'all' || category.language === lang,
    );

    return (
        <>
            <PageHeader
                title={activeCategory ? `Vocabulary — ${activeCategory.name}` : 'Vocabulary'}
                description={
                    activeCategory?.description ??
                    'Kosakata terkurasi per tema, lengkap dengan pelafalan IPA, contoh kalimat, sinonim, dan antonim.'
                }
                backTo={categoryId ? '/app/vocabulary' : undefined}
                backLabel="Semua kategori"
            />

            {/* Language chips */}
            <div className="mb-3 flex flex-wrap gap-2">
                {LANGUAGES.map((item) => (
                    <Chip
                        key={item.id}
                        active={lang === item.id}
                        onClick={() => {
                            setLang(item.id);
                            navigate('/app/vocabulary');
                        }}
                    >
                        {item.label}
                    </Chip>
                ))}
            </div>

            {/* Category chips */}
            <div className="mb-4 flex flex-wrap gap-2">
                <Chip active={!categoryId} onClick={() => navigate('/app/vocabulary')}>
                    Semua
                </Chip>
                {visibleCategories.map((category) => (
                    <Chip
                        key={category.id}
                        active={categoryId === category.id}
                        onClick={() => navigate(`/app/vocabulary/${category.id}`)}
                        icon={<DynamicIcon name={category.icon} className="size-3.5" />}
                    >
                        {category.name}
                        <span className="ml-0.5 opacity-60">{category.wordCount}</span>
                    </Chip>
                ))}
            </div>

            {/* Search + level filter */}
            <div className="mb-5 flex flex-wrap items-center gap-3">
                <Input
                    placeholder="Cari kata, arti, atau contoh…"
                    icon={<Search className="size-4" />}
                    value={search}
                    onChange={(event) => setSearch(event.target.value)}
                    wrapperClassName="min-w-56 flex-1"
                />

                <div className="flex flex-wrap gap-1.5">
                    <Chip active={cefr === ''} onClick={() => setCefr('')}>
                        Semua level
                    </Chip>
                    {CEFR_LEVELS.map((level) => (
                        <Chip key={level} active={cefr === level} onClick={() => setCefr(level)}>
                            {level}
                        </Chip>
                    ))}
                </div>
            </div>

            <div className="grid gap-5 lg:grid-cols-[1fr_22rem]">
                {/* Word list */}
                <div>
                    {loading ? (
                        <div className="space-y-2">
                            {Array.from({ length: 8 }, (_, i) => (
                                <Skeleton key={i} className="h-16 w-full" />
                            ))}
                        </div>
                    ) : (words ?? []).length === 0 ? (
                        <EmptyState
                            icon={<Search className="size-6" />}
                            title="Tidak ada kata yang cocok"
                            description="Coba kata kunci lain atau longgarkan filter level."
                        />
                    ) : (
                        <>
                            <p className="mb-3 text-sm text-fg-muted">{words!.length} kata ditemukan</p>

                            <ul className="space-y-2">
                                {words!.map((word) => (
                                    <li key={word.id}>
                                        <button
                                            type="button"
                                            onClick={() => setSelected(word)}
                                            className={cn(
                                                'flex w-full items-center gap-3 rounded-sm border p-3.5 text-left transition',
                                                selected?.id === word.id
                                                    ? 'border-primary bg-primary-50 dark:bg-primary/10'
                                                    : 'border-[var(--surface-border)] bg-surface hover:border-primary-300 hover:bg-surface-sunken',
                                            )}
                                        >
                                            <div className="min-w-0 flex-1">
                                                <div className="flex flex-wrap items-baseline gap-2">
                                                    <span className="font-display font-bold">{word.word}</span>
                                                    <span className="ipa text-xs text-fg-muted">{word.ipa}</span>
                                                </div>
                                                <p className="mt-0.5 truncate text-sm text-fg-muted">{word.meaning}</p>
                                            </div>

                                            <div className="flex shrink-0 items-center gap-2">
                                                <CefrBadge level={word.cefr} />
                                                {isBookmarked('vocabulary', word.id) && (
                                                    <BookmarkCheck className="size-4 text-secondary" />
                                                )}
                                            </div>
                                        </button>
                                    </li>
                                ))}
                            </ul>
                        </>
                    )}
                </div>

                {/* Detail panel */}
                <div className={cn('lg:sticky lg:top-24 lg:self-start', !selected && 'hidden lg:block')}>
                    <WordDetail word={selected} onClose={() => setSelected(null)} />
                </div>
            </div>

            {/* Category overview when no category is picked */}
            {!categoryId && visibleCategories.length > 0 && (
                <Card className="mt-8">
                    <CardHeader title="Jelajahi per kategori" />
                    <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        {visibleCategories.map((category) => (
                            <button
                                key={category.id}
                                type="button"
                                onClick={() => navigate(`/app/vocabulary/${category.id}`)}
                                className="flex items-center gap-3 rounded-sm border border-[var(--surface-border)] p-3 text-left transition hover:border-primary-300 hover:bg-surface-sunken"
                            >
                                <span className="grid size-10 shrink-0 place-items-center rounded-sm bg-primary-100 text-primary dark:bg-primary/20">
                                    <DynamicIcon name={category.icon} className="size-5" />
                                </span>
                                <div className="min-w-0">
                                    <p className="truncate text-sm font-semibold">{category.name}</p>
                                    <p className="text-xs text-fg-muted">{category.wordCount} kata</p>
                                </div>
                            </button>
                        ))}
                    </div>
                </Card>
            )}
        </>
    );
}

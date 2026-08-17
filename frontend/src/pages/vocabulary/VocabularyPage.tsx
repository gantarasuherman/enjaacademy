import { useEffect, useState } from 'react';
import { BookMarked, Bookmark, BookmarkCheck, Search, Volume2, X } from 'lucide-react';
import { useAsync } from '@/hooks/useAsync';
import { useDebounce } from '@/hooks/useDebounce';
import { useSpeechSynthesis } from '@/hooks/useSpeechSynthesis';
import { vocabularyBankService } from '@/services/api';
import { useProgressStore } from '@/store/progressStore';
import { useUiStore } from '@/store/uiStore';
import { cn } from '@/utils/cn';
import type { VocabularyBankWord, VocabularyItem } from '@/types';
import { Card } from '@/components/ui/Card';
import { Button, IconButton } from '@/components/ui/Button';
import { Badge, CefrBadge, Chip } from '@/components/ui/Badge';
import { Input } from '@/components/ui/Input';
import { EmptyState, Skeleton } from '@/components/ui/Feedback';
import { Pagination } from '@/components/ui/Pagination';
import { PageHeader } from '@/components/feature/shared/PageHeader';

const BANK_PER_PAGE = 30;

type BankLanguage = 'english' | 'japanese';

const LEVELS_BY_LANGUAGE: Record<BankLanguage, string[]> = {
    english: ['Beginner', 'Elementary', 'Intermediate', 'Upper-Intermediate', 'Advanced'],
    japanese: ['N5', 'N4', 'N3', 'N2', 'N1'],
};

const LANGUAGES: { id: BankLanguage | 'all'; label: string }[] = [
    { id: 'all', label: 'Semua Bahasa' },
    { id: 'japanese', label: '🇯🇵 Bahasa Jepang' },
    { id: 'english', label: '🇬🇧 Bahasa Inggris' },
];

/**
 * All vocabulary — English and Japanese — now comes from the same
 * `vocabulary_words` bank the Daily Quiz draws from. The older
 * lessons-derived source (categories like "salam", "angka", ...) required
 * the viewer to be *enrolled* in the `english-vocabulary`/`kosakata-jepang`
 * course modules to load anything (`LearningModulePolicy::study()`), which
 * silently produced an empty list for anyone not enrolled — the bank has no
 * such gate, so this is both simpler and actually works for every user.
 */
function toVocabularyItem(w: VocabularyBankWord, language: BankLanguage): VocabularyItem {
    const example = w.examples?.[0];

    return {
        // Prefixed so a bank word can never collide with a legacy lesson-item
        // id in bookmark state (both are plain numeric-as-string ids).
        id: `bank-${w.id}`,
        word: w.word,
        ipa: w.phonetic ?? '',
        partOfSpeech: w.partOfSpeech ?? '',
        audioUrl: null,
        imageUrl: null,
        meaning: w.meaningId,
        example: example?.sentenceEn ?? '',
        exampleMeaning: example?.sentenceId ?? '',
        synonyms: w.synonyms,
        antonyms: w.antonyms,
        categoryId: '',
        cefr: w.level,
        difficulty: 'beginner',
        language,
    };
}

/** Bank rows don't carry a language flag back — infer it from which level scale the word's level belongs to. */
function inferLanguage(level: string): BankLanguage {
    return LEVELS_BY_LANGUAGE.japanese.includes(level) ? 'japanese' : 'english';
}

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

                {word.example && (
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
                )}

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
    const [search, setSearch] = useState('');
    const [level, setLevel] = useState<string | null>(null);
    const [lang, setLang] = useState<BankLanguage | 'all'>('all');
    const [selected, setSelected] = useState<VocabularyItem | null>(null);
    const [page, setPage] = useState(1);

    const debounced = useDebounce(search, 250);

    const { data, loading } = useAsync(
        () =>
            vocabularyBankService.list({
                language: lang === 'all' ? undefined : lang,
                level: level ?? undefined,
                search: debounced || undefined,
                page,
                perPage: BANK_PER_PAGE,
            }),
        [lang, level, debounced, page],
    );

    const { isBookmarked } = useProgressStore();

    const words = (data?.words ?? []).map((w) => toVocabularyItem(w, lang === 'all' ? inferLanguage(w.level) : lang));

    // Changing language invalidates a level picked from the other scale (CEFR vs JLPT), and any filter change lands back on page 1.
    useEffect(() => setLevel(null), [lang]);
    useEffect(() => setPage(1), [lang, level, debounced]);
    useEffect(() => setSelected(null), [lang]);

    const levelOptions = lang === 'all' ? [] : LEVELS_BY_LANGUAGE[lang];

    return (
        <>
            <PageHeader
                title="Vocabulary"
                description="Kosakata Inggris & Jepang, lengkap dengan pelafalan, contoh kalimat, sinonim, dan antonim."
            />

            {/* Language chips */}
            <div className="mb-4 flex flex-wrap gap-2">
                {LANGUAGES.map((item) => (
                    <Chip key={item.id} active={lang === item.id} onClick={() => setLang(item.id)}>
                        {item.label}
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

                {levelOptions.length > 0 ? (
                    <div className="flex flex-wrap gap-1.5">
                        <Chip active={level === null} onClick={() => setLevel(null)}>
                            Semua level
                        </Chip>
                        {levelOptions.map((lvl) => (
                            <Chip key={lvl} active={level === lvl} onClick={() => setLevel(lvl)}>
                                {lvl}
                            </Chip>
                        ))}
                    </div>
                ) : (
                    <p className="text-xs text-fg-muted">Pilih bahasa untuk memfilter level.</p>
                )}
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
                    ) : words.length === 0 ? (
                        <EmptyState
                            icon={<Search className="size-6" />}
                            title="Tidak ada kata yang cocok"
                            description="Coba kata kunci lain atau longgarkan filter level."
                        />
                    ) : (
                        <>
                            <p className="mb-3 text-sm text-fg-muted">{(data?.total ?? 0).toLocaleString('id-ID')} kata ditemukan</p>

                            <ul className="space-y-2">
                                {words.map((word) => (
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

                            {data && data.lastPage > 1 && (
                                <Pagination page={data.page} totalPages={data.lastPage} onChange={setPage} className="mt-5" />
                            )}
                        </>
                    )}
                </div>

                {/* Detail panel */}
                <div className={cn('lg:sticky lg:top-24 lg:self-start', !selected && 'hidden lg:block')}>
                    <WordDetail word={selected} onClose={() => setSelected(null)} />
                </div>
            </div>
        </>
    );
}

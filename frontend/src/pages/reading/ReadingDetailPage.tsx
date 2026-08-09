import { useEffect, useMemo, useRef, useState } from 'react';
import { useParams } from 'react-router-dom';
import { BookMarked, Bookmark, BookmarkCheck, Clock, GraduationCap, Minus, Plus, Volume2 } from 'lucide-react';
import { useAsync } from '@/hooks/useAsync';
import { useSpeechSynthesis } from '@/hooks/useSpeechSynthesis';
import { readingService } from '@/services/api';
import { useProgressStore } from '@/store/progressStore';
import { useUiStore } from '@/store/uiStore';
import { cn } from '@/utils/cn';
import { Card, CardHeader } from '@/components/ui/Card';
import { Button, IconButton } from '@/components/ui/Button';
import { Badge, CefrBadge } from '@/components/ui/Badge';
import { EmptyState, PageLoader } from '@/components/ui/Feedback';
import { PageHeader } from '@/components/feature/shared/PageHeader';

/**
 * The "word" unit `speechSynthesis`'s `onboundary` event indexes against.
 * English is whitespace-delimited, so a run of non-space characters is one
 * word. Japanese has no spaces between words at all — treating a whole
 * sentence as a single `\S+` match would make the highlight jump once per
 * paragraph instead of tracking speech, so each individual character is its
 * own unit instead, which is the closest a boundary index can get without a
 * real Japanese word segmenter.
 */
function wordUnitsRegex(isJapanese: boolean): RegExp {
    return isJapanese ? /[^\s]/gu : /\S+/g;
}

function tokenizeForHighlight(raw: string, isJapanese: boolean): string[] {
    return raw.match(isJapanese ? /\s+|[^\s]/gu : /\S+|\s+/g) ?? [];
}

/** Matches the `rate` passed to `speak()` in `speakArticle()` below. */
const ARTICLE_SPEAK_RATE = 0.9;

/**
 * Rough, voice-agnostic reading-speed guesses at rate=1.0 (units/second) —
 * used only when the browser/voice never fires a single `onboundary` event
 * for the whole utterance, which is common for Japanese voices. Real
 * boundary events are always preferred the moment even one arrives; this is
 * strictly a "better than a frozen highlight" fallback, not a precise sync.
 */
const ESTIMATED_UNITS_PER_SECOND = { japanese: 7.5, other: 2.3 };

/** How long to wait for a first real boundary event before assuming the voice doesn't send them. */
const BOUNDARY_GRACE_MS = 700;

export default function ReadingDetailPage() {
    const { articleId = '' } = useParams();
    const { data: text, loading } = useAsync(() => readingService.get(articleId), [articleId]);
    const isJapanese = text?.language === 'japanese';

    const { speak, stop, speaking, charIndex, lastBoundaryAt, supported } = useSpeechSynthesis();
    const { isBookmarked, toggleBookmark } = useProgressStore();
    const toast = useUiStore((state) => state.toast);

    const [fontSize, setFontSize] = useState(16);
    const [activeWord, setActiveWord] = useState<string | null>(null);
    // Distinguishes "reading the whole article aloud" (word-by-word highlight
    // makes sense) from "pronouncing a single glossary word" (it doesn't —
    // that utterance's char offsets have nothing to do with the article).
    const [ttsMode, setTtsMode] = useState<'article' | 'word' | null>(null);
    const articleRef = useRef<HTMLElement>(null);

    // Glossary lookup is case-insensitive, so "Queue" matches "queue".
    const glossaryMap = useMemo(() => {
        const map = new Map<string, string>();
        text?.glossary.forEach((entry) => map.set(entry.word.toLowerCase(), entry.meaning));
        return map;
    }, [text]);

    /**
     * Matches glossary entries as literal substrings rather than
     * whitespace-split tokens — Japanese text has no spaces between words,
     * so a `\s+` split (which works fine for English) would never isolate a
     * single kanji/kana term. Longest terms first so a short entry can't
     * shadow a longer one that contains it.
     */
    const glossaryRegex = useMemo(() => {
        const words = (text?.glossary ?? []).map((entry) => entry.word).filter(Boolean);
        if (words.length === 0) return null;

        const escaped = [...words]
            .sort((a, b) => b.length - a.length)
            .map((word) => word.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'));

        return new RegExp(`(${escaped.join('|')})`, 'gi');
    }, [text]);

    const translatedParagraphs = useMemo(
        () => text?.translatedBody?.split('\n\n') ?? [],
        [text],
    );

    // Same plain text (bold markers stripped) as what gets handed to
    // `speak()` for the "Bacakan" button — the `charIndex` boundary events
    // reference offsets into exactly this string, so highlighting has to be
    // computed against the same text, not the raw `**bold**` source.
    const plainParagraphs = useMemo(
        () => (text?.body ?? '').split('\n\n').map((p) => p.replace(/\*\*/g, '')),
        [text],
    );
    const plainBody = useMemo(() => plainParagraphs.join('\n\n'), [plainParagraphs]);

    // Start offset (in `plainBody`) of every word, plus how many words come
    // before each paragraph — together these let a per-paragraph word
    // counter be compared against the single global `charIndex` the browser
    // reports.
    const { wordStarts, paragraphWordOffsets } = useMemo(() => {
        const starts = [...plainBody.matchAll(wordUnitsRegex(isJapanese))].map((m) => m.index ?? 0);

        const offsets: number[] = [];
        let running = 0;

        for (const paragraph of plainParagraphs) {
            offsets.push(running);
            running += (paragraph.match(wordUnitsRegex(isJapanese)) ?? []).length;
        }

        return { wordStarts: starts, paragraphWordOffsets: offsets };
    }, [plainBody, plainParagraphs, isJapanese]);

    // Tracks when the current "Bacakan" utterance started, and ticks
    // periodically for the whole utterance so the time-based fallback
    // estimate below can recompute smoothly and so staleness (see
    // `useTimingFallback`) gets re-checked continuously — a voice that fires
    // one or two real boundary events and then stops (common for Japanese,
    // which has no word boundaries to report mid-sentence) needs this to
    // keep running past that point, not just until the first event arrives.
    const speechStartRef = useRef<number | null>(null);
    const [estimateTick, setEstimateTick] = useState(0);

    useEffect(() => {
        if (!speaking || ttsMode !== 'article') {
            speechStartRef.current = null;
            return;
        }

        speechStartRef.current ??= Date.now();

        const interval = setInterval(() => setEstimateTick((t) => t + 1), 150);
        return () => clearInterval(interval);
    }, [speaking, ttsMode]);

    // Neither "no boundary event has ever fired" nor "one fired a while ago"
    // can be trusted — only recency. `lastBoundaryAt` falls back to the
    // utterance's own start time when nothing has fired yet, so this reads
    // uniformly as "how long since we last heard from the voice."
    const useTimingFallback =
        speaking &&
        ttsMode === 'article' &&
        speechStartRef.current !== null &&
        Date.now() - (lastBoundaryAt ?? speechStartRef.current) > BOUNDARY_GRACE_MS;

    // The word whose start offset is at-or-before `charIndex` — Chrome/Edge
    // report the boundary at the word's first character, so this lands on
    // exactly the word currently being spoken. Falls back to a time-based
    // estimate (see `useTimingFallback` above) when the voice never sends a
    // single real boundary event, rather than leaving the highlight frozen
    // on word/character zero for the whole utterance.
    const activeGlobalWordIndex = useMemo(() => {
        if (ttsMode !== 'article' || !speaking || wordStarts.length === 0) return null;

        if (useTimingFallback) {
            const elapsedSeconds = (Date.now() - (speechStartRef.current ?? Date.now())) / 1000;
            const unitsPerSecond =
                (isJapanese ? ESTIMATED_UNITS_PER_SECOND.japanese : ESTIMATED_UNITS_PER_SECOND.other) *
                ARTICLE_SPEAK_RATE;

            return Math.min(Math.floor(elapsedSeconds * unitsPerSecond), wordStarts.length - 1);
        }

        if (charIndex === null) return null;

        let lo = 0;
        let hi = wordStarts.length - 1;
        let answer = -1;

        while (lo <= hi) {
            const mid = (lo + hi) >> 1;

            if (wordStarts[mid] <= charIndex) {
                answer = mid;
                lo = mid + 1;
            } else {
                hi = mid - 1;
            }
        }

        return answer >= 0 ? answer : null;
        // `estimateTick` isn't read directly — it exists purely to force this
        // memo to recompute on a timer while the fallback path is active.
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [ttsMode, speaking, charIndex, wordStarts, useTimingFallback, isJapanese, estimateTick]);

    // Keep the word being spoken in view as "Bacakan" plays through long articles.
    useEffect(() => {
        if (activeGlobalWordIndex === null) return;

        articleRef.current
            ?.querySelector('[data-word-active="true"]')
            ?.scrollIntoView({ block: 'center', behavior: 'smooth' });
    }, [activeGlobalWordIndex]);

    if (loading) return <PageLoader />;

    if (!text) {
        return <EmptyState title="Bacaan tidak ditemukan" action={<Button to="/app/reading">Kembali</Button>} />;
    }

    const bookmarked = isBookmarked('reading', text.id);

    function speakArticle() {
        if (speaking) {
            stop();
            return;
        }

        setTtsMode('article');
        speak(plainBody, { rate: 0.9 });
    }

    function speakWord(word: string) {
        setTtsMode('word');
        speak(word);
    }

    /** Wraps each run of non-space characters in `raw` with a highlight span when it's the word currently being spoken. */
    function renderWords(raw: string, counter: { current: number }, keyPrefix: string) {
        const tokens = tokenizeForHighlight(raw, isJapanese);

        return tokens.map((token, i) => {
            if (/^\s+$/.test(token)) return <span key={`${keyPrefix}-${i}`}>{token}</span>;

            const isActive = counter.current === activeGlobalWordIndex;
            counter.current += 1;

            if (!isActive) return <span key={`${keyPrefix}-${i}`}>{token}</span>;

            return (
                <mark
                    key={`${keyPrefix}-${i}`}
                    data-word-active="true"
                    className="rounded bg-primary/25 px-0.5 text-fg ring-1 ring-primary/50"
                >
                    {token}
                </mark>
            );
        });
    }

    /**
     * Renders one paragraph: glossary hits become hover/tap targets with an
     * inline tooltip (same word + meaning shown in the sidebar glossary
     * card), a translated counterpart (when one exists at the same
     * paragraph index) appears on hover, and — while "Bacakan" is playing —
     * the word currently being spoken gets a highlight.
     */
    function renderParagraph(paragraph: string, index: number) {
        // Bold markers used in the documentation-style article.
        const segments = paragraph.split(/(\*\*[^*]+\*\*)/g);
        const translatedParagraph = translatedParagraphs[index];
        const counter = { current: paragraphWordOffsets[index] ?? 0 };

        return (
            <div key={index} className="group/para relative">
                <p style={{ fontSize, lineHeight: 1.85 }} className="text-fg">
                    {segments.map((segment, segIndex) => {
                        if (segment.startsWith('**') && segment.endsWith('**')) {
                            return (
                                <strong key={segIndex} className="font-display font-bold">
                                    {renderWords(segment.slice(2, -2), counter, `b${segIndex}`)}
                                </strong>
                            );
                        }

                        if (!glossaryRegex) return <span key={segIndex}>{renderWords(segment, counter, `p${segIndex}`)}</span>;

                        return segment.split(glossaryRegex).map((part, partIndex) => {
                            const meaning = glossaryMap.get(part.toLowerCase());

                            if (!meaning) {
                                return (
                                    <span key={`${segIndex}-${partIndex}`}>
                                        {renderWords(part, counter, `g${segIndex}-${partIndex}`)}
                                    </span>
                                );
                            }

                            const isActiveGlossary = activeWord === part.toLowerCase();
                            const wordsInPart = (part.match(wordUnitsRegex(isJapanese)) ?? []).length || 1;
                            const isBeingSpoken =
                                activeGlobalWordIndex !== null &&
                                activeGlobalWordIndex >= counter.current &&
                                activeGlobalWordIndex < counter.current + wordsInPart;
                            counter.current += wordsInPart;

                            return (
                                <span key={`${segIndex}-${partIndex}`} className="group/word relative inline">
                                    <button
                                        type="button"
                                        data-word-active={isBeingSpoken ? 'true' : undefined}
                                        onClick={() => setActiveWord(part.toLowerCase())}
                                        className={cn(
                                            'rounded px-0.5 underline decoration-secondary decoration-dotted decoration-2 underline-offset-4 transition',
                                            isActiveGlossary ? 'bg-secondary/25' : 'hover:bg-secondary/15',
                                            isBeingSpoken && 'bg-primary/25 ring-1 ring-primary/50',
                                        )}
                                    >
                                        {part}
                                    </button>
                                    <span className="pointer-events-none absolute bottom-full left-1/2 z-10 mb-1.5 hidden -translate-x-1/2 whitespace-nowrap rounded-sm border border-[var(--surface-border)] bg-surface px-2.5 py-1.5 text-xs shadow-lg group-hover/word:block">
                                        <span className="block font-semibold">{part}</span>
                                        <span className="block text-fg-muted">{meaning}</span>
                                    </span>
                                </span>
                            );
                        });
                    })}
                </p>

                {translatedParagraph && (
                    <p className="mt-2 hidden rounded-sm border border-dashed border-[var(--surface-border)] bg-surface-sunken px-3 py-2 text-sm text-fg-muted group-hover/para:block">
                        {translatedParagraph}
                    </p>
                )}
            </div>
        );
    }

    return (
        <>
            <PageHeader
                backTo="/app/reading"
                backLabel="Semua bacaan"
                title={text.title}
                badge={<CefrBadge level={text.cefr} />}
                action={
                    <div className="flex flex-wrap gap-2">
                        {supported && (
                            <Button
                                variant="outline"
                                icon={<Volume2 className="size-4" />}
                                onClick={speakArticle}
                            >
                                {speaking && ttsMode === 'article' ? 'Hentikan' : 'Bacakan'}
                            </Button>
                        )}
                        <Button
                            variant={bookmarked ? 'primary' : 'outline'}
                            icon={bookmarked ? <BookmarkCheck className="size-4" /> : <Bookmark className="size-4" />}
                            onClick={() => {
                                toggleBookmark({
                                    kind: 'reading',
                                    refId: text.id,
                                    title: text.title,
                                    subtitle: `${text.type} · ${text.cefr}`,
                                });
                                toast(bookmarked ? 'Bookmark dihapus.' : 'Ditambahkan ke bookmark.');
                            }}
                        >
                            {bookmarked ? 'Tersimpan' : 'Simpan'}
                        </Button>
                    </div>
                }
            />

            <div className="mb-5 flex flex-wrap items-center gap-4 text-sm text-fg-muted">
                <span className="flex items-center gap-1.5">
                    <Clock className="size-4" />
                    {text.readingMinutes} menit baca
                </span>
                <span>{text.wordCount} kata</span>

                <span className="ml-auto flex items-center gap-1">
                    <IconButton
                        label="Perkecil teks"
                        variant="outline"
                        size="sm"
                        onClick={() => setFontSize((size) => Math.max(13, size - 1))}
                    >
                        <Minus className="size-3.5" />
                    </IconButton>
                    <span className="w-10 text-center font-mono text-xs">{fontSize}px</span>
                    <IconButton
                        label="Perbesar teks"
                        variant="outline"
                        size="sm"
                        onClick={() => setFontSize((size) => Math.min(24, size + 1))}
                    >
                        <Plus className="size-3.5" />
                    </IconButton>
                </span>
            </div>

            <div className="grid gap-6 lg:grid-cols-[1fr_20rem]">
                <div className="space-y-6">
                    <Card>
                        <p className="mb-4 text-xs text-fg-muted">
                            Kata bergaris putus-putus punya penjelasan — arahkan kursor atau ketuk untuk melihat
                            artinya. Arahkan kursor ke sebuah paragraf untuk melihat terjemahannya.
                            {supported && ' Tekan "Bacakan" untuk mendengarkan sambil kata yang dibaca disorot.'}
                        </p>
                        <article ref={articleRef} className="space-y-5">
                            {text.body.split('\n\n').map((paragraph, index) => renderParagraph(paragraph, index))}
                        </article>
                    </Card>
                </div>

                <div className="space-y-6 lg:sticky lg:top-24 lg:self-start">
                    {activeWord && glossaryMap.has(activeWord) && (
                        <Card className="border-secondary bg-secondary-50 dark:bg-secondary/10">
                            <div className="flex items-start justify-between gap-2">
                                <div>
                                    <p className="font-display text-lg font-bold">{activeWord}</p>
                                    <p className="mt-1 text-sm text-fg-muted">{glossaryMap.get(activeWord)}</p>
                                </div>
                                {supported && (
                                    <IconButton
                                        label="Dengarkan"
                                        variant="outline"
                                        size="sm"
                                        onClick={() => speakWord(activeWord)}
                                    >
                                        <Volume2 className="size-4" />
                                    </IconButton>
                                )}
                            </div>
                        </Card>
                    )}

                    <Card>
                        <CardHeader
                            title="Glosarium"
                            subtitle={`${text.glossary.length} kata`}
                            action={<BookMarked className="size-5 text-fg-muted" />}
                        />

                        <ul className="space-y-2.5">
                            {text.glossary.map((entry) => (
                                <li key={entry.word}>
                                    <button
                                        type="button"
                                        onClick={() => setActiveWord(entry.word.toLowerCase())}
                                        className={cn(
                                            'w-full rounded-sm px-2 py-1.5 text-left transition hover:bg-surface-sunken',
                                            activeWord === entry.word.toLowerCase() && 'bg-surface-sunken',
                                        )}
                                    >
                                        <p className="text-sm font-semibold">{entry.word}</p>
                                        <p className="text-xs text-fg-muted">{entry.meaning}</p>
                                    </button>
                                </li>
                            ))}
                        </ul>
                    </Card>

                    {text.quizId && (
                        <Card className="bg-primary-50 dark:bg-primary/10">
                            <div className="flex items-center gap-3">
                                <GraduationCap className="size-6 shrink-0 text-primary" />
                                <div className="min-w-0">
                                    <p className="font-display font-bold">Kuis pemahaman</p>
                                    <p className="text-xs text-fg-muted">Cek seberapa banyak yang kamu tangkap.</p>
                                </div>
                            </div>
                            <Button to={`/app/quiz/${text.quizId}`} fullWidth className="mt-4">
                                Kerjakan kuis
                            </Button>
                        </Card>
                    )}

                    <Badge tone="info" className="w-full justify-center py-2">
                        Tip: baca sekali tanpa berhenti, lalu ulangi sambil membuka glosarium.
                    </Badge>
                </div>
            </div>
        </>
    );
}

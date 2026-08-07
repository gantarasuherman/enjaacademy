import { useMemo, useState } from 'react';
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

export default function ReadingDetailPage() {
    const { articleId = '' } = useParams();
    const { data: text, loading } = useAsync(() => readingService.get(articleId), [articleId]);

    const { speak, stop, speaking, supported } = useSpeechSynthesis();
    const { isBookmarked, toggleBookmark } = useProgressStore();
    const toast = useUiStore((state) => state.toast);

    const [fontSize, setFontSize] = useState(16);
    const [activeWord, setActiveWord] = useState<string | null>(null);

    // Glossary lookup is case-insensitive, so "Queue" matches "queue".
    const glossaryMap = useMemo(() => {
        const map = new Map<string, string>();
        text?.glossary.forEach((entry) => map.set(entry.word.toLowerCase(), entry.meaning));
        return map;
    }, [text]);

    if (loading) return <PageLoader />;

    if (!text) {
        return <EmptyState title="Bacaan tidak ditemukan" action={<Button to="/app/reading">Kembali</Button>} />;
    }

    const bookmarked = isBookmarked('reading', text.id);

    /**
     * Splits a paragraph into tokens and turns glossary hits into buttons.
     * Punctuation is stripped for the lookup but kept in the rendered token.
     */
    function renderParagraph(paragraph: string, key: number) {
        // Bold markers used in the documentation-style article.
        const segments = paragraph.split(/(\*\*[^*]+\*\*)/g);

        return (
            <p key={key} style={{ fontSize, lineHeight: 1.85 }} className="text-fg">
                {segments.map((segment, segIndex) => {
                    if (segment.startsWith('**') && segment.endsWith('**')) {
                        return (
                            <strong key={segIndex} className="font-display font-bold">
                                {segment.slice(2, -2)}
                            </strong>
                        );
                    }

                    return segment.split(/(\s+)/).map((token, tokenIndex) => {
                        const bare = token.replace(/[^\w'-]/g, '').toLowerCase();
                        const meaning = glossaryMap.get(bare);

                        if (!meaning) return <span key={`${segIndex}-${tokenIndex}`}>{token}</span>;

                        return (
                            <button
                                key={`${segIndex}-${tokenIndex}`}
                                type="button"
                                onClick={() => setActiveWord(bare)}
                                className={cn(
                                    'rounded px-0.5 underline decoration-secondary decoration-dotted decoration-2 underline-offset-4 transition',
                                    activeWord === bare ? 'bg-secondary/25' : 'hover:bg-secondary/15',
                                )}
                            >
                                {token}
                            </button>
                        );
                    });
                })}
            </p>
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
                    <div className="flex gap-2">
                        {supported && (
                            <Button
                                variant="outline"
                                icon={<Volume2 className="size-4" />}
                                onClick={() => (speaking ? stop() : speak(text.body.replace(/\*\*/g, ''), { rate: 0.9 }))}
                            >
                                {speaking ? 'Hentikan' : 'Bacakan'}
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
                <Card>
                    <p className="mb-4 text-xs text-fg-muted">
                        Kata bergaris putus-putus punya penjelasan — ketuk untuk melihat artinya.
                    </p>

                    <article className="space-y-5">
                        {text.body.split('\n\n').map((paragraph, index) => renderParagraph(paragraph, index))}
                    </article>
                </Card>

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
                                        onClick={() => speak(activeWord)}
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

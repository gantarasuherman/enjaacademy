import { useState } from 'react';
import { Link } from 'react-router-dom';
import { BookOpen, Clock, FileText, Newspaper, ScrollText, Terminal } from 'lucide-react';
import { useAsync } from '@/hooks/useAsync';
import { readingService } from '@/services/api';
import { useProgressStore } from '@/store/progressStore';
import type { LanguageSlug, ReadingText } from '@/types';
import { Card } from '@/components/ui/Card';
import { Badge, CefrBadge, Chip } from '@/components/ui/Badge';
import { EmptyState, Skeleton } from '@/components/ui/Feedback';
import { PageHeader } from '@/components/feature/shared/PageHeader';

const TYPES: { id: ReadingText['type'] | 'all'; label: string; icon: typeof BookOpen }[] = [
    { id: 'all', label: 'Semua', icon: BookOpen },
    { id: 'story', label: 'Cerita', icon: ScrollText },
    { id: 'news', label: 'Berita', icon: Newspaper },
    { id: 'article', label: 'Artikel', icon: FileText },
    { id: 'documentation', label: 'Dokumentasi', icon: Terminal },
];

const LANGUAGES: { id: LanguageSlug | 'all'; label: string }[] = [
    { id: 'all', label: 'Semua Bahasa' },
    { id: 'japanese', label: '🇯🇵 Bahasa Jepang' },
    { id: 'english', label: '🇬🇧 Bahasa Inggris' },
];

const TYPE_META: Record<ReadingText['type'], { label: string; icon: typeof BookOpen }> = {
    story: { label: 'Cerita', icon: ScrollText },
    news: { label: 'Berita', icon: Newspaper },
    article: { label: 'Artikel', icon: FileText },
    documentation: { label: 'Dokumentasi', icon: Terminal },
};

export default function ReadingPage() {
    const [type, setType] = useState<ReadingText['type'] | 'all'>('all');
    const [lang, setLang] = useState<LanguageSlug | 'all'>('all');

    const { data: allTexts, loading } = useAsync(
        () => readingService.list(type === 'all' ? undefined : type),
        [type],
    );

    const texts = (allTexts ?? []).filter((text) => lang === 'all' || text.language === lang);

    const isBookmarked = useProgressStore((state) => state.isBookmarked);

    return (
        <>
            <PageHeader
                title="Reading"
                description="Cerita pendek, berita, artikel opini, dan dokumentasi teknis — masing-masing dengan glosarium kata sulit dan kuis pemahaman."
            />

            <div className="mb-3 flex flex-wrap gap-2">
                {LANGUAGES.map((item) => (
                    <Chip key={item.id} active={lang === item.id} onClick={() => setLang(item.id)}>
                        {item.label}
                    </Chip>
                ))}
            </div>

            <div className="mb-5 flex flex-wrap gap-2">
                {TYPES.map((item) => {
                    const Icon = item.icon;

                    return (
                        <Chip
                            key={item.id}
                            active={type === item.id}
                            onClick={() => setType(item.id)}
                            icon={<Icon className="size-3.5" />}
                        >
                            {item.label}
                        </Chip>
                    );
                })}
            </div>

            {loading ? (
                <div className="grid gap-4 sm:grid-cols-2">
                    {Array.from({ length: 4 }, (_, i) => (
                        <Skeleton key={i} className="h-44 w-full" />
                    ))}
                </div>
            ) : texts.length === 0 ? (
                <EmptyState
                    icon={<BookOpen className="size-6" />}
                    title="Belum ada bacaan di kategori ini"
                    description="Coba pilih kategori lain."
                />
            ) : (
                <div className="grid gap-4 sm:grid-cols-2">
                    {texts.map((text) => {
                        const meta = TYPE_META[text.type];
                        const Icon = meta.icon;

                        return (
                            <Link key={text.id} to={`/app/reading/${text.id}`} className="block h-full">
                                <Card interactive className="flex h-full flex-col">
                                    <div className="flex items-start justify-between gap-3">
                                        <span className="grid size-10 shrink-0 place-items-center rounded-sm bg-primary-100 text-primary dark:bg-primary/20">
                                            <Icon className="size-5" />
                                        </span>
                                        <div className="flex flex-wrap justify-end gap-1.5">
                                            {text.language === 'japanese' && <Badge tone="neutral">🇯🇵</Badge>}
                                            {text.language === 'english' && <Badge tone="neutral">🇬🇧</Badge>}
                                            <Badge tone="neutral">{meta.label}</Badge>
                                            <CefrBadge level={text.cefr} />
                                        </div>
                                    </div>

                                    <h3 className="mt-4 font-display text-base font-bold leading-snug">{text.title}</h3>

                                    <p className="mt-2 line-clamp-3 flex-1 text-sm text-fg-muted">
                                        {text.body.split('\n\n')[0]}
                                    </p>

                                    <div className="mt-4 flex flex-wrap items-center gap-4 text-xs text-fg-muted">
                                        <span className="flex items-center gap-1">
                                            <Clock className="size-3.5" />
                                            {text.readingMinutes} menit baca
                                        </span>
                                        <span>{text.wordCount} kata</span>
                                        <span>{text.glossary.length} kata sulit</span>
                                        {isBookmarked('reading', text.id) && (
                                            <Badge tone="secondary">Tersimpan</Badge>
                                        )}
                                    </div>
                                </Card>
                            </Link>
                        );
                    })}
                </div>
            )}
        </>
    );
}

import { useState } from 'react';
import { Link } from 'react-router-dom';
import { BookMarked, BookOpen, Bookmark, Headphones, MessagesSquare, Sigma, Trash2 } from 'lucide-react';
import { useProgressStore } from '@/store/progressStore';
import { useUiStore } from '@/store/uiStore';
import { formatRelative } from '@/utils/format';
import type { BookmarkKind } from '@/types';
import { Card } from '@/components/ui/Card';
import { Button, IconButton } from '@/components/ui/Button';
import { Badge, Chip } from '@/components/ui/Badge';
import { EmptyState } from '@/components/ui/Feedback';
import { PageHeader } from '@/components/feature/shared/PageHeader';

const KIND_META: Record<BookmarkKind, { label: string; icon: typeof BookMarked; to: (id: string) => string }> = {
    vocabulary: { label: 'Kosakata', icon: BookMarked, to: () => '/app/vocabulary' },
    grammar: { label: 'Grammar', icon: Sigma, to: (id) => `/app/grammar/${id}` },
    reading: { label: 'Bacaan', icon: BookOpen, to: (id) => `/app/reading/${id}` },
    listening: { label: 'Menyimak', icon: Headphones, to: (id) => `/app/listening/${id}` },
    conversation: { label: 'Percakapan', icon: MessagesSquare, to: (id) => `/app/conversation/${id}` },
};

export default function BookmarkPage() {
    const [kind, setKind] = useState<BookmarkKind | 'all'>('all');

    const { bookmarks, toggleBookmark } = useProgressStore();
    const toast = useUiStore((state) => state.toast);

    const filtered = bookmarks.filter((item) => kind === 'all' || item.kind === kind);

    const counts = (Object.keys(KIND_META) as BookmarkKind[]).map((key) => ({
        key,
        count: bookmarks.filter((item) => item.kind === key).length,
    }));

    return (
        <>
            <PageHeader
                title="Bookmark"
                description="Semua yang kamu tandai — kosakata, topik grammar, bacaan, audio, dan percakapan — dalam satu tempat."
            />

            <div className="mb-5 flex flex-wrap gap-2">
                <Chip active={kind === 'all'} onClick={() => setKind('all')}>
                    Semua ({bookmarks.length})
                </Chip>
                {counts.map(({ key, count }) => {
                    const Icon = KIND_META[key].icon;

                    return (
                        <Chip
                            key={key}
                            active={kind === key}
                            onClick={() => setKind(key)}
                            icon={<Icon className="size-3.5" />}
                        >
                            {KIND_META[key].label} ({count})
                        </Chip>
                    );
                })}
            </div>

            {filtered.length === 0 ? (
                <EmptyState
                    icon={<Bookmark className="size-6" />}
                    title={bookmarks.length === 0 ? 'Belum ada bookmark' : 'Tidak ada bookmark di kategori ini'}
                    description="Ketuk ikon bookmark pada kosakata, bacaan, atau materi lain untuk menyimpannya di sini."
                    action={<Button to="/app/vocabulary">Jelajahi kosakata</Button>}
                />
            ) : (
                <div className="grid gap-3 sm:grid-cols-2">
                    {filtered.map((item) => {
                        const meta = KIND_META[item.kind];
                        const Icon = meta.icon;

                        return (
                            <Card key={item.id} className="flex items-start gap-3">
                                <span className="grid size-10 shrink-0 place-items-center rounded-sm bg-secondary-100 text-secondary-700 dark:bg-secondary/20 dark:text-secondary-300">
                                    <Icon className="size-5" />
                                </span>

                                <div className="min-w-0 flex-1">
                                    <div className="flex flex-wrap items-center gap-2">
                                        <Link
                                            to={meta.to(item.refId)}
                                            className="truncate font-display font-bold hover:text-primary hover:underline"
                                        >
                                            {item.title}
                                        </Link>
                                        <Badge tone="neutral">{meta.label}</Badge>
                                    </div>
                                    <p className="mt-0.5 truncate text-sm text-fg-muted">{item.subtitle}</p>
                                    <p className="mt-1 text-xs text-fg-muted">Disimpan {formatRelative(item.createdAt)}</p>
                                </div>

                                <IconButton
                                    label={`Hapus bookmark ${item.title}`}
                                    variant="ghost"
                                    size="sm"
                                    onClick={() => {
                                        toggleBookmark({
                                            kind: item.kind,
                                            refId: item.refId,
                                            title: item.title,
                                            subtitle: item.subtitle,
                                        });
                                        toast('Bookmark dihapus.');
                                    }}
                                >
                                    <Trash2 className="size-4" />
                                </IconButton>
                            </Card>
                        );
                    })}
                </div>
            )}
        </>
    );
}

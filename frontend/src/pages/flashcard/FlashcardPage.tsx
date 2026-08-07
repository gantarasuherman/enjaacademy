import { Link } from 'react-router-dom';
import { Layers, Play, Sparkles } from 'lucide-react';
import { useAsync } from '@/hooks/useAsync';
import { flashcardService } from '@/services/api';
import { useFlashcardStore } from '@/store/flashcardStore';
import { Card } from '@/components/ui/Card';
import { Button } from '@/components/ui/Button';
import { Badge } from '@/components/ui/Badge';
import { ProgressBar } from '@/components/ui/Progress';
import { EmptyState, Skeleton } from '@/components/ui/Feedback';
import { DynamicIcon } from '@/components/ui/DynamicIcon';
import { PageHeader } from '@/components/feature/shared/PageHeader';

const COLOR_CLASSES: Record<string, string> = {
    primary: 'bg-primary-100 text-primary dark:bg-primary/20 dark:text-primary-300',
    secondary: 'bg-secondary-100 text-secondary-700 dark:bg-secondary/20 dark:text-secondary-300',
    success: 'bg-success/12 text-emerald-600 dark:text-emerald-400',
    info: 'bg-info/12 text-sky-600 dark:text-sky-400',
    danger: 'bg-danger/12 text-red-600 dark:text-red-400',
};

export default function FlashcardPage() {
    const { data: decks, loading } = useAsync(() => flashcardService.listDecks(), []);
    const { reviews, dueCount } = useFlashcardStore();

    const totalDue = dueCount();
    const totalStudied = Object.keys(reviews).length;

    return (
        <>
            <PageHeader
                title="Flashcard"
                description="Sistem spaced repetition (SM-2): kartu yang sulit muncul lebih sering, yang sudah kamu kuasai muncul makin jarang."
                action={
                    totalDue > 0 && (
                        <Button to="/app/flashcard/all" icon={<Play className="size-4" />}>
                            Belajar {totalDue} kartu
                        </Button>
                    )
                }
            />

            {/* Summary */}
            <div className="mb-6 grid gap-4 sm:grid-cols-3">
                <Card className="text-center">
                    <p className="font-display text-3xl font-extrabold text-secondary">{totalDue}</p>
                    <p className="mt-1 text-sm text-fg-muted">kartu jatuh tempo</p>
                </Card>
                <Card className="text-center">
                    <p className="font-display text-3xl font-extrabold text-primary">{totalStudied}</p>
                    <p className="mt-1 text-sm text-fg-muted">kartu pernah direview</p>
                </Card>
                <Card className="text-center">
                    <p className="font-display text-3xl font-extrabold text-success">{decks?.length ?? 0}</p>
                    <p className="mt-1 text-sm text-fg-muted">dek tersedia</p>
                </Card>
            </div>

            {loading ? (
                <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    {Array.from({ length: 5 }, (_, i) => (
                        <Skeleton key={i} className="h-48 w-full" />
                    ))}
                </div>
            ) : (decks ?? []).length === 0 ? (
                <EmptyState icon={<Layers className="size-6" />} title="Belum ada dek flashcard" />
            ) : (
                <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    {decks!.map((deck) => {
                        const studied = deck.cardIds.filter((id) => reviews[id]).length;
                        const percent = deck.cardIds.length === 0 ? 0 : Math.round((studied / deck.cardIds.length) * 100);
                        const due = flashcardService.listDue(deck.id, reviews, 999).length;

                        return (
                            <Link key={deck.id} to={`/app/flashcard/${deck.id}`} className="block h-full">
                                <Card interactive className="flex h-full flex-col">
                                    <div className="flex items-start justify-between gap-3">
                                        <span
                                            className={`grid size-11 shrink-0 place-items-center rounded-sm ${
                                                COLOR_CLASSES[deck.color] ?? COLOR_CLASSES.primary
                                            }`}
                                        >
                                            <DynamicIcon name={deck.icon} className="size-5" />
                                        </span>

                                        {due > 0 && <Badge tone="secondary">{due} due</Badge>}
                                    </div>

                                    <h3 className="mt-4 font-display text-base font-bold">{deck.title}</h3>
                                    <p className="mt-1.5 line-clamp-2 flex-1 text-sm text-fg-muted">{deck.description}</p>

                                    <div className="mt-4">
                                        <div className="mb-1.5 flex justify-between text-xs">
                                            <span className="text-fg-muted">
                                                {studied}/{deck.cardIds.length} kartu dikenali
                                            </span>
                                            <span className="font-mono">{percent}%</span>
                                        </div>
                                        <ProgressBar value={percent} size="sm" tone={percent === 100 ? 'success' : 'secondary'} />
                                    </div>
                                </Card>
                            </Link>
                        );
                    })}
                </div>
            )}

            <Card className="mt-8 flex flex-wrap items-center gap-4 bg-primary-50 dark:bg-primary/10">
                <Sparkles className="size-6 shrink-0 text-primary" />
                <div className="min-w-0 flex-1">
                    <p className="font-display font-bold">Cara kerja spaced repetition</p>
                    <p className="mt-0.5 text-sm text-fg-muted">
                        Setiap kartu punya jadwal sendiri. Jawab "Mudah" dan jeda berikutnya melar; jawab "Ulangi" dan
                        kartunya kembali hari ini juga. Konsisten 10 menit sehari mengalahkan hafalan semalam suntuk.
                    </p>
                </div>
            </Card>
        </>
    );
}

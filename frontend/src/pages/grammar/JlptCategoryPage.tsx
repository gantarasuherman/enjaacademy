import { Link, useParams } from 'react-router-dom';
import { ArrowRight } from 'lucide-react';
import { useAsync } from '@/hooks/useAsync';
import { jlptGrammarService } from '@/services/api';
import { Card, CardHeader } from '@/components/ui/Card';
import { Badge, type BadgeTone } from '@/components/ui/Badge';
import { EmptyState, PageLoader } from '@/components/ui/Feedback';
import { PageHeader } from '@/components/feature/shared/PageHeader';

const LEVEL_COLOR_TONE: Record<string, BadgeTone> = {
    emerald: 'success',
    sky: 'info',
    amber: 'warning',
    orange: 'secondary',
    rose: 'danger',
};

export default function JlptCategoryPage() {
    const { categoryId = '' } = useParams();
    const { data: category, loading: categoryLoading } = useAsync(
        () => jlptGrammarService.getCategory(categoryId),
        [categoryId],
    );
    const { data: patterns, loading: patternsLoading } = useAsync(
        () => jlptGrammarService.listPatterns(categoryId),
        [categoryId],
    );

    if (categoryLoading || patternsLoading) return <PageLoader />;

    if (!category) {
        return (
            <EmptyState
                title="Kategori tidak ditemukan"
                action={<Link to="/app/grammar" className="text-sm font-semibold text-primary">Kembali ke Grammar</Link>}
            />
        );
    }

    const level = category.level;

    return (
        <>
            <PageHeader
                backTo="/app/grammar"
                backLabel="Grammar"
                title={category.name}
                badge={level && <Badge tone={LEVEL_COLOR_TONE[level.color] ?? 'neutral'}>{level.name}</Badge>}
            />

            {category.children.length > 0 && (
                <Card className="mb-6">
                    <CardHeader title="Subkategori" subtitle={`${category.children.length} subkategori`} />
                    <div className="grid gap-4 sm:grid-cols-2">
                        {category.children.map((child) => (
                            <Link key={child.id} to={`/app/grammar/jlpt/${child.id}`} className="block h-full">
                                <Card interactive className="flex h-full flex-col">
                                    <h3 className="font-display text-base font-bold">{child.name}</h3>
                                    <p className="mt-2 flex-1 text-sm text-fg-muted">{child.patternsCount} pola</p>
                                    <span className="mt-3 inline-flex items-center gap-1 text-sm font-semibold text-primary">
                                        Lihat pola <ArrowRight className="size-3.5" />
                                    </span>
                                </Card>
                            </Link>
                        ))}
                    </div>
                </Card>
            )}

            <Card>
                <CardHeader title="Pola grammar" subtitle={`${patterns?.length ?? 0} pola di kategori ini`} />

                {!patterns || patterns.length === 0 ? (
                    <EmptyState title="Belum ada pola di kategori ini" />
                ) : (
                    <ul className="space-y-2">
                        {patterns.map((pattern) => (
                            <li key={pattern.id}>
                                <Link
                                    to={`/app/grammar/jlpt/pattern/${pattern.id}`}
                                    className="flex items-center justify-between gap-3 rounded-sm border border-[var(--surface-border)] p-3 transition hover:border-primary hover:bg-primary-50 dark:hover:bg-primary/10"
                                >
                                    <div className="min-w-0">
                                        <p className="truncate font-semibold">{pattern.title}</p>
                                        {pattern.titleRomaji && (
                                            <p className="truncate text-xs italic text-fg-muted">{pattern.titleRomaji}</p>
                                        )}
                                        {pattern.formula && (
                                            <p className="mt-0.5 truncate font-mono text-xs text-fg-muted">{pattern.formula}</p>
                                        )}
                                    </div>
                                    <ArrowRight className="size-4 shrink-0 text-fg-muted" />
                                </Link>
                            </li>
                        ))}
                    </ul>
                )}
            </Card>
        </>
    );
}

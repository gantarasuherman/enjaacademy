import { Link } from 'react-router-dom';
import { Clock, Headphones, Play } from 'lucide-react';
import { useAsync } from '@/hooks/useAsync';
import { listeningService } from '@/services/api';
import { formatDuration } from '@/utils/format';
import { Card } from '@/components/ui/Card';
import { Badge, CefrBadge } from '@/components/ui/Badge';
import { EmptyState, Skeleton } from '@/components/ui/Feedback';
import { PageHeader } from '@/components/feature/shared/PageHeader';

const ACCENT_LABEL: Record<string, string> = {
    american: '🇺🇸 Amerika',
    british: '🇬🇧 Britania',
    australian: '🇦🇺 Australia',
};

export default function ListeningPage() {
    const { data: tracks, loading } = useAsync(() => listeningService.list(), []);

    return (
        <>
            <PageHeader
                title="Listening"
                description="Latih telingamu dengan percakapan nyata. Setiap track punya transkrip, subtitle bilingual, dan kontrol kecepatan."
            />

            {loading ? (
                <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    {Array.from({ length: 3 }, (_, i) => (
                        <Skeleton key={i} className="h-48 w-full" />
                    ))}
                </div>
            ) : (tracks ?? []).length === 0 ? (
                <EmptyState icon={<Headphones className="size-6" />} title="Belum ada materi listening" />
            ) : (
                <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    {tracks!.map((track) => (
                        <Link key={track.id} to={`/app/listening/${track.id}`} className="block h-full">
                            <Card interactive className="flex h-full flex-col">
                                <div className="flex items-start justify-between gap-3">
                                    <span className="grid size-11 shrink-0 place-items-center rounded-sm bg-info/12 text-sky-600 dark:text-sky-400">
                                        <Headphones className="size-5" />
                                    </span>
                                    <CefrBadge level={track.cefr} />
                                </div>

                                <h3 className="mt-4 font-display text-base font-bold">{track.title}</h3>
                                <p className="mt-1.5 line-clamp-2 flex-1 text-sm text-fg-muted">{track.description}</p>

                                <div className="mt-4 flex flex-wrap items-center gap-3 text-xs text-fg-muted">
                                    <Badge tone="neutral">{ACCENT_LABEL[track.accent]}</Badge>
                                    <span className="flex items-center gap-1">
                                        <Clock className="size-3.5" />
                                        {formatDuration(track.durationSeconds)}
                                    </span>
                                </div>

                                <span className="mt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-primary">
                                    <Play className="size-3.5" /> Putar
                                </span>
                            </Card>
                        </Link>
                    ))}
                </div>
            )}
        </>
    );
}

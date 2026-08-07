import { Link } from 'react-router-dom';
import { MessagesSquare, Play } from 'lucide-react';
import { useAsync } from '@/hooks/useAsync';
import { conversationService } from '@/services/api';
import { Card } from '@/components/ui/Card';
import { CefrBadge } from '@/components/ui/Badge';
import { EmptyState, Skeleton } from '@/components/ui/Feedback';
import { DynamicIcon } from '@/components/ui/DynamicIcon';
import { PageHeader } from '@/components/feature/shared/PageHeader';

export default function ConversationPage() {
    const { data: scenarios, loading } = useAsync(() => conversationService.list(), []);

    return (
        <>
            <PageHeader
                title="Conversation"
                description="Latih percakapan situasional. Kamu memerankan salah satu pihak — dengarkan lawan bicara, lalu ucapkan giliranmu."
            />

            {loading ? (
                <div className="grid gap-4 sm:grid-cols-2">
                    {Array.from({ length: 4 }, (_, i) => (
                        <Skeleton key={i} className="h-44 w-full" />
                    ))}
                </div>
            ) : (scenarios ?? []).length === 0 ? (
                <EmptyState icon={<MessagesSquare className="size-6" />} title="Belum ada skenario percakapan" />
            ) : (
                <div className="grid gap-4 sm:grid-cols-2">
                    {scenarios!.map((scenario) => (
                        <Link key={scenario.id} to={`/app/conversation/${scenario.id}`} className="block h-full">
                            <Card interactive className="flex h-full flex-col">
                                <div className="flex items-start justify-between gap-3">
                                    <span className="grid size-11 shrink-0 place-items-center rounded-sm bg-info/12 text-sky-600 dark:text-sky-400">
                                        <DynamicIcon
                                            name={scenario.icon}
                                            fallback={MessagesSquare}
                                            className="size-5"
                                        />
                                    </span>
                                    <CefrBadge level={scenario.cefr} />
                                </div>

                                <h3 className="mt-4 font-display text-base font-bold">{scenario.title}</h3>
                                <p className="mt-1.5 flex-1 text-sm text-fg-muted">{scenario.context}</p>

                                <div className="mt-4 flex flex-wrap items-center gap-3 text-xs text-fg-muted">
                                    <span>{scenario.lines.length} baris dialog</span>
                                    <span>{scenario.lines.filter((line) => line.userTurn).length} giliranmu</span>
                                    <span>{scenario.keyPhrases.length} frasa kunci</span>
                                </div>

                                <span className="mt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-primary">
                                    <Play className="size-3.5" /> Mulai
                                </span>
                            </Card>
                        </Link>
                    ))}
                </div>
            )}
        </>
    );
}

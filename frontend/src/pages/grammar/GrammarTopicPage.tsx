import { useParams } from 'react-router-dom';
import { AlertTriangle, ArrowRight, Bookmark, BookmarkCheck, CheckCircle2, Volume2, XCircle } from 'lucide-react';
import { useAsync } from '@/hooks/useAsync';
import { useSpeechSynthesis } from '@/hooks/useSpeechSynthesis';
import { grammarService } from '@/services/api';
import { useProgressStore } from '@/store/progressStore';
import { splitHighlight } from '@/utils/format';
import { Card, CardHeader } from '@/components/ui/Card';
import { Button, IconButton } from '@/components/ui/Button';
import { CefrBadge } from '@/components/ui/Badge';
import { EmptyState, PageLoader } from '@/components/ui/Feedback';
import { PageHeader } from '@/components/feature/shared/PageHeader';

export default function GrammarTopicPage() {
    const { topicId = '' } = useParams();
    const { data: topic, loading } = useAsync(() => grammarService.getTopic(topicId), [topicId]);
    const { speak, supported } = useSpeechSynthesis();
    const { isBookmarked, toggleBookmark } = useProgressStore();

    if (loading) return <PageLoader />;

    if (!topic) {
        return (
            <EmptyState
                title="Topik tidak ditemukan"
                action={<Button to="/app/grammar">Kembali ke Grammar</Button>}
            />
        );
    }

    const bookmarked = isBookmarked('grammar', topic.id);

    return (
        <>
            <PageHeader
                backTo="/app/grammar"
                backLabel="Semua topik"
                title={topic.title}
                badge={<CefrBadge level={topic.cefr} />}
                action={
                    <Button
                        variant={bookmarked ? 'primary' : 'outline'}
                        icon={bookmarked ? <BookmarkCheck className="size-4" /> : <Bookmark className="size-4" />}
                        onClick={() =>
                            toggleBookmark({
                                kind: 'grammar',
                                refId: topic.id,
                                title: topic.title,
                                subtitle: `Grammar · ${topic.cefr}`,
                            })
                        }
                    >
                        {bookmarked ? 'Tersimpan' : 'Simpan'}
                    </Button>
                }
            />

            <div className="grid gap-6 lg:grid-cols-3">
                <div className="space-y-6 lg:col-span-2">
                    <Card>
                        <CardHeader title="Penjelasan" />
                        <p className="text-sm leading-7 text-fg-muted">
                            {topic.explanation.split(/(\*[^*]+\*)/g).map((chunk, index) =>
                                chunk.startsWith('*') && chunk.endsWith('*') ? (
                                    <em key={index} className="font-mono not-italic text-primary">
                                        {chunk.slice(1, -1)}
                                    </em>
                                ) : (
                                    <span key={index}>{chunk}</span>
                                ),
                            )}
                        </p>

                        {topic.formula && (
                            <div className="mt-4 rounded-sm border-l-4 border-l-primary bg-primary-50 p-4 dark:bg-primary/10">
                                <p className="text-xs font-bold uppercase tracking-wide text-primary">Pola</p>
                                <p className="mt-1 font-mono text-sm leading-relaxed">{topic.formula}</p>
                            </div>
                        )}
                    </Card>

                    <Card>
                        <CardHeader title="Contoh kalimat" subtitle={`${topic.examples.length} contoh dengan terjemahan`} />

                        <ul className="space-y-3">
                            {topic.examples.map((example, index) => {
                                const plain = example.sentence.replace(/\*\*/g, '');

                                return (
                                    <li
                                        key={index}
                                        className="flex items-start gap-3 rounded-sm border border-[var(--surface-border)] p-3"
                                    >
                                        <div className="min-w-0 flex-1">
                                            <p className="text-sm">
                                                {splitHighlight(example.sentence).map((chunk, i) =>
                                                    chunk.bold ? (
                                                        <strong
                                                            key={i}
                                                            className="rounded bg-secondary/20 px-1 font-semibold text-secondary-700 dark:text-secondary-300"
                                                        >
                                                            {chunk.text}
                                                        </strong>
                                                    ) : (
                                                        <span key={i}>{chunk.text}</span>
                                                    ),
                                                )}
                                            </p>
                                            <p className="mt-1 text-sm text-fg-muted">{example.meaning}</p>
                                        </div>

                                        {supported && (
                                            <IconButton
                                                label="Dengarkan"
                                                variant="ghost"
                                                size="sm"
                                                onClick={() => speak(plain)}
                                            >
                                                <Volume2 className="size-4" />
                                            </IconButton>
                                        )}
                                    </li>
                                );
                            })}
                        </ul>
                    </Card>
                </div>

                <div className="space-y-6">
                    {topic.commonMistakes.length > 0 && (
                        <Card>
                            <CardHeader
                                title="Kesalahan umum"
                                subtitle="Pola yang paling sering keliru dipakai."
                                action={<AlertTriangle className="size-5 text-warning" />}
                            />

                            <ul className="space-y-4">
                                {topic.commonMistakes.map((mistake, index) => (
                                    <li key={index} className="rounded-sm bg-surface-sunken p-3">
                                        <p className="flex items-start gap-2 text-sm text-danger">
                                            <XCircle className="mt-0.5 size-4 shrink-0" />
                                            <span className="line-through">{mistake.wrong}</span>
                                        </p>
                                        <p className="mt-1.5 flex items-start gap-2 text-sm font-semibold text-success">
                                            <CheckCircle2 className="mt-0.5 size-4 shrink-0" />
                                            {mistake.right}
                                        </p>
                                        <p className="mt-2 border-t border-[var(--surface-border)] pt-2 text-xs text-fg-muted">
                                            {mistake.why}
                                        </p>
                                    </li>
                                ))}
                            </ul>
                        </Card>
                    )}

                    <Button to="/app/quiz" fullWidth iconRight={<ArrowRight className="size-4" />}>
                        Latihan soal
                    </Button>
                </div>
            </div>
        </>
    );
}

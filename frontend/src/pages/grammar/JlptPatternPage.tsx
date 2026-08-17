import { useParams } from 'react-router-dom';
import { AlertTriangle, ArrowRight, CheckCircle2, Volume2, XCircle } from 'lucide-react';
import { useAsync } from '@/hooks/useAsync';
import { useSpeechSynthesis } from '@/hooks/useSpeechSynthesis';
import { jlptGrammarService } from '@/services/api';
import { Card, CardHeader } from '@/components/ui/Card';
import { Button, IconButton } from '@/components/ui/Button';
import { Badge, type BadgeTone } from '@/components/ui/Badge';
import { EmptyState, PageLoader } from '@/components/ui/Feedback';
import { PageHeader } from '@/components/feature/shared/PageHeader';

const LEVEL_NAME_TONE: Record<string, BadgeTone> = {
    N5: 'success',
    N4: 'info',
    N3: 'warning',
    N2: 'secondary',
    N1: 'danger',
    Beginner: 'success',
    Elementary: 'info',
    Intermediate: 'warning',
    'Upper-Intermediate': 'secondary',
    Advanced: 'danger',
};

export default function JlptPatternPage() {
    const { patternId = '' } = useParams();
    const { data: pattern, loading } = useAsync(() => jlptGrammarService.getPattern(patternId), [patternId]);
    const { speak, supported } = useSpeechSynthesis();

    if (loading) return <PageLoader />;

    if (!pattern) {
        return (
            <EmptyState
                title="Pola tidak ditemukan"
                action={<Button to="/app/grammar">Kembali ke Grammar</Button>}
            />
        );
    }

    return (
        <>
            <PageHeader
                backTo="/app/grammar"
                backLabel="Semua topik"
                title={pattern.title}
                badge={pattern.level && <Badge tone={LEVEL_NAME_TONE[pattern.level] ?? 'neutral'}>{pattern.level}</Badge>}
                description={
                    pattern.titleRomaji ? (
                        <>
                            <span className="italic text-fg-muted">{pattern.titleRomaji}</span>
                            {pattern.category && <span> · {pattern.category}</span>}
                        </>
                    ) : (
                        pattern.category
                    )
                }
            />

            <div className="grid gap-6 lg:grid-cols-3">
                <div className="space-y-6 lg:col-span-2">
                    <Card>
                        <CardHeader title="Penjelasan" />
                        <p className="text-sm leading-7 text-fg-muted">{pattern.explanation}</p>

                        {pattern.formula && (
                            <div className="mt-4 rounded-sm border-l-4 border-l-primary bg-primary-50 p-4 dark:bg-primary/10">
                                <p className="text-xs font-bold uppercase tracking-wide text-primary">Pola</p>
                                <p className="mt-1 font-mono text-sm leading-relaxed">{pattern.formula}</p>
                            </div>
                        )}
                    </Card>

                    <Card>
                        <CardHeader title="Contoh kalimat" subtitle={`${pattern.examples.length} contoh dengan terjemahan`} />

                        {pattern.examples.length === 0 ? (
                            <EmptyState title="Belum ada contoh" />
                        ) : (
                            <ul className="space-y-3">
                                {pattern.examples.map((example, index) => (
                                    <li
                                        key={index}
                                        className="flex items-start gap-3 rounded-sm border border-[var(--surface-border)] p-3"
                                    >
                                        <div className="min-w-0 flex-1">
                                            <p className="text-sm">{example.sentence}</p>
                                            {example.romaji && (
                                                <p className="mt-0.5 text-xs italic text-fg-muted">{example.romaji}</p>
                                            )}
                                            {example.translation && (
                                                <p className="mt-1 text-sm text-fg-muted">{example.translation}</p>
                                            )}
                                        </div>

                                        {supported && (
                                            <IconButton
                                                label="Dengarkan"
                                                variant="ghost"
                                                size="sm"
                                                onClick={() => speak(example.sentence)}
                                            >
                                                <Volume2 className="size-4" />
                                            </IconButton>
                                        )}
                                    </li>
                                ))}
                            </ul>
                        )}
                    </Card>
                </div>

                <div className="space-y-6">
                    {pattern.mistakes.length > 0 && (
                        <Card>
                            <CardHeader
                                title="Kesalahan umum"
                                subtitle="Pola yang paling sering keliru dipakai."
                                action={<AlertTriangle className="size-5 text-warning" />}
                            />

                            <ul className="space-y-4">
                                {pattern.mistakes.map((mistake, index) => (
                                    <li key={index} className="rounded-sm bg-surface-sunken p-3">
                                        <p className="flex items-start gap-2 text-sm text-danger">
                                            <XCircle className="mt-0.5 size-4 shrink-0" />
                                            <span className="line-through">{mistake.wrong}</span>
                                        </p>
                                        {mistake.wrongRomaji && (
                                            <p className="ml-6 text-xs italic text-fg-muted">{mistake.wrongRomaji}</p>
                                        )}
                                        {mistake.right && (
                                            <p className="mt-1.5 flex items-start gap-2 text-sm font-semibold text-success">
                                                <CheckCircle2 className="mt-0.5 size-4 shrink-0" />
                                                {mistake.right}
                                            </p>
                                        )}
                                        {mistake.rightRomaji && (
                                            <p className="ml-6 text-xs italic text-fg-muted">{mistake.rightRomaji}</p>
                                        )}
                                        {mistake.why && (
                                            <p className="mt-2 border-t border-[var(--surface-border)] pt-2 text-xs text-fg-muted">
                                                {mistake.why}
                                            </p>
                                        )}
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

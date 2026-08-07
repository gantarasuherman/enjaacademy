import { useParams } from 'react-router-dom';
import { ArrowRight, Bookmark, BookmarkCheck, Volume2 } from 'lucide-react';
import { useAsync } from '@/hooks/useAsync';
import { useSpeechSynthesis } from '@/hooks/useSpeechSynthesis';
import { grammarService } from '@/services/api';
import { useProgressStore } from '@/store/progressStore';
import { splitHighlight } from '@/utils/format';
import { Card, CardHeader } from '@/components/ui/Card';
import { Button, IconButton } from '@/components/ui/Button';
import { Badge } from '@/components/ui/Badge';
import { EmptyState, PageLoader } from '@/components/ui/Feedback';
import { PageHeader } from '@/components/feature/shared/PageHeader';

const GROUP_LABEL: Record<string, string> = {
    present: 'Present',
    past: 'Past',
    future: 'Future',
    perfect: 'Perfect',
};

/** Past → Present → Future strip that shows where the tense sits in time. */
function Timeline({ group }: { group: string }) {
    const points = ['Lampau', 'Sekarang', 'Depan'];

    const activeIndex = group === 'past' ? 0 : group === 'future' ? 2 : group === 'perfect' ? 1 : 1;

    return (
        <div className="relative py-6">
            <div className="absolute left-0 right-0 top-1/2 h-0.5 -translate-y-1/2 rounded bg-surface-sunken" />

            <div className="relative flex justify-between">
                {points.map((point, index) => {
                    const active = index === activeIndex;
                    // Perfect tenses span from the past up to the reference point.
                    const spanning = group === 'perfect' && index === 0;

                    return (
                        <div key={point} className="flex flex-col items-center gap-2">
                            <span
                                className={`size-3.5 rounded-full ring-4 ring-[var(--surface)] transition ${
                                    active ? 'bg-primary' : spanning ? 'bg-primary/40' : 'bg-surface-sunken'
                                }`}
                            />
                            <span className={`text-xs ${active ? 'font-bold text-primary' : 'text-fg-muted'}`}>
                                {point}
                            </span>
                        </div>
                    );
                })}
            </div>
        </div>
    );
}

export default function TensePage() {
    const { tenseId = '' } = useParams();
    const { data: tense, loading } = useAsync(() => grammarService.getTense(tenseId), [tenseId]);
    const { speak, supported } = useSpeechSynthesis();
    const { isBookmarked, toggleBookmark } = useProgressStore();

    if (loading) return <PageLoader />;

    if (!tense) {
        return (
            <EmptyState
                title="Tense tidak ditemukan"
                action={<Button to="/app/grammar">Kembali ke Grammar</Button>}
            />
        );
    }

    const bookmarked = isBookmarked('grammar', tense.id);

    return (
        <>
            <PageHeader
                backTo="/app/grammar"
                backLabel="Semua tense"
                title={tense.name}
                badge={<Badge tone="primary">{GROUP_LABEL[tense.group]}</Badge>}
                action={
                    <Button
                        variant={bookmarked ? 'primary' : 'outline'}
                        icon={bookmarked ? <BookmarkCheck className="size-4" /> : <Bookmark className="size-4" />}
                        onClick={() =>
                            toggleBookmark({
                                kind: 'grammar',
                                refId: tense.id,
                                title: tense.name,
                                subtitle: `Tense · ${GROUP_LABEL[tense.group]}`,
                            })
                        }
                    >
                        {bookmarked ? 'Tersimpan' : 'Simpan'}
                    </Button>
                }
            />

            <div className="grid gap-6 lg:grid-cols-3">
                <div className="space-y-6 lg:col-span-2">
                    {/* Formulas */}
                    <Card>
                        <CardHeader title="Rumus" subtitle="Tiga bentuk dasar yang harus dihafal." />

                        <div className="space-y-3">
                            {[
                                { label: 'Positif', value: tense.formula.positive, tone: 'border-l-success' },
                                { label: 'Negatif', value: tense.formula.negative, tone: 'border-l-danger' },
                                { label: 'Tanya', value: tense.formula.question, tone: 'border-l-info' },
                            ].map((row) => (
                                <div key={row.label} className={`rounded-sm border-l-4 bg-surface-sunken p-3 ${row.tone}`}>
                                    <p className="text-xs font-bold uppercase tracking-wide text-fg-muted">{row.label}</p>
                                    <p className="mt-1 font-mono text-sm">{row.value}</p>
                                </div>
                            ))}
                        </div>
                    </Card>

                    {/* Examples */}
                    <Card>
                        <CardHeader title="Contoh kalimat" />

                        <ul className="space-y-3">
                            {tense.examples.map((example, index) => {
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
                                                label="Dengarkan kalimat"
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
                    <Card>
                        <CardHeader title="Posisi waktu" />
                        <Timeline group={tense.group} />
                    </Card>

                    <Card>
                        <CardHeader title="Kapan dipakai" />
                        <ul className="space-y-2.5">
                            {tense.usage.map((item) => (
                                <li key={item} className="flex gap-2.5 text-sm">
                                    <span className="mt-1.5 size-1.5 shrink-0 rounded-full bg-primary" />
                                    {item}
                                </li>
                            ))}
                        </ul>
                    </Card>

                    <Card>
                        <CardHeader title="Kata penanda" subtitle="Petunjuk cepat saat mengerjakan soal." />
                        <div className="flex flex-wrap gap-1.5">
                            {tense.signalWords.map((word) => (
                                <Badge key={word} tone="secondary">
                                    {word}
                                </Badge>
                            ))}
                        </div>
                    </Card>

                    <Button to="/app/quiz" fullWidth iconRight={<ArrowRight className="size-4" />}>
                        Uji dengan kuis
                    </Button>
                </div>
            </div>
        </>
    );
}

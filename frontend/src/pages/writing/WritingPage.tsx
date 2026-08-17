import { useEffect, useMemo, useState } from 'react';
import { useParams } from 'react-router-dom';
import { CheckCircle2, Eye, EyeOff, Lightbulb, PenLine, Save, Trash2 } from 'lucide-react';
import { useAsync } from '@/hooks/useAsync';
import { countWords, writingService } from '@/services/api';
import { useUiStore } from '@/store/uiStore';
import { useProgressStore } from '@/store/progressStore';
import { cn } from '@/utils/cn';
import type { WritingTask } from '@/types';
import { Card, CardHeader } from '@/components/ui/Card';
import { Button } from '@/components/ui/Button';
import { Badge, CefrBadge } from '@/components/ui/Badge';
import { Textarea } from '@/components/ui/Input';
import { ProgressBar } from '@/components/ui/Progress';
import { Alert, EmptyState, Skeleton } from '@/components/ui/Feedback';
import { PageHeader } from '@/components/feature/shared/PageHeader';

const TYPE_LABEL: Record<WritingTask['type'], string> = {
    sentence: 'Kalimat',
    paragraph: 'Paragraf',
    essay: 'Esai',
};

const DRAFT_KEY = 'ea.writing.drafts';

/** Drafts survive reloads — losing an essay to a refresh is unforgivable. */
function loadDrafts(): Record<string, string> {
    try {
        return JSON.parse(localStorage.getItem(DRAFT_KEY) ?? '{}');
    } catch {
        return {};
    }
}

function saveDraft(taskId: string, body: string) {
    const drafts = loadDrafts();
    drafts[taskId] = body;
    localStorage.setItem(DRAFT_KEY, JSON.stringify(drafts));
}

export default function WritingPage() {
    const { taskId } = useParams();

    const { data: tasks, loading } = useAsync(() => writingService.list(), []);
    const [selectedId, setSelectedId] = useState<string | null>(taskId ?? null);
    const [body, setBody] = useState('');
    const [showSample, setShowSample] = useState(false);
    const [submitted, setSubmitted] = useState(false);

    const toast = useUiStore((state) => state.toast);
    const awardXp = useProgressStore((state) => state.awardXp);

    const task = useMemo(
        () => (tasks ?? []).find((item) => item.id === (selectedId ?? tasks?.[0]?.id)) ?? null,
        [tasks, selectedId],
    );

    // Restore the saved draft whenever the active task changes.
    useEffect(() => {
        if (!task) return;

        setBody(loadDrafts()[task.id] ?? '');
        setShowSample(false);
        setSubmitted(false);
    }, [task?.id]);

    // Autosave two seconds after typing stops.
    useEffect(() => {
        if (!task || !body) return;

        const timer = setTimeout(() => saveDraft(task.id, body), 2000);

        return () => clearTimeout(timer);
    }, [body, task]);

    const words = countWords(body);
    const withinRange = task ? words >= task.minWords && words <= task.maxWords : false;
    const rangePercent = task ? Math.min(100, Math.round((words / task.maxWords) * 100)) : 0;

    async function handleSubmit() {
        if (!task || !withinRange) return;

        saveDraft(task.id, body);
        setSubmitted(true);
        setShowSample(true);

        // Server decides the real amount — 0 on a repeat submission of a task
        // already rewarded once, so the local store never gets out of sync
        // with what actually persisted.
        const { earnedXp } = await writingService.submit(task.id, body);

        if (earnedXp > 0) {
            const { levelUp, level } = awardXp(earnedXp);
            toast(levelUp ? `+${earnedXp} XP — naik ke level ${level}!` : `+${earnedXp} XP — tulisan tersimpan.`, 'success', 'Kerja bagus');
        } else {
            toast('Tulisan tersimpan.', 'success', 'Kerja bagus');
        }
    }

    if (loading) {
        return (
            <>
                <PageHeader title="Writing" />
                <Skeleton className="h-96 w-full" />
            </>
        );
    }

    if (!task) {
        return <EmptyState icon={<PenLine className="size-6" />} title="Belum ada latihan menulis" />;
    }

    return (
        <>
            <PageHeader
                title="Writing"
                description="Latihan menulis dengan rubrik penilaian yang sama seperti TOEFL dan IELTS."
            />

            <div className="grid gap-6 lg:grid-cols-[16rem_1fr]">
                {/* Task list */}
                <aside className="space-y-2 lg:sticky lg:top-24 lg:self-start">
                    <p className="mb-2 text-xs font-bold uppercase tracking-wide text-fg-muted">Pilih latihan</p>

                    {(tasks ?? []).map((item) => (
                        <button
                            key={item.id}
                            type="button"
                            onClick={() => setSelectedId(item.id)}
                            className={cn(
                                'w-full rounded-sm border p-3 text-left transition',
                                item.id === task.id
                                    ? 'border-primary bg-primary-50 dark:bg-primary/10'
                                    : 'border-[var(--surface-border)] hover:border-primary-300 hover:bg-surface-sunken',
                            )}
                        >
                            <div className="flex items-center justify-between gap-2">
                                <Badge tone="neutral">{TYPE_LABEL[item.type]}</Badge>
                                <CefrBadge level={item.cefr} />
                            </div>
                            <p className="mt-2 line-clamp-2 text-sm font-medium">{item.prompt}</p>
                            <p className="mt-1 text-[11px] text-fg-muted">
                                {item.minWords}–{item.maxWords} kata
                            </p>
                        </button>
                    ))}
                </aside>

                {/* Editor */}
                <div className="space-y-6">
                    <Card>
                        <CardHeader
                            title="Instruksi"
                            subtitle={`${TYPE_LABEL[task.type]} · ${task.minWords}–${task.maxWords} kata`}
                            action={<CefrBadge level={task.cefr} />}
                        />
                        <p className="text-sm leading-relaxed">{task.prompt}</p>
                    </Card>

                    <Card>
                        <CardHeader
                            title="Tulisanmu"
                            action={
                                <span
                                    className={cn(
                                        'font-mono text-sm font-semibold',
                                        withinRange ? 'text-success' : words > task.maxWords ? 'text-danger' : 'text-fg-muted',
                                    )}
                                >
                                    {words} / {task.minWords}–{task.maxWords}
                                </span>
                            }
                        />

                        <Textarea
                            value={body}
                            onChange={(event) => setBody(event.target.value)}
                            placeholder="Mulai menulis di sini… Draf tersimpan otomatis."
                            className="min-h-64 font-sans leading-7"
                        />

                        <ProgressBar
                            value={rangePercent}
                            tone={withinRange ? 'success' : words > task.maxWords ? 'danger' : 'secondary'}
                            size="sm"
                            className="mt-3"
                        />

                        {words > task.maxWords && (
                            <Alert tone="warning" className="mt-3">
                                Tulisanmu {words - task.maxWords} kata melebihi batas. Ringkas kalimat yang berulang.
                            </Alert>
                        )}

                        <div className="mt-4 flex flex-wrap gap-2">
                            <Button
                                onClick={() => void handleSubmit()}
                                disabled={!withinRange}
                                icon={<CheckCircle2 className="size-4" />}
                            >
                                Kumpulkan
                            </Button>
                            <Button
                                variant="outline"
                                icon={<Save className="size-4" />}
                                onClick={() => {
                                    saveDraft(task.id, body);
                                    toast('Draf tersimpan.');
                                }}
                            >
                                Simpan draf
                            </Button>
                            <Button
                                variant="ghost"
                                icon={showSample ? <EyeOff className="size-4" /> : <Eye className="size-4" />}
                                onClick={() => setShowSample((v) => !v)}
                            >
                                {showSample ? 'Sembunyikan contoh' : 'Lihat contoh jawaban'}
                            </Button>
                            {body && (
                                <Button
                                    variant="ghost"
                                    icon={<Trash2 className="size-4" />}
                                    onClick={() => {
                                        setBody('');
                                        saveDraft(task.id, '');
                                    }}
                                >
                                    Kosongkan
                                </Button>
                            )}
                        </div>

                        {submitted && (
                            <Alert tone="success" title="Tulisan dikumpulkan" className="mt-4">
                                Bandingkan dengan contoh jawaban di bawah, lalu nilai sendiri memakai rubrik.
                            </Alert>
                        )}
                    </Card>

                    <div className="grid gap-6 md:grid-cols-2">
                        <Card>
                            <CardHeader title="Petunjuk" action={<Lightbulb className="size-5 text-warning" />} />
                            <ul className="space-y-2.5">
                                {task.hints.map((hint) => (
                                    <li key={hint} className="flex gap-2.5 text-sm">
                                        <span className="mt-1.5 size-1.5 shrink-0 rounded-full bg-secondary" />
                                        {hint}
                                    </li>
                                ))}
                            </ul>
                        </Card>

                        <Card>
                            <CardHeader title="Rubrik penilaian" subtitle="Nilai tulisanmu sendiri dengan kriteria ini." />
                            <ul className="space-y-3">
                                {task.rubric.map((criterion) => (
                                    <li key={criterion.criterion}>
                                        <div className="flex items-baseline justify-between gap-2">
                                            <p className="text-sm font-semibold">{criterion.criterion}</p>
                                            <span className="font-mono text-xs text-fg-muted">{criterion.weight}%</span>
                                        </div>
                                        <p className="mt-0.5 text-xs text-fg-muted">{criterion.description}</p>
                                    </li>
                                ))}
                            </ul>
                        </Card>
                    </div>

                    {showSample && (
                        <Card className="bg-surface-sunken">
                            <CardHeader
                                title="Contoh jawaban"
                                subtitle="Satu cara mengerjakannya — bukan satu-satunya jawaban benar."
                            />
                            <div className="space-y-3 text-sm leading-7">
                                {task.sampleAnswer.split('\n\n').map((paragraph, index) => (
                                    <p key={index}>{paragraph}</p>
                                ))}
                            </div>
                        </Card>
                    )}
                </div>
            </div>
        </>
    );
}

import { Award, Download, Lock, ScrollText } from 'lucide-react';
import { useAsync } from '@/hooks/useAsync';
import { userService } from '@/services/api';
import { useAuthStore } from '@/store/authStore';
import { useUiStore } from '@/store/uiStore';
import { formatDate } from '@/utils/format';
import { cn } from '@/utils/cn';
import { Card, CardHeader } from '@/components/ui/Card';
import { Button } from '@/components/ui/Button';
import { CefrBadge } from '@/components/ui/Badge';
import { ProgressBar } from '@/components/ui/Progress';
import { EmptyState, Skeleton } from '@/components/ui/Feedback';
import { PageHeader } from '@/components/feature/shared/PageHeader';

export default function CertificatePage() {
    const { data: certificates, loading } = useAsync(() => userService.listCertificates(), []);
    const user = useAuthStore((state) => state.user);
    const toast = useUiStore((state) => state.toast);

    const earned = (certificates ?? []).filter((certificate) => certificate.issuedAt);

    return (
        <>
            <PageHeader
                title="Certificate"
                description="Sertifikat terbit otomatis begitu kamu menyelesaikan seluruh materi sebuah modul dan lulus kuisnya."
                action={
                    <div className="rounded-sm bg-surface-sunken px-4 py-2 text-center">
                        <p className="font-display text-lg font-extrabold">
                            {earned.length}/{certificates?.length ?? 0}
                        </p>
                        <p className="text-[11px] text-fg-muted">terbit</p>
                    </div>
                }
            />

            {loading ? (
                <div className="grid gap-5 sm:grid-cols-2">
                    {Array.from({ length: 4 }, (_, i) => (
                        <Skeleton key={i} className="h-64 w-full" />
                    ))}
                </div>
            ) : (certificates ?? []).length === 0 ? (
                <EmptyState icon={<ScrollText className="size-6" />} title="Belum ada sertifikat tersedia" />
            ) : (
                <div className="grid gap-5 sm:grid-cols-2">
                    {certificates!.map((certificate) => {
                        const issued = !!certificate.issuedAt;

                        return (
                            <Card
                                key={certificate.id}
                                padded={false}
                                className={cn('overflow-hidden', !issued && 'opacity-90')}
                            >
                                {/* Certificate face */}
                                <div
                                    className={cn(
                                        'relative p-6 text-center',
                                        issued
                                            ? 'bg-gradient-to-br from-primary to-primary-700 text-white'
                                            : 'bg-surface-sunken',
                                    )}
                                >
                                    {issued && (
                                        <>
                                            <div
                                                aria-hidden
                                                className="absolute -right-10 -top-10 size-32 rounded-full bg-white/8"
                                            />
                                            <div
                                                aria-hidden
                                                className="absolute -bottom-12 -left-8 size-32 rounded-full bg-secondary/25"
                                            />
                                        </>
                                    )}

                                    <div className="relative">
                                        <span
                                            className={cn(
                                                'mx-auto grid size-14 place-items-center rounded-full',
                                                issued ? 'bg-white/15' : 'bg-surface text-fg-muted',
                                            )}
                                        >
                                            {issued ? <Award className="size-7" /> : <Lock className="size-6" />}
                                        </span>

                                        <p
                                            className={cn(
                                                'mt-4 text-[11px] font-bold uppercase tracking-[0.2em]',
                                                issued ? 'text-white/70' : 'text-fg-muted',
                                            )}
                                        >
                                            Sertifikat Penyelesaian
                                        </p>

                                        <h3
                                            className={cn(
                                                'mt-2 font-display text-xl font-extrabold',
                                                !issued && 'text-fg',
                                            )}
                                        >
                                            {certificate.title}
                                        </h3>

                                        {issued && (
                                            <>
                                                <p className="mt-3 text-sm text-white/80">Diberikan kepada</p>
                                                <p className="font-display text-lg font-bold">{user?.name ?? 'Peserta'}</p>
                                                <p className="mt-3 text-xs text-white/70">
                                                    Terbit {formatDate(certificate.issuedAt!)}
                                                </p>
                                            </>
                                        )}
                                    </div>
                                </div>

                                {/* Footer */}
                                <div className="p-5">
                                    <div className="mb-3 flex items-center justify-between gap-2">
                                        <CefrBadge level={certificate.cefr} />
                                        <span className="font-mono text-xs text-fg-muted">
                                            {certificate.progressPercent}%
                                        </span>
                                    </div>

                                    <ProgressBar
                                        value={certificate.progressPercent}
                                        size="sm"
                                        tone={issued ? 'success' : 'secondary'}
                                    />

                                    <p className="mt-3 text-xs text-fg-muted">{certificate.requirement}</p>

                                    {issued ? (
                                        <Button
                                            fullWidth
                                            variant="outline"
                                            className="mt-4"
                                            icon={<Download className="size-4" />}
                                            onClick={() =>
                                                toast(
                                                    'Unduhan PDF akan tersedia saat backend Laravel aktif.',
                                                    'info',
                                                    'Segera hadir',
                                                )
                                            }
                                        >
                                            Unduh sertifikat
                                        </Button>
                                    ) : (
                                        <Button
                                            fullWidth
                                            variant="ghost"
                                            className="mt-4"
                                            to={`/app/learning/${certificate.moduleId}`}
                                        >
                                            Lanjutkan modul
                                        </Button>
                                    )}
                                </div>
                            </Card>
                        );
                    })}
                </div>
            )}

            <Card className="mt-8">
                <CardHeader
                    title="Bagaimana sertifikat diterbitkan?"
                    subtitle="Tiga syarat yang harus dipenuhi untuk setiap modul."
                />
                <ol className="space-y-3 text-sm">
                    {[
                        'Selesaikan seluruh materi di dalam modul.',
                        'Lulus kuis penutup dengan skor minimal yang tertera.',
                        'Sertifikat terbit otomatis — tidak perlu mengajukan apa pun.',
                    ].map((step, index) => (
                        <li key={step} className="flex gap-3">
                            <span className="grid size-5 shrink-0 place-items-center rounded-full bg-primary-100 text-[11px] font-bold text-primary dark:bg-primary/20">
                                {index + 1}
                            </span>
                            {step}
                        </li>
                    ))}
                </ol>
            </Card>
        </>
    );
}

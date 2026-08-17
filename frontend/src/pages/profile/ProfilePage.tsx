import { useState, type FormEvent } from 'react';
import { Award, Calendar, Flame, Mail, Save, Target, User as UserIcon, Zap } from 'lucide-react';
import { useAuthStore } from '@/store/authStore';
import { useProgressStore } from '@/store/progressStore';
import { useUiStore } from '@/store/uiStore';
import { userService } from '@/services/api';
import { levelInfo } from '@/utils/gamification';
import { formatDate, formatNumber } from '@/utils/format';
import { Card, CardHeader } from '@/components/ui/Card';
import { Button } from '@/components/ui/Button';
import { Input, Select } from '@/components/ui/Input';
import { Avatar } from '@/components/ui/Avatar';
import { Badge } from '@/components/ui/Badge';
import { ProgressBar, ProgressRing } from '@/components/ui/Progress';
import { PageHeader } from '@/components/feature/shared/PageHeader';
import { StatCard } from '@/components/feature/shared/StatCard';

const LEVELS = ['Beginner', 'Elementary', 'Intermediate', 'Upper-Intermediate', 'Advanced'].map((level) => ({
    value: level,
    label: level,
}));

const GOALS = [10, 15, 20, 30, 45, 60].map((minutes) => ({
    value: String(minutes),
    label: `${minutes} menit / hari`,
}));

export default function ProfilePage() {
    const { user, patchUser } = useAuthStore();
    const { xp, unlocked, streakDays, lessonsCompleted } = useProgressStore();
    const toast = useUiStore((state) => state.toast);

    const [form, setForm] = useState({
        name: user?.name ?? '',
        email: user?.email ?? '',
        targetLevel: user?.targetLevel ?? 'Intermediate',
        dailyGoalMinutes: String(user?.dailyGoalMinutes ?? 20),
    });
    const [saving, setSaving] = useState(false);

    const info = levelInfo(xp);
    const streak = streakDays;
    const completedLessons = lessonsCompleted;
    const unlockedCount = unlocked.filter((item) => item.unlockedAt).length;

    async function handleSubmit(event: FormEvent) {
        event.preventDefault();
        setSaving(true);

        const patch = {
            name: form.name,
            email: form.email,
            targetLevel: form.targetLevel as typeof form.targetLevel & never,
            dailyGoalMinutes: Number(form.dailyGoalMinutes),
        };

        try {
            await userService.updateProfile(patch as never);
            patchUser(patch as never);
            toast('Profil diperbarui.', 'success');
        } catch {
            toast('Gagal menyimpan profil.', 'danger');
        } finally {
            setSaving(false);
        }
    }

    return (
        <>
            <PageHeader title="Profil" description="Kelola identitas dan target belajarmu." />

            <div className="grid gap-6 lg:grid-cols-3">
                {/* Identity + stats */}
                <div className="space-y-6">
                    <Card className="text-center">
                        <Avatar name={user?.name ?? 'Tamu'} src={user?.avatar} size="xl" ring className="mx-auto" />

                        <h2 className="mt-4 font-display text-xl font-extrabold">{user?.name ?? 'Tamu'}</h2>
                        <p className="mt-0.5 text-sm text-fg-muted">{user?.email}</p>

                        <div className="mt-3 flex flex-wrap justify-center gap-1.5">
                            <Badge tone="primary">Level {info.level}</Badge>
                            <Badge tone="secondary" icon={<Flame className="size-3" />}>
                                {streak} hari
                            </Badge>
                            <Badge tone="info">Target {user?.targetLevel ?? 'Intermediate'}</Badge>
                        </div>

                        {user?.joinedAt && (
                            <p className="mt-4 flex items-center justify-center gap-1.5 text-xs text-fg-muted">
                                <Calendar className="size-3.5" />
                                Bergabung {formatDate(user.joinedAt)}
                            </p>
                        )}
                    </Card>

                    <Card className="text-center">
                        <CardHeader title="Progres level" className="justify-center text-center" />
                        <ProgressRing value={info.percent} size={128} stroke={10} tone="secondary">
                            <div>
                                <p className="font-display text-2xl font-extrabold">{info.level}</p>
                                <p className="text-[11px] text-fg-muted">level</p>
                            </div>
                        </ProgressRing>
                        <ProgressBar value={info.percent} tone="secondary" className="mt-4" />
                        <p className="mt-2 text-xs text-fg-muted">
                            {formatNumber(info.xpIntoLevel)} / {formatNumber(info.xpForNextLevel)} XP
                        </p>
                    </Card>
                </div>

                {/* Form + stats */}
                <div className="space-y-6 lg:col-span-2">
                    <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <StatCard icon={Zap} label="Total XP" value={formatNumber(xp)} tone="secondary" />
                        <StatCard icon={Flame} label="Streak" value={streak} hint="hari" tone="danger" />
                        <StatCard icon={UserIcon} label="Materi" value={completedLessons} hint="selesai" tone="success" />
                        <StatCard icon={Award} label="Lencana" value={unlockedCount} hint="terbuka" tone="info" />
                    </div>

                    <Card>
                        <CardHeader title="Informasi akun" subtitle="Perubahan langsung tersimpan di perangkat ini." />

                        <form onSubmit={handleSubmit} className="space-y-4">
                            <Input
                                label="Nama lengkap"
                                icon={<UserIcon className="size-4" />}
                                value={form.name}
                                onChange={(event) => setForm({ ...form, name: event.target.value })}
                                required
                            />

                            <Input
                                type="email"
                                label="Email"
                                icon={<Mail className="size-4" />}
                                value={form.email}
                                onChange={(event) => setForm({ ...form, email: event.target.value })}
                                required
                            />

                            <div className="grid gap-4 sm:grid-cols-2">
                                <Select
                                    label="Target level CEFR"
                                    hint="Menentukan materi yang direkomendasikan."
                                    options={LEVELS}
                                    value={form.targetLevel}
                                    onChange={(event) => setForm({ ...form, targetLevel: event.target.value as never })}
                                />

                                <Select
                                    label="Target harian"
                                    hint="Dipakai untuk cincin progres di dashboard."
                                    options={GOALS}
                                    value={form.dailyGoalMinutes}
                                    onChange={(event) => setForm({ ...form, dailyGoalMinutes: event.target.value })}
                                />
                            </div>

                            <Button type="submit" loading={saving} icon={<Save className="size-4" />}>
                                Simpan perubahan
                            </Button>
                        </form>
                    </Card>

                    <Card className="bg-primary-50 dark:bg-primary/10">
                        <div className="flex items-start gap-3">
                            <Target className="size-5 shrink-0 text-primary" />
                            <div>
                                <p className="text-sm font-bold">Kenapa target harian kecil lebih baik</p>
                                <p className="mt-1 text-sm text-fg-muted">
                                    Target yang bisa kamu penuhi bahkan di hari terburuk adalah target yang bertahan.
                                    Sepuluh menit setiap hari mengalahkan dua jam sekali seminggu.
                                </p>
                            </div>
                        </div>
                    </Card>
                </div>
            </div>
        </>
    );
}

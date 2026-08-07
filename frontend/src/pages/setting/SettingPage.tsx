import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import {
    Bell,
    Database,
    Download,
    LogOut,
    Monitor,
    Moon,
    Palette,
    Sun,
    Trash2,
    Volume2,
} from 'lucide-react';
import { useThemeStore, type Theme } from '@/store/themeStore';
import { useAuthStore } from '@/store/authStore';
import { useProgressStore } from '@/store/progressStore';
import { useFlashcardStore } from '@/store/flashcardStore';
import { useUiStore } from '@/store/uiStore';
import { DATA_SOURCE } from '@/services/api';
import { cn } from '@/utils/cn';
import { Card, CardHeader } from '@/components/ui/Card';
import { Button } from '@/components/ui/Button';
import { Select, Toggle } from '@/components/ui/Input';
import { Modal } from '@/components/ui/Modal';
import { Badge } from '@/components/ui/Badge';
import { Alert } from '@/components/ui/Feedback';
import { PageHeader } from '@/components/feature/shared/PageHeader';

const THEMES: { id: Theme; label: string; icon: typeof Sun }[] = [
    { id: 'light', label: 'Terang', icon: Sun },
    { id: 'dark', label: 'Gelap', icon: Moon },
    { id: 'system', label: 'Ikuti sistem', icon: Monitor },
];

export default function SettingPage() {
    const { theme, setTheme } = useThemeStore();
    const { logout } = useAuthStore();
    const toast = useUiStore((state) => state.toast);
    const navigate = useNavigate();

    const [sound, setSound] = useState(true);
    const [autoplay, setAutoplay] = useState(false);
    const [reminder, setReminder] = useState(true);
    const [reminderTime, setReminderTime] = useState('19:30');
    const [locale, setLocale] = useState('id');
    const [resetOpen, setResetOpen] = useState(false);

    /** Exports every local store so a learner can move devices. */
    function exportData() {
        const payload = {
            exportedAt: new Date().toISOString(),
            auth: JSON.parse(localStorage.getItem('ea.auth') ?? 'null'),
            progress: JSON.parse(localStorage.getItem('ea.progress') ?? 'null'),
            flashcards: JSON.parse(localStorage.getItem('ea.flashcards') ?? 'null'),
            writingDrafts: JSON.parse(localStorage.getItem('ea.writing.drafts') ?? 'null'),
        };

        const blob = new Blob([JSON.stringify(payload, null, 2)], { type: 'application/json' });
        const url = URL.createObjectURL(blob);

        const link = document.createElement('a');
        link.href = url;
        link.download = `english-academy-data-${new Date().toISOString().slice(0, 10)}.json`;
        link.click();

        URL.revokeObjectURL(url);
        toast('Data berhasil diunduh.', 'success');
    }

    function resetProgress() {
        useProgressStore.persist.clearStorage();
        useFlashcardStore.persist.clearStorage();
        localStorage.removeItem('ea.writing.drafts');

        setResetOpen(false);
        toast('Progres direset. Halaman akan dimuat ulang.', 'warning');

        setTimeout(() => window.location.reload(), 900);
    }

    return (
        <>
            <PageHeader title="Pengaturan" description="Tampilan, suara, pengingat, dan pengelolaan data belajarmu." />

            <div className="grid gap-6 lg:grid-cols-2">
                {/* Appearance */}
                <Card>
                    <CardHeader
                        title="Tampilan"
                        subtitle="Tema mengikuti sistem secara default."
                        action={<Palette className="size-5 text-fg-muted" />}
                    />

                    <div className="grid grid-cols-3 gap-2">
                        {THEMES.map((item) => {
                            const Icon = item.icon;
                            const active = theme === item.id;

                            return (
                                <button
                                    key={item.id}
                                    type="button"
                                    onClick={() => setTheme(item.id)}
                                    className={cn(
                                        'flex flex-col items-center gap-2 rounded-sm border-2 p-4 text-sm font-medium transition',
                                        active
                                            ? 'border-primary bg-primary-50 text-primary dark:bg-primary/12'
                                            : 'border-[var(--surface-border)] text-fg-muted hover:border-primary-300',
                                    )}
                                >
                                    <Icon className="size-5" />
                                    {item.label}
                                </button>
                            );
                        })}
                    </div>

                    <div className="mt-5 border-t border-[var(--surface-border)] pt-4">
                        <Select
                            label="Bahasa antarmuka"
                            options={[
                                { value: 'id', label: 'Bahasa Indonesia' },
                                { value: 'en', label: 'English' },
                            ]}
                            value={locale}
                            onChange={(event) => {
                                setLocale(event.target.value);
                                toast('Bahasa antarmuka akan aktif penuh saat backend terhubung.', 'info');
                            }}
                        />
                    </div>
                </Card>

                {/* Audio */}
                <Card>
                    <CardHeader
                        title="Suara"
                        subtitle="Efek suara dan pemutaran otomatis."
                        action={<Volume2 className="size-5 text-fg-muted" />}
                    />

                    <div className="space-y-5">
                        <Toggle
                            checked={sound}
                            onChange={setSound}
                            label="Efek suara"
                            description="Bunyi saat jawaban benar atau salah di kuis."
                        />
                        <Toggle
                            checked={autoplay}
                            onChange={setAutoplay}
                            label="Putar audio otomatis"
                            description="Kata langsung dibacakan saat kartu atau detail dibuka."
                        />
                    </div>
                </Card>

                {/* Reminders */}
                <Card>
                    <CardHeader
                        title="Pengingat"
                        subtitle="Notifikasi harian agar streak tidak putus."
                        action={<Bell className="size-5 text-fg-muted" />}
                    />

                    <div className="space-y-5">
                        <Toggle
                            checked={reminder}
                            onChange={setReminder}
                            label="Pengingat harian"
                            description="Kirim notifikasi jika target harian belum tercapai."
                        />

                        {reminder && (
                            <div>
                                <label htmlFor="reminder-time" className="mb-1.5 block text-sm font-medium">
                                    Waktu pengingat
                                </label>
                                <input
                                    id="reminder-time"
                                    type="time"
                                    value={reminderTime}
                                    onChange={(event) => setReminderTime(event.target.value)}
                                    className="rounded-sm border border-[var(--surface-border)] bg-surface px-3 py-2 text-sm focus:border-primary focus:outline-none"
                                />
                            </div>
                        )}
                    </div>
                </Card>

                {/* Data */}
                <Card>
                    <CardHeader
                        title="Data"
                        subtitle="Progres tersimpan di perangkat ini."
                        action={<Database className="size-5 text-fg-muted" />}
                    />

                    <Alert tone="info" className="mb-4">
                        Sumber data saat ini:{' '}
                        <Badge tone={DATA_SOURCE === 'mock' ? 'warning' : 'success'}>{DATA_SOURCE}</Badge>{' '}
                        {DATA_SOURCE === 'mock'
                            ? '— membaca JSON bawaan. Ubah VITE_DATA_SOURCE=api untuk terhubung ke Laravel.'
                            : '— terhubung ke backend Laravel.'}
                    </Alert>

                    <div className="space-y-2">
                        <Button fullWidth variant="outline" icon={<Download className="size-4" />} onClick={exportData}>
                            Unduh data belajarku
                        </Button>

                        <Button
                            fullWidth
                            variant="outline"
                            className="border-danger/40 text-danger hover:bg-danger/8"
                            icon={<Trash2 className="size-4" />}
                            onClick={() => setResetOpen(true)}
                        >
                            Reset semua progres
                        </Button>
                    </div>
                </Card>
            </div>

            {/* Account */}
            <Card className="mt-6">
                <CardHeader title="Akun" subtitle="Keluar dari sesi ini." />
                <Button
                    variant="outline"
                    className="border-danger/40 text-danger hover:bg-danger/8"
                    icon={<LogOut className="size-4" />}
                    onClick={async () => {
                        await logout();
                        navigate('/');
                    }}
                >
                    Keluar
                </Button>
            </Card>

            <Modal
                open={resetOpen}
                onClose={() => setResetOpen(false)}
                title="Reset semua progres?"
                description="Tindakan ini tidak bisa dibatalkan."
                footer={
                    <>
                        <Button variant="outline" onClick={() => setResetOpen(false)}>
                            Batal
                        </Button>
                        <Button variant="danger" onClick={resetProgress}>
                            Ya, reset semuanya
                        </Button>
                    </>
                }
            >
                <Alert tone="danger" title="Yang akan dihapus">
                    <ul className="mt-1 list-inside list-disc space-y-0.5">
                        <li>XP, level, dan riwayat aktivitas</li>
                        <li>Status penyelesaian semua materi</li>
                        <li>Jadwal spaced repetition flashcard</li>
                        <li>Bookmark dan draf tulisan</li>
                    </ul>
                </Alert>

                <p className="mt-4 text-sm text-fg-muted">
                    Sebaiknya unduh datamu dulu lewat tombol "Unduh data belajarku" sebelum melanjutkan.
                </p>
            </Modal>
        </>
    );
}

import { useState, type FormEvent } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { Lock, Mail, User } from 'lucide-react';
import { useAuthStore } from '@/store/authStore';
import { useUiStore } from '@/store/uiStore';
import { Button } from '@/components/ui/Button';
import { Checkbox, Input, Select } from '@/components/ui/Input';
import { Alert } from '@/components/ui/Feedback';

const LEVELS = [
    { value: 'Beginner', label: 'Beginner — Baru mulai dari nol' },
    { value: 'Elementary', label: 'Elementary — Bisa kalimat sederhana' },
    { value: 'Intermediate', label: 'Intermediate — Cukup untuk percakapan harian' },
    { value: 'Upper-Intermediate', label: 'Upper-Intermediate — Lancar untuk kerja' },
    { value: 'Advanced', label: 'Advanced — Mendekati penutur asli' },
];

export default function RegisterPage() {
    const { register, status, error, clearError } = useAuthStore();
    const toast = useUiStore((state) => state.toast);
    const navigate = useNavigate();

    const [form, setForm] = useState({ name: '', email: '', password: '', confirm: '', target: 'Intermediate' });
    const [agreed, setAgreed] = useState(false);
    const [localError, setLocalError] = useState<string | null>(null);

    const update = (key: keyof typeof form) => (event: { target: { value: string } }) =>
        setForm((prev) => ({ ...prev, [key]: event.target.value }));

    async function handleSubmit(event: FormEvent) {
        event.preventDefault();
        clearError();
        setLocalError(null);

        if (form.password.length < 6) {
            setLocalError('Kata sandi minimal 6 karakter.');
            return;
        }

        if (form.password !== form.confirm) {
            setLocalError('Konfirmasi kata sandi tidak cocok.');
            return;
        }

        if (!agreed) {
            setLocalError('Kamu harus menyetujui ketentuan layanan.');
            return;
        }

        if (await register(form.name, form.email, form.password)) {
            toast('Akun berhasil dibuat. Selamat belajar!', 'success', 'Halo, ' + form.name);
            navigate('/app/dashboard', { replace: true });
        }
    }

    return (
        <div>
            <h1 className="font-display text-2xl font-extrabold">Buat akun gratis</h1>
            <p className="mt-1.5 text-sm text-fg-muted">
                Sudah punya akun?{' '}
                <Link to="/login" className="font-semibold text-primary hover:underline">
                    Masuk di sini
                </Link>
            </p>

            {(error || localError) && (
                <Alert tone="danger" className="mt-5">
                    {localError ?? error}
                </Alert>
            )}

            <form onSubmit={handleSubmit} className="mt-6 space-y-4">
                <Input
                    label="Nama lengkap"
                    placeholder="Nama kamu"
                    autoComplete="name"
                    required
                    icon={<User className="size-4" />}
                    value={form.name}
                    onChange={update('name')}
                />

                <Input
                    type="email"
                    label="Email"
                    placeholder="nama@email.com"
                    autoComplete="email"
                    required
                    icon={<Mail className="size-4" />}
                    value={form.email}
                    onChange={update('email')}
                />

                <Input
                    type="password"
                    label="Kata sandi"
                    placeholder="Minimal 6 karakter"
                    autoComplete="new-password"
                    required
                    icon={<Lock className="size-4" />}
                    value={form.password}
                    onChange={update('password')}
                />

                <Input
                    type="password"
                    label="Ulangi kata sandi"
                    placeholder="••••••••"
                    autoComplete="new-password"
                    required
                    icon={<Lock className="size-4" />}
                    value={form.confirm}
                    onChange={update('confirm')}
                />

                <Select
                    label="Target levelmu"
                    hint="Jalur belajar akan disesuaikan dengan target ini."
                    options={LEVELS}
                    value={form.target}
                    onChange={update('target')}
                />

                <Checkbox
                    label="Saya menyetujui Ketentuan Layanan dan Kebijakan Privasi"
                    checked={agreed}
                    onChange={(event) => setAgreed(event.target.checked)}
                />

                <Button type="submit" fullWidth size="lg" loading={status === 'loading'}>
                    Daftar Sekarang
                </Button>
            </form>
        </div>
    );
}

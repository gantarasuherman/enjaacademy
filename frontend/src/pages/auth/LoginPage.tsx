import { useState, type FormEvent } from 'react';
import { Link, useLocation, useNavigate } from 'react-router-dom';
import { Eye, EyeOff, Lock, Mail } from 'lucide-react';
import { useAuthStore } from '@/store/authStore';
import { useUiStore } from '@/store/uiStore';
import { usingMockData } from '@/services/api';
import { Button } from '@/components/ui/Button';
import { Input, Checkbox } from '@/components/ui/Input';
import { Alert } from '@/components/ui/Feedback';

export default function LoginPage() {
    const { login, status, error, clearError } = useAuthStore();
    const toast = useUiStore((state) => state.toast);
    const navigate = useNavigate();
    const location = useLocation();

    const [email, setEmail] = useState(usingMockData ? 'tesar@example.com' : '');
    const [password, setPassword] = useState(usingMockData ? 'password' : '');
    const [showPassword, setShowPassword] = useState(false);

    const from = (location.state as { from?: string } | null)?.from ?? '/app/dashboard';

    async function handleSubmit(event: FormEvent) {
        event.preventDefault();
        clearError();

        if (await login(email, password)) {
            toast('Selamat datang kembali!', 'success');
            navigate(from, { replace: true });
        }
    }

    return (
        <div>
            <h1 className="font-display text-2xl font-extrabold">Masuk ke akunmu</h1>
            <p className="mt-1.5 text-sm text-fg-muted">
                Belum punya akun?{' '}
                <Link to="/register" className="font-semibold text-primary hover:underline">
                    Daftar gratis
                </Link>
            </p>

            {usingMockData && (
                <Alert tone="info" className="mt-5">
                    Mode demo: gunakan email apa pun dengan kata sandi minimal 6 karakter.
                </Alert>
            )}

            {error && (
                <Alert tone="danger" className="mt-5">
                    {error}
                </Alert>
            )}

            <form onSubmit={handleSubmit} className="mt-6 space-y-4">
                <Input
                    type="email"
                    label="Email"
                    placeholder="nama@email.com"
                    autoComplete="email"
                    required
                    icon={<Mail className="size-4" />}
                    value={email}
                    onChange={(event) => setEmail(event.target.value)}
                />

                <Input
                    type={showPassword ? 'text' : 'password'}
                    label="Kata sandi"
                    placeholder="••••••••"
                    autoComplete="current-password"
                    required
                    icon={<Lock className="size-4" />}
                    trailing={
                        <button
                            type="button"
                            onClick={() => setShowPassword((v) => !v)}
                            aria-label={showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'}
                            className="text-fg-muted transition hover:text-fg"
                        >
                            {showPassword ? <EyeOff className="size-4" /> : <Eye className="size-4" />}
                        </button>
                    }
                    value={password}
                    onChange={(event) => setPassword(event.target.value)}
                />

                <div className="flex items-center justify-between">
                    <Checkbox label="Ingat saya" defaultChecked />
                    <Link to="/login" className="text-sm font-medium text-primary hover:underline">
                        Lupa kata sandi?
                    </Link>
                </div>

                <Button type="submit" fullWidth size="lg" loading={status === 'loading'}>
                    Masuk
                </Button>
            </form>

            <p className="mt-6 text-center text-xs text-fg-muted">
                Dengan masuk, kamu menyetujui Ketentuan Layanan dan Kebijakan Privasi kami.
            </p>
        </div>
    );
}

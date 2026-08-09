import { Suspense, useState } from 'react';
import { Link, NavLink, Outlet } from 'react-router-dom';
import { GraduationCap, Menu, Moon, Sun, X } from 'lucide-react';
import { cn } from '@/utils/cn';
import { useThemeStore } from '@/store/themeStore';
import { useAuthStore, selectIsAuthenticated } from '@/store/authStore';
import { Button, IconButton } from '@/components/ui/Button';
import { PageLoader } from '@/components/ui/Feedback';

const LINKS = [
    { label: 'Beranda', to: '/' },
    { label: 'Fitur', to: '/#features' },
    { label: 'Modul', to: '/#modules' },
    { label: 'Harga', to: '/#pricing' },
];

export function PublicLayout() {
    const [open, setOpen] = useState(false);
    const { theme, toggle } = useThemeStore();
    const isAuthenticated = useAuthStore(selectIsAuthenticated);

    const isDark = theme === 'dark' || (theme === 'system' && document.documentElement.classList.contains('dark'));

    return (
        <div className="flex min-h-dvh flex-col bg-bg">
            <header className="sticky top-0 z-30 border-b border-[var(--surface-border)] bg-surface/85 backdrop-blur-md">
                <div className="mx-auto flex h-16 max-w-6xl items-center gap-4 px-4 lg:px-6">
                    <Link to="/" className="flex items-center gap-2">
                        <span className="grid size-9 place-items-center rounded-sm bg-primary text-white">
                            <GraduationCap className="size-5" />
                        </span>
                        <span className="font-display text-base font-extrabold">Enja Academy</span>
                    </Link>

                    <nav className="ml-6 hidden items-center gap-1 md:flex">
                        {LINKS.map((link) => (
                            <NavLink
                                key={link.to}
                                to={link.to}
                                className={({ isActive }) =>
                                    cn(
                                        'rounded-sm px-3 py-2 text-sm font-medium transition',
                                        isActive && link.to === '/'
                                            ? 'text-primary'
                                            : 'text-fg-muted hover:text-fg',
                                    )
                                }
                            >
                                {link.label}
                            </NavLink>
                        ))}
                    </nav>

                    <div className="ml-auto flex items-center gap-2">
                        <IconButton label={isDark ? 'Mode terang' : 'Mode gelap'} variant="ghost" onClick={toggle}>
                            {isDark ? <Sun className="size-5" /> : <Moon className="size-5" />}
                        </IconButton>

                        {isAuthenticated ? (
                            <Button to="/app/dashboard" size="sm">
                                Ke Dashboard
                            </Button>
                        ) : (
                            <>
                                <Button to="/login" variant="ghost" size="sm" className="hidden sm:inline-flex">
                                    Masuk
                                </Button>
                                <Button to="/register" size="sm">
                                    Daftar Gratis
                                </Button>
                            </>
                        )}

                        <IconButton label="Menu" variant="ghost" className="md:hidden" onClick={() => setOpen((v) => !v)}>
                            {open ? <X className="size-5" /> : <Menu className="size-5" />}
                        </IconButton>
                    </div>
                </div>

                {open && (
                    <nav className="border-t border-[var(--surface-border)] px-4 py-3 md:hidden">
                        {LINKS.map((link) => (
                            <Link
                                key={link.to}
                                to={link.to}
                                onClick={() => setOpen(false)}
                                className="block rounded-sm px-3 py-2 text-sm font-medium text-fg-muted transition hover:bg-surface-sunken hover:text-fg"
                            >
                                {link.label}
                            </Link>
                        ))}
                    </nav>
                )}
            </header>

            <main className="flex-1">
                <Suspense fallback={<PageLoader />}>
                    <Outlet />
                </Suspense>
            </main>

            <footer className="border-t border-[var(--surface-border)] bg-surface">
                <div className="mx-auto grid max-w-6xl gap-8 px-4 py-12 md:grid-cols-4 lg:px-6">
                    <div className="md:col-span-2">
                        <div className="flex items-center gap-2">
                            <span className="grid size-8 place-items-center rounded-sm bg-primary text-white">
                                <GraduationCap className="size-4" />
                            </span>
                            <span className="font-display font-extrabold">Enja Academy</span>
                        </div>
                        <p className="mt-3 max-w-sm text-sm text-fg-muted">
                            Belajar Bahasa Inggris terstruktur dari A1 sampai C2 — vocabulary, grammar, listening,
                            speaking, reading, dan writing dalam satu tempat.
                        </p>
                    </div>

                    <div>
                        <p className="mb-3 text-sm font-bold">Belajar</p>
                        <ul className="space-y-2 text-sm text-fg-muted">
                            <li><Link to="/app/vocabulary" className="hover:text-fg">Vocabulary</Link></li>
                            <li><Link to="/app/grammar" className="hover:text-fg">Grammar</Link></li>
                            <li><Link to="/app/listening" className="hover:text-fg">Listening</Link></li>
                            <li><Link to="/app/speaking" className="hover:text-fg">Speaking</Link></li>
                        </ul>
                    </div>

                    <div>
                        <p className="mb-3 text-sm font-bold">Akun</p>
                        <ul className="space-y-2 text-sm text-fg-muted">
                            <li><Link to="/login" className="hover:text-fg">Masuk</Link></li>
                            <li><Link to="/register" className="hover:text-fg">Daftar</Link></li>
                            <li><Link to="/app/leaderboard" className="hover:text-fg">Leaderboard</Link></li>
                        </ul>
                    </div>
                </div>

                <div className="border-t border-[var(--surface-border)] px-4 py-5 text-center text-xs text-fg-muted">
                    © {new Date().getFullYear()} Enja Academy. Dibangun dengan React 19, TypeScript & Laravel.
                </div>
            </footer>
        </div>
    );
}

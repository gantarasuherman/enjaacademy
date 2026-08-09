import { useEffect, useMemo, useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { Bell, Flame, LogOut, Menu, Moon, Search, Settings, Sun, User as UserIcon } from 'lucide-react';
import { cn } from '@/utils/cn';
import { useUiStore } from '@/store/uiStore';
import { useThemeStore } from '@/store/themeStore';
import { useAuthStore } from '@/store/authStore';
import { useProgressStore } from '@/store/progressStore';
import { ALL_NAV_ITEMS } from './navigation';
import { Avatar } from '@/components/ui/Avatar';
import { IconButton } from '@/components/ui/Button';
import { Modal } from '@/components/ui/Modal';
import { Input } from '@/components/ui/Input';

function SearchPalette() {
    const { searchOpen, setSearchOpen } = useUiStore();
    const [query, setQuery] = useState('');
    const navigate = useNavigate();

    const results = useMemo(() => {
        const term = query.trim().toLowerCase();

        if (!term) return ALL_NAV_ITEMS.slice(0, 6);

        return ALL_NAV_ITEMS.filter((item) => item.label.toLowerCase().includes(term)).slice(0, 8);
    }, [query]);

    useEffect(() => {
        if (!searchOpen) setQuery('');
    }, [searchOpen]);

    return (
        <Modal open={searchOpen} onClose={() => setSearchOpen(false)} size="md">
            <Input
                autoFocus
                icon={<Search className="size-4" />}
                placeholder="Cari halaman, modul, atau latihan…"
                value={query}
                onChange={(event) => setQuery(event.target.value)}
                onKeyDown={(event) => {
                    if (event.key === 'Enter' && results[0]) {
                        navigate(results[0].to);
                        setSearchOpen(false);
                    }
                }}
            />

            <ul className="mt-3 space-y-1">
                {results.map((item) => {
                    const Icon = item.icon;

                    return (
                        <li key={item.to}>
                            <button
                                type="button"
                                onClick={() => {
                                    navigate(item.to);
                                    setSearchOpen(false);
                                }}
                                className="flex w-full items-center gap-3 rounded-sm px-3 py-2 text-left text-sm transition hover:bg-surface-sunken"
                            >
                                <Icon className="size-4 text-fg-muted" />
                                {item.label}
                            </button>
                        </li>
                    );
                })}

                {results.length === 0 && (
                    <li className="px-3 py-6 text-center text-sm text-fg-muted">Tidak ada hasil untuk "{query}".</li>
                )}
            </ul>
        </Modal>
    );
}

export function Topbar() {
    const { toggleSidebar, sidebarCollapsed, setSearchOpen } = useUiStore();
    const { theme, toggle: toggleTheme } = useThemeStore();
    const { user, logout } = useAuthStore();
    const streak = useProgressStore((state) => state.streakDays);
    const [menuOpen, setMenuOpen] = useState(false);
    const navigate = useNavigate();

    const isDark = theme === 'dark' || (theme === 'system' && document.documentElement.classList.contains('dark'));

    // Cmd/Ctrl+K opens the palette, matching the shortcut users expect.
    useEffect(() => {
        const handler = (event: KeyboardEvent) => {
            if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k') {
                event.preventDefault();
                setSearchOpen(true);
            }
        };

        window.addEventListener('keydown', handler);
        return () => window.removeEventListener('keydown', handler);
    }, [setSearchOpen]);

    return (
        <>
            <header
                className={cn(
                    'sticky top-0 z-20 flex h-16 items-center gap-3 border-b border-[var(--surface-border)]',
                    'bg-surface/85 px-4 backdrop-blur-md lg:px-6',
                )}
            >
                <IconButton label="Buka menu" variant="ghost" className="lg:hidden" onClick={toggleSidebar}>
                    <Menu className="size-5" />
                </IconButton>

                <button
                    type="button"
                    onClick={() => setSearchOpen(true)}
                    className={cn(
                        'hidden h-9 flex-1 items-center gap-2 rounded-sm border border-[var(--surface-border)]',
                        'bg-surface-sunken px-3 text-sm text-fg-muted transition hover:border-primary-300 sm:flex',
                        sidebarCollapsed ? 'max-w-md' : 'max-w-sm',
                    )}
                >
                    <Search className="size-4" />
                    <span>Cari…</span>
                    <kbd className="ml-auto rounded border border-[var(--surface-border)] bg-surface px-1.5 py-0.5 font-mono text-[10px]">
                        ⌘K
                    </kbd>
                </button>

                <div className="ml-auto flex items-center gap-1.5">
                    <span
                        title={`Streak ${streak} hari`}
                        className="hidden items-center gap-1.5 rounded-full bg-secondary-100 px-3 py-1.5 text-sm font-bold text-secondary-700 sm:flex dark:bg-secondary/20 dark:text-secondary-300"
                    >
                        <Flame className="size-4" />
                        {streak}
                    </span>

                    <IconButton label="Cari" variant="ghost" className="sm:hidden" onClick={() => setSearchOpen(true)}>
                        <Search className="size-5" />
                    </IconButton>

                    <IconButton
                        label={isDark ? 'Mode terang' : 'Mode gelap'}
                        variant="ghost"
                        onClick={toggleTheme}
                    >
                        {isDark ? <Sun className="size-5" /> : <Moon className="size-5" />}
                    </IconButton>

                    <IconButton label="Notifikasi" variant="ghost" className="relative">
                        <Bell className="size-5" />
                        <span className="absolute right-2 top-2 size-2 rounded-full bg-secondary ring-2 ring-[var(--surface)]" />
                    </IconButton>

                    <div className="relative">
                        <button
                            type="button"
                            onClick={() => setMenuOpen((open) => !open)}
                            aria-haspopup="menu"
                            aria-expanded={menuOpen}
                            className="flex items-center gap-2 rounded-full p-0.5 transition hover:bg-surface-sunken"
                        >
                            <Avatar name={user?.name ?? 'Tamu'} src={user?.avatar} size="sm" />
                        </button>

                        {menuOpen && (
                            <>
                                <div className="fixed inset-0 z-10" onClick={() => setMenuOpen(false)} />
                                <div
                                    role="menu"
                                    className="absolute right-0 z-20 mt-2 w-56 overflow-hidden rounded-sm border border-[var(--surface-border)] bg-surface shadow-[var(--shadow-pop)]"
                                >
                                    <div className="border-b border-[var(--surface-border)] px-4 py-3">
                                        <p className="truncate text-sm font-semibold">{user?.name ?? 'Tamu'}</p>
                                        <p className="truncate text-xs text-fg-muted">{user?.email ?? '—'}</p>
                                    </div>

                                    <Link
                                        to="/app/profile"
                                        onClick={() => setMenuOpen(false)}
                                        className="flex items-center gap-2.5 px-4 py-2.5 text-sm transition hover:bg-surface-sunken"
                                    >
                                        <UserIcon className="size-4 text-fg-muted" /> Profil
                                    </Link>
                                    <Link
                                        to="/app/setting"
                                        onClick={() => setMenuOpen(false)}
                                        className="flex items-center gap-2.5 px-4 py-2.5 text-sm transition hover:bg-surface-sunken"
                                    >
                                        <Settings className="size-4 text-fg-muted" /> Pengaturan
                                    </Link>
                                    <button
                                        type="button"
                                        onClick={async () => {
                                            setMenuOpen(false);
                                            await logout();
                                            navigate('/');
                                        }}
                                        className="flex w-full items-center gap-2.5 border-t border-[var(--surface-border)] px-4 py-2.5 text-left text-sm text-danger transition hover:bg-danger/8"
                                    >
                                        <LogOut className="size-4" /> Keluar
                                    </button>
                                </div>
                            </>
                        )}
                    </div>
                </div>
            </header>

            <SearchPalette />
        </>
    );
}

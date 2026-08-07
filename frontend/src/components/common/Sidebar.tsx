import { NavLink, useLocation } from 'react-router-dom';
import { AnimatePresence, motion } from 'framer-motion';
import { GraduationCap, PanelLeftClose, PanelLeftOpen, X } from 'lucide-react';
import { cn } from '@/utils/cn';
import { useUiStore } from '@/store/uiStore';
import { useProgressStore } from '@/store/progressStore';
import { levelInfo } from '@/utils/gamification';
import { formatCompact } from '@/utils/format';
import { NAV_SECTIONS, type NavItem } from './navigation';
import { IconButton } from '@/components/ui/Button';
import { ProgressBar } from '@/components/ui/Progress';
import { Tooltip } from '@/components/ui/Tooltip';

function NavRow({ item, collapsed, onNavigate }: { item: NavItem; collapsed: boolean; onNavigate?: () => void }) {
    const location = useLocation();
    const Icon = item.icon;

    // `end` alone can't express "active for every child route", so matching
    // prefixes is done here where each item declares its own rule.
    const isActive = item.match
        ? location.pathname.startsWith(item.match)
        : location.pathname === item.to;

    const link = (
        <NavLink
            to={item.to}
            onClick={onNavigate}
            aria-current={isActive ? 'page' : undefined}
            className={cn(
                'group relative flex items-center gap-3 rounded-sm px-3 py-2 text-sm font-medium transition',
                'duration-150 ease-[var(--ease-out-soft)]',
                collapsed && 'justify-center px-0',
                isActive
                    ? 'bg-primary-100 text-primary-700 dark:bg-primary/20 dark:text-primary-200'
                    : 'text-fg-muted hover:bg-surface-sunken hover:text-fg',
            )}
        >
            {isActive && (
                <motion.span
                    layoutId="sidebar-active"
                    className="absolute left-0 h-6 w-1 rounded-r-full bg-primary"
                    transition={{ duration: 0.2, ease: [0.16, 1, 0.3, 1] }}
                />
            )}
            <Icon className="size-4.5 shrink-0" aria-hidden />
            {!collapsed && <span className="truncate">{item.label}</span>}
            {!collapsed && item.badge && (
                <span className="ml-auto rounded-full bg-secondary px-1.5 py-0.5 text-[10px] font-bold text-white">
                    {item.badge}
                </span>
            )}
        </NavLink>
    );

    return collapsed ? (
        <Tooltip content={item.label} side="right" className="block">
            {link}
        </Tooltip>
    ) : (
        link
    );
}

function SidebarBody({ collapsed, onNavigate }: { collapsed: boolean; onNavigate?: () => void }) {
    const xp = useProgressStore((state) => state.xp);
    const info = levelInfo(xp);

    return (
        <>
            <nav className="flex-1 space-y-5 overflow-y-auto px-3 py-4 scrollbar-thin">
                {NAV_SECTIONS.map((section) => (
                    <div key={section.title}>
                        {!collapsed && (
                            <p className="mb-1.5 px-3 text-[11px] font-bold uppercase tracking-wider text-fg-muted/70">
                                {section.title}
                            </p>
                        )}
                        <div className="space-y-0.5">
                            {section.items.map((item) => (
                                <NavRow key={item.to} item={item} collapsed={collapsed} onNavigate={onNavigate} />
                            ))}
                        </div>
                    </div>
                ))}
            </nav>

            {!collapsed && (
                <div className="border-t border-[var(--surface-border)] p-4">
                    <div className="rounded-sm bg-gradient-to-br from-primary to-primary-700 p-4 text-white">
                        <p className="text-xs font-medium opacity-90">Level {info.level}</p>
                        <p className="mt-0.5 font-display text-2xl font-extrabold">{formatCompact(xp)} XP</p>
                        <ProgressBar
                            value={info.percent}
                            tone="secondary"
                            size="sm"
                            className="mt-3 [&>div]:bg-white/25"
                        />
                        <p className="mt-1.5 text-[11px] opacity-80">
                            {formatCompact(info.xpRemaining)} XP lagi ke level {info.level + 1}
                        </p>
                    </div>
                </div>
            )}
        </>
    );
}

export function Sidebar() {
    const { sidebarOpen, setSidebarOpen, sidebarCollapsed, toggleCollapsed } = useUiStore();

    return (
        <>
            {/* Desktop rail */}
            <aside
                className={cn(
                    'fixed inset-y-0 left-0 z-30 hidden shrink-0 flex-col border-r border-[var(--surface-border)] bg-surface lg:flex',
                    'transition-[width] duration-200 ease-[var(--ease-out-soft)]',
                    sidebarCollapsed ? 'w-18' : 'w-64',
                )}
            >
                <div
                    className={cn(
                        'flex h-16 shrink-0 items-center gap-2 border-b border-[var(--surface-border)] px-4',
                        sidebarCollapsed && 'justify-center px-0',
                    )}
                >
                    <span className="grid size-9 shrink-0 place-items-center rounded-sm bg-primary text-white">
                        <GraduationCap className="size-5" />
                    </span>
                    {!sidebarCollapsed && (
                        <span className="truncate font-display text-base font-extrabold">English Academy</span>
                    )}
                    {!sidebarCollapsed && (
                        <IconButton
                            label="Ciutkan sidebar"
                            variant="ghost"
                            size="sm"
                            className="ml-auto"
                            onClick={toggleCollapsed}
                        >
                            <PanelLeftClose className="size-4" />
                        </IconButton>
                    )}
                </div>

                {sidebarCollapsed && (
                    <IconButton
                        label="Bentangkan sidebar"
                        variant="ghost"
                        size="sm"
                        className="mx-auto mt-2"
                        onClick={toggleCollapsed}
                    >
                        <PanelLeftOpen className="size-4" />
                    </IconButton>
                )}

                <SidebarBody collapsed={sidebarCollapsed} />
            </aside>

            {/* Mobile drawer */}
            <AnimatePresence>
                {sidebarOpen && (
                    <div className="fixed inset-0 z-40 lg:hidden">
                        <motion.div
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            exit={{ opacity: 0 }}
                            transition={{ duration: 0.15 }}
                            onClick={() => setSidebarOpen(false)}
                            className="absolute inset-0 bg-slate-950/50 backdrop-blur-sm"
                        />
                        <motion.aside
                            initial={{ x: '-100%' }}
                            animate={{ x: 0 }}
                            exit={{ x: '-100%' }}
                            transition={{ duration: 0.25, ease: [0.16, 1, 0.3, 1] }}
                            className="relative flex h-full w-72 flex-col bg-surface shadow-[var(--shadow-pop)]"
                        >
                            <div className="flex h-16 shrink-0 items-center gap-2 border-b border-[var(--surface-border)] px-4">
                                <span className="grid size-9 place-items-center rounded-sm bg-primary text-white">
                                    <GraduationCap className="size-5" />
                                </span>
                                <span className="font-display text-base font-extrabold">English Academy</span>
                                <IconButton
                                    label="Tutup menu"
                                    variant="ghost"
                                    size="sm"
                                    className="ml-auto"
                                    onClick={() => setSidebarOpen(false)}
                                >
                                    <X className="size-4" />
                                </IconButton>
                            </div>

                            <SidebarBody collapsed={false} onNavigate={() => setSidebarOpen(false)} />
                        </motion.aside>
                    </div>
                )}
            </AnimatePresence>
        </>
    );
}

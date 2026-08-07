import type { LucideIcon } from 'lucide-react';
import {
    Award,
    BookMarked,
    BookOpen,
    Bookmark,
    GraduationCap,
    Headphones,
    LayoutDashboard,
    Layers,
    MessagesSquare,
    Mic,
    PenLine,
    ScrollText,
    Settings,
    Sigma,
    Trophy,
    TrendingUp,
    User,
} from 'lucide-react';

export interface NavItem {
    label: string;
    to: string;
    icon: LucideIcon;
    /** Marks the item active for nested routes too. */
    match?: string;
    badge?: string;
}

export interface NavSection {
    title: string;
    items: NavItem[];
}

/**
 * Sidebar structure. Kept as data in one file so a new module means one entry
 * here plus a route — the shell itself never changes.
 */
export const NAV_SECTIONS: NavSection[] = [
    {
        title: 'Utama',
        items: [
            { label: 'Dashboard', to: '/app/dashboard', icon: LayoutDashboard },
            { label: 'Learning Path', to: '/app/learning', icon: Layers, match: '/app/learning' },
        ],
    },
    {
        title: 'Skill',
        items: [
            { label: 'Vocabulary', to: '/app/vocabulary', icon: BookMarked, match: '/app/vocabulary' },
            { label: 'Grammar', to: '/app/grammar', icon: Sigma, match: '/app/grammar' },
            { label: 'Listening', to: '/app/listening', icon: Headphones, match: '/app/listening' },
            { label: 'Speaking', to: '/app/speaking', icon: Mic, match: '/app/speaking' },
            { label: 'Reading', to: '/app/reading', icon: BookOpen, match: '/app/reading' },
            { label: 'Writing', to: '/app/writing', icon: PenLine, match: '/app/writing' },
            { label: 'Conversation', to: '/app/conversation', icon: MessagesSquare, match: '/app/conversation' },
        ],
    },
    {
        title: 'Latihan',
        items: [
            { label: 'Flashcard', to: '/app/flashcard', icon: Layers, match: '/app/flashcard' },
            { label: 'Quiz', to: '/app/quiz', icon: GraduationCap, match: '/app/quiz' },
            { label: 'Bookmark', to: '/app/bookmark', icon: Bookmark },
        ],
    },
    {
        title: 'Pencapaian',
        items: [
            { label: 'Progress', to: '/app/progress', icon: TrendingUp },
            { label: 'Achievement', to: '/app/achievement', icon: Award },
            { label: 'Leaderboard', to: '/app/leaderboard', icon: Trophy },
            { label: 'Certificate', to: '/app/certificate', icon: ScrollText },
        ],
    },
    {
        title: 'Akun',
        items: [
            { label: 'Profil', to: '/app/profile', icon: User },
            { label: 'Pengaturan', to: '/app/setting', icon: Settings },
        ],
    },
];

/** Flat list used by the command palette / search. */
export const ALL_NAV_ITEMS: NavItem[] = NAV_SECTIONS.flatMap((section) => section.items);

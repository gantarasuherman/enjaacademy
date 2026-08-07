import {
    BedDouble,
    Book,
    BookMarked,
    BookOpen,
    Briefcase,
    ChevronUp,
    ChevronsUp,
    ClipboardCheck,
    Code,
    Coffee,
    Crown,
    Flame,
    Footprints,
    GraduationCap,
    Headphones,
    Layers,
    Library,
    MessagesSquare,
    Mic,
    PenLine,
    Plane,
    Repeat,
    Rocket,
    Shuffle,
    Sigma,
    Star,
    Target,
    Users,
    UtensilsCrossed,
    Zap,
    type LucideIcon,
} from 'lucide-react';

/**
 * Icons referenced by name from the JSON data.
 *
 * This is an explicit map rather than `import * as Icons` on purpose: a
 * namespace import pulls the entire lucide bundle (~760 kB) into the build,
 * because the bundler cannot know which icons a data string will select.
 * Adding a new icon to the content means adding one line here.
 */
const REGISTRY: Record<string, LucideIcon> = {
    BedDouble,
    Book,
    BookMarked,
    BookOpen,
    Briefcase,
    ChevronUp,
    ChevronsUp,
    ClipboardCheck,
    Code,
    Coffee,
    Crown,
    Flame,
    Footprints,
    GraduationCap,
    Headphones,
    Layers,
    Library,
    MessagesSquare,
    Mic,
    PenLine,
    Plane,
    Repeat,
    Rocket,
    Shuffle,
    Sigma,
    Star,
    Target,
    Users,
    UtensilsCrossed,
    Zap,
};

export function DynamicIcon({
    name,
    fallback = Layers,
    className,
}: {
    name: string;
    fallback?: LucideIcon;
    className?: string;
}) {
    const Icon = REGISTRY[name] ?? fallback;

    return <Icon className={className} aria-hidden />;
}

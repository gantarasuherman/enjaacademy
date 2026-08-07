/**
 * XP curve. Level N requires `BASE * (N - 1) ^ EXPONENT` cumulative XP, the
 * same formula the Laravel backend uses so both agree on a user's level.
 */
const BASE = 100;
const EXPONENT = 1.5;
export const MAX_LEVEL = 100;

export function xpForLevel(level: number): number {
    if (level <= 1) return 0;

    return Math.round(BASE * (level - 1) ** EXPONENT);
}

export function levelForXp(xp: number): number {
    for (let level = 1; level < MAX_LEVEL; level++) {
        if (xp < xpForLevel(level + 1)) return level;
    }

    return MAX_LEVEL;
}

export interface LevelInfo {
    level: number;
    xpIntoLevel: number;
    xpForNextLevel: number;
    percent: number;
    xpRemaining: number;
}

export function levelInfo(xp: number): LevelInfo {
    const level = levelForXp(xp);
    const floor = xpForLevel(level);
    const ceiling = xpForLevel(level + 1);
    const span = Math.max(1, ceiling - floor);
    const into = Math.max(0, xp - floor);

    return {
        level,
        xpIntoLevel: into,
        xpForNextLevel: span,
        percent: Math.min(100, Math.round((into / span) * 100)),
        xpRemaining: Math.max(0, ceiling - xp),
    };
}

/** Achievement progress as a 0–100 percentage. */
export function achievementPercent(progress: number, threshold: number): number {
    if (threshold <= 0) return 100;

    return Math.min(100, Math.round((progress / threshold) * 100));
}

export const TIER_STYLES: Record<string, { ring: string; text: string; bg: string }> = {
    bronze: { ring: 'ring-amber-700/30', text: 'text-amber-700', bg: 'bg-amber-700/10' },
    silver: { ring: 'ring-slate-400/40', text: 'text-slate-500', bg: 'bg-slate-400/10' },
    gold: { ring: 'ring-yellow-500/40', text: 'text-yellow-600', bg: 'bg-yellow-500/10' },
    platinum: { ring: 'ring-cyan-400/40', text: 'text-cyan-600', bg: 'bg-cyan-400/10' },
};

/**
 * Streak state for the calendar strip: a day counts as active when it has
 * any recorded minutes.
 */
export function streakFromActivity(activity: { date: string; minutes: number }[]): number {
    const active = new Set(activity.filter((day) => day.minutes > 0).map((day) => day.date));

    let streak = 0;
    const cursor = new Date();

    // Today not being done yet shouldn't break a streak, so start from
    // yesterday if today is still empty.
    if (!active.has(cursor.toISOString().slice(0, 10))) {
        cursor.setDate(cursor.getDate() - 1);
    }

    while (active.has(cursor.toISOString().slice(0, 10))) {
        streak += 1;
        cursor.setDate(cursor.getDate() - 1);
    }

    return streak;
}

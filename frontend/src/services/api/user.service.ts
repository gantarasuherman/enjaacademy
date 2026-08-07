import type {
    Achievement,
    Bookmark,
    Certificate,
    DailyActivity,
    LeaderboardEntry,
    UnlockedAchievement,
    User,
    UserSettings,
} from '@/types';
import userData from '@/data/user.json';
import gamificationData from '@/data/gamification.json';
import { http, tokenStorage, unwrap } from './client';
import { delay, isMock, source } from './config';

export const userService = {
    login: (email: string, password: string) =>
        source(
            () => {
                // Mock auth: any password of 6+ characters signs you in.
                if (password.length < 6) {
                    return Promise.reject(new Error('Password minimal 6 karakter.'));
                }

                return delay({
                    user: { ...(userData.user as User), email },
                    token: 'mock-token',
                });
            },
            async () => {
                const { data } = await http.post('/login', { email, password, device_name: 'web' });
                tokenStorage.set(data.token);
                return { user: unwrap<User>(data.user), token: data.token as string };
            },
        ),

    register: (name: string, email: string, password: string) =>
        source(
            () =>
                delay({
                    user: { ...(userData.user as User), name, email, xp: 0, level: 1, streak: 0 },
                    token: 'mock-token',
                }),
            async () => {
                const { data } = await http.post('/register', { name, email, password });
                tokenStorage.set(data.token);
                return { user: unwrap<User>(data.user), token: data.token as string };
            },
        ),

    logout: () =>
        source(
            () => {
                tokenStorage.clear();
                return delay(true, 0);
            },
            async () => {
                await http.post('/logout');
                tokenStorage.clear();
                return true;
            },
        ),

    me: () =>
        source(
            () => delay(userData.user as User),
            async () => unwrap<User>((await http.get('/me')).data),
        ),

    updateProfile: (patch: Partial<User>) =>
        source(
            () => delay({ ...(userData.user as User), ...patch }),
            async () => unwrap<User>((await http.put('/profile', patch)).data),
        ),

    getSettings: () =>
        source(
            () => delay(userData.settings as UserSettings),
            async () => unwrap<UserSettings>((await http.get('/settings')).data),
        ),

    updateSettings: (patch: Partial<UserSettings>) =>
        source(
            () => delay({ ...(userData.settings as UserSettings), ...patch }),
            async () => unwrap<UserSettings>((await http.put('/settings', patch)).data),
        ),

    getActivity: () =>
        source(
            () => delay(userData.activity as DailyActivity[]),
            async () => unwrap<DailyActivity[]>((await http.get('/activity')).data),
        ),

    listAchievements: () =>
        source(
            () => delay(gamificationData.achievements as Achievement[]),
            async () => unwrap<Achievement[]>((await http.get('/achievements')).data),
        ),

    listUnlocked: () =>
        source(
            () => delay(userData.unlockedAchievements as UnlockedAchievement[]),
            async () => unwrap<UnlockedAchievement[]>((await http.get('/achievements/unlocked')).data),
        ),

    getLeaderboard: (scope: 'weekly' | 'monthly' | 'all' = 'all') =>
        source(
            () => delay(gamificationData.leaderboard as LeaderboardEntry[]),
            async () => unwrap<LeaderboardEntry[]>((await http.get('/leaderboard', { params: { scope } })).data),
        ),

    listCertificates: () =>
        source(
            () => delay(gamificationData.certificates as Certificate[]),
            async () => unwrap<Certificate[]>((await http.get('/certificates')).data),
        ),

    listBookmarks: () =>
        source(
            () => delay(userData.bookmarks as Bookmark[]),
            async () => unwrap<Bookmark[]>((await http.get('/bookmarks')).data),
        ),

    toggleBookmark: (payload: Omit<Bookmark, 'id' | 'createdAt'>) =>
        source(
            () => delay({ ...payload, id: `bm-${Date.now()}`, createdAt: new Date().toISOString() } as Bookmark),
            async () => unwrap<Bookmark>((await http.post('/bookmarks/toggle', payload)).data),
        ),
};

/** Exposed so the UI can show a "demo data" banner without importing config. */
export const usingMockData = isMock;

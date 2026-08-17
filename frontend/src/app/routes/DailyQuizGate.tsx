import type { ReactNode } from 'react';
import { dailyQuizService } from '@/services/api';
import { useAsync } from '@/hooks/useAsync';
import { PageLoader } from '@/components/ui/Feedback';
import { DailyQuizPage } from '@/pages/daily-quiz/DailyQuizPage';

/**
 * Wraps the whole authenticated app shell. Until today's daily quiz is
 * finished (submitted or skipped), this renders the quiz full-screen instead
 * of the sidebar/topbar + the requested route — the mandatory pre-learning
 * gate from the spec ("Setelah selesai kuis → baru boleh masuk ke dashboard
 * pembelajaran").
 *
 * Fails open on a status-check error: a broken endpoint should not lock every
 * user out of the app, so `children` renders through instead of retrying.
 */
export function DailyQuizGate({ children }: { children: ReactNode }) {
    const { data: status, loading, error, reload } = useAsync(() => dailyQuizService.status());

    if (loading) {
        return <PageLoader label="Menyiapkan kuis harian…" />;
    }

    if (!error && status && !status.satisfiedToday) {
        return <DailyQuizPage onFinished={() => void reload()} />;
    }

    return <>{children}</>;
}

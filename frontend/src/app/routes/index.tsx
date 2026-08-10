import { lazy } from 'react';
import { createBrowserRouter, Navigate, type RouteObject } from 'react-router-dom';
import { AppLayout } from '../layouts/AppLayout';
import { AuthLayout } from '../layouts/AuthLayout';
import { PublicLayout } from '../layouts/PublicLayout';
import { ProtectedRoute } from './ProtectedRoute';
import { ErrorBoundaryPage, NotFoundPage } from '@/pages/error/ErrorPages';

/* Every page is code-split; the layouts render a Suspense fallback. */

const LandingPage = lazy(() => import('@/pages/landing/LandingPage'));
const LoginPage = lazy(() => import('@/pages/auth/LoginPage'));
const RegisterPage = lazy(() => import('@/pages/auth/RegisterPage'));

const DashboardPage = lazy(() => import('@/pages/dashboard/DashboardPage'));

const LearningListPage = lazy(() => import('@/pages/learning/LearningListPage'));
const LearningModulePage = lazy(() => import('@/pages/learning/LearningModulePage'));
const LessonPage = lazy(() => import('@/pages/learning/LessonPage'));

const VocabularyPage = lazy(() => import('@/pages/vocabulary/VocabularyPage'));
const GrammarPage = lazy(() => import('@/pages/grammar/GrammarPage'));
const GrammarTopicPage = lazy(() => import('@/pages/grammar/GrammarTopicPage'));
const TensePage = lazy(() => import('@/pages/grammar/TensePage'));
const JlptCategoryPage = lazy(() => import('@/pages/grammar/JlptCategoryPage'));
const JlptPatternPage = lazy(() => import('@/pages/grammar/JlptPatternPage'));

const ListeningPage = lazy(() => import('@/pages/listening/ListeningPage'));
const ListeningDetailPage = lazy(() => import('@/pages/listening/ListeningDetailPage'));
const SpeakingPage = lazy(() => import('@/pages/speaking/SpeakingPage'));
const ReadingPage = lazy(() => import('@/pages/reading/ReadingPage'));
const ReadingDetailPage = lazy(() => import('@/pages/reading/ReadingDetailPage'));
const WritingPage = lazy(() => import('@/pages/writing/WritingPage'));
const ConversationPage = lazy(() => import('@/pages/conversation/ConversationPage'));
const ConversationDetailPage = lazy(() => import('@/pages/conversation/ConversationDetailPage'));

const FlashcardPage = lazy(() => import('@/pages/flashcard/FlashcardPage'));
const FlashcardStudyPage = lazy(() => import('@/pages/flashcard/FlashcardStudyPage'));

const QuizListPage = lazy(() => import('@/pages/quiz/QuizListPage'));
const QuizStartPage = lazy(() => import('@/pages/quiz/QuizStartPage'));
const QuizPlayPage = lazy(() => import('@/pages/quiz/QuizPlayPage'));
const QuizResultPage = lazy(() => import('@/pages/quiz/QuizResultPage'));

const ProgressPage = lazy(() => import('@/pages/progress/ProgressPage'));
const AchievementPage = lazy(() => import('@/pages/achievement/AchievementPage'));
const LeaderboardPage = lazy(() => import('@/pages/leaderboard/LeaderboardPage'));
const BookmarkPage = lazy(() => import('@/pages/bookmark/BookmarkPage'));
const CertificatePage = lazy(() => import('@/pages/certificate/CertificatePage'));
const ProfilePage = lazy(() => import('@/pages/profile/ProfilePage'));
const SettingPage = lazy(() => import('@/pages/setting/SettingPage'));

const routes: RouteObject[] = [
    {
        element: <PublicLayout />,
        errorElement: <ErrorBoundaryPage />,
        children: [{ index: true, element: <LandingPage /> }],
    },
    {
        element: <AuthLayout />,
        errorElement: <ErrorBoundaryPage />,
        children: [
            { path: 'login', element: <LoginPage /> },
            { path: 'register', element: <RegisterPage /> },
        ],
    },
    {
        path: 'app',
        element: (
            <ProtectedRoute>
                <AppLayout />
            </ProtectedRoute>
        ),
        errorElement: <ErrorBoundaryPage />,
        children: [
            { index: true, element: <Navigate to="/app/dashboard" replace /> },
            { path: 'dashboard', element: <DashboardPage /> },

            { path: 'learning', element: <LearningListPage /> },
            { path: 'learning/:moduleId', element: <LearningModulePage /> },
            { path: 'learning/lesson/:lessonId', element: <LessonPage /> },

            { path: 'vocabulary', element: <VocabularyPage /> },
            { path: 'vocabulary/:categoryId', element: <VocabularyPage /> },

            { path: 'grammar', element: <GrammarPage /> },
            { path: 'grammar/tenses/:tenseId', element: <TensePage /> },
            { path: 'grammar/jlpt/pattern/:patternId', element: <JlptPatternPage /> },
            { path: 'grammar/jlpt/:categoryId', element: <JlptCategoryPage /> },
            { path: 'grammar/:topicId', element: <GrammarTopicPage /> },

            { path: 'listening', element: <ListeningPage /> },
            { path: 'listening/:lessonId', element: <ListeningDetailPage /> },

            { path: 'speaking', element: <SpeakingPage /> },
            { path: 'speaking/:lessonId', element: <SpeakingPage /> },

            { path: 'reading', element: <ReadingPage /> },
            { path: 'reading/:articleId', element: <ReadingDetailPage /> },

            { path: 'writing', element: <WritingPage /> },
            { path: 'writing/:taskId', element: <WritingPage /> },

            { path: 'conversation', element: <ConversationPage /> },
            { path: 'conversation/:scenarioId', element: <ConversationDetailPage /> },

            { path: 'flashcard', element: <FlashcardPage /> },
            { path: 'flashcard/:deckId', element: <FlashcardStudyPage /> },

            { path: 'quiz', element: <QuizListPage /> },
            { path: 'quiz/:quizId', element: <QuizStartPage /> },
            { path: 'quiz/:quizId/play', element: <QuizPlayPage /> },
            { path: 'quiz/:quizId/result', element: <QuizResultPage /> },

            { path: 'progress', element: <ProgressPage /> },
            { path: 'achievement', element: <AchievementPage /> },
            { path: 'leaderboard', element: <LeaderboardPage /> },
            { path: 'bookmark', element: <BookmarkPage /> },
            { path: 'certificate', element: <CertificatePage /> },
            { path: 'profile', element: <ProfilePage /> },
            { path: 'setting', element: <SettingPage /> },
        ],
    },
    { path: '*', element: <NotFoundPage /> },
];

export const router = createBrowserRouter(routes);

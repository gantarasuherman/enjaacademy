/**
 * Domain types. These mirror the Laravel API payloads exactly, so switching
 * `VITE_DATA_SOURCE` from `mock` to `api` needs no type changes.
 */

export type CEFRLevel = 'Beginner' | 'Elementary' | 'Intermediate' | 'Upper-Intermediate' | 'Advanced';
export type Difficulty = 'beginner' | 'intermediate' | 'advanced';
/** Which language this content teaches — absent on older mock rows that predate the bilingual catalog. */
export type LanguageSlug = 'japanese' | 'english';

export type SkillType =
    | 'vocabulary'
    | 'grammar'
    | 'listening'
    | 'speaking'
    | 'reading'
    | 'writing'
    | 'conversation';

/* ---------------------------------------------------------------- User ---- */

export interface User {
    id: string;
    name: string;
    email: string;
    avatar: string | null;
    level: number;
    xp: number;
    streak: number;
    joinedAt: string;
    targetLevel: CEFRLevel;
    dailyGoalMinutes: number;
}

export interface UserSettings {
    theme: 'light' | 'dark' | 'system';
    locale: 'id' | 'en';
    dailyGoalMinutes: number;
    soundEnabled: boolean;
    autoplayAudio: boolean;
    reminderEnabled: boolean;
    reminderTime: string;
}

/* ------------------------------------------------------------ Learning ---- */

/**
 * Mirrors `ModuleResource` (backend) exactly — a module is a row in
 * `learning_modules`, not a per-skill mock concept. `content_type` is the
 * backend's `content_type` enum (kana, kanji, vocabulary, grammar,
 * conversation, listening, speaking, reading, writing, exam).
 */
export interface LearningModule {
    id: number;
    name: string;
    slug: string;
    icon: string;
    color: string;
    content_type: SkillType | 'kana' | 'kanji' | 'exam' | 'video';
    description: string | null;
    lessons_count?: number;
    /** Whether the current user took this class. A record, not an access gate. */
    is_enrolled?: boolean;
    is_paid: boolean;
    /** Rupiah, no decimals — `null` unless `is_paid`. */
    price: number | null;
    language?: { name: string; slug: string; flag: string };
}

/** Mirrors `OrderResource` (backend) — a checkout/payment record for a paid course. */
export interface Order {
    id: number;
    reference: string;
    amount: number;
    status: 'pending' | 'paid' | 'failed' | 'expired';
    /** Tripay-hosted checkout page — set only when `payment_mode: 'tripay'`. */
    checkout_url: string | null;
    /** QRIS image URL — set only when `payment_mode: 'tripay'`. */
    qr_url: string | null;
    paid_at: string | null;
    created_at: string;
    module?: { id: number; name: string; slug: string };
}

/** One row inside a lesson — mirrors the `items` array of `LessonResource`. */
export interface LessonItem {
    id: number;
    term: string;
    reading: string | null;
    romaji: string | null;
    meaning: string | null;
    example: string | null;
    example_meaning: string | null;
    extra: Record<string, unknown> | null;
}

/**
 * Mirrors `LessonResource` (backend) exactly. A lesson's content is generic
 * — HTML `content` plus a flat list of `items` — regardless of what skill
 * its parent module teaches. Per-skill pages (Vocabulary, Grammar,
 * Conversation) build their own richer view on top of the same items.
 */
export interface Lesson {
    id: number;
    title: string;
    slug: string;
    level: string | null;
    summary: string | null;
    content?: string | null;
    translated_content?: string | null;
    video_url?: string | null;
    estimated_minutes: number;
    xp_reward: number;
    items_count?: number;
    module?: LearningModule;
    items?: LessonItem[];
}

/* ---------------------------------------------------------- Vocabulary ---- */

export interface Category {
    id: string;
    name: string;
    icon: string;
    color: string;
    description: string;
    wordCount: number;
    language?: LanguageSlug;
}

export interface VocabularyItem {
    id: string;
    word: string;
    ipa: string;
    partOfSpeech: string;
    audioUrl: string | null;
    imageUrl: string | null;
    meaning: string;
    example: string;
    exampleMeaning: string;
    synonyms: string[];
    antonyms: string[];
    categoryId: string;
    /** Beginner..Advanced for English words, N5..N1 (JLPT) for Japanese — the two scales don't mix, see `language`. */
    cefr: string;
    difficulty: Difficulty;
    language?: LanguageSlug;
}

/* ------------------------------------------------------------- Grammar ---- */

export type GrammarKind = 'parts-of-speech' | 'tense' | 'structure';

export interface GrammarTopic {
    id: string;
    title: string;
    kind: GrammarKind;
    cefr: CEFRLevel;
    explanation: string;
    formula: string | null;
    /** Rendered as a past → present → future strip for tenses. */
    timeline: string | null;
    examples: GrammarExample[];
    commonMistakes: { wrong: string; right: string; why: string }[];
    exerciseIds: string[];
    language?: LanguageSlug;
}

export interface GrammarExample {
    sentence: string;
    meaning: string;
    highlight?: string;
}

/* ------------------------------------------------------ JLPT Grammar CMS ---- */
/** The admin-extensible level -> category -> pattern system (currently Japanese-only). */

export type GrammarTrackLanguage = 'japanese' | 'english';
export type GrammarTrack = 'grammar' | 'structure';

export interface JlptGrammarCategory {
    id: string;
    name: string;
    slug: string;
    patternsCount: number;
    children: JlptGrammarCategory[];
    level?: JlptGrammarLevel;
    parent?: { id: string; name: string } | null;
}

export interface JlptGrammarLevel {
    id: string;
    name: string;
    language: GrammarTrackLanguage;
    track: GrammarTrack;
    color: string;
    description: string | null;
    categories: JlptGrammarCategory[];
}

export interface JlptGrammarPattern {
    id: string;
    title: string;
    titleRomaji: string | null;
    explanation: string;
    formula: string | null;
    level: string | null;
    category: string | null;
    examples: { sentence: string; romaji: string | null; translation: string | null }[];
    mistakes: {
        wrong: string;
        wrongRomaji: string | null;
        right: string | null;
        rightRomaji: string | null;
        why: string | null;
    }[];
}

export interface Tense {
    id: string;
    name: string;
    group: 'present' | 'past' | 'future' | 'perfect';
    formula: { positive: string; negative: string; question: string };
    usage: string[];
    examples: GrammarExample[];
    signalWords: string[];
}

/* ----------------------------------------------------------- Listening ---- */

export interface SubtitleCue {
    start: number;
    end: number;
    text: string;
    translation: string;
}

export interface ListeningTrack {
    id: string;
    title: string;
    description: string;
    audioUrl: string;
    durationSeconds: number;
    accent: 'american' | 'british' | 'australian';
    cefr: CEFRLevel;
    transcript: string;
    subtitles: SubtitleCue[];
    quizId: string | null;
}

/* ------------------------------------------------------------ Speaking ---- */

export interface SpeakingExercise {
    id: string;
    /** Raw LessonItem id, for POSTing XP back — undefined in mock mode, where there's no backend row to award against. */
    itemId?: string;
    targetSentence: string;
    ipa: string;
    meaning: string;
    audioSample: string | null;
    focusSounds: string[];
    cefr: CEFRLevel;
}

export interface SpeakingAttempt {
    id: string;
    exerciseId: string;
    transcript: string;
    score: number;
    wordScores: { word: string; matched: boolean }[];
    feedback: string;
    attemptedAt: string;
}

/* ------------------------------------------------------------- Reading ---- */

export interface ReadingText {
    id: string;
    title: string;
    type: 'story' | 'news' | 'article' | 'documentation';
    cefr: CEFRLevel;
    wordCount: number;
    readingMinutes: number;
    coverImage: string | null;
    body: string;
    translatedBody?: string | null;
    glossary: { word: string; meaning: string }[];
    quizId: string | null;
    language?: LanguageSlug;
}

/* ------------------------------------------------------------- Writing ---- */

export interface WritingTask {
    id: string;
    prompt: string;
    type: 'sentence' | 'paragraph' | 'essay';
    cefr: CEFRLevel;
    minWords: number;
    maxWords: number;
    hints: string[];
    rubric: { criterion: string; description: string; weight: number }[];
    sampleAnswer: string;
}

export interface WritingSubmission {
    id: string;
    taskId: string;
    body: string;
    wordCount: number;
    submittedAt: string;
    selfScore: Record<string, number>;
}

/* -------------------------------------------------------- Conversation ---- */

export interface DialogueLine {
    id: string;
    speaker: string;
    role: 'a' | 'b';
    text: string;
    romaji?: string | null;
    translation: string;
    audioUrl: string | null;
    /** When set, the learner speaks this line instead of listening to it. */
    userTurn?: boolean;
}

export interface ConversationScenario {
    id: string;
    title: string;
    context: string;
    situation: string;
    cefr: CEFRLevel;
    icon: string;
    keyPhrases: { phrase: string; meaning: string }[];
    lines: DialogueLine[];
    language?: LanguageSlug;
}

/* ---------------------------------------------------------- Flashcards ---- */

export interface FlashcardDeck {
    id: string;
    title: string;
    description: string;
    categoryId: string | null;
    color: string;
    icon: string;
    cardIds: string[];
}

export interface FlashcardItem {
    id: string;
    deckId: string;
    front: string;
    back: string;
    hint: string | null;
    ipa: string | null;
    audioUrl: string | null;
    /** Set when the card was generated from a vocabulary entry. */
    vocabularyId: string | null;
}

/** SM-2 state, stored per user per card. */
export interface ReviewState {
    cardId: string;
    ease: number;
    intervalDays: number;
    repetitions: number;
    lapses: number;
    dueAt: string;
    lastReviewedAt: string | null;
}

export type ReviewGrade = 'again' | 'hard' | 'good' | 'easy';

/* --------------------------------------------------------------- Quiz ---- */

export type QuestionType =
    | 'multiple-choice'
    | 'typing'
    | 'listening'
    | 'speaking'
    | 'matching'
    | 'fill-blank'
    | 'drag-drop'
    | 'true-false';

export interface QuizOption {
    id: string;
    label: string;
    imageUrl?: string | null;
}

export interface MatchPair {
    left: string;
    right: string;
}

export interface Question {
    id: string;
    type: QuestionType;
    prompt: string;
    /** Sentence with `___` where the answer goes (fill-blank). */
    template?: string;
    audioUrl?: string | null;
    imageUrl?: string | null;
    options?: QuizOption[];
    /** Option id, plain text, or an ordered array for drag-drop/matching. */
    correctAnswer: string | string[];
    pairs?: MatchPair[];
    tokens?: string[];
    explanation: string;
    points: number;
}

export type QuizCategory = 'quiz' | 'test';

export interface Quiz {
    id: string;
    lessonId: string | null;
    moduleId: string | null;
    title: string;
    description: string;
    /** Beginner..Advanced for English, N5..N1 (JLPT) for Japanese — the two scales don't mix. */
    cefr: string;
    category: QuizCategory;
    timeLimitSeconds: number | null;
    passScore: number;
    xpReward: number;
    questionIds: string[];
}

export interface AnswerRecord {
    questionId: string;
    given: string | string[];
    correct: boolean;
    timeSpentSeconds: number;
}

export interface QuizResult {
    id: string;
    quizId: string;
    score: number;
    correctCount: number;
    totalCount: number;
    passed: boolean;
    earnedXp: number;
    durationSeconds: number;
    answers: AnswerRecord[];
    completedAt: string;
}

export interface QuizAttemptSummary {
    attemptNumber: number;
    score: number;
    correctCount: number;
    totalCount: number;
    passed: boolean;
    durationSeconds: number;
    completedAt: string;
}

/** Attempt history + stats + remaining-attempts info for one quiz/user pair. */
export interface QuizAttemptsOverview {
    quizId: string;
    attemptsUsed: number;
    /** `null` = unlimited. */
    attemptsLimit: number | null;
    /** `null` = unlimited. */
    attemptsLeft: number | null;
    canAttempt: boolean;
    best: number | null;
    worst: number | null;
    average: number | null;
    latest: number | null;
    /** Newest first. */
    history: QuizAttemptSummary[];
}

/* ----------------------------------------------------------- Progress ---- */

export type ProgressStatus = 'not-started' | 'in-progress' | 'completed';

export interface Progress {
    id: string;
    userId: string;
    moduleId: string | null;
    lessonId: string | null;
    status: ProgressStatus;
    percent: number;
    score: number | null;
    completedAt: string | null;
}

export interface DailyActivity {
    date: string;
    minutes: number;
    xp: number;
    lessonsCompleted: number;
}

/** Raw shape of `GET /learning/dashboard` — see ProgressService::dashboardFor(). */
export interface DashboardStats {
    stat: {
        id: number;
        xp_total: number;
        level: number;
        streak_days: number;
        longest_streak: number;
        last_active_date: string | null;
        lessons_completed: number;
        quizzes_completed: number;
        perfect_quizzes: number;
        flashcards_reviewed: number;
        minutes_learned: number;
    };
    level: number;
    xp_total: number;
    xp_into_level: number;
    xp_for_next: number;
    level_percent: number;
    /** Keyed by ISO date (`YYYY-MM-DD`), not an array — the backend returns a gap-filled map. */
    xp_per_day: Record<string, number>;
    modules: (LearningModule & { completed_lessons: number; completion_percent: number })[];
    recent: Array<{
        id: number;
        amount: number;
        reason: string;
        created_at: string;
        source_type: string | null;
    }>;
    active_module_id: string | null;
}

/* -------------------------------------------------------- Achievement ---- */

export type AchievementCriteria =
    | 'lessons-completed'
    | 'quizzes-completed'
    | 'perfect-quizzes'
    | 'streak-days'
    | 'words-learned'
    | 'xp-total'
    | 'level';

export interface Achievement {
    id: string;
    title: string;
    description: string;
    icon: string;
    color: string;
    criteria: AchievementCriteria;
    threshold: number;
    xpReward: number;
    tier: 'bronze' | 'silver' | 'gold' | 'platinum';
}

export interface UnlockedAchievement {
    achievementId: string;
    progress: number;
    unlockedAt: string | null;
}

/* -------------------------------------------------------- Leaderboard ---- */

export interface LeaderboardEntry {
    rank: number;
    userId: string;
    name: string;
    avatar: string | null;
    xp: number;
    level: number;
    streak: number;
    isCurrentUser: boolean;
}

/* ----------------------------------------------------------- Bookmark ---- */

export type BookmarkKind = 'vocabulary' | 'grammar' | 'reading' | 'listening' | 'conversation';

export interface Bookmark {
    id: string;
    kind: BookmarkKind;
    refId: string;
    title: string;
    subtitle: string;
    createdAt: string;
}

/* -------------------------------------------------------- Certificate ---- */

export interface Certificate {
    id: string;
    title: string;
    /** Actually the module's slug, not its numeric id — matches the `/app/learning/:moduleId` route param, which is slug-keyed. */
    moduleId: string;
    /** Null when a module's lessons don't share a single level. */
    cefr: string | null;
    issuedAt: string | null;
    requirement: string;
    progressPercent: number;
}

/* ---------------------------------------------------- Vocabulary Bank ---- */

export interface VocabularyWordExampleSentence {
    sentenceEn: string;
    sentenceId: string | null;
}

export interface VocabularyBankWord {
    id: string;
    word: string;
    phonetic: string | null;
    partOfSpeech: string | null;
    meaningId: string;
    /** Null for Japanese words — there's no "English meaning" field for those, only the Indonesian one. */
    meaningEn: string | null;
    /** CEFR (Beginner..Advanced) for English, JLPT (N5..N1) for Japanese. */
    level: string;
    synonyms: string[];
    antonyms: string[];
    collocations: string[];
    examples?: VocabularyWordExampleSentence[];
}

export interface VocabularyLevelProgress {
    level: string;
    total: number;
    learned: number;
}

/* ------------------------------------------------------------ Daily Quiz ---- */

export type DailyQuizQuestionType = 'multiple_choice' | 'fill_blank' | 'matching' | 'true_false' | 'context';

export interface DailyQuizQuestionPayload {
    prompt: string;
    options?: string[];
    pairs?: { word: string; meaning: string }[];
    /** fill_blank only — hangman-style pattern like "p__________e", first/last letter of each word revealed. */
    hint?: string;
}

export interface DailyQuizQuestionItem {
    id: string;
    type: DailyQuizQuestionType;
    payload: DailyQuizQuestionPayload;
}

export interface DailyQuizAttempt {
    id: string;
    quizDate: string;
    totalQuestions: number;
    correctCount: number;
    score: number;
    skipped: boolean;
    completedAt: string | null;
    questions: DailyQuizQuestionItem[];
}

export interface DailyQuizStatus {
    required: boolean;
    hasAttemptToday: boolean;
    completedToday: boolean;
    skippedToday: boolean;
    satisfiedToday: boolean;
}

export interface DailyQuizReviewItem {
    questionId: string;
    word: string;
    type: DailyQuizQuestionType;
    prompt: string;
    givenAnswer: string | null;
    correctAnswer: string | null;
    isCorrect: boolean;
}

export interface DailyQuizResult {
    id: string;
    score: number;
    correctCount: number;
    totalQuestions: number;
    earnedXp: number;
    completedAt: string | null;
    review: DailyQuizReviewItem[];
}

export interface WeakWord {
    id: string;
    word: string;
    meaningId: string;
    meaningEn: string;
    level: string;
    wrongCount: number;
    correctStreak: number;
    lastWrongAt: string | null;
}

/* -------------------------------------------------------------- Misc ---- */

export interface Paginated<T> {
    data: T[];
    total: number;
    page: number;
    perPage: number;
}

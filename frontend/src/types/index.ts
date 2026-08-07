/**
 * Domain types. These mirror the Laravel API payloads exactly, so switching
 * `VITE_DATA_SOURCE` from `mock` to `api` needs no type changes.
 */

export type CEFRLevel = 'A1' | 'A2' | 'B1' | 'B2' | 'C1' | 'C2';
export type Difficulty = 'beginner' | 'intermediate' | 'advanced';

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

export interface LearningModule {
    id: string;
    title: string;
    description: string;
    level: Difficulty;
    cefr: CEFRLevel;
    category: SkillType;
    thumbnail: string;
    icon: string;
    color: string;
    durationMinutes: number;
    lessonIds: string[];
    order: number;
}

export interface Lesson {
    id: string;
    moduleId: string;
    title: string;
    description: string;
    type: SkillType;
    order: number;
    durationMinutes: number;
    xpReward: number;
    /** Populated depending on `type`. */
    vocabularyIds?: string[];
    grammarTopicId?: string;
    listeningTrackId?: string;
    readingTextId?: string;
    writingTaskId?: string;
    conversationId?: string;
    speakingIds?: string[];
    quizId?: string;
}

/* ---------------------------------------------------------- Vocabulary ---- */

export interface Category {
    id: string;
    name: string;
    icon: string;
    color: string;
    description: string;
    wordCount: number;
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
    cefr: CEFRLevel;
    difficulty: Difficulty;
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
}

export interface GrammarExample {
    sentence: string;
    meaning: string;
    highlight?: string;
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
    glossary: { word: string; meaning: string }[];
    quizId: string | null;
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

export interface Quiz {
    id: string;
    lessonId: string | null;
    moduleId: string | null;
    title: string;
    description: string;
    cefr: CEFRLevel;
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
    moduleId: string;
    cefr: CEFRLevel;
    issuedAt: string | null;
    requirement: string;
    progressPercent: number;
}

/* -------------------------------------------------------------- Misc ---- */

export interface Paginated<T> {
    data: T[];
    total: number;
    page: number;
    perPage: number;
}

import type {
    CEFRLevel,
    ConversationScenario,
    DialogueLine,
    LanguageSlug,
    Lesson,
    ListeningTrack,
    ReadingText,
    SpeakingExercise,
    SubtitleCue,
    WritingTask,
} from '@/types';
import listeningData from '@/data/listening.json';
import speakingData from '@/data/speaking.json';
import readingData from '@/data/reading.json';
import writingData from '@/data/writing.json';
import conversationData from '@/data/conversation.json';
import { learningService } from './learning.service';
import { http, unwrap } from './client';
import { delay, source } from './config';

const tracks = listeningData as ListeningTrack[];
const exercises = speakingData as SpeakingExercise[];
const texts = readingData as ReadingText[];
const tasks = writingData as WritingTask[];
const scenarios = conversationData as ConversationScenario[];

// Reading, Conversation, Grammar, Vocabulary, Listening, Speaking and
// Writing all have real content now and go live when VITE_DATA_SOURCE=api —
// see each domain's adapter below for exactly how its module maps to the
// mock-compatible shape.
const LIVE = import.meta.env.VITE_DATA_SOURCE === 'api';

/**
 * No dedicated Listening API — reads the real `listening` module through
 * the generic Learning endpoints. There is no recorded audio or per-line
 * timed transcript in the backend, so this is honest about what's missing
 * rather than inventing it: `audioUrl` stays empty (the player already
 * shows a graceful error with a "Bacakan transkrip" text-to-speech
 * fallback for exactly this case), `subtitles` are derived by splitting
 * the transcript into paragraphs/dialogue turns with placeholder timing
 * (seeking does nothing useful without real audio, but the list itself is
 * still a legitimate line-by-line reading aid), and `translation` per cue
 * is left blank since no per-line translation exists.
 */
const LISTENING_MODULE_SLUG = 'listening';

function toListeningTrack(lesson: Lesson): ListeningTrack {
    const transcript = lesson.content ?? '';
    const paragraphs = transcript.split('\n\n').filter(Boolean);

    const subtitles: SubtitleCue[] = paragraphs.map((text, index) => ({
        start: index * 8,
        end: (index + 1) * 8,
        text,
        translation: '',
    }));

    return {
        id: lesson.slug,
        title: lesson.title,
        description: lesson.summary ?? '',
        audioUrl: '',
        durationSeconds: lesson.estimated_minutes * 60,
        accent: 'american',
        cefr: (lesson.level as CEFRLevel | null) ?? 'Elementary',
        transcript,
        subtitles,
        quizId: null,
    };
}

async function liveListeningTracks(): Promise<ListeningTrack[]> {
    const lessons = await learningService.listLessons(LISTENING_MODULE_SLUG).catch(() => []);
    const full = await Promise.all(lessons.map((l) => learningService.getLesson(l.slug)));

    return full.filter((l): l is Lesson => l !== null).map(toListeningTrack);
}

export const listeningService = {
    list: (): Promise<ListeningTrack[]> =>
        LIVE
            ? liveListeningTracks()
            : source(() => delay(tracks), async () => unwrap<ListeningTrack[]>((await http.get('/listening')).data)),
    get: (id: string): Promise<ListeningTrack | null> =>
        LIVE
            ? liveListeningTracks().then((all) => all.find((t) => t.id === id) ?? null)
            : source(
                  () => delay(tracks.find((t) => t.id === id) ?? null),
                  async () => unwrap<ListeningTrack>((await http.get(`/listening/${id}`)).data),
              ),
};

/**
 * No dedicated Speaking API — reads the real `speaking` module. Each
 * lesson holds several phrases to practice, and `SpeakingExercise` is a
 * flat one-phrase-per-exercise shape, so every lesson item becomes its own
 * exercise (`term` → `targetSentence`, `meaning` → `meaning`). There is no
 * real IPA transcription or tagged focus-sound data, so both are left
 * empty rather than invented — the UI already renders those fields
 * conditionally-empty without breaking.
 */
const SPEAKING_MODULE_SLUG = 'speaking';

function toSpeakingExercises(lesson: Lesson): SpeakingExercise[] {
    return (lesson.items ?? [])
        .filter((item) => item.term)
        .map((item) => ({
            id: `${lesson.slug}-${item.id}`,
            itemId: String(item.id),
            targetSentence: item.term,
            ipa: '',
            meaning: item.meaning ?? '',
            audioSample: null,
            focusSounds: [],
            cefr: (lesson.level as CEFRLevel | null) ?? 'Elementary',
        }));
}

async function liveSpeakingExercises(): Promise<SpeakingExercise[]> {
    const lessons = await learningService.listLessons(SPEAKING_MODULE_SLUG).catch(() => []);
    const full = await Promise.all(lessons.map((l) => learningService.getLesson(l.slug)));

    return full.filter((l): l is Lesson => l !== null).flatMap(toSpeakingExercises);
}

export const speakingService = {
    list: (): Promise<SpeakingExercise[]> =>
        LIVE
            ? liveSpeakingExercises()
            : source(() => delay(exercises), async () => unwrap<SpeakingExercise[]>((await http.get('/speaking')).data)),
    get: (id: string): Promise<SpeakingExercise | null> =>
        LIVE
            ? liveSpeakingExercises().then((all) => all.find((e) => e.id === id) ?? null)
            : source(
                  () => delay(exercises.find((e) => e.id === id) ?? null),
                  async () => unwrap<SpeakingExercise>((await http.get(`/speaking/${id}`)).data),
              ),
    listByIds: (ids: string[]) =>
        LIVE
            ? liveSpeakingExercises().then((all) => all.filter((e) => ids.includes(e.id)))
            : delay(ids.map((id) => exercises.find((e) => e.id === id)).filter(Boolean) as SpeakingExercise[]),
    /**
     * Persists XP server-side (see SkillPracticeController) instead of the
     * page mutating the local progress store directly — that XP used to be
     * lost on reload since it never reached the backend. No-op in mock mode
     * (no `itemId`, nothing to award against).
     */
    complete: async (itemId: string | undefined, score: number): Promise<{ earnedXp: number }> =>
        LIVE && itemId
            ? unwrap<{ earnedXp: number }>((await http.post(`/skill-practice/speaking/${itemId}/complete`, { score })).data)
            : { earnedXp: 0 },
};

/**
 * No dedicated Reading API — reads the `reading` (English) and
 * `membaca-jepang` (Japanese) modules through the generic Learning
 * endpoints, the same adapter pattern used by conversationService below.
 * Both hold the same three Indonesian folk tales, retold in each learning
 * language. `type` has no backend source, so every live text is reported
 * as `'story'` (accurate today — both modules only hold story-style
 * content). `glossary` maps from each lesson's items (`term`/`meaning`);
 * `quizId` has no backend source and stays null. `translatedBody` comes
 * from the lesson's `translated_content` column — a real Bahasa Indonesia
 * translation seeded alongside the story, not a machine translation done
 * client-side.
 */
const READING_MODULES: { slug: string; language: LanguageSlug }[] = [
    { slug: 'reading', language: 'english' },
    { slug: 'membaca-jepang', language: 'japanese' },
];

function toReadingText(lesson: Lesson, language: LanguageSlug): ReadingText {
    const body = lesson.content ?? '';

    return {
        id: lesson.slug,
        title: lesson.title,
        type: 'story',
        cefr: (lesson.level as CEFRLevel | null) ?? 'Elementary',
        wordCount: countWords(body),
        readingMinutes: lesson.estimated_minutes,
        coverImage: null,
        body,
        translatedBody: lesson.translated_content ?? null,
        glossary: (lesson.items ?? []).map((item) => ({ word: item.term, meaning: item.meaning ?? '' })),
        quizId: null,
        language,
    };
}

async function liveReadingTexts(): Promise<ReadingText[]> {
    const perModule = await Promise.all(
        READING_MODULES.map(async ({ slug, language }) => {
            const lessons = await learningService.listLessons(slug).catch(() => []);
            const full = await Promise.all(lessons.map((l) => learningService.getLesson(l.slug)));

            return full.filter((l): l is Lesson => l !== null).map((lesson) => toReadingText(lesson, language));
        }),
    );

    return perModule.flat();
}

export const readingService = {
    list: (type?: ReadingText['type']): Promise<ReadingText[]> =>
        LIVE
            ? liveReadingTexts().then((all) => (type ? all.filter((t) => t.type === type) : all))
            : source(
                  () => delay(type ? texts.filter((t) => t.type === type) : texts),
                  async () => unwrap<ReadingText[]>((await http.get('/reading', { params: { type } })).data),
              ),
    get: (id: string): Promise<ReadingText | null> =>
        LIVE
            ? learningService.getLesson(id).then((lesson) => {
                  if (!lesson) return null;

                  const language =
                      READING_MODULES.find((m) => m.slug === lesson.module?.slug)?.language ?? 'english';

                  return toReadingText(lesson, language);
              })
            : source(
                  () => delay(texts.find((t) => t.id === id) ?? null),
                  async () => unwrap<ReadingText>((await http.get(`/reading/${id}`)).data),
              ),
};

/**
 * No dedicated Writing API — reads the real `writing` module. Each lesson
 * is one task: `content` (the HTML task brief) becomes `prompt` (tags
 * stripped, since the UI renders it as plain text), items become `hints`
 * (each item's `meaning` is a short piece of guidance/frame), and the
 * item whose term mentions "Contoh" ("example") holds a complete model
 * answer, used as `sampleAnswer`. There is no structured word-count target
 * or scoring rubric in the backend, so `minWords`/`maxWords` fall back to
 * a sensible range for the lesson's level and `rubric` stays empty rather
 * than inventing criteria/weights.
 */
const WRITING_MODULE_SLUG = 'writing';

const WORD_RANGE_BY_LEVEL: Record<string, [number, number]> = {
    Beginner: [15, 40],
    Elementary: [40, 80],
    Intermediate: [60, 120],
    'Upper-Intermediate': [100, 180],
    Advanced: [120, 220],
};

function stripHtml(html: string): string {
    return html
        .replace(/<[^>]+>/g, ' ')
        .replace(/\s+/g, ' ')
        .trim();
}

function toWritingTask(lesson: Lesson): WritingTask {
    const items = lesson.items ?? [];
    const sampleItem = items.find((item) => item.term.toLowerCase().includes('contoh')) ?? items.at(-1) ?? null;
    const [minWords, maxWords] = WORD_RANGE_BY_LEVEL[lesson.level ?? 'Elementary'] ?? WORD_RANGE_BY_LEVEL.Elementary;

    return {
        id: lesson.slug,
        prompt: stripHtml(lesson.content ?? lesson.summary ?? ''),
        type: 'paragraph',
        cefr: (lesson.level as CEFRLevel | null) ?? 'Elementary',
        minWords,
        maxWords,
        hints: items
            .filter((item) => item !== sampleItem)
            .map((item) => item.meaning || item.term)
            .filter(Boolean) as string[],
        rubric: [],
        sampleAnswer: sampleItem?.example ?? '',
    };
}

async function liveWritingTasks(): Promise<WritingTask[]> {
    const lessons = await learningService.listLessons(WRITING_MODULE_SLUG).catch(() => []);
    const full = await Promise.all(lessons.map((l) => learningService.getLesson(l.slug)));

    return full.filter((l): l is Lesson => l !== null).map(toWritingTask);
}

export const writingService = {
    list: (): Promise<WritingTask[]> =>
        LIVE
            ? liveWritingTasks()
            : source(() => delay(tasks), async () => unwrap<WritingTask[]>((await http.get('/writing')).data)),
    get: (id: string): Promise<WritingTask | null> =>
        LIVE
            ? liveWritingTasks().then((all) => all.find((t) => t.id === id) ?? null)
            : source(
                  () => delay(tasks.find((t) => t.id === id) ?? null),
                  async () => unwrap<WritingTask>((await http.get(`/writing/${id}`)).data),
              ),
    /**
     * Grading is still self-assessed client-side against the sample
     * answer/rubric (no backend rubric exists) — but the XP for submitting
     * now goes through the real backend (SkillPracticeController) instead
     * of a client-only store mutation, so it survives a reload. `taskId` is
     * the Lesson's slug (see toWritingTask() above), which Lesson's route
     * binding already resolves by slug.
     */
    submit: async (taskId: string, body: string) => {
        const earnedXp = LIVE
            ? unwrap<{ earnedXp: number }>((await http.post(`/skill-practice/writing/${taskId}/submit`, { text: body })).data).earnedXp
            : 0;

        return { taskId, wordCount: countWords(body), submittedAt: new Date().toISOString(), earnedXp };
    },
};

/**
 * No dedicated Conversation API — reads the real `percakapan-jepang` module
 * through the generic Learning endpoints. Each item's `extra.speaker` /
 * `extra.giliran` (set by ConversationSeeder.php) map onto `speaker` /
 * `userTurn`; `role` ('a' | 'b', used only for left/right bubble alignment)
 * is derived from that same split so the UI still matches who's "you" vs the
 * other speaker. `keyPhrases` has no source in the generic schema and is
 * left empty rather than picking arbitrary lines and calling them "key".
 */
const CONVERSATION_MODULE_SLUG = 'percakapan-jepang';

function toScenario(lesson: Lesson): ConversationScenario {
    const items = lesson.items ?? [];

    // The "peserta" (learner) speaker is whoever's lines are marked giliran
    // === 'peserta' — everyone else is the other party, role 'a'.
    const userSpeaker = items.find((item) => item.extra?.giliran === 'peserta')?.extra?.speaker as
        | string
        | undefined;

    const lines: DialogueLine[] = items.map((item) => {
        const speaker = (item.extra?.speaker as string | undefined) ?? '?';
        const userTurn = item.extra?.giliran === 'peserta';

        return {
            id: String(item.id),
            speaker,
            role: userSpeaker && speaker === userSpeaker ? 'b' : 'a',
            text: item.term,
            romaji: item.romaji ?? null,
            translation: item.meaning ?? '',
            audioUrl: null,
            userTurn,
        };
    });

    return {
        id: String(lesson.id),
        title: lesson.title,
        context: lesson.summary ?? '',
        situation: '',
        cefr: (lesson.level as CEFRLevel | null) ?? 'Beginner',
        icon: 'MessagesSquare',
        keyPhrases: [],
        lines,
        // Only percakapan-jepang exists live today — see CONVERSATION_MODULE_SLUG below.
        language: 'japanese',
    };
}

async function liveScenarios(): Promise<ConversationScenario[]> {
    const lessons = await learningService.listLessons(CONVERSATION_MODULE_SLUG).catch(() => []);
    const full = await Promise.all(lessons.map((l) => learningService.getLesson(l.slug)));

    return full.filter((l): l is Lesson => l !== null).map(toScenario);
}

export const conversationService = {
    list: (): Promise<ConversationScenario[]> =>
        LIVE
            ? liveScenarios()
            : source(
                  () => delay(scenarios),
                  async () => unwrap<ConversationScenario[]>((await http.get('/conversation')).data),
              ),

    get: (id: string): Promise<ConversationScenario | null> =>
        LIVE
            ? liveScenarios().then((all) => all.find((s) => s.id === id) ?? null)
            : source(
                  () => delay(scenarios.find((s) => s.id === id) ?? null),
                  async () => unwrap<ConversationScenario>((await http.get(`/conversation/${id}`)).data),
              ),
};

export function countWords(text: string): number {
    return text.trim().split(/\s+/).filter(Boolean).length;
}

import type { CEFRLevel, GrammarExample, GrammarKind, GrammarTopic, LanguageSlug, Lesson, LessonItem, Tense } from '@/types';
import grammarData from '@/data/grammar.json';
import tensesData from '@/data/tenses.json';
import { learningService } from './learning.service';
import { http, unwrap } from './client';
import { delay, source } from './config';

const topics = grammarData as GrammarTopic[];
const tenses = tensesData as Tense[];

/**
 * No dedicated Grammar API on the backend — this reads the real
 * `tata-bahasa-jepang` / `english-grammar` modules through the generic
 * Learning endpoints. Tenses have no equivalent at all (that's an English
 * tense-grid concept with a 3-part formula the backend never stores), so
 * `listTenses`/`getTense` return empty in `api` mode rather than guessing —
 * `GrammarPage` hides the Tenses tab entirely when `VITE_DATA_SOURCE=api`.
 */
const LIVE = import.meta.env.VITE_DATA_SOURCE === 'api';
const GRAMMAR_MODULES: { slug: string; language: LanguageSlug }[] = [
    { slug: 'tata-bahasa-jepang', language: 'japanese' },
    { slug: 'english-grammar', language: 'english' },
];

/**
 * Structured grammar data (formula box, kind, wrong/right mistake pairs)
 * rides inside `LessonItem.extra` as `{ type: 'formula' | 'mistake' | 'example', kind?: GrammarKind }` —
 * the only way to carry it through the generic Lesson/LessonItem schema.
 * See `LearningContentSeeder::englishGrammar()` for the seeder-side contract.
 */
function extraType(item: LessonItem): string | undefined {
    const extra = item.extra as { type?: string } | null;

    return extra?.type;
}

function toGrammarTopic(lesson: Lesson, language: LanguageSlug): GrammarTopic {
    const items = lesson.items ?? [];

    const formulaItem = items.find((item) => extraType(item) === 'formula');
    const formulaExtra = formulaItem?.extra as { kind?: GrammarKind } | null;

    const examples: GrammarExample[] = items
        .filter((item) => extraType(item) !== 'formula' && extraType(item) !== 'mistake' && item.example)
        .map((item) => ({ sentence: item.example as string, meaning: item.example_meaning ?? '' }));

    const commonMistakes = items
        .filter((item) => extraType(item) === 'mistake')
        .map((item) => ({ wrong: item.term, right: item.meaning ?? '', why: item.example ?? '' }));

    return {
        id: String(lesson.id),
        title: lesson.title,
        kind: formulaExtra?.kind ?? 'structure',
        cefr: (lesson.level as CEFRLevel | null) ?? 'Beginner',
        explanation: lesson.summary ?? '',
        formula: formulaItem?.meaning ?? null,
        timeline: null,
        examples,
        commonMistakes,
        exerciseIds: [],
        language,
    };
}

/**
 * `listLessons` only loads `withCount('items')` on the backend (a list view
 * needs to stay light), so its lessons never carry `items` — every topic
 * would show 0 examples/formula/mistakes. Fetch each lesson's full detail
 * (which does eager-load items) by slug instead.
 */
async function liveTopics(): Promise<GrammarTopic[]> {
    const perModule = await Promise.all(
        GRAMMAR_MODULES.map(async ({ slug, language }) => {
            const list = await learningService.listLessons(slug).catch(() => []);
            const detailed = await Promise.all(
                list.map(async (lesson) => (await learningService.getLesson(lesson.slug).catch(() => null)) ?? lesson),
            );

            return detailed.map((lesson) => toGrammarTopic(lesson, language));
        }),
    );

    return perModule.flat();
}

export const grammarService = {
    listTopics: (kind?: GrammarKind): Promise<GrammarTopic[]> =>
        LIVE
            ? liveTopics().then((all) => (kind ? all.filter((t) => t.kind === kind) : all))
            : source(
                  () => delay(kind ? topics.filter((t) => t.kind === kind) : topics),
                  async () => unwrap<GrammarTopic[]>((await http.get('/grammar/topics', { params: { kind } })).data),
              ),

    getTopic: (id: string): Promise<GrammarTopic | null> =>
        LIVE
            ? liveTopics().then((all) => all.find((t) => t.id === id) ?? null)
            : source(
                  () => delay(topics.find((t) => t.id === id) ?? null),
                  async () => unwrap<GrammarTopic>((await http.get(`/grammar/topics/${id}`)).data),
              ),

    listTenses: (): Promise<Tense[]> =>
        LIVE
            ? Promise.resolve([])
            : source(
                  () => delay(tenses),
                  async () => unwrap<Tense[]>((await http.get('/grammar/tenses')).data),
              ),

    getTense: (id: string): Promise<Tense | null> =>
        LIVE
            ? Promise.resolve(null)
            : source(
                  () => delay(tenses.find((t) => t.id === id) ?? null),
                  async () => unwrap<Tense>((await http.get(`/grammar/tenses/${id}`)).data),
              ),

    /** Tenses bucketed by group, which is how the tense grid is laid out. Empty in `api` mode — see module note. */
    tensesByGroup: (): Record<string, Tense[]> =>
        LIVE
            ? {}
            : tenses.reduce<Record<string, Tense[]>>((groups, tense) => {
                  (groups[tense.group] ??= []).push(tense);

                  return groups;
              }, {}),
};

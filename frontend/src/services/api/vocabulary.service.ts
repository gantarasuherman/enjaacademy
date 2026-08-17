import type { Category, LanguageSlug, Lesson, LessonItem, VocabularyItem } from '@/types';
import categoriesData from '@/data/categories.json';
import vocabularyData from '@/data/vocabulary.json';
import { learningService } from './learning.service';
import { http, unwrap } from './client';
import { delay, source } from './config';

const categories = categoriesData as Category[];
const words = vocabularyData as VocabularyItem[];

/**
 * The backend has no dedicated Vocabulary API — this reads the real
 * `kosakata-jepang` / `english-vocabulary` modules through the generic
 * Learning endpoints and reshapes lessons into "categories" and items into
 * `VocabularyItem`. Fields the generic schema never stores (IPA, part of
 * speech, synonyms, antonyms, audio) are left empty rather than invented —
 * `VocabularyPage` already hides those sections when they're empty.
 */
const LIVE = import.meta.env.VITE_DATA_SOURCE === 'api';
const VOCAB_MODULES: { slug: string; language: LanguageSlug }[] = [
    { slug: 'kosakata-jepang', language: 'japanese' },
    { slug: 'english-vocabulary', language: 'english' },
];

function toVocabularyItem(item: LessonItem, lesson: Lesson, language: LanguageSlug): VocabularyItem {
    return {
        id: String(item.id),
        word: item.term,
        // Japanese entries store the hiragana/katakana *reading* here, which
        // just repeats `term` for kana-only words like もし — romaji (Latin
        // script an Indonesian reader can actually pronounce) belongs in the
        // pronunciation slot instead. English entries have no `romaji`, so
        // `reading` (real IPA, e.g. /bɪɡ/) is what that field should show.
        ipa: language === 'japanese' ? (item.romaji ?? item.reading ?? '') : (item.reading ?? ''),
        partOfSpeech: '',
        audioUrl: null,
        imageUrl: null,
        meaning: item.meaning ?? '',
        example: item.example ?? '',
        exampleMeaning: item.example_meaning ?? '',
        synonyms: [],
        antonyms: [],
        categoryId: String(lesson.id),
        cefr: lesson.level ?? (language === 'japanese' ? 'N5' : 'Beginner'),
        difficulty: 'beginner',
        language,
    };
}

/**
 * `GET .../lessons` doesn't include items (only the single-lesson endpoint
 * does), so this fetches every vocabulary lesson's detail individually — an
 * N+1 pattern, acceptable at this dataset's size (~22 lessons total) and
 * avoids adding a new backend endpoint for this domain.
 */
async function liveCategoriesWithItems(): Promise<{ lesson: Lesson; items: LessonItem[]; language: LanguageSlug }[]> {
    const perModule = await Promise.all(
        VOCAB_MODULES.map(async ({ slug, language }) => {
            const lessons = await learningService.listLessons(slug).catch(() => []);

            return lessons.map((lesson) => ({ lesson, language }));
        }),
    );

    return Promise.all(
        perModule.flat().map(async ({ lesson, language }) => {
            const full = await learningService.getLesson(lesson.slug);

            return { lesson, items: full?.items ?? [], language };
        }),
    );
}

export interface VocabularyFilters {
    categoryId?: string;
    search?: string;
    cefr?: string;
    partOfSpeech?: string;
    language?: LanguageSlug;
}

function applyFilters(items: VocabularyItem[], filters?: VocabularyFilters): VocabularyItem[] {
    const term = filters?.search?.trim().toLowerCase();

    return items.filter((item) => {
        if (filters?.categoryId && item.categoryId !== filters.categoryId) return false;
        if (filters?.cefr && item.cefr !== filters.cefr) return false;
        if (filters?.partOfSpeech && item.partOfSpeech !== filters.partOfSpeech) return false;
        if (filters?.language && item.language !== filters.language) return false;

        if (term) {
            const haystack = `${item.word} ${item.meaning} ${item.example}`.toLowerCase();
            if (!haystack.includes(term)) return false;
        }

        return true;
    });
}

export const vocabularyService = {
    listCategories: (): Promise<Category[]> =>
        LIVE
            ? liveCategoriesWithItems().then((rows) =>
                  rows.map(({ lesson, items, language }) => ({
                      id: String(lesson.id),
                      name: lesson.title,
                      icon: 'BookMarked',
                      color: 'secondary',
                      description: lesson.summary ?? '',
                      wordCount: items.length,
                      language,
                  })),
              )
            : source(
                  () =>
                      // Keep the displayed count honest even if the JSON drifts.
                      delay(
                          categories.map((category) => ({
                              ...category,
                              wordCount: words.filter((w) => w.categoryId === category.id).length,
                          })),
                      ),
                  async () => unwrap<Category[]>((await http.get('/vocabulary/categories')).data),
              ),

    list: (filters?: VocabularyFilters): Promise<VocabularyItem[]> =>
        LIVE
            ? liveCategoriesWithItems().then((rows) =>
                  applyFilters(
                      rows.flatMap(({ lesson, items, language }) =>
                          items.map((item) => toVocabularyItem(item, lesson, language)),
                      ),
                      filters,
                  ),
              )
            : source(
                  () => delay(applyFilters(words, filters)),
                  async () => unwrap<VocabularyItem[]>((await http.get('/vocabulary', { params: filters })).data),
              ),

    get: (id: string): Promise<VocabularyItem | null> =>
        LIVE
            ? liveCategoriesWithItems().then((rows) => {
                  for (const { lesson, items, language } of rows) {
                      const found = items.find((item) => String(item.id) === id);
                      if (found) return toVocabularyItem(found, lesson, language);
                  }

                  return null;
              })
            : source(
                  () => delay(words.find((w) => w.id === id) ?? null),
                  async () => unwrap<VocabularyItem>((await http.get(`/vocabulary/${id}`)).data),
              ),

    listByIds: (ids: string[]): Promise<VocabularyItem[]> =>
        LIVE
            ? vocabularyService.list().then((all) => all.filter((w) => ids.includes(w.id)))
            : source(
                  () => delay(ids.map((id) => words.find((w) => w.id === id)).filter(Boolean) as VocabularyItem[]),
                  async () => unwrap<VocabularyItem[]>((await http.get('/vocabulary', { params: { ids } })).data),
              ),

    /** Distinct part-of-speech values, for the filter chips. Empty in `api` mode — the field is never populated. */
    partsOfSpeech: (): string[] => (LIVE ? [] : [...new Set(words.map((w) => w.partOfSpeech))].sort()),
};

import type { Category, VocabularyItem } from '@/types';
import categoriesData from '@/data/categories.json';
import vocabularyData from '@/data/vocabulary.json';
import { http, unwrap } from './client';
import { delay, source } from './config';

const categories = categoriesData as Category[];
const words = vocabularyData as VocabularyItem[];

export interface VocabularyFilters {
    categoryId?: string;
    search?: string;
    cefr?: string;
    partOfSpeech?: string;
}

function applyFilters(items: VocabularyItem[], filters?: VocabularyFilters): VocabularyItem[] {
    const term = filters?.search?.trim().toLowerCase();

    return items.filter((item) => {
        if (filters?.categoryId && item.categoryId !== filters.categoryId) return false;
        if (filters?.cefr && item.cefr !== filters.cefr) return false;
        if (filters?.partOfSpeech && item.partOfSpeech !== filters.partOfSpeech) return false;

        if (term) {
            const haystack = `${item.word} ${item.meaning} ${item.example}`.toLowerCase();
            if (!haystack.includes(term)) return false;
        }

        return true;
    });
}

export const vocabularyService = {
    listCategories: () =>
        source(
            () =>
                delay(
                    // Keep the displayed count honest even if the JSON drifts.
                    categories.map((category) => ({
                        ...category,
                        wordCount: words.filter((w) => w.categoryId === category.id).length,
                    })),
                ),
            async () => unwrap<Category[]>((await http.get('/vocabulary/categories')).data),
        ),

    list: (filters?: VocabularyFilters) =>
        source(
            () => delay(applyFilters(words, filters)),
            async () => unwrap<VocabularyItem[]>((await http.get('/vocabulary', { params: filters })).data),
        ),

    get: (id: string) =>
        source(
            () => delay(words.find((w) => w.id === id) ?? null),
            async () => unwrap<VocabularyItem>((await http.get(`/vocabulary/${id}`)).data),
        ),

    listByIds: (ids: string[]) =>
        source(
            () => delay(ids.map((id) => words.find((w) => w.id === id)).filter(Boolean) as VocabularyItem[]),
            async () => unwrap<VocabularyItem[]>((await http.get('/vocabulary', { params: { ids } })).data),
        ),

    /** Distinct part-of-speech values, for the filter chips. */
    partsOfSpeech: () => [...new Set(words.map((w) => w.partOfSpeech))].sort(),
};

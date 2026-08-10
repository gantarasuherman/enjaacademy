import type { GrammarTrack, GrammarTrackLanguage, JlptGrammarCategory, JlptGrammarLevel, JlptGrammarPattern } from '@/types';
import { http, unwrap } from './client';

/**
 * The admin-extensible Grammar CMS (levels/categories/patterns) is a brand
 * new feature with no mock counterpart yet — like `learningService`, it
 * always hits the real API regardless of `VITE_DATA_SOURCE`. One set of
 * tables backs four independent trees, selected by (language, track):
 * Japanese/English Grammar (JLPT-style and CEFR-style level names) and
 * Japanese/English sentence Structure.
 */
export const jlptGrammarService = {
    listLevels: async (params: { language?: GrammarTrackLanguage; track?: GrammarTrack } = {}): Promise<JlptGrammarLevel[]> =>
        unwrap<JlptGrammarLevel[]>((await http.get('/grammar/levels', { params })).data),

    getCategory: async (categoryId: string): Promise<JlptGrammarCategory> =>
        unwrap<JlptGrammarCategory>((await http.get(`/grammar/categories/${categoryId}`)).data),

    listPatterns: async (categoryId: string): Promise<JlptGrammarPattern[]> =>
        unwrap<JlptGrammarPattern[]>((await http.get(`/grammar/categories/${categoryId}/patterns`)).data),

    getPattern: async (patternId: string): Promise<JlptGrammarPattern> =>
        unwrap<JlptGrammarPattern>((await http.get(`/grammar/patterns/${patternId}`)).data),
};

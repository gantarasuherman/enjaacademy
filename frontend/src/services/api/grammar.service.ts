import type { GrammarKind, GrammarTopic, Tense } from '@/types';
import grammarData from '@/data/grammar.json';
import tensesData from '@/data/tenses.json';
import { http, unwrap } from './client';
import { delay, source } from './config';

const topics = grammarData as GrammarTopic[];
const tenses = tensesData as Tense[];

export const grammarService = {
    listTopics: (kind?: GrammarKind) =>
        source(
            () => delay(kind ? topics.filter((t) => t.kind === kind) : topics),
            async () => unwrap<GrammarTopic[]>((await http.get('/grammar/topics', { params: { kind } })).data),
        ),

    getTopic: (id: string) =>
        source(
            () => delay(topics.find((t) => t.id === id) ?? null),
            async () => unwrap<GrammarTopic>((await http.get(`/grammar/topics/${id}`)).data),
        ),

    listTenses: () =>
        source(
            () => delay(tenses),
            async () => unwrap<Tense[]>((await http.get('/grammar/tenses')).data),
        ),

    getTense: (id: string) =>
        source(
            () => delay(tenses.find((t) => t.id === id) ?? null),
            async () => unwrap<Tense>((await http.get(`/grammar/tenses/${id}`)).data),
        ),

    /** Tenses bucketed by group, which is how the tense grid is laid out. */
    tensesByGroup: () =>
        tenses.reduce<Record<string, Tense[]>>((groups, tense) => {
            (groups[tense.group] ??= []).push(tense);
            return groups;
        }, {}),
};

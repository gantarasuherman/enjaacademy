import type {
    ConversationScenario,
    ListeningTrack,
    ReadingText,
    SpeakingExercise,
    WritingTask,
} from '@/types';
import listeningData from '@/data/listening.json';
import speakingData from '@/data/speaking.json';
import readingData from '@/data/reading.json';
import writingData from '@/data/writing.json';
import conversationData from '@/data/conversation.json';
import { http, unwrap } from './client';
import { delay, source } from './config';

const tracks = listeningData as ListeningTrack[];
const exercises = speakingData as SpeakingExercise[];
const texts = readingData as ReadingText[];
const tasks = writingData as WritingTask[];
const scenarios = conversationData as ConversationScenario[];

export const listeningService = {
    list: () => source(() => delay(tracks), async () => unwrap<ListeningTrack[]>((await http.get('/listening')).data)),
    get: (id: string) =>
        source(
            () => delay(tracks.find((t) => t.id === id) ?? null),
            async () => unwrap<ListeningTrack>((await http.get(`/listening/${id}`)).data),
        ),
};

export const speakingService = {
    list: () =>
        source(() => delay(exercises), async () => unwrap<SpeakingExercise[]>((await http.get('/speaking')).data)),
    get: (id: string) =>
        source(
            () => delay(exercises.find((e) => e.id === id) ?? null),
            async () => unwrap<SpeakingExercise>((await http.get(`/speaking/${id}`)).data),
        ),
    listByIds: (ids: string[]) =>
        delay(ids.map((id) => exercises.find((e) => e.id === id)).filter(Boolean) as SpeakingExercise[]),
};

export const readingService = {
    list: (type?: ReadingText['type']) =>
        source(
            () => delay(type ? texts.filter((t) => t.type === type) : texts),
            async () => unwrap<ReadingText[]>((await http.get('/reading', { params: { type } })).data),
        ),
    get: (id: string) =>
        source(
            () => delay(texts.find((t) => t.id === id) ?? null),
            async () => unwrap<ReadingText>((await http.get(`/reading/${id}`)).data),
        ),
};

export const writingService = {
    list: () => source(() => delay(tasks), async () => unwrap<WritingTask[]>((await http.get('/writing')).data)),
    get: (id: string) =>
        source(
            () => delay(tasks.find((t) => t.id === id) ?? null),
            async () => unwrap<WritingTask>((await http.get(`/writing/${id}`)).data),
        ),
    submit: (taskId: string, body: string) =>
        source(
            () => delay({ taskId, wordCount: countWords(body), submittedAt: new Date().toISOString() }),
            async () => unwrap((await http.post(`/writing/${taskId}/submit`, { body })).data),
        ),
};

export const conversationService = {
    list: () =>
        source(
            () => delay(scenarios),
            async () => unwrap<ConversationScenario[]>((await http.get('/conversation')).data),
        ),
    get: (id: string) =>
        source(
            () => delay(scenarios.find((s) => s.id === id) ?? null),
            async () => unwrap<ConversationScenario>((await http.get(`/conversation/${id}`)).data),
        ),
};

export function countWords(text: string): number {
    return text.trim().split(/\s+/).filter(Boolean).length;
}

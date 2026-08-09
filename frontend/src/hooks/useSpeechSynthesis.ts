import { useCallback, useEffect, useRef, useState } from 'react';

/**
 * Hiragana, katakana, and CJK ideographs (kanji) — if any of these show up in
 * the text, it's Japanese. Call sites across Vocabulary/Grammar/Conversation/
 * Lesson pages now render both Japanese and English content from the same
 * generic data, so the language can't be assumed from the page — it has to
 * be read off the text itself.
 */
const JAPANESE_SCRIPT = /[぀-ヿ一-鿿]/u;

function detectLang(text: string, fallback: string): string {
    return JAPANESE_SCRIPT.test(text) ? 'ja-JP' : fallback;
}

/**
 * The browser usually offers several voices per language (a compact local
 * OS voice plus, on Chrome, a cloud "network" voice from Google) and picking
 * whichever comes first in `getVoices()` order tends to land on the
 * stiffest-sounding one. Score candidates so the more natural voice wins:
 * network/cloud voices generally sound less robotic than a device's
 * lightweight built-in one, and some OSes literally label their better
 * voices as "Natural"/"Enhanced"/"Premium".
 */
function scoreVoice(voice: SpeechSynthesisVoice): number {
    const name = voice.name.toLowerCase();
    let score = 0;

    if (!voice.localService) score += 3;
    if (/natural|neural|enhanced|premium|plus/.test(name)) score += 2;
    if (/compact/.test(name)) score -= 2;

    return score;
}

function pickBestVoice(candidates: SpeechSynthesisVoice[]): SpeechSynthesisVoice | undefined {
    return candidates.reduce<SpeechSynthesisVoice | undefined>(
        (best, candidate) => (!best || scoreVoice(candidate) > scoreVoice(best) ? candidate : best),
        undefined,
    );
}

/**
 * Text-to-speech for vocabulary and example sentences. The dummy dataset has
 * no audio files, so the browser's own voices stand in for them.
 */
export function useSpeechSynthesis(defaultLang = 'en-US') {
    const [supported] = useState(() => typeof window !== 'undefined' && 'speechSynthesis' in window);
    const [speaking, setSpeaking] = useState(false);
    const [voices, setVoices] = useState<SpeechSynthesisVoice[]>([]);
    // Character offset of the word currently being spoken, within whatever
    // text was last passed to `speak()` — lets callers highlight along as
    // the browser reads (Chrome/Edge fire this per word; Safari/Firefox may
    // not fire it at all, in which case this just stays null).
    const [charIndex, setCharIndex] = useState<number | null>(null);
    // Timestamp of the most recent *real* `onboundary` firing (not the
    // `charIndex=0` set on `onstart`) — `null` means none has arrived yet
    // this utterance. Some voices, notably many Japanese ones, fire this
    // once (or a handful of times for whole-sentence chunks) and then never
    // again for the rest of a long utterance — a one-time "has it ever
    // fired" check would trust `charIndex` forever after that single event
    // and then sit frozen just the same, so callers need the timestamp to
    // detect *staleness*, not just presence.
    const [lastBoundaryAt, setLastBoundaryAt] = useState<number | null>(null);
    const utteranceRef = useRef<SpeechSynthesisUtterance | null>(null);

    useEffect(() => {
        if (!supported) return;

        const load = () => setVoices(window.speechSynthesis.getVoices());

        load();
        window.speechSynthesis.addEventListener('voiceschanged', load);

        return () => {
            window.speechSynthesis.removeEventListener('voiceschanged', load);
            window.speechSynthesis.cancel();
        };
    }, [supported]);

    const speak = useCallback(
        (text: string, options?: { lang?: string; rate?: number; voiceName?: string }) => {
            if (!supported || !text.trim()) return;

            // Cancel first — queued utterances feel broken when a learner taps
            // several words in a row.
            window.speechSynthesis.cancel();

            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = options?.lang ?? detectLang(text, defaultLang);
            // Slowing speech down reads as careful in English but as stilted,
            // mora-by-mora in Japanese — its natural cadence wants full speed.
            utterance.rate = options?.rate ?? (utterance.lang === 'ja-JP' ? 1 : 0.95);

            const exactLangMatches = voices.filter((v) => v.lang === utterance.lang);
            const prefixMatches = voices.filter((v) => v.lang.startsWith(utterance.lang.slice(0, 2)));

            const preferred =
                (options?.voiceName && voices.find((v) => v.name === options.voiceName)) ??
                pickBestVoice(exactLangMatches.length ? exactLangMatches : prefixMatches);

            if (preferred) utterance.voice = preferred;

            utterance.onstart = () => {
                setSpeaking(true);
                setCharIndex(0);
                setLastBoundaryAt(null);
            };
            utterance.onboundary = (event) => {
                setCharIndex(event.charIndex);
                setLastBoundaryAt(Date.now());
            };
            utterance.onend = () => {
                setSpeaking(false);
                setCharIndex(null);
                setLastBoundaryAt(null);
            };
            utterance.onerror = () => {
                setSpeaking(false);
                setCharIndex(null);
                setLastBoundaryAt(null);
            };

            utteranceRef.current = utterance;
            window.speechSynthesis.speak(utterance);
        },
        [supported, defaultLang, voices],
    );

    const stop = useCallback(() => {
        if (!supported) return;

        window.speechSynthesis.cancel();
        setSpeaking(false);
        setCharIndex(null);
    }, [supported]);

    return { supported, speaking, voices, charIndex, lastBoundaryAt, speak, stop };
}

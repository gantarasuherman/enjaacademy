import { useCallback, useEffect, useRef, useState } from 'react';

/**
 * Text-to-speech for vocabulary and example sentences. The dummy dataset has
 * no audio files, so the browser's own voices stand in for them.
 */
export function useSpeechSynthesis(defaultLang = 'en-US') {
    const [supported] = useState(() => typeof window !== 'undefined' && 'speechSynthesis' in window);
    const [speaking, setSpeaking] = useState(false);
    const [voices, setVoices] = useState<SpeechSynthesisVoice[]>([]);
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
            utterance.lang = options?.lang ?? defaultLang;
            utterance.rate = options?.rate ?? 0.95;

            const preferred =
                (options?.voiceName && voices.find((v) => v.name === options.voiceName)) ??
                voices.find((v) => v.lang === utterance.lang) ??
                voices.find((v) => v.lang.startsWith(utterance.lang.slice(0, 2)));

            if (preferred) utterance.voice = preferred;

            utterance.onstart = () => setSpeaking(true);
            utterance.onend = () => setSpeaking(false);
            utterance.onerror = () => setSpeaking(false);

            utteranceRef.current = utterance;
            window.speechSynthesis.speak(utterance);
        },
        [supported, defaultLang, voices],
    );

    const stop = useCallback(() => {
        if (!supported) return;

        window.speechSynthesis.cancel();
        setSpeaking(false);
    }, [supported]);

    return { supported, speaking, voices, speak, stop };
}

import { useCallback, useEffect, useRef, useState } from 'react';

/**
 * Minimal typings for the Web Speech API, which is still vendor-prefixed and
 * absent from lib.dom.d.ts.
 */
interface SpeechRecognitionAlternative {
    transcript: string;
    confidence: number;
}

interface SpeechRecognitionResultLike {
    readonly length: number;
    readonly isFinal: boolean;
    [index: number]: SpeechRecognitionAlternative;
}

interface SpeechRecognitionEventLike extends Event {
    resultIndex: number;
    results: { length: number; [index: number]: SpeechRecognitionResultLike };
}

interface SpeechRecognitionLike extends EventTarget {
    lang: string;
    continuous: boolean;
    interimResults: boolean;
    maxAlternatives: number;
    start: () => void;
    stop: () => void;
    abort: () => void;
    onresult: ((event: SpeechRecognitionEventLike) => void) | null;
    onerror: ((event: Event & { error?: string }) => void) | null;
    onend: (() => void) | null;
}

type SpeechRecognitionCtor = new () => SpeechRecognitionLike;

function getRecognitionCtor(): SpeechRecognitionCtor | null {
    const w = window as unknown as {
        SpeechRecognition?: SpeechRecognitionCtor;
        webkitSpeechRecognition?: SpeechRecognitionCtor;
    };

    return w.SpeechRecognition ?? w.webkitSpeechRecognition ?? null;
}

export interface UseSpeechRecognitionOptions {
    lang?: string;
    continuous?: boolean;
    interimResults?: boolean;
}

export function useSpeechRecognition({
    lang = 'en-US',
    continuous = false,
    interimResults = true,
}: UseSpeechRecognitionOptions = {}) {
    const [supported] = useState(() => getRecognitionCtor() !== null);
    const [listening, setListening] = useState(false);
    const [transcript, setTranscript] = useState('');
    const [interim, setInterim] = useState('');
    const [error, setError] = useState<string | null>(null);

    const recognitionRef = useRef<SpeechRecognitionLike | null>(null);

    useEffect(() => {
        const Ctor = getRecognitionCtor();
        if (!Ctor) return;

        const recognition = new Ctor();
        recognition.lang = lang;
        recognition.continuous = continuous;
        recognition.interimResults = interimResults;
        recognition.maxAlternatives = 1;

        recognition.onresult = (event) => {
            let finalText = '';
            let interimText = '';

            for (let i = event.resultIndex; i < event.results.length; i++) {
                const result = event.results[i];
                const text = result[0]?.transcript ?? '';

                if (result.isFinal) {
                    finalText += text;
                } else {
                    interimText += text;
                }
            }

            if (finalText) setTranscript((prev) => (prev ? `${prev} ${finalText}`.trim() : finalText.trim()));
            setInterim(interimText);
        };

        recognition.onerror = (event) => {
            const code = (event as Event & { error?: string }).error;

            setError(
                code === 'not-allowed'
                    ? 'Akses mikrofon ditolak. Izinkan mikrofon di pengaturan browser.'
                    : code === 'no-speech'
                      ? 'Tidak ada suara terdeteksi. Coba lagi.'
                      : 'Pengenalan suara gagal. Coba lagi.',
            );
            setListening(false);
        };

        recognition.onend = () => {
            setListening(false);
            setInterim('');
        };

        recognitionRef.current = recognition;

        return () => {
            recognition.onresult = null;
            recognition.onerror = null;
            recognition.onend = null;
            recognition.abort();
        };
    }, [lang, continuous, interimResults]);

    const start = useCallback(() => {
        if (!recognitionRef.current || listening) return;

        setTranscript('');
        setInterim('');
        setError(null);

        try {
            recognitionRef.current.start();
            setListening(true);
        } catch {
            // start() throws if called while already running; ignore.
        }
    }, [listening]);

    const stop = useCallback(() => {
        recognitionRef.current?.stop();
        setListening(false);
    }, []);

    const reset = useCallback(() => {
        setTranscript('');
        setInterim('');
        setError(null);
    }, []);

    return { supported, listening, transcript, interim, error, start, stop, reset };
}

/**
 * Word-level comparison between a target sentence and what was heard.
 * Returns a 0–100 score plus per-word matches for the highlighted diff.
 */
export function scorePronunciation(target: string, spoken: string) {
    const clean = (value: string) =>
        value
            .toLowerCase()
            .replace(/[.,!?;:'"]/g, '')
            .split(/\s+/)
            .filter(Boolean);

    const targetWords = clean(target);
    const spokenWords = clean(spoken);
    const remaining = [...spokenWords];

    const wordScores = targetWords.map((word) => {
        const index = remaining.indexOf(word);

        if (index >= 0) {
            // Consume the match so a repeated word isn't credited twice.
            remaining.splice(index, 1);
            return { word, matched: true };
        }

        return { word, matched: false };
    });

    const matched = wordScores.filter((w) => w.matched).length;
    const score = targetWords.length === 0 ? 0 : Math.round((matched / targetWords.length) * 100);

    const feedback =
        score >= 90
            ? 'Luar biasa! Pelafalanmu sangat jelas.'
            : score >= 70
              ? 'Bagus. Beberapa kata masih kurang jelas — coba pelan-pelan.'
              : score >= 40
                ? 'Lumayan. Fokus pada kata yang ditandai merah dan ulangi.'
                : 'Belum tertangkap dengan baik. Bicara lebih dekat ke mikrofon dan lebih pelan.';

    return { score, wordScores, feedback };
}

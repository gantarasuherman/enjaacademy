import { useCallback, useEffect, useRef, useState } from 'react';

/**
 * Wraps an <audio> element with the state the listening player needs.
 * Missing audio files are reported through `error` rather than failing
 * silently, because the dummy dataset ships without real recordings.
 */
export function useAudioPlayer(src: string | null) {
    const audioRef = useRef<HTMLAudioElement | null>(null);
    const [playing, setPlaying] = useState(false);
    const [currentTime, setCurrentTime] = useState(0);
    const [duration, setDuration] = useState(0);
    const [rate, setRate] = useState(1);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        if (!src) {
            audioRef.current = null;
            return;
        }

        const audio = new Audio(src);
        audio.preload = 'metadata';
        audioRef.current = audio;

        const onLoaded = () => setDuration(audio.duration || 0);
        const onTime = () => setCurrentTime(audio.currentTime);
        const onEnd = () => setPlaying(false);
        const onError = () => setError('File audio belum tersedia untuk materi ini.');

        audio.addEventListener('loadedmetadata', onLoaded);
        audio.addEventListener('timeupdate', onTime);
        audio.addEventListener('ended', onEnd);
        audio.addEventListener('error', onError);

        return () => {
            audio.pause();
            audio.removeEventListener('loadedmetadata', onLoaded);
            audio.removeEventListener('timeupdate', onTime);
            audio.removeEventListener('ended', onEnd);
            audio.removeEventListener('error', onError);
        };
    }, [src]);

    const toggle = useCallback(() => {
        const audio = audioRef.current;
        if (!audio) return;

        if (audio.paused) {
            void audio.play().then(() => setPlaying(true)).catch(() => setError('Audio tidak dapat diputar.'));
        } else {
            audio.pause();
            setPlaying(false);
        }
    }, []);

    const seek = useCallback((seconds: number) => {
        const audio = audioRef.current;
        if (!audio) return;

        audio.currentTime = Math.max(0, Math.min(audio.duration || 0, seconds));
        setCurrentTime(audio.currentTime);
    }, []);

    const skip = useCallback((delta: number) => seek((audioRef.current?.currentTime ?? 0) + delta), [seek]);

    const changeRate = useCallback((value: number) => {
        if (audioRef.current) audioRef.current.playbackRate = value;
        setRate(value);
    }, []);

    return { playing, currentTime, duration, rate, error, toggle, seek, skip, changeRate };
}

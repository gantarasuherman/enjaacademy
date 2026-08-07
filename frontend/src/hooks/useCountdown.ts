import { useEffect, useRef } from 'react';

/**
 * Calls `onTick` once per second while `active`. The callback is held in a ref
 * so a re-render never restarts the interval mid-quiz.
 */
export function useCountdown(active: boolean, onTick: () => void) {
    const savedTick = useRef(onTick);

    useEffect(() => {
        savedTick.current = onTick;
    }, [onTick]);

    useEffect(() => {
        if (!active) return;

        const timer = setInterval(() => savedTick.current(), 1000);

        return () => clearInterval(timer);
    }, [active]);
}

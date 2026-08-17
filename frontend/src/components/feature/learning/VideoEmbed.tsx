import { forwardRef, useEffect, useImperativeHandle, useMemo, useRef } from 'react';
import { VideoOff } from 'lucide-react';
import { EmptyState } from '@/components/ui/Feedback';

export interface VideoEmbedHandle {
    seekTo: (seconds: number) => void;
}

type ParsedVideo = { provider: 'youtube'; id: string } | { provider: 'vimeo'; id: string };

/** Extracts the provider + video id from the handful of URL shapes admins realistically paste in. */
function parseVideoUrl(url: string): ParsedVideo | null {
    const youtube = url.match(/(?:youtube\.com\/watch\?v=|youtube\.com\/embed\/|youtu\.be\/)([\w-]{6,})/);
    if (youtube) return { provider: 'youtube', id: youtube[1] };

    const vimeo = url.match(/vimeo\.com\/(?:video\/)?(\d+)/);
    if (vimeo) return { provider: 'vimeo', id: vimeo[1] };

    return null;
}

declare global {
    interface Window {
        YT?: { Player: new (target: Element, options: Record<string, unknown>) => YouTubePlayer; PlayerState: { ENDED: number } };
        onYouTubeIframeAPIReady?: () => void;
        Vimeo?: { Player: new (target: Element, options: Record<string, unknown>) => VimeoPlayer };
    }
}

interface YouTubePlayer {
    seekTo: (seconds: number, allowSeekAhead: boolean) => void;
    getIframe: () => HTMLIFrameElement;
    destroy: () => void;
}

interface VimeoPlayer {
    setCurrentTime: (seconds: number) => void;
    on: (event: string, handler: () => void) => void;
    destroy: () => Promise<void>;
    element?: HTMLIFrameElement;
}

const scriptPromises = new Map<string, Promise<void>>();

function loadScript(src: string): Promise<void> {
    let promise = scriptPromises.get(src);

    if (!promise) {
        promise = new Promise((resolve) => {
            const script = document.createElement('script');
            script.src = src;
            script.async = true;
            script.onload = () => resolve();
            document.head.appendChild(script);
        });
        scriptPromises.set(src, promise);
    }

    return promise;
}

function loadYouTubeApi(): Promise<NonNullable<Window['YT']>> {
    if (window.YT?.Player) return Promise.resolve(window.YT);

    return new Promise((resolve) => {
        const previous = window.onYouTubeIframeAPIReady;
        window.onYouTubeIframeAPIReady = () => {
            previous?.();
            resolve(window.YT!);
        };
        loadScript('https://www.youtube.com/iframe_api');
    });
}

function loadVimeoApi(): Promise<NonNullable<Window['Vimeo']>> {
    return loadScript('https://player.vimeo.com/api/player.js').then(() => window.Vimeo!);
}

/** Fills its container edge-to-edge — the wrapping element controls the aspect ratio. */
const FILL_IFRAME_CLASSES = ['absolute', 'inset-0', 'size-full', 'border-0'];

/**
 * YouTube/Vimeo embed using each provider's official player SDK (loaded on
 * demand, no npm dependency) — gives reliable `seekTo` and an `onEnded`
 * callback for autoplay-next, which the raw postMessage protocol can't do
 * without reverse-engineering undocumented event formats.
 */
export const VideoEmbed = forwardRef<VideoEmbedHandle, { url: string; onEnded?: () => void }>(function VideoEmbed(
    { url, onEnded },
    ref,
) {
    const containerRef = useRef<HTMLDivElement>(null);
    const playerRef = useRef<YouTubePlayer | VimeoPlayer | null>(null);
    const onEndedRef = useRef(onEnded);
    onEndedRef.current = onEnded;

    const parsed = useMemo(() => parseVideoUrl(url), [url]);

    useImperativeHandle(ref, () => ({
        seekTo(seconds: number) {
            if (!parsed || !playerRef.current) return;

            if (parsed.provider === 'youtube') {
                (playerRef.current as YouTubePlayer).seekTo(seconds, true);
            } else {
                (playerRef.current as VimeoPlayer).setCurrentTime(seconds);
            }
        },
    }));

    useEffect(() => {
        if (!parsed || !containerRef.current) return;

        let cancelled = false;

        if (parsed.provider === 'youtube') {
            loadYouTubeApi().then((YT) => {
                if (cancelled || !containerRef.current) return;

                const player = new YT.Player(containerRef.current, {
                    videoId: parsed.id,
                    playerVars: { autoplay: 1 },
                    events: {
                        onStateChange: (e: { data: number }) => {
                            if (e.data === YT.PlayerState.ENDED) onEndedRef.current?.();
                        },
                    },
                });

                player.getIframe().classList.add(...FILL_IFRAME_CLASSES);
                playerRef.current = player;
            });
        } else {
            loadVimeoApi().then((Vimeo) => {
                if (cancelled || !containerRef.current) return;

                const player = new Vimeo.Player(containerRef.current, { id: Number(parsed.id), autoplay: true });
                player.on('ended', () => onEndedRef.current?.());
                player.element?.classList.add(...FILL_IFRAME_CLASSES);
                playerRef.current = player;
            });
        }

        return () => {
            cancelled = true;
            playerRef.current?.destroy();
            playerRef.current = null;
        };
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [parsed?.provider, parsed?.id]);

    if (!parsed) {
        return (
            <EmptyState
                icon={<VideoOff className="size-5" />}
                title="Format URL video tidak dikenali"
                description="Gunakan link YouTube atau Vimeo."
            />
        );
    }

    return (
        <div className="relative aspect-video overflow-hidden bg-black">
            <div ref={containerRef} className="absolute inset-0" />
        </div>
    );
});

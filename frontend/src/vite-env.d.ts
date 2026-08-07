/// <reference types="vite/client" />
/// <reference types="vite-plugin-pwa/client" />

interface ImportMetaEnv {
    readonly VITE_DATA_SOURCE?: 'mock' | 'api';
    readonly VITE_API_BASE_URL?: string;
    readonly VITE_PROXY_TARGET?: string;
    readonly VITE_APP_NAME?: string;
}

interface ImportMeta {
    readonly env: ImportMetaEnv;
}

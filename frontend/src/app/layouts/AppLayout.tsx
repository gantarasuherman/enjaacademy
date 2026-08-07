import { Suspense, useEffect } from 'react';
import { Outlet, useLocation } from 'react-router-dom';
import { motion } from 'framer-motion';
import { cn } from '@/utils/cn';
import { useUiStore } from '@/store/uiStore';
import { useProgressStore } from '@/store/progressStore';
import { Sidebar } from '@/components/common/Sidebar';
import { Topbar } from '@/components/common/Topbar';
import { PageLoader } from '@/components/ui/Feedback';

export function AppLayout() {
    const sidebarCollapsed = useUiStore((state) => state.sidebarCollapsed);
    const hydrate = useProgressStore((state) => state.hydrate);
    const progressLoaded = useProgressStore((state) => state.progress.length > 0);
    const location = useLocation();

    useEffect(() => {
        if (!progressLoaded) void hydrate();
    }, [hydrate, progressLoaded]);

    // Route changes should start at the top, not wherever the previous page
    // was scrolled to.
    useEffect(() => {
        window.scrollTo({ top: 0, behavior: 'instant' as ScrollBehavior });
    }, [location.pathname]);

    return (
        <div className="min-h-dvh bg-bg">
            <Sidebar />

            <div className={cn('transition-[padding] duration-200 ease-[var(--ease-out-soft)]', sidebarCollapsed ? 'lg:pl-18' : 'lg:pl-64')}>
                <Topbar />

                <main className="mx-auto w-full max-w-7xl px-4 py-6 lg:px-6 lg:py-8">
                    <Suspense fallback={<PageLoader />}>
                        <motion.div
                            key={location.pathname}
                            initial={{ opacity: 0, y: 4 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.2, ease: [0.16, 1, 0.3, 1] }}
                        >
                            <Outlet />
                        </motion.div>
                    </Suspense>
                </main>
            </div>
        </div>
    );
}

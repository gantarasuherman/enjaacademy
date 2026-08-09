import { Suspense } from 'react';
import { Link, Outlet } from 'react-router-dom';
import { CheckCircle2, GraduationCap } from 'lucide-react';
import { PageLoader } from '@/components/ui/Feedback';

const HIGHLIGHTS = [
    'Learning path terstruktur dari A1 hingga C2',
    'Quiz tujuh tipe soal dengan pembahasan',
    'Flashcard spaced repetition (SM-2)',
    'Skor pelafalan otomatis dari suaramu',
];

export function AuthLayout() {
    return (
        <div className="grid min-h-dvh lg:grid-cols-2">
            {/* Brand panel — hidden on mobile where it would just push the form down. */}
            <aside className="relative hidden overflow-hidden bg-primary p-12 text-white lg:flex lg:flex-col">
                <div
                    aria-hidden
                    className="absolute -right-24 -top-24 size-96 rounded-full bg-white/8 blur-2xl"
                />
                <div
                    aria-hidden
                    className="absolute -bottom-32 -left-16 size-80 rounded-full bg-secondary/25 blur-3xl"
                />

                <Link to="/" className="relative flex items-center gap-2">
                    <span className="grid size-10 place-items-center rounded-sm bg-white/15">
                        <GraduationCap className="size-5" />
                    </span>
                    <span className="font-display text-lg font-extrabold">Enja Academy</span>
                </Link>

                <div className="relative mt-auto">
                    <h2 className="font-display text-3xl font-extrabold leading-tight text-white">
                        Belajar Bahasa Inggris
                        <br />
                        tanpa tebak-tebakan.
                    </h2>
                    <p className="mt-4 max-w-md text-white/80">
                        Satu jalur belajar yang jelas, latihan yang terukur, dan progres yang bisa kamu lihat setiap
                        hari.
                    </p>

                    <ul className="mt-8 space-y-3">
                        {HIGHLIGHTS.map((item) => (
                            <li key={item} className="flex items-center gap-3 text-sm text-white/90">
                                <CheckCircle2 className="size-4.5 shrink-0 text-secondary-300" />
                                {item}
                            </li>
                        ))}
                    </ul>
                </div>
            </aside>

            <main className="grid place-items-center px-4 py-12">
                <div className="w-full max-w-sm">
                    <Link to="/" className="mb-8 flex items-center justify-center gap-2 lg:hidden">
                        <span className="grid size-9 place-items-center rounded-sm bg-primary text-white">
                            <GraduationCap className="size-5" />
                        </span>
                        <span className="font-display text-base font-extrabold">Enja Academy</span>
                    </Link>

                    <Suspense fallback={<PageLoader />}>
                        <Outlet />
                    </Suspense>
                </div>
            </main>
        </div>
    );
}

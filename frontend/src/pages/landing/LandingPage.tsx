import { useState } from 'react';
import { motion } from 'framer-motion';
import {
    ArrowRight,
    BookMarked,
    BookOpen,
    CheckCircle2,
    GraduationCap,
    Headphones,
    Layers,
    MessagesSquare,
    Mic,
    PenLine,
    Sigma,
    Sparkles,
    Target,
    TrendingUp,
    Zap,
} from 'lucide-react';
import { useAsync } from '@/hooks/useAsync';
import { catalogService } from '@/services/api';
import { Button } from '@/components/ui/Button';
import { Card } from '@/components/ui/Card';
import { Badge, CefrBadge, Chip } from '@/components/ui/Badge';
import { Skeleton } from '@/components/ui/Feedback';

const CATALOG_LANGUAGES: { id: string; label: string }[] = [
    { id: '', label: 'Semua Bahasa' },
    { id: 'japanese', label: '🇯🇵 Jepang' },
    { id: 'english', label: '🇬🇧 Inggris' },
];

const SKILLS = [
    { icon: BookMarked, label: 'Vocabulary', desc: '1.000+ kata per tema, lengkap dengan IPA dan contoh.', color: 'text-secondary' },
    { icon: Sigma, label: 'Grammar', desc: 'Parts of speech, 8 tense inti, conditional, dan passive.', color: 'text-primary' },
    { icon: Headphones, label: 'Listening', desc: 'Aksen Amerika, Britania, dan Australia dengan transkrip.', color: 'text-info' },
    { icon: Mic, label: 'Speaking', desc: 'Rekam suaramu, dapatkan skor pelafalan per kata.', color: 'text-success' },
    { icon: BookOpen, label: 'Reading', desc: 'Cerita, berita, artikel, dan dokumentasi teknis.', color: 'text-primary' },
    { icon: PenLine, label: 'Writing', desc: 'Dari kalimat, paragraf, sampai esai — dengan rubrik.', color: 'text-secondary' },
    { icon: MessagesSquare, label: 'Conversation', desc: 'Skenario restoran, hotel, dan wawancara kerja.', color: 'text-info' },
    { icon: Layers, label: 'Flashcard', desc: 'Spaced repetition SM-2 supaya kata benar-benar melekat.', color: 'text-success' },
];

const PERSONAS = [
    {
        title: 'Pemula',
        need: 'Struktur bertahap, umpan balik instan, visual yang jelas.',
        level: 'Beginner',
        icon: Sparkles,
    },
    {
        title: 'Profesional',
        need: 'Business English, sesi singkat, nyaman dipakai di ponsel.',
        level: 'Intermediate',
        icon: TrendingUp,
    },
    {
        title: 'Programmer',
        need: 'Istilah teknis dan latihan membaca dokumentasi API.',
        level: 'Upper-Intermediate',
        icon: Zap,
    },
    {
        title: 'Persiapan Ujian',
        need: 'Drilling soal TOEFL/IELTS dengan statistik progres.',
        level: 'Advanced',
        icon: Target,
    },
];

const STATS = [
    { value: '7', label: 'Skill terpadu' },
    { value: '7', label: 'Tipe soal quiz' },
    { value: '1.000+', label: 'Kosakata terkurasi' },
    { value: 'A1–C2', label: 'Rentang level' },
];

export default function LandingPage() {
    const [catalogLang, setCatalogLang] = useState('');
    const { data: catalogModules, loading: catalogLoading } = useAsync(
        () => catalogService.listModules({ language: catalogLang || undefined }),
        [catalogLang],
    );

    return (
        <>
            {/* ---------------------------------------------------------- Hero */}
            <section className="relative overflow-hidden">
                <div
                    aria-hidden
                    className="absolute inset-x-0 -top-40 h-96 bg-gradient-to-b from-primary-100 to-transparent dark:from-primary/12"
                />

                <div className="relative mx-auto max-w-6xl px-4 pb-20 pt-16 lg:px-6 lg:pb-28 lg:pt-24">
                    <div className="grid items-center gap-12 lg:grid-cols-2">
                        <motion.div
                            initial={{ opacity: 0, y: 12 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.4, ease: [0.16, 1, 0.3, 1] }}
                        >
                            <Badge tone="secondary" icon={<Sparkles className="size-3" />}>
                                Belajar terstruktur, bukan acak
                            </Badge>

                            <h1 className="mt-4 font-display text-4xl font-extrabold leading-[1.1] tracking-tight lg:text-5xl">
                                Kuasai Bahasa Jepang & Inggris
                                <span className="block text-primary">satu langkah setiap hari.</span>
                            </h1>

                            <p className="mt-5 max-w-lg text-base text-fg-muted">
                                Dua bahasa dalam satu jalur belajar: hiragana, katakana, kanji, vocabulary,
                                grammar, listening, speaking, reading, writing, dan conversation. Lengkap dengan
                                quiz, flashcard, dan pelacakan progres yang jujur.
                            </p>

                            <div className="mt-8 flex flex-wrap gap-3">
                                <Button to="/register" size="lg" iconRight={<ArrowRight className="size-4" />}>
                                    Mulai Belajar Gratis
                                </Button>
                                <Button to="/login" size="lg" variant="outline">
                                    Sudah punya akun
                                </Button>
                            </div>

                            <div className="mt-8 flex flex-wrap gap-x-6 gap-y-2">
                                {['Tanpa kartu kredit', 'Bisa dipakai offline', 'Gratis selamanya'].map((item) => (
                                    <span key={item} className="flex items-center gap-1.5 text-sm text-fg-muted">
                                        <CheckCircle2 className="size-4 text-success" />
                                        {item}
                                    </span>
                                ))}
                            </div>
                        </motion.div>

                        {/* Product preview — a static mock of the dashboard cards. */}
                        <motion.div
                            initial={{ opacity: 0, scale: 0.97 }}
                            animate={{ opacity: 1, scale: 1 }}
                            transition={{ duration: 0.5, delay: 0.1, ease: [0.16, 1, 0.3, 1] }}
                            className="relative"
                        >
                            <Card className="shadow-[var(--shadow-pop)]">
                                <div className="flex items-center justify-between">
                                    <div>
                                        <p className="text-sm text-fg-muted">Selamat datang kembali</p>
                                        <p className="font-display text-xl font-extrabold">Halo, Tesar 👋</p>
                                    </div>
                                    <Badge tone="secondary">🔥 12 hari</Badge>
                                </div>

                                <div className="mt-5 grid grid-cols-3 gap-3">
                                    {[
                                        { label: 'Level', value: '15' },
                                        { label: 'XP', value: '6.180' },
                                        { label: 'Kata', value: '238' },
                                    ].map((stat) => (
                                        <div key={stat.label} className="rounded-sm bg-surface-sunken p-3 text-center">
                                            <p className="font-display text-lg font-extrabold">{stat.value}</p>
                                            <p className="text-[11px] text-fg-muted">{stat.label}</p>
                                        </div>
                                    ))}
                                </div>

                                <div className="mt-5 space-y-3">
                                    {[
                                        { label: 'Core Vocabulary', pct: 66, tone: 'bg-secondary' },
                                        { label: 'Grammar Foundations', pct: 100, tone: 'bg-success' },
                                        { label: 'Listening Lab', pct: 40, tone: 'bg-info' },
                                    ].map((row) => (
                                        <div key={row.label}>
                                            <div className="mb-1 flex justify-between text-xs">
                                                <span className="font-medium">{row.label}</span>
                                                <span className="font-mono text-fg-muted">{row.pct}%</span>
                                            </div>
                                            <div className="h-2 overflow-hidden rounded-full bg-surface-sunken">
                                                <div className={`h-full rounded-full ${row.tone}`} style={{ width: `${row.pct}%` }} />
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </Card>

                            <motion.div
                                animate={{ y: [0, -8, 0] }}
                                transition={{ duration: 4, repeat: Infinity, ease: 'easeInOut' }}
                                className="absolute -bottom-5 -left-5 hidden rounded-md bg-surface p-3 shadow-[var(--shadow-pop)] sm:block"
                            >
                                <p className="text-xs text-fg-muted">Kuis selesai</p>
                                <p className="font-display text-lg font-extrabold text-success">92%</p>
                            </motion.div>
                        </motion.div>
                    </div>

                    <div className="mt-16 grid grid-cols-2 gap-4 border-t border-[var(--surface-border)] pt-8 sm:grid-cols-4">
                        {STATS.map((stat) => (
                            <div key={stat.label} className="text-center">
                                <p className="font-display text-2xl font-extrabold text-primary">{stat.value}</p>
                                <p className="mt-0.5 text-sm text-fg-muted">{stat.label}</p>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* ------------------------------------------------------ Skills */}
            <section id="features" className="scroll-mt-20 border-t border-[var(--surface-border)] bg-surface py-20">
                <div className="mx-auto max-w-6xl px-4 lg:px-6">
                    <div className="max-w-2xl">
                        <Badge tone="primary">Modul</Badge>
                        <h2 className="mt-3 font-display text-3xl font-extrabold">Tujuh skill, satu jalur belajar</h2>
                        <p className="mt-3 text-fg-muted">
                            Setiap modul punya materi, latihan, dan kuis penutupnya sendiri — jadi kamu selalu tahu
                            apa yang harus dikerjakan berikutnya.
                        </p>
                    </div>

                    <div id="modules" className="mt-10 grid gap-4 scroll-mt-20 sm:grid-cols-2 lg:grid-cols-4">
                        {SKILLS.map((skill, index) => {
                            const Icon = skill.icon;

                            return (
                                <motion.div
                                    key={skill.label}
                                    initial={{ opacity: 0, y: 12 }}
                                    whileInView={{ opacity: 1, y: 0 }}
                                    viewport={{ once: true, margin: '-60px' }}
                                    transition={{ duration: 0.3, delay: index * 0.04 }}
                                >
                                    <Card className="h-full">
                                        <Icon className={`size-6 ${skill.color}`} />
                                        <p className="mt-3 font-display font-bold">{skill.label}</p>
                                        <p className="mt-1 text-sm text-fg-muted">{skill.desc}</p>
                                    </Card>
                                </motion.div>
                            );
                        })}
                    </div>
                </div>
            </section>

            {/* ------------------------------------------------- Course catalog */}
            <section id="kursus" className="scroll-mt-20 border-t border-[var(--surface-border)] py-20">
                <div className="mx-auto max-w-6xl px-4 lg:px-6">
                    <div className="flex flex-wrap items-end justify-between gap-4">
                        <div className="max-w-2xl">
                            <Badge tone="secondary">Katalog</Badge>
                            <h2 className="mt-3 font-display text-3xl font-extrabold">Jelajahi kursus yang tersedia</h2>
                            <p className="mt-3 text-fg-muted">
                                Lihat silabus lengkap sebelum mendaftar — setiap kursus berisi rangkaian materi
                                terstruktur per level.
                            </p>
                        </div>

                        <div className="flex flex-wrap gap-2">
                            {CATALOG_LANGUAGES.map((item) => (
                                <Chip key={item.id} active={catalogLang === item.id} onClick={() => setCatalogLang(item.id)}>
                                    {item.label}
                                </Chip>
                            ))}
                        </div>
                    </div>

                    {catalogLoading ? (
                        <div className="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            {Array.from({ length: 6 }, (_, i) => (
                                <Skeleton key={i} className="h-40 w-full" />
                            ))}
                        </div>
                    ) : !catalogModules || catalogModules.length === 0 ? (
                        <p className="mt-10 text-sm text-fg-muted">Belum ada kursus untuk filter ini.</p>
                    ) : (
                        <div className="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            {catalogModules.map((module) => (
                                <Card key={module.id} interactive className="flex h-full flex-col">
                                    <div className="flex items-start justify-between gap-3">
                                        <span className="grid size-10 shrink-0 place-items-center rounded-sm bg-primary-100 text-primary dark:bg-primary/20">
                                            <GraduationCap className="size-5" />
                                        </span>
                                        {module.language && (
                                            <span className="rounded-full bg-surface-sunken px-2.5 py-1 text-[11px] font-medium text-fg-muted">
                                                {module.language.flag} {module.language.name}
                                            </span>
                                        )}
                                    </div>

                                    <h3 className="mt-4 font-display text-base font-bold">{module.name}</h3>
                                    <p className="mt-1 line-clamp-2 flex-1 text-sm text-fg-muted">{module.description}</p>

                                    <div className="mt-4 flex items-center justify-between">
                                        <span className="flex items-center gap-1 text-xs text-fg-muted">
                                            <Layers className="size-3.5" />
                                            {module.lessons_count ?? 0} materi
                                        </span>
                                        <Button to={`/kursus/${module.slug}`} variant="ghost" size="sm" iconRight={<ArrowRight className="size-3.5" />}>
                                            Lihat silabus
                                        </Button>
                                    </div>
                                </Card>
                            ))}
                        </div>
                    )}
                </div>
            </section>

            {/* ---------------------------------------------------- Personas */}
            <section className="py-20">
                <div className="mx-auto max-w-6xl px-4 lg:px-6">
                    <div className="max-w-2xl">
                        <Badge tone="secondary">Untuk siapa</Badge>
                        <h2 className="mt-3 font-display text-3xl font-extrabold">Disesuaikan dengan tujuanmu</h2>
                        <p className="mt-3 text-fg-muted">
                            Jalur belajar menyesuaikan level awal dan target yang kamu pilih saat mendaftar.
                        </p>
                    </div>

                    <div className="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        {PERSONAS.map((persona) => {
                            const Icon = persona.icon;

                            return (
                                <Card key={persona.title} className="h-full">
                                    <div className="flex items-start justify-between">
                                        <span className="grid size-10 place-items-center rounded-sm bg-primary-100 text-primary dark:bg-primary/20">
                                            <Icon className="size-5" />
                                        </span>
                                        <CefrBadge level={persona.level} />
                                    </div>
                                    <p className="mt-4 font-display font-bold">{persona.title}</p>
                                    <p className="mt-1 text-sm text-fg-muted">{persona.need}</p>
                                </Card>
                            );
                        })}
                    </div>
                </div>
            </section>

            {/* ------------------------------------------------------ Pricing */}
            <section id="pricing" className="scroll-mt-20 border-t border-[var(--surface-border)] bg-surface py-20">
                <div className="mx-auto max-w-3xl px-4 text-center lg:px-6">
                    <Badge tone="success">Harga</Badge>
                    <h2 className="mt-3 font-display text-3xl font-extrabold">Gratis, tanpa tanda bintang</h2>
                    <p className="mx-auto mt-3 max-w-xl text-fg-muted">
                        Semua modul, semua kuis, semua flashcard. Tidak ada batas harian, tidak ada iklan yang
                        memotong sesi belajarmu.
                    </p>

                    <Card className="mx-auto mt-10 max-w-md text-left">
                        <p className="font-display text-4xl font-extrabold">
                            Rp0
                            <span className="text-base font-medium text-fg-muted">/selamanya</span>
                        </p>

                        <ul className="mt-6 space-y-2.5">
                            {[
                                'Akses 7 modul skill penuh',
                                'Quiz 7 tipe soal + pembahasan',
                                'Flashcard spaced repetition',
                                'Skor pelafalan otomatis',
                                'Progress, achievement, leaderboard',
                                'Mode offline (PWA)',
                            ].map((item) => (
                                <li key={item} className="flex items-center gap-2.5 text-sm">
                                    <CheckCircle2 className="size-4 shrink-0 text-success" />
                                    {item}
                                </li>
                            ))}
                        </ul>

                        <Button to="/register" fullWidth size="lg" className="mt-7">
                            Buat Akun Sekarang
                        </Button>
                    </Card>
                </div>
            </section>

            {/* ---------------------------------------------------------- CTA */}
            <section className="bg-primary py-16 text-white">
                <div className="mx-auto max-w-3xl px-4 text-center lg:px-6">
                    <h2 className="font-display text-3xl font-extrabold">Sepuluh menit hari ini lebih baik daripada tiga jam bulan depan.</h2>
                    <p className="mt-3 text-white/80">
                        Mulai dari kosakata dasar, dan biarkan progres harian yang mengerjakan sisanya.
                    </p>
                    <Button to="/register" size="lg" variant="secondary" className="mt-7">
                        Mulai Sekarang
                    </Button>
                </div>
            </section>
        </>
    );
}

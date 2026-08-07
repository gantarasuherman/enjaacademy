@extends('layouts.app')
@section('title', __('Dashboard'))

@php
    // Chart payloads are built here rather than inline: Blade's directive
    // parser cannot handle a multi-line array literal inside @json(...).
    $registrationChart = ['labels' => $registrations->keys(), 'values' => $registrations->values(), 'label' => __('Pendaftar')];
    $roleChart = ['labels' => $usersByRole->pluck('name'), 'values' => $usersByRole->pluck('total')];
@endphp

@section('content')
    <x-page-header
        :title="__('Dashboard')"
        :description="__('Ringkasan pengguna, konten, dan aktivitas sistem.')"
    />

    {{-- KPIs --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
        <x-stat icon="users" tone="brand" :label="__('Total pengguna')" :value="number_format($stats['users'])"
                :hint="__(':n aktif', ['n' => number_format($stats['active_users'])])" />
        <x-stat icon="file-text" tone="sky" :label="__('Materi')" :value="number_format($stats['lessons'])"
                :hint="__(':n terbit', ['n' => number_format($stats['published_lessons'])])" />
        <x-stat icon="clipboard" tone="emerald" :label="__('Kuis')" :value="number_format($stats['quizzes'])" />
        <x-stat icon="activity" tone="amber" :label="__('Percobaan hari ini')" :value="number_format($stats['attempts_today'])" />
        <x-stat icon="layers" tone="rose" :label="__('Modul aktif')" :value="$modules->count()" />
        <x-stat icon="shield" tone="brand" :label="__('Role')" :value="$usersByRole->count()" />
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            {{-- Registrations --}}
            <div class="card p-5">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h2 class="font-semibold">{{ __('Pendaftaran 12 bulan terakhir') }}</h2>
                        <p class="text-xs text-slate-500">{{ __('Jumlah akun baru per bulan') }}</p>
                    </div>
                </div>

                <div class="h-64">
                    <canvas data-chart="line" data-chart-data='@json($registrationChart)'></canvas>
                </div>
            </div>

            {{-- Module content --}}
            <div class="card">
                <div class="card-header">
                    <h2 class="font-semibold">{{ __('Konten per modul') }}</h2>
                    @can('modules.view')
                        <a href="{{ route('admin.modules.index') }}" class="text-sm font-medium text-brand-600 hover:underline">
                            {{ __('Kelola modul') }}
                        </a>
                    @endcan
                </div>

                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Modul') }}</th>
                                <th>{{ __('Bahasa') }}</th>
                                <th class="text-right">{{ __('Materi') }}</th>
                                <th class="text-right">{{ __('Kuis') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($modules as $module)
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-2.5">
                                            <x-icon :name="$module->icon" class="size-4 text-slate-400" />
                                            <span class="font-medium">{{ $module->name }}</span>
                                        </div>
                                    </td>
                                    <td class="text-slate-500">{{ $module->language?->name ?? '—' }}</td>
                                    <td class="text-right font-mono">{{ $module->lessons_count }}</td>
                                    <td class="text-right font-mono">{{ $module->quizzes_count }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-sm text-slate-500">
                                        {{ __('Belum ada modul. Jalankan seeder atau buat lewat menu Master Data.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Recent activity --}}
            @can('audit-logs.view')
                <div class="card">
                    <div class="card-header">
                        <h2 class="font-semibold">{{ __('Aktivitas terbaru') }}</h2>
                        <a href="{{ route('admin.audit-logs.index') }}" class="text-sm font-medium text-brand-600 hover:underline">
                            {{ __('Semua log') }}
                        </a>
                    </div>

                    <ul class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse ($recentActivity as $log)
                            <li class="flex items-center gap-3 px-5 py-3">
                                <span class="badge shrink-0 bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                    {{ $log->event }}
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm">
                                        <span class="font-medium">{{ $log->user_name ?? __('Sistem') }}</span>
                                        @if ($log->auditable_label)
                                            <span class="text-slate-500">— {{ $log->auditable_label }}</span>
                                        @endif
                                    </p>
                                </div>
                                <span class="shrink-0 text-xs text-slate-400">{{ $log->created_at?->diffForHumans() }}</span>
                            </li>
                        @empty
                            <li class="px-5 py-8 text-center text-sm text-slate-500">{{ __('Belum ada aktivitas tercatat.') }}</li>
                        @endforelse
                    </ul>
                </div>
            @endcan
        </div>

        <div class="space-y-6">
            {{-- Users by role --}}
            <div class="card p-5">
                <h2 class="mb-4 font-semibold">{{ __('Pengguna per role') }}</h2>

                <div class="h-56">
                    <canvas data-chart="doughnut" data-chart-data='@json($roleChart)'></canvas>
                </div>
            </div>

            {{-- Leaderboard --}}
            <div class="card">
                <div class="card-header">
                    <h2 class="font-semibold">{{ __('Papan peringkat XP') }}</h2>
                </div>

                <ol class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($leaderboard as $index => $entry)
                        <li class="flex items-center gap-3 px-5 py-2.5">
                            <span class="w-5 shrink-0 text-center font-mono text-xs font-bold text-slate-400">{{ $index + 1 }}</span>
                            <span class="min-w-0 flex-1 truncate text-sm font-medium">{{ $entry->name }}</span>
                            <span class="shrink-0 text-xs text-slate-500">Lv {{ $entry->level }}</span>
                            <span class="shrink-0 font-mono text-xs font-semibold">{{ number_format($entry->xp_total) }}</span>
                        </li>
                    @empty
                        <li class="px-5 py-8 text-center text-sm text-slate-500">{{ __('Belum ada data XP.') }}</li>
                    @endforelse
                </ol>
            </div>

            {{-- Recent users --}}
            @can('users.view')
                <div class="card">
                    <div class="card-header">
                        <h2 class="font-semibold">{{ __('Pengguna terbaru') }}</h2>
                        <a href="{{ route('admin.users.index') }}" class="text-sm font-medium text-brand-600 hover:underline">
                            {{ __('Semua') }}
                        </a>
                    </div>

                    <ul class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($recentUsers as $user)
                            <li class="flex items-center gap-3 px-5 py-2.5">
                                <img src="{{ $user->avatar_url }}" alt="" class="size-8 shrink-0 rounded-full object-cover">
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium">{{ $user->name }}</p>
                                    <p class="truncate text-xs text-slate-500">{{ $user->getRoleNames()->implode(', ') ?: __('tanpa role') }}</p>
                                </div>
                                <span class="shrink-0 text-xs text-slate-400">{{ $user->created_at?->diffForHumans() }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endcan
        </div>
    </div>
@endsection

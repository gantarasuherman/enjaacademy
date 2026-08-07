@extends('layouts.app')
@section('title', __('Laporan Progres'))

@section('content')
    <x-page-header :title="__('Laporan Progres Peserta')" :description="__('Penyelesaian materi dan capaian XP tiap peserta.')">
        <x-slot:actions>
            <a href="{{ route('admin.reports.export', request()->query()) }}" class="btn-secondary">{{ __('Export CSV') }}</a>
        </x-slot:actions>
    </x-page-header>

    <div class="mb-6 grid gap-4 sm:grid-cols-3">
        <x-stat icon="users" tone="brand" :label="__('Peserta aktif')" :value="number_format($totals['learners'])" />
        <x-stat icon="trending-up" tone="emerald" :label="__('Penyelesaian')" :value="number_format($totals['completions'])" />
        <x-stat icon="activity" tone="amber" :label="__('Sedang berjalan')" :value="number_format($totals['in_progress'])" />
    </div>

    <div class="card">
        <div class="card-header flex-wrap">
            <h2 class="font-semibold">{{ __('Peserta') }}</h2>

            <form method="GET" class="flex flex-wrap gap-2">
                <select name="module" class="input w-44 text-sm">
                    <option value="">{{ __('Semua modul') }}</option>
                    @foreach ($modules as $id => $name)
                        <option value="{{ $id }}" @selected($moduleId == $id)>{{ $name }}</option>
                    @endforeach
                </select>
                <input type="search" name="search" value="{{ request('search') }}" placeholder="{{ __('Cari peserta…') }}" class="input w-44 text-sm">
                <button class="btn-secondary text-sm">{{ __('Filter') }}</button>
            </form>
        </div>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('Peserta') }}</th>
                        <th class="text-right">{{ __('Level') }}</th>
                        <th class="text-right">{{ __('XP') }}</th>
                        <th class="text-right">{{ __('Streak') }}</th>
                        <th class="text-right">{{ __('Materi selesai') }}</th>
                        <th class="text-right">{{ __('Kuis') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $user)
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <img src="{{ $user->avatar_url }}" alt="" class="size-8 shrink-0 rounded-full object-cover">
                                    <div class="min-w-0">
                                        <p class="truncate font-medium">{{ $user->name }}</p>
                                        <p class="truncate text-xs text-slate-500">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="text-right font-mono">{{ $user->stat?->level ?? 1 }}</td>
                            <td class="text-right font-mono">{{ number_format($user->stat?->xp_total ?? 0) }}</td>
                            <td class="text-right font-mono">{{ $user->stat?->streak_days ?? 0 }}</td>
                            <td class="text-right font-mono">{{ $user->completed_lessons }}</td>
                            <td class="text-right font-mono">{{ $user->stat?->quizzes_completed ?? 0 }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-10 text-center text-sm text-slate-500">{{ __('Belum ada data.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($rows->hasPages())
            <div class="border-t border-slate-200 px-5 py-3 dark:border-slate-800">{{ $rows->links() }}</div>
        @endif
    </div>
@endsection

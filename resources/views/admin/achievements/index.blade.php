@extends('layouts.app')
@section('title', __('Pencapaian'))

@section('content')
    <x-page-header :title="__('Pencapaian')" :description="__('Lencana yang terbuka otomatis saat statistik peserta memenuhi kriteria.')">
        <x-slot:actions>
            @can('achievements.create')
                <a href="{{ route('admin.achievements.create') }}" class="btn-primary">
                    <x-icon name="plus" class="size-4" /> {{ __('Pencapaian baru') }}
                </a>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <div class="card">
        <div class="card-header flex-wrap">
            <h2 class="font-semibold">{{ __('Daftar pencapaian') }}</h2>

            <form method="GET" class="flex flex-wrap gap-2">
                <select name="criteria_type" class="input w-44 text-sm">
                    <option value="">{{ __('Semua kriteria') }}</option>
                    @foreach (['xp_total', 'level', 'lessons_completed', 'quizzes_completed', 'perfect_quizzes', 'streak_days', 'flashcards_reviewed', 'manual'] as $type)
                        <option value="{{ $type }}" @selected(request('criteria_type') === $type)>{{ $type }}</option>
                    @endforeach
                </select>
                <input type="search" name="search" value="{{ request('search') }}" placeholder="{{ __('Cari…') }}" class="input w-40 text-sm">
                <button class="btn-secondary text-sm">{{ __('Filter') }}</button>
            </form>
        </div>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('Pencapaian') }}</th>
                        <th>{{ __('Kriteria') }}</th>
                        <th class="text-right">{{ __('Ambang') }}</th>
                        <th class="text-right">{{ __('XP') }}</th>
                        <th class="text-right">{{ __('Terbuka oleh') }}</th>
                        <th class="text-right">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($achievements as $achievement)
                        <tr>
                            <td>
                                <div class="flex items-center gap-2.5">
                                    <span class="grid size-8 shrink-0 place-items-center rounded-full bg-{{ $achievement->badge_color }}-100 text-{{ $achievement->badge_color }}-700 dark:bg-{{ $achievement->badge_color }}-500/15">
                                        <x-icon :name="$achievement->icon" class="size-4" />
                                    </span>
                                    <div class="min-w-0">
                                        <p class="truncate font-medium">{{ $achievement->name }}</p>
                                        <p class="truncate text-xs text-slate-500">{{ Str::limit($achievement->description, 50) }}</p>
                                    </div>
                                </div>
                            </td>
                            <td><code class="text-xs">{{ $achievement->criteria_type }}</code></td>
                            <td class="text-right font-mono">{{ number_format($achievement->criteria_value) }}</td>
                            <td class="text-right font-mono">{{ $achievement->xp_reward }}</td>
                            <td class="text-right font-mono">{{ $achievement->users_count }}</td>
                            <td class="text-right">
                                <div class="flex justify-end gap-1">
                                    @can('achievements.update')
                                        <a href="{{ route('admin.achievements.edit', $achievement) }}" class="btn-ghost px-2 py-1 text-xs">{{ __('Edit') }}</a>
                                    @endcan
                                    @can('achievements.delete')
                                        <form method="POST" action="{{ route('admin.achievements.destroy', $achievement) }}"
                                              onsubmit="return confirm('{{ __('Hapus pencapaian ini?') }}')">
                                            @csrf @method('DELETE')
                                            <button class="btn-ghost px-2 py-1 text-xs text-rose-600">{{ __('Hapus') }}</button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-10 text-center text-sm text-slate-500">{{ __('Belum ada pencapaian.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($achievements->hasPages())
            <div class="border-t border-slate-200 px-5 py-3 dark:border-slate-800">{{ $achievements->links() }}</div>
        @endif
    </div>
@endsection

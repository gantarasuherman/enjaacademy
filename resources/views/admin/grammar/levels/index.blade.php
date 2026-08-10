@extends('layouts.app')
@section('title', __('Level Grammar'))

@section('content')
    <x-page-header :title="__('Level Grammar')" :description="__('N5 sampai N1 (atau skema apa pun) — level baru cukup ditambahkan di sini, tidak perlu ubah kode.')">
        <x-slot:actions>
            @can('grammar.create')
                <a href="{{ route('admin.grammar.levels.create') }}" class="btn-primary">
                    <x-icon name="plus" class="size-4" /> {{ __('Level baru') }}
                </a>
            @endcan
        </x-slot:actions>
    </x-page-header>

    @php
        $groupLabels = [
            'japanese|grammar' => __('Grammar — Bahasa Jepang'),
            'english|grammar' => __('Grammar — Bahasa Inggris'),
            'japanese|structure' => __('Struktur Kalimat — Bahasa Jepang'),
            'english|structure' => __('Struktur Kalimat — Bahasa Inggris'),
        ];
    @endphp

    @forelse ($levels as $groupKey => $groupLevels)
        <div class="card mb-6">
            <div class="card-header">
                <h2 class="font-semibold">{{ $groupLabels[$groupKey] ?? $groupKey }}</h2>
            </div>

            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ __('Level') }}</th>
                            <th class="text-right">{{ __('Kategori') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th class="text-right">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($groupLevels as $level)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-2.5">
                                        <span class="badge bg-indigo-100 text-indigo-700 dark:bg-indigo-500/15">{{ $level->name }}</span>
                                        <span class="text-xs text-slate-500">{{ $level->description }}</span>
                                    </div>
                                </td>
                                <td class="text-right font-mono">{{ $level->categories_count }}</td>
                                <td>
                                    @if ($level->is_active)
                                        <span class="badge bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15">{{ __('Aktif') }}</span>
                                    @else
                                        <span class="badge bg-slate-200 text-slate-600 dark:bg-slate-700">{{ __('Nonaktif') }}</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <div class="flex justify-end gap-1">
                                        @can('grammar.update')
                                            <a href="{{ route('admin.grammar.levels.edit', $level) }}" class="btn-ghost px-2 py-1 text-xs">{{ __('Edit') }}</a>
                                        @endcan
                                        @can('grammar.delete')
                                            <form method="POST" action="{{ route('admin.grammar.levels.destroy', $level) }}"
                                                  onsubmit="return confirm('{{ __('Hapus level ini?') }}')">
                                                @csrf @method('DELETE')
                                                <button class="btn-ghost px-2 py-1 text-xs text-rose-600">{{ __('Hapus') }}</button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="card py-10 text-center text-sm text-slate-500">{{ __('Belum ada level.') }}</div>
    @endforelse
@endsection

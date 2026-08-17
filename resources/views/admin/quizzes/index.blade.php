@extends('layouts.app')
@section('title', __('Kuis'))

@section('content')
    <x-page-header :title="__('Kuis')" :description="__('Soal pilihan ganda, benar-salah, isian, dan menjodohkan — dengan pembahasan.')">
        <x-slot:actions>
            @can('quizzes.create')
                <a href="{{ route('admin.quizzes.create') }}" class="btn-primary">
                    <x-icon name="plus" class="size-4" /> {{ __('Kuis baru') }}
                </a>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <div class="card">
        <div class="card-header flex-wrap">
            <h2 class="font-semibold">{{ __('Daftar kuis') }}</h2>

            <form method="GET" class="flex flex-wrap gap-2">
                <select name="module" class="input w-44 text-sm">
                    <option value="">{{ __('Semua modul') }}</option>
                    @foreach ($modules as $module)
                        <option value="{{ $module->id }}" @selected(request('module') == $module->id)>{{ $module->name }}</option>
                    @endforeach
                </select>
                <select name="difficulty" class="input w-32 text-sm">
                    <option value="">{{ __('Semua tingkat') }}</option>
                    @foreach (['easy' => __('Mudah'), 'medium' => __('Sedang'), 'hard' => __('Sulit')] as $value => $label)
                        <option value="{{ $value }}" @selected(request('difficulty') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <select name="category" class="input w-32 text-sm">
                    <option value="">{{ __('Semua jenis') }}</option>
                    <option value="quiz" @selected(request('category') === 'quiz')>{{ __('Kuis') }}</option>
                    <option value="test" @selected(request('category') === 'test')>{{ __('Tes') }}</option>
                </select>
                <input type="search" name="search" value="{{ request('search') }}" placeholder="{{ __('Cari…') }}" class="input w-40 text-sm">
                <button class="btn-secondary text-sm">{{ __('Filter') }}</button>
            </form>
        </div>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('Judul') }}</th>
                        <th>{{ __('Jenis') }}</th>
                        <th>{{ __('Modul') }}</th>
                        <th>{{ __('Tingkat') }}</th>
                        <th class="text-right">{{ __('Soal') }}</th>
                        <th class="text-right">{{ __('Lulus') }}</th>
                        <th>{{ __('Percobaan') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th class="text-right">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($quizzes as $quiz)
                        <tr>
                            <td class="font-medium">{{ $quiz->title }}</td>
                            <td>
                                @if ($quiz->category === 'test')
                                    <span class="badge bg-indigo-100 text-indigo-700 dark:bg-indigo-500/15">{{ __('Tes') }}</span>
                                @else
                                    <span class="badge bg-slate-200 text-slate-600 dark:bg-slate-700">{{ __('Kuis') }}</span>
                                @endif
                            </td>
                            <td class="text-slate-500">{{ $quiz->module?->name ?? '—' }}</td>
                            <td>
                                @php
                                    $tone = ['easy' => 'emerald', 'medium' => 'amber', 'hard' => 'rose'][$quiz->difficulty] ?? 'slate';
                                @endphp
                                <span class="badge bg-{{ $tone }}-100 text-{{ $tone }}-700 dark:bg-{{ $tone }}-500/15 dark:text-{{ $tone }}-300">
                                    {{ $quiz->difficulty }}
                                </span>
                            </td>
                            <td class="text-right font-mono">{{ $quiz->questions_count }}</td>
                            <td class="text-right font-mono">{{ $quiz->pass_score }}%</td>
                            <td>
                                @if ($quiz->max_attempts)
                                    <span class="badge bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300">{{ __('Maks. :n', ['n' => $quiz->max_attempts]) }}</span>
                                @else
                                    <span class="badge bg-sky-100 text-sky-700 dark:bg-sky-500/15 dark:text-sky-300">∞ {{ __('Tidak Terbatas') }}</span>
                                @endif
                            </td>
                            <td>
                                @if ($quiz->is_published)
                                    <span class="badge bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15">{{ __('Terbit') }}</span>
                                @else
                                    <span class="badge bg-slate-200 text-slate-600 dark:bg-slate-700">{{ __('Draf') }}</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <div class="flex justify-end gap-1">
                                    @can('quizzes.view')
                                        <a href="{{ route('admin.quizzes.show', $quiz) }}" class="btn-ghost px-2 py-1 text-xs">{{ __('Detail') }}</a>
                                    @endcan
                                    @can('quizzes.update')
                                        <a href="{{ route('admin.quizzes.edit', $quiz) }}" class="btn-ghost px-2 py-1 text-xs">{{ __('Edit') }}</a>
                                    @endcan
                                    @can('quizzes.delete')
                                        <form method="POST" action="{{ route('admin.quizzes.destroy', $quiz) }}"
                                              onsubmit="return confirm('{{ __('Hapus kuis ini?') }}')">
                                            @csrf @method('DELETE')
                                            <button class="btn-ghost px-2 py-1 text-xs text-rose-600">{{ __('Hapus') }}</button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="py-10 text-center text-sm text-slate-500">{{ __('Tidak ada kuis yang cocok.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($quizzes->hasPages())
            <div class="border-t border-slate-200 px-5 py-3 dark:border-slate-800">{{ $quizzes->links() }}</div>
        @endif
    </div>
@endsection

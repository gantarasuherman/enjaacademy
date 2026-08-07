@extends('layouts.app')
@section('title', $module->name)

@section('content')
    <x-page-header
        :title="$module->name"
        :description="$module->description"
        :back="route('admin.modules.index')"
        :back-label="__('Kembali ke modul')"
    >
        <x-slot:actions>
            @can('lessons.create')
                <a href="{{ route('admin.lessons.create', ['module' => $module->id]) }}" class="btn-secondary">
                    {{ __('Tambah materi') }}
                </a>
            @endcan
            @can('modules.update')
                <a href="{{ route('admin.modules.edit', $module) }}" class="btn-primary">{{ __('Edit modul') }}</a>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-stat icon="language" tone="brand" :label="__('Bahasa')" :value="$module->language?->name ?? '—'" />
        <x-stat icon="layers" tone="sky" :label="__('Tipe konten')" :value="$module->content_type" />
        <x-stat icon="file-text" tone="emerald" :label="__('Materi')" :value="$lessons->count()"
                :hint="__(':n terbit', ['n' => $lessons->where('is_published', true)->count()])" />
        <x-stat icon="clipboard" tone="amber" :label="__('Kuis')" :value="$quizzes->count()" />
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <div class="card">
                <div class="card-header">
                    <h2 class="font-semibold">{{ __('Materi') }}</h2>
                    <span class="text-xs text-slate-500">{{ __(':n materi', ['n' => $lessons->count()]) }}</span>
                </div>

                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="w-10">#</th>
                                <th>{{ __('Judul') }}</th>
                                <th>{{ __('Level') }}</th>
                                <th class="text-right">{{ __('Item') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th class="text-right">{{ __('Aksi') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($lessons as $index => $lesson)
                                <tr>
                                    <td class="font-mono text-xs text-slate-400">{{ $index + 1 }}</td>
                                    <td>
                                        <p class="font-medium">{{ $lesson->title }}</p>
                                        <p class="truncate text-xs text-slate-500">{{ Str::limit($lesson->summary, 60) }}</p>
                                    </td>
                                    <td>
                                        @if ($lesson->level)
                                            <span class="badge bg-slate-100 font-mono text-slate-600 dark:bg-slate-800">{{ $lesson->level }}</span>
                                        @else
                                            <span class="text-xs text-slate-400">—</span>
                                        @endif
                                    </td>
                                    <td class="text-right font-mono">{{ $lesson->items_count }}</td>
                                    <td>
                                        @if ($lesson->is_published)
                                            <span class="badge bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15">{{ __('Terbit') }}</span>
                                        @else
                                            <span class="badge bg-slate-200 text-slate-600 dark:bg-slate-700">{{ __('Draf') }}</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        @can('lessons.update')
                                            <a href="{{ route('admin.lessons.edit', $lesson) }}" class="btn-ghost px-2 py-1 text-xs">{{ __('Edit') }}</a>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-10 text-center text-sm text-slate-500">
                                        {{ __('Belum ada materi di modul ini.') }}
                                        @can('lessons.create')
                                            <a href="{{ route('admin.lessons.create', ['module' => $module->id]) }}"
                                               class="mt-2 block font-medium text-brand-600 hover:underline">
                                                {{ __('Tambah materi pertama') }}
                                            </a>
                                        @endcan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="card p-5">
                <h2 class="mb-3 font-semibold">{{ __('Permission modul') }}</h2>
                <p class="mb-3 text-xs text-slate-500">
                    {{ __('Peserta butuh :perm untuk membuka modul ini.', ['perm' => $module->permission('view')]) }}
                </p>

                <div class="space-y-1.5">
                    @foreach (['view', 'create', 'update', 'delete'] as $action)
                        @php $name = $module->permission($action); @endphp
                        <div class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2 dark:bg-slate-800/60">
                            <code class="text-xs">{{ $name }}</code>
                            @if (\Spatie\Permission\Models\Permission::where('name', $name)->exists())
                                <span class="text-xs text-emerald-600">✓ {{ __('ada') }}</span>
                            @else
                                <span class="text-xs text-amber-600">{{ __('belum dibuat') }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>

                @can('permissions.view')
                    <a href="{{ route('admin.permissions.index', ['module' => $module->permission_prefix]) }}"
                       class="btn-secondary mt-4 w-full">{{ __('Kelola permission') }}</a>
                @endcan
            </div>

            <div class="card">
                <div class="card-header"><h2 class="font-semibold">{{ __('Kuis') }}</h2></div>

                <ul class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($quizzes as $quiz)
                        <li class="flex items-center gap-3 px-5 py-3">
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium">{{ $quiz->title }}</p>
                                <p class="text-xs text-slate-500">{{ $quiz->questions_count }} {{ __('soal') }}</p>
                            </div>
                            @can('quizzes.view')
                                <a href="{{ route('admin.quizzes.show', $quiz) }}" class="btn-ghost px-2 py-1 text-xs">{{ __('Lihat') }}</a>
                            @endcan
                        </li>
                    @empty
                        <li class="px-5 py-8 text-center text-sm text-slate-500">{{ __('Belum ada kuis.') }}</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
@endsection

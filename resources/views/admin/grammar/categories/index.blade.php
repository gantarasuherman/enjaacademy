@extends('layouts.app')
@section('title', __('Kategori Grammar'))

@section('content')
    <x-page-header :title="__('Kategori Grammar')" :description="__('Dikelompokkan per level. Kategori bisa punya subkategori — tinggal pilih induknya saat membuat.')">
        <x-slot:actions>
            @can('grammar.create')
                <a href="{{ route('admin.grammar.categories.create') }}" class="btn-primary">
                    <x-icon name="plus" class="size-4" /> {{ __('Kategori baru') }}
                </a>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <div class="space-y-4">
        @forelse ($levels as $level)
            <div class="card p-5">
                <h2 class="mb-3 font-semibold">
                    <span class="badge bg-indigo-100 text-indigo-700 dark:bg-indigo-500/15">{{ $level->name }}</span>
                </h2>

                @if ($level->categories->isEmpty())
                    <p class="text-sm text-slate-500">{{ __('Belum ada kategori di level ini.') }}</p>
                @else
                    <ul class="space-y-1">
                        @foreach ($level->categories as $category)
                            <li>
                                <div class="flex items-center justify-between rounded-lg px-3 py-2 hover:bg-slate-50 dark:hover:bg-slate-800/60">
                                    <span class="text-sm font-medium">{{ $category->name }}</span>
                                    <div class="flex gap-1">
                                        @can('grammar.update')
                                            <a href="{{ route('admin.grammar.categories.edit', $category) }}" class="btn-ghost px-2 py-1 text-xs">{{ __('Edit') }}</a>
                                        @endcan
                                        @can('grammar.delete')
                                            <form method="POST" action="{{ route('admin.grammar.categories.destroy', $category) }}"
                                                  onsubmit="return confirm('{{ __('Hapus kategori ini?') }}')">
                                                @csrf @method('DELETE')
                                                <button class="btn-ghost px-2 py-1 text-xs text-rose-600">{{ __('Hapus') }}</button>
                                            </form>
                                        @endcan
                                    </div>
                                </div>
                                @if ($category->children->isNotEmpty())
                                    <ul class="ml-6 space-y-1 border-l border-slate-200 pl-3 dark:border-slate-800">
                                        @foreach ($category->children as $child)
                                            <li class="flex items-center justify-between rounded-lg px-3 py-2 hover:bg-slate-50 dark:hover:bg-slate-800/60">
                                                <span class="text-sm text-slate-600 dark:text-slate-300">{{ $child->name }}</span>
                                                <div class="flex gap-1">
                                                    @can('grammar.update')
                                                        <a href="{{ route('admin.grammar.categories.edit', $child) }}" class="btn-ghost px-2 py-1 text-xs">{{ __('Edit') }}</a>
                                                    @endcan
                                                    @can('grammar.delete')
                                                        <form method="POST" action="{{ route('admin.grammar.categories.destroy', $child) }}"
                                                              onsubmit="return confirm('{{ __('Hapus kategori ini?') }}')">
                                                            @csrf @method('DELETE')
                                                            <button class="btn-ghost px-2 py-1 text-xs text-rose-600">{{ __('Hapus') }}</button>
                                                        </form>
                                                    @endcan
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @empty
            <div class="card p-8 text-center text-sm text-slate-500">{{ __('Belum ada level. Buat level dulu sebelum kategori.') }}</div>
        @endforelse
    </div>
@endsection

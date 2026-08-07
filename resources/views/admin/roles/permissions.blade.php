@extends('layouts.app')
@section('title', __('Permission untuk :role', ['role' => $role->name]))

@section('content')
    <x-page-header
        :title="__('Permission — :role', ['role' => $role->name])"
        :description="__('Atur hak akses satu role. Untuk mengatur banyak role sekaligus, gunakan matriks permission.')"
        :back="route('admin.roles.index')"
        :back-label="__('Kembali ke role')"
    >
        <x-slot:actions>
            <a href="{{ route('admin.roles.matrix') }}" class="btn-secondary">{{ __('Buka matriks') }}</a>
        </x-slot:actions>
    </x-page-header>

    @if ($isSuper)
        <x-alert type="info">
            {{ __('Role :name selalu memiliki semua permission melalui Gate::before, jadi tidak ada yang perlu diatur di sini.', ['name' => $role->name]) }}
        </x-alert>
    @else
        <form method="POST" action="{{ route('admin.roles.update-permissions', $role) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($grouped as $module => $permissions)
                    <div class="card p-4">
                        <div class="mb-3 flex items-center justify-between">
                            <p class="font-semibold">{{ $module }}</p>
                            <label class="flex items-center gap-1.5 text-xs text-slate-500">
                                <input type="checkbox"
                                       @click="$el.closest('.card').querySelectorAll('input[name=\'permissions[]\']').forEach(el => el.checked = $el.checked)"
                                       x-data
                                       class="rounded border-slate-300 text-brand-600">
                                {{ __('semua') }}
                            </label>
                        </div>

                        <div class="space-y-1.5">
                            @foreach ($permissions as $permission)
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                           @checked(in_array($permission->id, old('permissions', $assigned), true))
                                           class="rounded border-slate-300 text-brand-600">
                                    <code class="text-xs">{{ $permission->name }}</code>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('admin.roles.index') }}" class="btn-secondary">{{ __('Batal') }}</a>
                <button type="submit" class="btn-primary">{{ __('Simpan permission') }}</button>
            </div>
        </form>
    @endif
@endsection

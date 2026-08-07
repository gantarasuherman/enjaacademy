@extends('layouts.app')
@section('title', $user->name)

@section('content')
    <x-page-header
        :title="$user->name"
        :description="$user->email"
        :back="route('admin.users.index')"
        :back-label="__('Kembali ke pengguna')"
    >
        <x-slot:actions>
            @can('users.update')
                <a href="{{ route('admin.users.edit', $user) }}" class="btn-primary">{{ __('Edit pengguna') }}</a>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6">
            <div class="card p-5 text-center">
                <img src="{{ $user->avatar_url }}" alt="" class="mx-auto size-20 rounded-full object-cover">
                <h2 class="mt-4 text-lg font-bold">{{ $user->name }}</h2>
                <p class="text-sm text-slate-500">{{ $user->email }}</p>

                <div class="mt-3 flex flex-wrap justify-center gap-1.5">
                    @foreach ($user->roles as $role)
                        <span class="badge bg-brand-100 text-brand-700 dark:bg-brand-500/15 dark:text-brand-300">{{ $role->name }}</span>
                    @endforeach
                </div>
            </div>

            <div class="card p-5">
                <h2 class="mb-4 font-semibold">{{ __('Statistik belajar') }}</h2>

                <dl class="space-y-3 text-sm">
                    @foreach ([
                        __('Level') => $user->stat?->level ?? 1,
                        __('Total XP') => number_format($user->stat?->xp_total ?? 0),
                        __('Streak') => ($user->stat?->streak_days ?? 0).' '.__('hari'),
                        __('Materi selesai') => $user->stat?->lessons_completed ?? 0,
                        __('Kuis selesai') => $user->stat?->quizzes_completed ?? 0,
                        __('Nilai sempurna') => $user->stat?->perfect_quizzes ?? 0,
                    ] as $label => $value)
                        <div class="flex justify-between">
                            <dt class="text-slate-500">{{ $label }}</dt>
                            <dd class="font-semibold">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>

            <div class="card p-5">
                <h2 class="mb-4 font-semibold">{{ __('Akun') }}</h2>

                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-slate-500">{{ __('Status') }}</dt>
                        <dd>
                            @if ($user->is_active)
                                <span class="badge bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15">{{ __('Aktif') }}</span>
                            @else
                                <span class="badge bg-slate-200 text-slate-600 dark:bg-slate-700">{{ __('Nonaktif') }}</span>
                            @endif
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-500">{{ __('Email terverifikasi') }}</dt>
                        <dd class="font-medium">{{ $user->email_verified_at?->format('d M Y') ?? __('belum') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-500">{{ __('Bergabung') }}</dt>
                        <dd class="font-medium">{{ $user->created_at?->format('d M Y') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-500">{{ __('Login terakhir') }}</dt>
                        <dd class="font-medium">{{ $user->last_login_at?->diffForHumans() ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-500">{{ __('IP terakhir') }}</dt>
                        <dd class="font-mono text-xs">{{ $user->last_login_ip ?? '—' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="space-y-6 lg:col-span-2">
            {{-- Role assignment --}}
            @can('users.update')
                <div class="card p-5">
                    <h2 class="mb-4 font-semibold">{{ __('Ubah role') }}</h2>

                    <form method="POST" action="{{ route('admin.users.assign-roles', $user) }}">
                        @csrf @method('PUT')

                        <div class="grid gap-2 sm:grid-cols-2">
                            @foreach (\Spatie\Permission\Models\Role::orderBy('name')->get() as $role)
                                <label class="flex items-center gap-2.5 rounded-lg border border-slate-200 p-2.5 text-sm dark:border-slate-700">
                                    <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                           @checked($user->hasRole($role->name))
                                           class="rounded border-slate-300 text-brand-600">
                                    {{ $role->name }}
                                </label>
                            @endforeach
                        </div>

                        <button type="submit" class="btn-primary mt-4">{{ __('Simpan role') }}</button>
                    </form>
                </div>
            @endcan

            {{-- Quiz attempts --}}
            <div class="card">
                <div class="card-header"><h2 class="font-semibold">{{ __('Percobaan kuis terakhir') }}</h2></div>

                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Kuis') }}</th>
                                <th class="text-right">{{ __('Skor') }}</th>
                                <th>{{ __('Hasil') }}</th>
                                <th>{{ __('Waktu') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($attempts as $attempt)
                                <tr>
                                    <td class="font-medium">{{ $attempt->quiz?->title ?? '—' }}</td>
                                    <td class="text-right font-mono">{{ $attempt->score }}%</td>
                                    <td>
                                        @if ($attempt->passed)
                                            <span class="badge bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15">{{ __('Lulus') }}</span>
                                        @else
                                            <span class="badge bg-rose-100 text-rose-700 dark:bg-rose-500/15">{{ __('Belum lulus') }}</span>
                                        @endif
                                    </td>
                                    <td class="text-xs text-slate-500">{{ $attempt->finished_at?->diffForHumans() ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="py-8 text-center text-sm text-slate-500">{{ __('Belum ada percobaan kuis.') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Activity log --}}
            @can('audit-logs.view')
                <div class="card">
                    <div class="card-header"><h2 class="font-semibold">{{ __('Riwayat aktivitas') }}</h2></div>

                    <ul class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse ($activity as $log)
                            <li class="flex items-center gap-3 px-5 py-3">
                                <span class="badge shrink-0 bg-slate-100 text-slate-600 dark:bg-slate-800">{{ $log->event }}</span>
                                <span class="min-w-0 flex-1 truncate text-sm">{{ $log->auditable_label ?? $log->description ?? '—' }}</span>
                                <span class="shrink-0 text-xs text-slate-400">{{ $log->created_at?->diffForHumans() }}</span>
                            </li>
                        @empty
                            <li class="px-5 py-8 text-center text-sm text-slate-500">{{ __('Belum ada aktivitas.') }}</li>
                        @endforelse
                    </ul>

                    @if ($activity->hasPages())
                        <div class="border-t border-slate-200 px-5 py-3 dark:border-slate-800">{{ $activity->links() }}</div>
                    @endif
                </div>
            @endcan
        </div>
    </div>
@endsection

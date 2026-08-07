@extends('layouts.app')
@section('title', __('Audit Log'))

@section('content')
    <x-page-header :title="__('Audit Log')" :description="__('Setiap perubahan pada pengguna, role, permission, dan menu tercatat di sini.')">
        <x-slot:actions>
            <a href="{{ route('admin.audit-logs.export', request()->query()) }}" class="btn-secondary">{{ __('Export CSV') }}</a>

            @can('audit-logs.delete')
                <form method="POST" action="{{ route('admin.audit-logs.prune') }}"
                      onsubmit="return confirm('{{ __('Hapus log lama secara permanen?') }}')" class="flex gap-2">
                    @csrf @method('DELETE')
                    <input type="number" name="days" min="1" value="{{ config('admin.audit.prune_days') }}"
                           class="input w-24 text-sm" aria-label="{{ __('Hari') }}">
                    <button class="btn-secondary text-sm text-rose-600">{{ __('Bersihkan') }}</button>
                </form>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <div class="card">
        <div class="card-header flex-wrap">
            <h2 class="font-semibold">{{ __('Riwayat') }}</h2>

            <form method="GET" class="flex flex-wrap gap-2">
                <select name="event" class="input w-32 text-sm">
                    <option value="">{{ __('Semua event') }}</option>
                    @foreach ($events as $event)
                        <option value="{{ $event }}" @selected(request('event') === $event)>{{ $event }}</option>
                    @endforeach
                </select>

                <select name="auditable_type" class="input w-32 text-sm">
                    <option value="">{{ __('Semua objek') }}</option>
                    @foreach ($types as $type)
                        <option value="{{ $type }}" @selected(request('auditable_type') === $type)>{{ $type }}</option>
                    @endforeach
                </select>

                <select name="user_id" class="input w-36 text-sm">
                    <option value="">{{ __('Semua pengguna') }}</option>
                    @foreach ($users as $id => $name)
                        <option value="{{ $id }}" @selected(request('user_id') == $id)>{{ $name }}</option>
                    @endforeach
                </select>

                <input type="date" name="from" value="{{ request('from') }}" class="input w-36 text-sm" aria-label="{{ __('Dari') }}">
                <input type="date" name="to" value="{{ request('to') }}" class="input w-36 text-sm" aria-label="{{ __('Sampai') }}">

                <button class="btn-secondary text-sm">{{ __('Filter') }}</button>
            </form>
        </div>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('Waktu') }}</th>
                        <th>{{ __('Pengguna') }}</th>
                        <th>{{ __('Event') }}</th>
                        <th>{{ __('Objek') }}</th>
                        <th>{{ __('IP') }}</th>
                        <th class="text-right">{{ __('Detail') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td class="whitespace-nowrap text-xs text-slate-500">
                                {{ $log->created_at?->format('d M Y H:i') }}
                            </td>
                            <td>
                                <p class="text-sm font-medium">{{ $log->user_name ?? __('Sistem') }}</p>
                            </td>
                            <td>
                                @php
                                    $tone = match (true) {
                                        str_contains($log->event, 'delete') => 'rose',
                                        str_contains($log->event, 'created') => 'emerald',
                                        str_contains($log->event, 'updated') => 'amber',
                                        default => 'slate',
                                    };
                                @endphp
                                <span class="badge bg-{{ $tone }}-100 text-{{ $tone }}-700 dark:bg-{{ $tone }}-500/15 dark:text-{{ $tone }}-300">
                                    {{ $log->event }}
                                </span>
                            </td>
                            <td>
                                <p class="truncate text-sm">{{ $log->auditable_label ?? '—' }}</p>
                                <p class="truncate text-xs text-slate-500">{{ $log->auditable_type }}</p>
                            </td>
                            <td class="font-mono text-xs text-slate-500">{{ $log->ip_address }}</td>
                            <td class="text-right">
                                <a href="{{ route('admin.audit-logs.show', $log) }}" class="btn-ghost px-2 py-1 text-xs">{{ __('Lihat') }}</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-10 text-center text-sm text-slate-500">{{ __('Tidak ada log yang cocok.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($logs->hasPages())
            <div class="border-t border-slate-200 px-5 py-3 dark:border-slate-800">{{ $logs->links() }}</div>
        @endif
    </div>
@endsection

@extends('layouts.app')
@section('title', __('Detail Log #:id', ['id' => $log->id]))

@section('content')
    <x-page-header
        :title="__('Log #:id', ['id' => $log->id])"
        :description="$log->description"
        :back="route('admin.audit-logs.index')"
        :back-label="__('Kembali ke audit log')"
    />

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="card p-5">
            <h2 class="mb-4 font-semibold">{{ __('Konteks') }}</h2>

            <dl class="space-y-3 text-sm">
                @foreach ([
                    __('Waktu') => $log->created_at?->format('d M Y H:i:s'),
                    __('Pengguna') => $log->user_name ?? __('Sistem'),
                    __('Event') => $log->event,
                    __('Objek') => $log->auditable_type ?? '—',
                    __('Label') => $log->auditable_label ?? '—',
                    __('Metode') => $log->method ?? '—',
                    __('IP') => $log->ip_address ?? '—',
                ] as $label => $value)
                    <div class="flex justify-between gap-3">
                        <dt class="shrink-0 text-slate-500">{{ $label }}</dt>
                        <dd class="truncate text-right font-medium">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>

            @if ($log->url)
                <div class="mt-4 border-t border-slate-200 pt-4 dark:border-slate-800">
                    <p class="text-xs text-slate-500">{{ __('URL') }}</p>
                    <p class="mt-1 break-all font-mono text-xs">{{ $log->url }}</p>
                </div>
            @endif
        </div>

        <div class="lg:col-span-2">
            <div class="card">
                <div class="card-header"><h2 class="font-semibold">{{ __('Perubahan') }}</h2></div>

                @if (empty($changes))
                    <p class="px-5 py-10 text-center text-sm text-slate-500">
                        {{ __('Tidak ada perubahan atribut yang tercatat untuk event ini.') }}
                    </p>
                @else
                    <div class="table-wrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Atribut') }}</th>
                                    <th>{{ __('Sebelum') }}</th>
                                    <th>{{ __('Sesudah') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($changes as $attribute => $pair)
                                    <tr>
                                        <td><code class="text-xs">{{ $attribute }}</code></td>
                                        <td class="text-rose-600">
                                            <span class="text-xs">{{ is_scalar($pair['old']) ? ($pair['old'] ?? '—') : json_encode($pair['old']) }}</span>
                                        </td>
                                        <td class="text-emerald-600">
                                            <span class="text-xs">{{ is_scalar($pair['new']) ? ($pair['new'] ?? '—') : json_encode($pair['new']) }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            @if ($log->user_agent)
                <div class="card mt-6 p-5">
                    <h2 class="mb-2 font-semibold">{{ __('User agent') }}</h2>
                    <p class="break-all font-mono text-xs text-slate-500">{{ $log->user_agent }}</p>
                </div>
            @endif
        </div>
    </div>
@endsection

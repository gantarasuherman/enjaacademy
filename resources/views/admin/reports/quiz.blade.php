@extends('layouts.app')
@section('title', __('Laporan Kuis'))

@php
    $dailyChart = ['labels' => $daily->keys(), 'values' => $daily->values(), 'label' => __('Percobaan')];
@endphp

@section('content')
    <x-page-header :title="__('Performa Kuis')" :description="__('Jumlah percobaan dan rata-rata skor tiap kuis.')" />

    <div class="card mb-6 p-5">
        <h2 class="mb-4 font-semibold">{{ __('Percobaan 30 hari terakhir') }}</h2>
        <div class="h-64">
            <canvas data-chart="bar" data-chart-data='@json($dailyChart)'></canvas>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2 class="font-semibold">{{ __('Per kuis') }}</h2>
            <form method="GET" class="flex gap-2">
                <input type="search" name="search" value="{{ request('search') }}" placeholder="{{ __('Cari kuis…') }}" class="input w-44 text-sm">
                <button class="btn-secondary text-sm">{{ __('Cari') }}</button>
            </form>
        </div>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('Kuis') }}</th>
                        <th>{{ __('Modul') }}</th>
                        <th class="text-right">{{ __('Percobaan') }}</th>
                        <th class="text-right">{{ __('Rata-rata skor') }}</th>
                        <th>{{ __('Distribusi') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $quiz)
                        @php $average = (int) round($quiz->average_score ?? 0); @endphp

                        <tr>
                            <td class="font-medium">{{ $quiz->title }}</td>
                            <td class="text-slate-500">{{ $quiz->module?->name ?? '—' }}</td>
                            <td class="text-right font-mono">{{ number_format($quiz->attempts_count) }}</td>
                            <td class="text-right font-mono">{{ $average }}%</td>
                            <td class="w-40">
                                <div class="h-2 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                                    <div class="h-full rounded-full {{ $average >= 70 ? 'bg-emerald-500' : ($average >= 40 ? 'bg-amber-500' : 'bg-rose-500') }}"
                                         style="width: {{ $average }}%"></div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-10 text-center text-sm text-slate-500">{{ __('Belum ada data kuis.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($rows->hasPages())
            <div class="border-t border-slate-200 px-5 py-3 dark:border-slate-800">{{ $rows->links() }}</div>
        @endif
    </div>
@endsection

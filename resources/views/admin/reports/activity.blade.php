@extends('layouts.app')
@section('title', __('Laporan Aktivitas'))

@php
    $registrationChart = ['labels' => $registrations->keys(), 'values' => $registrations->values(), 'label' => __('Pendaftar')];
    $roleChart = ['labels' => $byRole->pluck('name'), 'values' => $byRole->pluck('total')];
@endphp

@section('content')
    <x-page-header :title="__('Aktivitas')" :description="__('Pendaftaran, keaktifan, dan modul paling banyak dipelajari.')" />

    <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-stat icon="activity" tone="emerald" :label="__('Aktif hari ini')" :value="number_format($activeToday)" />
        <x-stat icon="trending-up" tone="sky" :label="__('Aktif 7 hari')" :value="number_format($activeWeek)" />
        <x-stat icon="users" tone="brand" :label="__('Total role')" :value="$byRole->count()" />
        <x-stat icon="layers" tone="amber" :label="__('Modul teratas')" :value="$topModules->first()->name ?? '—'" />
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="card p-5">
            <h2 class="mb-4 font-semibold">{{ __('Pendaftaran 12 bulan') }}</h2>
            <div class="h-64">
                <canvas data-chart="line" data-chart-data='@json($registrationChart)'></canvas>
            </div>
        </div>

        <div class="card p-5">
            <h2 class="mb-4 font-semibold">{{ __('Pengguna per role') }}</h2>
            <div class="h-64">
                <canvas data-chart="doughnut" data-chart-data='@json($roleChart)'></canvas>
            </div>
        </div>
    </div>

    <div class="card mt-6">
        <div class="card-header"><h2 class="font-semibold">{{ __('Modul paling banyak diselesaikan') }}</h2></div>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('Modul') }}</th>
                        <th class="text-right">{{ __('Penyelesaian materi') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($topModules as $module)
                        <tr>
                            <td class="font-medium">{{ $module->name }}</td>
                            <td class="text-right font-mono">{{ number_format($module->total) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="py-10 text-center text-sm text-slate-500">{{ __('Belum ada penyelesaian materi.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

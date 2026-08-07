@extends('layouts.app')
@section('title', __('Papan Peringkat'))

@section('content')
    <x-page-header :title="__('Papan Peringkat')" :description="__('50 peserta dengan XP tertinggi.')" />

    <div class="card">
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th class="w-16">{{ __('Peringkat') }}</th>
                        <th>{{ __('Peserta') }}</th>
                        <th class="text-right">{{ __('Level') }}</th>
                        <th class="text-right">{{ __('Total XP') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($leaders as $index => $entry)
                        <tr>
                            <td>
                                @if ($index < 3)
                                    <span class="text-lg">{{ ['🥇', '🥈', '🥉'][$index] }}</span>
                                @else
                                    <span class="font-mono text-sm text-slate-400">{{ $index + 1 }}</span>
                                @endif
                            </td>
                            <td class="font-medium">{{ $entry->name }}</td>
                            <td class="text-right font-mono">{{ $entry->level }}</td>
                            <td class="text-right font-mono font-semibold">{{ number_format($entry->xp_total) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-10 text-center text-sm text-slate-500">{{ __('Belum ada data XP.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

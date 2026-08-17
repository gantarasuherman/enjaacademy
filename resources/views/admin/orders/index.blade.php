@extends('layouts.app')
@section('title', __('Transaksi'))

@section('content')
    <x-page-header :title="__('Transaksi')" :description="__('Riwayat transaksi kursus berbayar. Pembayaran saat ini disimulasikan — belum terhubung ke payment gateway sungguhan.')" />

    <div class="card">
        <div class="card-header flex-wrap">
            <h2 class="font-semibold">{{ __('Daftar transaksi') }}</h2>

            <form method="GET" class="flex flex-wrap gap-2">
                <select name="module" class="input w-44 text-sm">
                    <option value="">{{ __('Semua kursus berbayar') }}</option>
                    @foreach ($modules as $module)
                        <option value="{{ $module->id }}" @selected(request('module') == $module->id)>{{ $module->name }}</option>
                    @endforeach
                </select>
                <select name="status" class="input w-36 text-sm">
                    <option value="">{{ __('Semua status') }}</option>
                    <option value="pending" @selected(request('status') === 'pending')>{{ __('Menunggu') }}</option>
                    <option value="paid" @selected(request('status') === 'paid')>{{ __('Lunas') }}</option>
                    <option value="failed" @selected(request('status') === 'failed')>{{ __('Gagal') }}</option>
                    <option value="expired" @selected(request('status') === 'expired')>{{ __('Kedaluwarsa') }}</option>
                </select>
                <input type="search" name="search" value="{{ request('search') }}" placeholder="{{ __('Cari nama/email peserta…') }}" class="input w-56 text-sm">
                <button class="btn-secondary text-sm">{{ __('Filter') }}</button>
            </form>
        </div>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('No. Transaksi') }}</th>
                        <th>{{ __('Peserta') }}</th>
                        <th>{{ __('Kursus') }}</th>
                        <th class="text-right">{{ __('Jumlah') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Tanggal') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td><code class="text-xs">{{ $order->reference }}</code></td>
                            <td>
                                <p class="font-medium">{{ $order->user->name }}</p>
                                <p class="text-xs text-slate-500">{{ $order->user->email }}</p>
                            </td>
                            <td class="text-slate-500">{{ $order->learningModule->name }}</td>
                            <td class="text-right font-mono">Rp {{ number_format($order->amount, 0, ',', '.') }}</td>
                            <td>
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/15',
                                        'paid' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15',
                                        'failed' => 'bg-rose-100 text-rose-700 dark:bg-rose-500/15',
                                        'expired' => 'bg-slate-200 text-slate-600 dark:bg-slate-700',
                                    ];
                                    $statusLabels = [
                                        'pending' => __('Menunggu'),
                                        'paid' => __('Lunas'),
                                        'failed' => __('Gagal'),
                                        'expired' => __('Kedaluwarsa'),
                                    ];
                                @endphp
                                <span class="badge {{ $statusColors[$order->status] }}">{{ $statusLabels[$order->status] }}</span>
                            </td>
                            <td class="text-slate-500">{{ $order->created_at->format('d M Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-10 text-center text-sm text-slate-500">{{ __('Belum ada transaksi.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($orders->hasPages())
            <div class="border-t border-slate-200 px-5 py-3 dark:border-slate-800">{{ $orders->links() }}</div>
        @endif
    </div>
@endsection

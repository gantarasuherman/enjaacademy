@extends('layouts.app')
@section('title', __('Kelas Diambil'))

@section('content')
    <x-page-header :title="__('Kelas Diambil')" :description="__('Peserta yang sudah mengambil kelas per modul. Ini catatan pendaftaran, bukan gerbang akses — akses tetap diatur lewat permission modul.')" />

    <div class="card">
        <div class="card-header flex-wrap">
            <h2 class="font-semibold">{{ __('Daftar pendaftaran') }}</h2>

            <form method="GET" class="flex flex-wrap gap-2">
                <input type="search" name="search" value="{{ request('search') }}" placeholder="{{ __('Cari nama/email peserta…') }}" class="input w-56 text-sm">
                <button class="btn-secondary text-sm">{{ __('Filter') }}</button>
            </form>
        </div>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('Peserta') }}</th>
                        <th>{{ __('Modul') }}</th>
                        <th>{{ __('Tanggal Ambil') }}</th>
                        <th class="text-right">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($enrollments as $enrollment)
                        <tr>
                            <td>
                                <p class="font-medium">{{ $enrollment->user->name }}</p>
                                <p class="text-xs text-slate-500">{{ $enrollment->user->email }}</p>
                            </td>
                            <td>{{ $enrollment->learningModule->name }}</td>
                            <td class="text-slate-500">{{ $enrollment->enrolled_at->format('d M Y H:i') }}</td>
                            <td class="text-right">
                                @can('enrollments.delete')
                                    <form method="POST" action="{{ route('admin.enrollments.destroy', $enrollment) }}"
                                          onsubmit="return confirm('{{ __('Batalkan pendaftaran kelas ini?') }}')">
                                        @csrf @method('DELETE')
                                        <button class="btn-ghost px-2 py-1 text-xs text-rose-600">{{ __('Batalkan') }}</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-10 text-center text-sm text-slate-500">{{ __('Belum ada peserta yang mengambil kelas.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($enrollments->hasPages())
            <div class="border-t border-slate-200 px-5 py-3 dark:border-slate-800">{{ $enrollments->links() }}</div>
        @endif
    </div>
@endsection

@extends('layouts.app')
@section('title', __('Backup'))

@section('content')
    <x-page-header
        :title="__('Backup Database')"
        :description="__('Cadangan dibuat dengan mysqldump dan disimpan di storage. Backup harian juga berjalan otomatis lewat scheduler pukul 02:00.')"
    >
        <x-slot:actions>
            @can('backups.create')
                <form method="POST" action="{{ route('admin.backups.store') }}">
                    @csrf
                    <button class="btn-primary">{{ __('Buat backup sekarang') }}</button>
                </form>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <x-alert type="info" class="mb-5">
        {{ __('Sistem menyimpan :n arsip terbaru; yang lebih lama dihapus otomatis.', ['n' => $keep]) }}
    </x-alert>

    <div class="card">
        <div class="card-header">
            <h2 class="font-semibold">{{ __('Arsip tersedia') }}</h2>
            <span class="text-xs text-slate-500">{{ __(':n berkas', ['n' => $backups->count()]) }}</span>
        </div>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('Nama berkas') }}</th>
                        <th class="text-right">{{ __('Ukuran') }}</th>
                        <th>{{ __('Dibuat') }}</th>
                        <th class="text-right">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($backups as $backup)
                        <tr>
                            <td><code class="text-xs">{{ $backup['name'] }}</code></td>
                            <td class="text-right font-mono text-sm">
                                {{ number_format($backup['size'] / 1048576, 2) }} MB
                            </td>
                            <td class="text-sm text-slate-500">
                                {{ $backup['created_at']->format('d M Y H:i') }}
                                <span class="text-xs">({{ $backup['created_at']->diffForHumans() }})</span>
                            </td>
                            <td class="text-right">
                                <div class="flex justify-end gap-1">
                                    @can('backups.view')
                                        <a href="{{ route('admin.backups.download', $backup['name']) }}" class="btn-ghost px-2 py-1 text-xs">
                                            {{ __('Unduh') }}
                                        </a>
                                    @endcan
                                    @can('backups.delete')
                                        <form method="POST" action="{{ route('admin.backups.destroy', $backup['name']) }}"
                                              onsubmit="return confirm('{{ __('Hapus arsip ini?') }}')">
                                            @csrf @method('DELETE')
                                            <button class="btn-ghost px-2 py-1 text-xs text-rose-600">{{ __('Hapus') }}</button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-10 text-center text-sm text-slate-500">
                                {{ __('Belum ada backup. Klik "Buat backup sekarang" untuk membuat yang pertama.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card mt-6 p-5">
        <h2 class="mb-3 font-semibold">{{ __('Memulihkan backup') }}</h2>
        <p class="mb-3 text-sm text-slate-500">
            {{ __('Pemulihan dilakukan lewat terminal agar tidak ada kecelakaan dari browser:') }}
        </p>
        <pre class="overflow-x-auto rounded-lg bg-slate-900 p-4 text-xs text-slate-100"><code>docker compose exec -T mysql mysql -u root -p{{ '{PASSWORD}' }} {{ config('database.connections.mysql.database') }} \
  &lt; storage/app/{{ config('admin.backup.path') }}/backup-YYYY-MM-DD_HHMMSS.sql</code></pre>
    </div>
@endsection

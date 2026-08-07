@extends('layouts.guest')
@section('title', __('Halaman tidak ditemukan'))

@section('content')
    <div class="text-center">
        <div class="mx-auto grid size-16 place-items-center rounded-full bg-brand-100 text-brand-600 dark:bg-brand-500/15">
            <x-icon name="help" class="size-7" />
        </div>

        <p class="mt-6 font-mono text-sm font-semibold text-slate-400">404</p>
        <h1 class="mt-1 text-2xl font-bold">{{ __('Halaman tidak ditemukan') }}</h1>
        <p class="mt-2 text-sm text-slate-500">{{ __('Tautan yang kamu buka mungkin sudah dipindahkan.') }}</p>

        <div class="mt-6 flex justify-center gap-2">
            <a href="{{ route('home') }}" class="btn-primary">{{ __('Kembali ke beranda') }}</a>
        </div>
    </div>
@endsection

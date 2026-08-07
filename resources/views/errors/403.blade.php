@extends('layouts.guest')
@section('title', __('Akses ditolak'))

@section('content')
    <div class="text-center">
        <div class="mx-auto grid size-16 place-items-center rounded-full bg-rose-100 text-rose-600 dark:bg-rose-500/15">
            <x-icon name="shield" class="size-7" />
        </div>

        <p class="mt-6 font-mono text-sm font-semibold text-slate-400">403</p>
        <h1 class="mt-1 text-2xl font-bold">{{ __('Akses ditolak') }}</h1>
        <p class="mt-2 text-sm text-slate-500">
            {{ $message ?? $exception?->getMessage() ?: __('Kamu tidak punya izin untuk membuka halaman ini.') }}
        </p>

        <div class="mt-6 flex justify-center gap-2">
            @auth
                <a href="{{ route('admin.home') }}" class="btn-primary">{{ __('Ke dashboard') }}</a>
            @else
                <a href="{{ route('login') }}" class="btn-primary">{{ __('Masuk') }}</a>
            @endauth
            <a href="{{ route('home') }}" class="btn-secondary">{{ __('Beranda') }}</a>
        </div>

        @auth
            <p class="mt-6 text-xs text-slate-400">
                {{ __('Role kamu: :roles', ['roles' => auth()->user()->getRoleNames()->implode(', ') ?: __('tidak ada')]) }}
            </p>
        @endauth
    </div>
@endsection

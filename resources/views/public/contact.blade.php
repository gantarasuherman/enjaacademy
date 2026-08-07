@extends('layouts.public')
@section('title', __('Kontak'))

@section('content')
    @php $settings = $appSettings(); @endphp

    <section class="mx-auto max-w-3xl px-4 py-20 lg:px-6">
        <h1 class="text-4xl font-bold tracking-tight">{{ __('Hubungi Kami') }}</h1>
        <p class="mt-4 text-slate-500">{{ __('Ada pertanyaan, masukan, atau kendala teknis? Kami membacanya.') }}</p>

        <div class="mt-10 grid gap-4 sm:grid-cols-3">
            @if (! empty($settings['contact_email']))
                <div class="card p-5">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">{{ __('Email') }}</p>
                    <a href="mailto:{{ $settings['contact_email'] }}" class="mt-1 block font-medium text-brand-600 hover:underline">
                        {{ $settings['contact_email'] }}
                    </a>
                </div>
            @endif

            @if (! empty($settings['contact_phone']))
                <div class="card p-5">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">{{ __('Telepon') }}</p>
                    <p class="mt-1 font-medium">{{ $settings['contact_phone'] }}</p>
                </div>
            @endif

            @if (! empty($settings['address']))
                <div class="card p-5">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">{{ __('Alamat') }}</p>
                    <p class="mt-1 text-sm">{{ $settings['address'] }}</p>
                </div>
            @endif
        </div>
    </section>
@endsection

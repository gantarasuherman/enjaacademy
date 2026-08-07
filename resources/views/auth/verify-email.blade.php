@extends('layouts.guest')
@section('title', __('Verify your email'))

@section('content')
    <h1 class="text-2xl font-bold">{{ __('Verify your email') }}</h1>
    <p class="mt-1.5 text-sm text-slate-500">
        {{ __('We sent a verification link to :email. Click it to activate your account.', ['email' => auth()->user()->email]) }}
    </p>

    @if (session('status') === 'verification-link-sent')
        <x-alert type="success" class="mt-5">
            {{ __('A fresh verification link has been sent.') }}
        </x-alert>
    @endif

    @if (app()->environment('local'))
        <x-alert type="info" class="mt-5">
            {{ __('In development, open Mailpit at') }}
            <a href="http://localhost:8025" target="_blank" rel="noopener" class="font-semibold underline">localhost:8025</a>
            {{ __('to read the email.') }}
        </x-alert>
    @endif

    <div class="mt-6 flex flex-wrap gap-2">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn-primary">{{ __('Resend link') }}</button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-secondary">{{ __('Sign out') }}</button>
        </form>
    </div>
@endsection

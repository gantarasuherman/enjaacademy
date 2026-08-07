@extends('layouts.guest')
@section('title', __('Sign in'))

@section('content')
    <h1 class="text-2xl font-bold">{{ __('Sign in to your account') }}</h1>
    <p class="mt-1.5 text-sm text-slate-500">
        {{ __("Don't have an account?") }}
        <a href="{{ route('register') }}" class="font-semibold text-brand-600 hover:underline">{{ __('Register free') }}</a>
    </p>

    @if ($errors->any())
        <x-alert type="error" class="mt-5">{{ $errors->first() }}</x-alert>
    @endif

    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
        @csrf

        <div>
            <label for="email" class="label">{{ __('Email') }}</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                   autocomplete="email" class="input" placeholder="nama@email.com">
        </div>

        <div x-data="{ show: false }">
            <label for="password" class="label">{{ __('Password') }}</label>
            <div class="relative">
                <input id="password" name="password" :type="show ? 'text' : 'password'" required
                       autocomplete="current-password" class="input pr-10" placeholder="••••••••">
                <button type="button" @click="show = !show"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"
                        :aria-label="show ? '{{ __('Hide password') }}' : '{{ __('Show password') }}'">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="remember" value="1" class="rounded border-slate-300 text-brand-600">
                {{ __('Remember me') }}
            </label>

            <a href="{{ route('password.request') }}" class="text-sm font-medium text-brand-600 hover:underline">
                {{ __('Forgot password?') }}
            </a>
        </div>

        <button type="submit" class="btn-primary w-full py-2.5">{{ __('Sign in') }}</button>
    </form>

    @if (app()->environment('local'))
        <div class="mt-6 rounded-lg border border-dashed border-slate-300 p-4 text-xs dark:border-slate-700">
            <p class="font-semibold text-slate-600 dark:text-slate-300">{{ __('Seeded demo accounts') }}</p>
            <ul class="mt-2 space-y-1 text-slate-500">
                <li>superadmin@nihongo.test — {{ __('full access') }}</li>
                <li>admin@nihongo.test — {{ __('admin') }}</li>
                <li>teacher@nihongo.test — {{ __('content editor') }}</li>
                <li>student@nihongo.test — {{ __('learner') }}</li>
            </ul>
            <p class="mt-2 text-slate-400">{{ __('Password for all: ') }}<code>password</code></p>
        </div>
    @endif
@endsection

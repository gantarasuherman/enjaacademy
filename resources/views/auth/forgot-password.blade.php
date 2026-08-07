@extends('layouts.guest')
@section('title', __('Reset password'))

@section('content')
    <h1 class="text-2xl font-bold">{{ __('Forgot your password?') }}</h1>
    <p class="mt-1.5 text-sm text-slate-500">
        {{ __('Enter your email and we will send you a link to choose a new one.') }}
    </p>

    <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-4">
        @csrf

        <div>
            <label for="email" class="label">{{ __('Email') }}</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                   class="input @error('email') border-rose-400 @enderror">
            @error('email') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="btn-primary w-full py-2.5">{{ __('Send reset link') }}</button>
    </form>

    <p class="mt-6 text-center text-sm">
        <a href="{{ route('login') }}" class="font-medium text-brand-600 hover:underline">{{ __('Back to sign in') }}</a>
    </p>
@endsection

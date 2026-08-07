@extends('layouts.guest')
@section('title', __('Create account'))

@section('content')
    <h1 class="text-2xl font-bold">{{ __('Create a free account') }}</h1>
    <p class="mt-1.5 text-sm text-slate-500">
        {{ __('Already registered?') }}
        <a href="{{ route('login') }}" class="font-semibold text-brand-600 hover:underline">{{ __('Sign in') }}</a>
    </p>

    <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
        @csrf

        <div>
            <label for="name" class="label">{{ __('Full name') }}</label>
            <input id="name" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                   class="input @error('name') border-rose-400 @enderror">
            @error('name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="email" class="label">{{ __('Email') }}</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email"
                   class="input @error('email') border-rose-400 @enderror">
            @error('email') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password" class="label">{{ __('Password') }}</label>
            <input id="password" name="password" type="password" required autocomplete="new-password"
                   class="input @error('password') border-rose-400 @enderror">
            @error('password') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password_confirmation" class="label">{{ __('Confirm password') }}</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required
                   autocomplete="new-password" class="input">
        </div>

        <label class="flex items-start gap-2.5 text-sm">
            <input type="checkbox" name="terms" value="1" @checked(old('terms'))
                   class="mt-0.5 rounded border-slate-300 text-brand-600">
            <span>{{ __('I agree to the Terms of Service and Privacy Policy.') }}</span>
        </label>
        @error('terms') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror

        <button type="submit" class="btn-primary w-full py-2.5">{{ __('Create account') }}</button>
    </form>
@endsection

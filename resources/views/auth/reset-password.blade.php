@extends('layouts.guest')
@section('title', __('Choose a new password'))

@section('content')
    <h1 class="text-2xl font-bold">{{ __('Choose a new password') }}</h1>
    <p class="mt-1.5 text-sm text-slate-500">{{ __('Pick something you have not used before.') }}</p>

    <form method="POST" action="{{ route('password.store') }}" class="mt-6 space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div>
            <label for="email" class="label">{{ __('Email') }}</label>
            <input id="email" name="email" type="email" value="{{ old('email', $email) }}" required readonly
                   class="input bg-slate-50 dark:bg-slate-800">
            @error('email') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password" class="label">{{ __('New password') }}</label>
            <input id="password" name="password" type="password" required autofocus autocomplete="new-password"
                   class="input @error('password') border-rose-400 @enderror">
            @error('password') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password_confirmation" class="label">{{ __('Confirm new password') }}</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required
                   autocomplete="new-password" class="input">
        </div>

        <button type="submit" class="btn-primary w-full py-2.5">{{ __('Reset password') }}</button>
    </form>
@endsection

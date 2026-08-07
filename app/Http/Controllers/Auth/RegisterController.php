<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Services\Setting\SettingService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function __construct(private readonly SettingService $settings) {}

    public function create(): View
    {
        abort_unless($this->registrationOpen(), 403, __('Registration is currently closed.'));

        return view('auth.register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        abort_unless($this->registrationOpen(), 403, __('Registration is currently closed.'));

        $user = DB::transaction(function () use ($request) {
            /** @var User $user */
            $user = User::create($request->safe()->only(['name', 'email', 'password']));

            // New sign-ups get the default learner role configured in settings.
            $user->assignRole($this->settings->get('default_role', 'Student'));
            $user->stat()->create();

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('verification.notice');
    }

    private function registrationOpen(): bool
    {
        return (bool) $this->settings->get('registration_open', true);
    }
}

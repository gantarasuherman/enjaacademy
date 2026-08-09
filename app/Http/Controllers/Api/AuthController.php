<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\User\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(private readonly UserService $users) {}

    /** Issues a Sanctum personal access token. */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages(['email' => [__('auth.failed')]]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages(['email' => [__('Your account is inactive.')]]);
        }

        $this->users->recordLogin($user);

        // Token abilities mirror the user's permissions, so the API honours
        // exactly the same RBAC as the web panel.
        $abilities = $user->isSuperAdmin()
            ? ['*']
            : $user->getAllPermissions()->pluck('name')->all();

        $token = $user->createToken($validated['device_name'] ?? 'api', $abilities ?: ['basic']);

        return response()->json([
            'token' => $token->plainTextToken,
            'user' => new UserResource($user->load('roles')),
        ]);
    }

    /**
     * Self-registration. Every new account lands on the Student role (view
     * access to every learning module — see LearningContentSeeder) and is
     * auto-verified/active, since there is no email-sending or admin-approval
     * flow in this app yet — the same trade-off the seeded demo accounts make.
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:180', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'email_verified_at' => now(),
            'is_active' => true,
            'locale' => 'id',
            'timezone' => 'Asia/Jakarta',
        ]);

        $user->assignRole('Student');
        $user->stat()->create();

        $token = $user->createToken(
            $validated['device_name'] ?? 'api',
            $user->getAllPermissions()->pluck('name')->all() ?: ['basic'],
        );

        return response()->json([
            'token' => $token->plainTextToken,
            'user' => new UserResource($user->load('roles')),
        ], 201);
    }

    public function me(Request $request): UserResource
    {
        return new UserResource($request->user()->load(['roles', 'stat']));
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => __('Signed out.')]);
    }

    public function logoutAll(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => __('All sessions were revoked.')]);
    }
}

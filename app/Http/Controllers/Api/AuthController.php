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

<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Requests\Api\Auth\ResetPasswordRequest;
use App\Http\Resources\UserResource;
use App\Models\DeviceToken;
use App\Models\User;
use App\Notifications\PasswordResetNotification;
use App\Notifications\WelcomeNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends BaseApiController
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $role = $request->input('role', 'buyer');

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => $request->password,
            'phone'     => $request->phone,
            'role'      => $role,
            'is_active' => true,
        ]);

        $token = $user->createToken($request->device_name)->plainTextToken;

        $this->storeDeviceToken($user, $request);

        $user->notify(new WelcomeNotification());

        $message = $role === 'seller'
            ? 'Compte vendeur créé. Créez votre boutique pour commencer à vendre.'
            : 'Compte créé avec succès.';

        return $this->success([
            'user'  => new UserResource($user),
            'token' => $token,
        ], $message, 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return $this->error('Identifiants incorrects.', 401);
        }

        if (! $user->is_active) {
            return $this->error('Votre compte est désactivé.', 403);
        }

        // Révoquer le token du même device s'il existe
        $user->tokens()->where('name', $request->device_name)->delete();

        // Limite : 3 appareils max — révoquer le plus ancien si dépassé
        $activeTokens = $user->tokens()->orderBy('created_at')->get();
        if ($activeTokens->count() >= 3) {
            $activeTokens->first()->delete();
        }

        $token = $user->createToken($request->device_name)->plainTextToken;

        $user->update(['last_login_at' => now()]);

        $this->storeDeviceToken($user, $request);

        return $this->success([
            'user'  => new UserResource($user),
            'token' => $token,
        ], 'Connexion réussie.');
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success(null, 'Déconnexion réussie.');
    }

    public function logoutAll(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return $this->success(null, 'Déconnexion de tous les appareils.');
    }

    public function verifyEmail(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->email_verified_at) {
            return $this->error('Email déjà vérifié.', 409);
        }

        $user->update(['email_verified_at' => now()]);

        return $this->success(null, 'Email vérifié avec succès.');
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return $this->success(null, 'Si cet email existe, un code vous a été envoyé.');
        }

        $otp = (string) random_int(100000, 999999);

        Cache::put("otp:reset:{$user->email}", Hash::make($otp), now()->addMinutes(15));

        $user->notify(new PasswordResetNotification($otp));

        $data = app()->isLocal() ? ['otp' => $otp] : null;

        return $this->success($data, 'Code OTP envoyé par email.');
    }

    public function verifyResetCode(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|string|size:6',
        ]);

        $user   = User::where('email', $request->email)->first();
        $cached = Cache::get("otp:reset:{$request->email}");

        if (! $user || ! $cached || ! Hash::check($request->otp, $cached)) {
            return $this->error('Code OTP invalide ou expiré.', 422);
        }

        Cache::forget("otp:reset:{$request->email}");

        $resetToken = Str::random(64);
        Cache::put("reset_token:{$resetToken}", $user->id, now()->addMinutes(10));

        return $this->success(['reset_token' => $resetToken], 'Code valide.');
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $userId = Cache::get("reset_token:{$request->reset_token}");

        if (! $userId) {
            return $this->error('Token de réinitialisation invalide ou expiré.', 422);
        }

        $user = User::findOrFail($userId);
        $user->update(['password' => $request->password]);

        Cache::forget("reset_token:{$request->reset_token}");

        $user->tokens()->delete();

        return $this->success(null, 'Mot de passe réinitialisé avec succès.');
    }

    /**
     * Révoque le token courant et en génère un nouveau (même device_name).
     * Utile pour la rotation de token côté mobile.
     */
    public function refresh(Request $request): JsonResponse
    {
        $currentToken = $request->user()->currentAccessToken();
        $deviceName   = $currentToken->name;

        $currentToken->delete();

        $newToken = $request->user()->createToken($deviceName)->plainTextToken;

        return $this->success(['token' => $newToken], 'Token renouvelé.');
    }

    private function storeDeviceToken(User $user, Request $request): void
    {
        if ($request->filled('fcm_token')) {
            DeviceToken::updateOrCreate(
                ['user_id' => $user->id, 'device_name' => $request->device_name],
                [
                    'token'        => $request->fcm_token,
                    'platform'     => $request->input('platform', 'flutter'),
                    'last_used_at' => now(),
                ]
            );
        }
    }
}

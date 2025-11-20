<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\RegisterRequest;
use App\Http\Requests\User\LoginRequest;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Requests\User\ChangePasswordRequest;
use App\Http\Requests\User\ForgotPasswordRequest;
use App\Http\Requests\User\ResetPasswordRequest;
use Illuminate\Support\Facades\Mail;
use App\Services\UserService;
use App\Repositories\EmailVerificationRepository;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected UserService $service;
    protected EmailVerificationRepository $emailVerification;

    /**
     * Inyección de dependencias del servicio.
     */
    public function __construct(UserService $service)
    {
        $this->service = $service;
        $this->emailVerification = new EmailVerificationRepository();
    }

    /**
     * Registra un usuario nuevo en el sistema.
     */
    public function register(RegisterRequest $request)
    {
        $user = $this->service->register($request->validated());

        return response()->json([
            'message' => 'User registered successfully.',
            'data'    => $user,
        ], 201);
    }

    /**
     * Autentica un usuario con email y contraseña.
     */
    public function login(LoginRequest $request)
    {
        $result = $this->service->authenticate($request->validated());

        if (! $result) {
            return response()->json([
                'message' => 'Invalid credentials.'
            ], 401);
        }

        return response()->json([
            'message' => 'Login successful.',
            'token'   => $result['token'],
            'data'    => $result['user'],
        ], 200);
    }

    public function me(Request $request)
    {
        return response()->json([
            'data' => $request->user(),
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'message' => 'Unauthorized.'
            ], 401);
        }

        $this->service->logout($user);

        return response()->json([
            'message' => 'Logged out successfully.'
        ]);
    }

    public function logoutAll(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'message' => 'Unauthorized.'
            ], 401);
        }

        $this->service->logoutAll($user);

        return response()->json([
            'message' => 'All sessions closed successfully.'
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = $request->user();

        $updated = $this->service->updateProfile($user, $request->validated());

        return response()->json([
            'message' => 'Profile updated successfully.',
            'data'    => $updated,
        ]);
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $user = $request->user();

        $result = $this->service->changePassword(
            $user,
            $request->current_password,
            $request->new_password
        );

        if (! $result) {
            return response()->json([
                'message' => 'Current password is incorrect.'
            ], 422);
        }

        return response()->json([
            'message' => 'Password updated successfully.'
        ]);
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $ok = $this->service->forgotPassword($request->email);

        if (!$ok) {
            return response()->json(['message' => 'Email not found'], 404);
        }

        return response()->json(['message' => 'Reset token sent']);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $ok = $this->service->resetPassword(
            $request->email,
            $request->token,
            $request->new_password
        );

        if (!$ok) {
            return response()->json(['message' => 'Invalid token or email'], 400);
        }

        return response()->json(['message' => 'Password updated successfully']);
    }

    public function sendVerificationEmail(Request $request)
    {
        $user = $request->user();

        $token = $this->emailVerification->createToken($user->email);

        Mail::raw("Verify your account with this token: $token", function($msg) use ($user) {
            $msg->to($user->email)->subject('Verify your email');
        });

        return response()->json(['message' => 'Verification email sent']);
    }

    public function verifyEmail(Request $request)
    {
        $record = $this->emailVerification->getToken($request->email);

        if (!$record || $record->token !== $request->token) {
            return response()->json(['message' => 'Invalid token'], 400);
        }
        if ($this->emailVerification->isExpired($record)) {
            return response()->json(['message' => 'Token expired'], 400);
        }

        $user = $this->service->getByEmail($request->email);

        $user->email_verified_at = now();
        $user->save();
        $user->save();

        $this->emailVerification->deleteToken($request->email);

        return response()->json(['message' => 'Email verified']);
    }

}
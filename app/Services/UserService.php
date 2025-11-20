<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Repositories\PasswordResetRepository;
use App\Repositories\EmailVerificationRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class UserService
{
    protected UserRepository $repository;
    protected PasswordResetRepository $passwordReset;
    protected EmailVerificationRepository $emailVerification;

    public function __construct(UserRepository $repository, PasswordResetRepository $passwordReset, EmailVerificationRepository $emailVerification)
    {
        $this->repository = $repository;
        $this->passwordReset = $passwordReset;
        $this->emailVerification = $emailVerification;
    }

    /**
     * Registra un usuario aplicando las reglas de negocio.
     */
    public function register(array $data): User
    {
        $data['password'] = Hash::make($data['password']);

        if (!isset($data['role']) || !in_array($data['role'], ['driver', 'host'])) {
            $data['role'] = 'driver';
        }

        // 1. Crear usuario
        $user = $this->repository->create($data);

        // 2. Generar token de verificación
        $token = $this->emailVerification->createToken($user->email);

        // 3. Enviar correo con el token
        Mail::raw("Verify your account with this token: $token", function($msg) use ($user) {
            $msg->to($user->email)->subject('Verify your email');
        });

        return $user;
    }

    /**
     * Autentica un usuario por email y contraseña.
     * Devuelve el usuario si las credenciales son válidas, o null en caso contrario.
     */
    public function authenticate(array $credentials): ?array
    {
        $user = $this->repository->findByEmail($credentials['email']);

        if (! $user) {
            return null;
        }

        if (! Hash::check($credentials['password'], $user->password)) {
            return null;
        }

        // Generación del token de Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }


    /**
     * Obtiene un usuario por email.
     */
    public function getByEmail(string $email): ?User
    {
        return $this->repository->findByEmail($email);
    }

    /**
     * Actualiza un usuario existente.
     */
    public function updateUser(User $user, array $data): User
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return $this->repository->update($user, $data);
    }

    /**
     * Elimina el token actual del usuario autenticado.
     */
    public function logout($user)
    {
        $user->currentAccessToken()->delete();
    }

    /**
     * Elimina todos los tokens del usuario (logout global).
     */
    public function logoutAll($user)
    {
        $user->tokens()->delete();
    }

    public function updateProfile(User $user, array $data): User
    {
        return $this->updateUser($user, $data);
    }

    public function changePassword(User $user, string $currentPassword, string $newPassword)
    {
        if (!Hash::check($currentPassword, $user->password)) {
            return false;
        }

        $user->password = Hash::make($newPassword);
        $user->save();

        return true;
    }

    public function forgotPassword(string $email)
    {
        $user = $this->repository->findByEmail($email);

        if (!$user) {
            return false;
        }

        $token = $this->passwordReset->createToken($email);

        // Enviar correo (por ahora texto plano)
        Mail::raw("Your reset token is: $token", function ($msg) use ($email) {
            $msg->to($email)->subject('Reset your password');
        });

        return true;
    }

    public function resetPassword(string $email, string $token, string $password): bool
    {
        $record = $this->passwordReset->getToken($email);

        if (!$record || $record->token !== $token) {
            return false;
        }

        $user = $this->repository->findByEmail($email);

        if (!$user) {
            return false;
        }

        $user->password = Hash::make($password);
        $user->save();

        $this->passwordReset->deleteToken($email);

        return true;
    }

}
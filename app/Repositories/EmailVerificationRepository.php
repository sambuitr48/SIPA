<?php

namespace App\Repositories;

use App\Models\EmailVerification;
use Illuminate\Support\Str;

class EmailVerificationRepository
{
    public function createToken(string $email): string
    {
        $token = Str::random(60);

        EmailVerification::updateOrCreate(
            ['email' => $email],
            ['token' => $token]
        );

        return $token;
    }

    public function getToken(string $email)
    {
        return EmailVerification::where('email', $email)->first();
    }

    public function deleteToken(string $email)
    {
        EmailVerification::where('email', $email)->delete();
    }

    public function isExpired($record): bool
    {
        return now()->diffInMinutes($record->created_at) > 15;
    }

}

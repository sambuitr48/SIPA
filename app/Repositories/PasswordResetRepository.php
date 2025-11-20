<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PasswordResetRepository
{
    public function createToken(string $email): string
    {
        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => $token,
                'created_at' => Carbon::now(),
            ]
        );

        return $token;
    }

    public function getToken(string $email)
    {
        return DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();
    }

    public function deleteToken(string $email)
    {
        DB::table('password_reset_tokens')->where('email', $email)->delete();
    }
}

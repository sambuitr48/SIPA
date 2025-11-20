<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    /**
     * Crea un nuevo usuario.
     */
    public function create(array $data): User
    {
        return User::create($data);
    }

    /**
     * Obtiene un usuario por correo.
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Obtiene un usuario por ID.
     */
    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    /**
     * Actualiza un usuario existente.
     */
    public function update(User $user, array $data): User
    {
        $user->update($data);
        return $user;
    }
}

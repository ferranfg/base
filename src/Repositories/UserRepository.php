<?php

namespace Ferranfg\Base\Repositories;

use Carbon\Carbon;
use Ferranfg\Base\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return User::find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        $user = new User;

        $user->forceFill([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'trial_ends_at' => null,
        ])->save();

        return $user;
    }
}
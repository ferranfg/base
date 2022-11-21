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
        $user = User::find($id);

        return $user ? $this->loadUserRelationships($user) : null;
    }

    /**
     * Load the relationships for the given user.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    protected function loadUserRelationships($user)
    {
        $user->load('subscriptions');

        if (config('spark.billables.team')) {
            $user->load(['ownedTeams.subscriptions', 'teams.subscriptions']);

            $user->currentTeam();
        }

        return $user;
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
            'last_read_announcements_at' => Carbon::now(),
            'trial_ends_at' => null,
        ])->save();

        return $user;
    }
}
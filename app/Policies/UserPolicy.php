<?php

namespace App\Policies;

use App\User;

class UserPolicy
{
    /**
     * Intercept checks.
     *
     * @return bool
     */
    public function before(User $currentUser)
    {
        if ($currentUser->tokenCan('admin')) {
            return true;
        }
    }

    /**
     * Determine if a given user has permission to show.
     *
     * @return bool
     */
    public function show(User $currentUser, User $user)
    {
        return $currentUser->id === $user->id;
    }

    /**
     * Determine if a given user can update.
     *
     * @return bool
     */
    public function update(User $currentUser, User $user)
    {
        return $currentUser->id === $user->id;
    }

    /**
     * Determine if a given user can delete.
     *
     * @return bool
     */
    public function destroy(User $currentUser, User $user)
    {
        return $currentUser->id === $user->id;
    }
}

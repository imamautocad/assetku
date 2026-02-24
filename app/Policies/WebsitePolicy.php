<?php

namespace App\Policies;

use App\Models\User;

class WebsitePolicy
{
    /**
     * Tentukan apakah user bisa melihat menu Website
     */
    public function view(User $user)
    {
        $permissions = json_decode($user->permissions, true);

        // hanya superuser atau admin
        return ($permissions['superuser'] ?? '0') === '1' || ($permissions['admin'] ?? '0') === '1';
    }
}
 
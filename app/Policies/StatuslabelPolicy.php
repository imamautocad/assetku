<?php

// namespace App\Policies;

// class StatuslabelPolicy extends SnipePermissionsPolicy
// {
//     protected function columnName()
//     {
//         return 'statuslabels';
//     }

namespace App\Policies;

use App\Models\User;
class StatuslabelPolicy extends SnipePermissionsPolicy
{
    protected function columnName()
    {
        return 'statuslabels';
    }

    /**
     * Allow GA to view status labels ONLY when accessed from assets
     */
    public function view(User $user, $item = null)
    {
        // Admin always allowed
        if ($user->isSuperUser()) {
            return true;
        }

        // Allow viewing status label when coming from assets / hardware
        if (
            request()->routeIs('hardware.*') ||
            request()->routeIs('api.hardware.*') ||
            request()->routeIs('statuslabels.show')
        ) {
            return true;
        }

        // Default behavior (Settings → Status Labels)
        return $user->hasAccess('statuslabels.view');
    }
} 

<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;

class ApplicationPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Application $application): bool
    {
        if ($user->isAdmin()) return true;
        if ($user->isConsumer()) return $application->applicant_id === $user->id;
        // Staff: own district OR regional backup
        return $user->district_id === $application->district_id || $user->is_regional_backup;
    }

    public function create(User $user): bool
    {
        // Consumers create online; staff can register written/offline applications too
        return true;
    }

    public function update(User $user, Application $application): bool
    {
        if ($user->isAdmin()) return true;
        return $user->isConsumer()
            && $application->applicant_id === $user->id
            && $application->isEditable();
    }

    public function delete(User $user, Application $application): bool
    {
        return $user->isAdmin();
    }
}

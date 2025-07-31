<?php

namespace App\Policies;

use App\Models\HealthRecord;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class HealthRecordPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, HealthRecord $healthRecord)
    {
        return $user->id === $healthRecord->pet->user_id;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, HealthRecord $healthRecord)
    {
        return $user->id === $healthRecord->pet->user_id;
    }

    public function delete(User $user, HealthRecord $healthRecord)
    {
        return $user->id === $healthRecord->pet->user_id;
    }

    public function restore(User $user, HealthRecord $healthRecord)
    {
        return $user->id === $healthRecord->pet->user_id;
    }

    public function forceDelete(User $user, HealthRecord $healthRecord)
    {
        return $user->id === $healthRecord->pet->user_id;
    }
}

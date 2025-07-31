<?php

namespace App\Policies;

use App\Models\BehaviorLog;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BehaviorLogPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, BehaviorLog $behaviorLog)
    {
        return $user->id === $behaviorLog->pet->user_id;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, BehaviorLog $behaviorLog)
    {
        return $user->id === $behaviorLog->pet->user_id;
    }

    public function delete(User $user, BehaviorLog $behaviorLog)
    {
        return $user->id === $behaviorLog->pet->user_id;
    }

    public function restore(User $user, BehaviorLog $behaviorLog)
    {
        return $user->id === $behaviorLog->pet->user_id;
    }

    public function forceDelete(User $user, BehaviorLog $behaviorLog)
    {
        return $user->id === $behaviorLog->pet->user_id;
    }
}

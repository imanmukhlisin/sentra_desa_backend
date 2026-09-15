<?php

namespace App\Policies;

use App\Models\Tourism;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TourismPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_tourism');
    }

    public function view(User $user, Tourism $tourism): bool
    {
        return $user->can('view_tourism');
    }

    public function create(User $user): bool
    {
        return $user->can('create_tourism');
    }

    public function update(User $user, Tourism $tourism): bool
    {
        return $user->can('update_tourism');
    }

    public function delete(User $user, Tourism $tourism): bool
    {
        return $user->can('delete_tourism');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_tourism');
    }

    public function forceDelete(User $user, Tourism $tourism): bool
    {
        return $user->can('force_delete_tourism');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_tourism');
    }

    public function restore(User $user, Tourism $tourism): bool
    {
        return $user->can('restore_tourism');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_tourism');
    }

    public function replicate(User $user, Tourism $tourism): bool
    {
        return $user->can('replicate_tourism');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_tourism');
    }
}

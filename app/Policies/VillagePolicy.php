<?php

namespace App\Policies;

use App\Models\Village;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class VillagePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_village');
    }

    public function view(User $user, Village $village): bool
    {
        return $user->can('view_village');
    }

    public function create(User $user): bool
    {
        return $user->can('create_village');
    }

    public function update(User $user, Village $village): bool
    {
        return $user->can('update_village');
    }

    public function delete(User $user, Village $village): bool
    {
        return $user->can('delete_village');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_village');
    }

    public function forceDelete(User $user, Village $village): bool
    {
        return $user->can('force_delete_village');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_village');
    }

    public function restore(User $user, Village $village): bool
    {
        return $user->can('restore_village');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_village');
    }

    public function replicate(User $user, Village $village): bool
    {
        return $user->can('replicate_village');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_village');
    }
}

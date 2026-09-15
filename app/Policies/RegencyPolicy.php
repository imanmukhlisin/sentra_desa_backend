<?php

namespace App\Policies;

use App\Models\Regency;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RegencyPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_regency');
    }

    public function view(User $user, Regency $regency): bool
    {
        return $user->can('view_regency');
    }

    public function create(User $user): bool
    {
        return $user->can('create_regency');
    }

    public function update(User $user, Regency $regency): bool
    {
        return $user->can('update_regency');
    }

    public function delete(User $user, Regency $regency): bool
    {
        return $user->can('delete_regency');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_regency');
    }

    public function forceDelete(User $user, Regency $regency): bool
    {
        return $user->can('force_delete_regency');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_regency');
    }

    public function restore(User $user, Regency $regency): bool
    {
        return $user->can('restore_regency');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_regency');
    }

    public function replicate(User $user, Regency $regency): bool
    {
        return $user->can('replicate_regency');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_regency');
    }
}

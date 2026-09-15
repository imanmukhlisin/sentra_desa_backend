<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DesaInspiratifPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_desa_inspiratif');
    }

    public function view(User $user, $model): bool
    {
        return $user->can('view_desa_inspiratif');
    }

    public function create(User $user): bool
    {
        return $user->can('create_desa_inspiratif');
    }

    public function update(User $user, $model): bool
    {
        return $user->can('update_desa_inspiratif');
    }

    public function delete(User $user, $model): bool
    {
        return $user->can('delete_desa_inspiratif');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_desa_inspiratif');
    }

    public function forceDelete(User $user, $model): bool
    {
        return $user->can('force_delete_desa_inspiratif');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_desa_inspiratif');
    }

    public function restore(User $user, $model): bool
    {
        return $user->can('restore_desa_inspiratif');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_desa_inspiratif');
    }
}

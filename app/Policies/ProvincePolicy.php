<?php

namespace App\Policies;

use App\Models\Province;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProvincePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_province');
    }

    public function view(User $user, Province $province): bool
    {
        return $user->can('view_province');
    }

    public function create(User $user): bool
    {
        return $user->can('create_province');
    }

    public function update(User $user, Province $province): bool
    {
        return $user->can('update_province');
    }

    public function delete(User $user, Province $province): bool
    {
        return $user->can('delete_province');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_province');
    }

    public function forceDelete(User $user, Province $province): bool
    {
        return $user->can('force_delete_province');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_province');
    }

    public function restore(User $user, Province $province): bool
    {
        return $user->can('restore_province');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_province');
    }

    public function replicate(User $user, Province $province): bool
    {
        return $user->can('replicate_province');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_province');
    }
}

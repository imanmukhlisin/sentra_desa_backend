<?php

namespace App\Policies;

use App\Models\Kdmp;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class KdmpPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_kdmp');
    }

    public function view(User $user, Kdmp $kdmp): bool
    {
        return $user->can('view_kdmp');
    }

    public function create(User $user): bool
    {
        return $user->can('create_kdmp');
    }

    public function update(User $user, Kdmp $kdmp): bool
    {
        return $user->can('update_kdmp');
    }

    public function delete(User $user, Kdmp $kdmp): bool
    {
        return $user->can('delete_kdmp');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_kdmp');
    }

    public function forceDelete(User $user, Kdmp $kdmp): bool
    {
        return $user->can('force_delete_kdmp');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_kdmp');
    }

    public function restore(User $user, Kdmp $kdmp): bool
    {
        return $user->can('restore_kdmp');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_kdmp');
    }

    public function replicate(User $user, Kdmp $kdmp): bool
    {
        return $user->can('replicate_kdmp');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_kdmp');
    }
}

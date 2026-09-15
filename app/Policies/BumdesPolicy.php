<?php

namespace App\Policies;

use App\Models\Bumdes;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BumdesPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_bumdes');
    }

    public function view(User $user, Bumdes $bumdes): bool
    {
        return $user->can('view_bumdes');
    }

    public function create(User $user): bool
    {
        return $user->can('create_bumdes');
    }

    public function update(User $user, Bumdes $bumdes): bool
    {
        return $user->can('update_bumdes');
    }

    public function delete(User $user, Bumdes $bumdes): bool
    {
        return $user->can('delete_bumdes');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_bumdes');
    }

    public function forceDelete(User $user, Bumdes $bumdes): bool
    {
        return $user->can('force_delete_bumdes');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_bumdes');
    }

    public function restore(User $user, Bumdes $bumdes): bool
    {
        return $user->can('restore_bumdes');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_bumdes');
    }

    public function replicate(User $user, Bumdes $bumdes): bool
    {
        return $user->can('replicate_bumdes');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_bumdes');
    }
}

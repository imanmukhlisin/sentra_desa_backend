<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class KoperasiDesaPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_koperasi_desa');
    }

    public function view(User $user, $model): bool
    {
        return $user->can('view_koperasi_desa');
    }

    public function create(User $user): bool
    {
        return $user->can('create_koperasi_desa');
    }

    public function update(User $user, $model): bool
    {
        return $user->can('update_koperasi_desa');
    }

    public function delete(User $user, $model): bool
    {
        return $user->can('delete_koperasi_desa');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_koperasi_desa');
    }

    public function forceDelete(User $user, $model): bool
    {
        return $user->can('force_delete_koperasi_desa');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_koperasi_desa');
    }

    public function restore(User $user, $model): bool
    {
        return $user->can('restore_koperasi_desa');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_koperasi_desa');
    }
}

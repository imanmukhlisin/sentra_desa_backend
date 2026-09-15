<?php

namespace App\Policies;

use App\Models\VillageService;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class VillageServicePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_village_service');
    }

    public function view(User $user, VillageService $villageService): bool
    {
        return $user->can('view_village_service');
    }

    public function create(User $user): bool
    {
        return $user->can('create_village_service');
    }

    public function update(User $user, VillageService $villageService): bool
    {
        return $user->can('update_village_service');
    }

    public function delete(User $user, VillageService $villageService): bool
    {
        return $user->can('delete_village_service');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_village_service');
    }

    public function forceDelete(User $user, VillageService $villageService): bool
    {
        return $user->can('force_delete_village_service');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_village_service');
    }

    public function restore(User $user, VillageService $villageService): bool
    {
        return $user->can('restore_village_service');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_village_service');
    }

    public function replicate(User $user, VillageService $villageService): bool
    {
        return $user->can('replicate_village_service');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_village_service');
    }
}

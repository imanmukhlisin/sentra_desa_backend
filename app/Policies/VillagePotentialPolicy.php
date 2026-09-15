<?php

namespace App\Policies;

use App\Models\VillagePotential;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class VillagePotentialPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_village_potential');
    }

    public function view(User $user, VillagePotential $villagePotential): bool
    {
        return $user->can('view_village_potential');
    }

    public function create(User $user): bool
    {
        return $user->can('create_village_potential');
    }

    public function update(User $user, VillagePotential $villagePotential): bool
    {
        return $user->can('update_village_potential');
    }

    public function delete(User $user, VillagePotential $villagePotential): bool
    {
        return $user->can('delete_village_potential');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_village_potential');
    }

    public function forceDelete(User $user, VillagePotential $villagePotential): bool
    {
        return $user->can('force_delete_village_potential');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_village_potential');
    }

    public function restore(User $user, VillagePotential $villagePotential): bool
    {
        return $user->can('restore_village_potential');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_village_potential');
    }

    public function replicate(User $user, VillagePotential $villagePotential): bool
    {
        return $user->can('replicate_village_potential');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_village_potential');
    }
}

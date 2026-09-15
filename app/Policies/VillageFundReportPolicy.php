<?php

namespace App\Policies;

use App\Models\VillageFundReport;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class VillageFundReportPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_village_fund_report');
    }

    public function view(User $user, VillageFundReport $villageFundReport): bool
    {
        return $user->can('view_village_fund_report');
    }

    public function create(User $user): bool
    {
        return $user->can('create_village_fund_report');
    }

    public function update(User $user, VillageFundReport $villageFundReport): bool
    {
        return $user->can('update_village_fund_report');
    }

    public function delete(User $user, VillageFundReport $villageFundReport): bool
    {
        return $user->can('delete_village_fund_report');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_village_fund_report');
    }

    public function forceDelete(User $user, VillageFundReport $villageFundReport): bool
    {
        return $user->can('force_delete_village_fund_report');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_village_fund_report');
    }

    public function restore(User $user, VillageFundReport $villageFundReport): bool
    {
        return $user->can('restore_village_fund_report');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_village_fund_report');
    }

    public function replicate(User $user, VillageFundReport $villageFundReport): bool
    {
        return $user->can('replicate_village_fund_report');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_village_fund_report');
    }
}

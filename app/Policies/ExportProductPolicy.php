<?php

namespace App\Policies;

use App\Models\ExportProduct;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExportProductPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_export_product');
    }

    public function view(User $user, ExportProduct $exportProduct): bool
    {
        return $user->can('view_export_product');
    }

    public function create(User $user): bool
    {
        return $user->can('create_export_product');
    }

    public function update(User $user, ExportProduct $exportProduct): bool
    {
        return $user->can('update_export_product');
    }

    public function delete(User $user, ExportProduct $exportProduct): bool
    {
        return $user->can('delete_export_product');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_export_product');
    }

    public function forceDelete(User $user, ExportProduct $exportProduct): bool
    {
        return $user->can('force_delete_export_product');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_export_product');
    }

    public function restore(User $user, ExportProduct $exportProduct): bool
    {
        return $user->can('restore_export_product');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_export_product');
    }

    public function replicate(User $user, ExportProduct $exportProduct): bool
    {
        return $user->can('replicate_export_product');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_export_product');
    }
}

<?php

namespace App\Policies;

use App\Models\Highlight;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class HighlightPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_highlight');
    }

    public function view(User $user, Highlight $highlight): bool
    {
        return $user->can('view_highlight');
    }

    public function create(User $user): bool
    {
        return $user->can('create_highlight');
    }

    public function update(User $user, Highlight $highlight): bool
    {
        return $user->can('update_highlight');
    }

    public function delete(User $user, Highlight $highlight): bool
    {
        return $user->can('delete_highlight');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_highlight');
    }

    public function forceDelete(User $user, Highlight $highlight): bool
    {
        return $user->can('force_delete_highlight');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_highlight');
    }

    public function restore(User $user, Highlight $highlight): bool
    {
        return $user->can('restore_highlight');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_highlight');
    }

    public function replicate(User $user, Highlight $highlight): bool
    {
        return $user->can('replicate_highlight');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_highlight');
    }
}

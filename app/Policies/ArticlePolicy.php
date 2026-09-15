<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArticlePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_article');
    }

    public function view(User $user, $model): bool
    {
        return $user->can('view_article');
    }

    public function create(User $user): bool
    {
        return $user->can('create_article');
    }

    public function update(User $user, $model): bool
    {
        return $user->can('update_article');
    }

    public function delete(User $user, $model): bool
    {
        return $user->can('delete_article');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_article');
    }

    public function forceDelete(User $user, $model): bool
    {
        return $user->can('force_delete_article');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_article');
    }

    public function restore(User $user, $model): bool
    {
        return $user->can('restore_article');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_article');
    }
}

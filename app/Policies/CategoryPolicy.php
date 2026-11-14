<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return (bool) $user->is_admin;
    }

    public function view(User $user, Category $category): bool
    {
        return (bool) $user->is_admin;
    }

    public function create(User $user): bool
    {
        return (bool) $user->is_admin;
    }

    public function update(User $user, Category $category): bool
    {
        return (bool) $user->is_admin;
    }

    /**
     * Prevent deleting a category that is still referenced by posts.
     */
    public function delete(User $user, Category $category): bool
    {
        return $user->is_admin && ! $category->posts()->exists();
    }

    public function restore(User $user, Category $category): bool
    {
        return (bool) $user->is_admin;
    }

    /**
     * Prevent force deleting a category that still has posts, including trashed ones.
     */
    public function forceDelete(User $user, Category $category): bool
    {
        return $user->is_admin && ! $category->posts()->withTrashed()->exists();
    }
}

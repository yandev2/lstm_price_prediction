<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Distribusi;
use Illuminate\Auth\Access\HandlesAuthorization;

class DistribusiPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Distribusi');
    }

    public function view(AuthUser $authUser, Distribusi $distribusi): bool
    {
        return $authUser->can('View:Distribusi');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Distribusi');
    }

    public function update(AuthUser $authUser, Distribusi $distribusi): bool
    {
        return $authUser->can('Update:Distribusi');
    }

    public function delete(AuthUser $authUser, Distribusi $distribusi): bool
    {
        return $authUser->can('Delete:Distribusi');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Distribusi');
    }

    public function restore(AuthUser $authUser, Distribusi $distribusi): bool
    {
        return $authUser->can('Restore:Distribusi');
    }

    public function forceDelete(AuthUser $authUser, Distribusi $distribusi): bool
    {
        return $authUser->can('ForceDelete:Distribusi');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Distribusi');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Distribusi');
    }

    public function replicate(AuthUser $authUser, Distribusi $distribusi): bool
    {
        return $authUser->can('Replicate:Distribusi');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Distribusi');
    }

}
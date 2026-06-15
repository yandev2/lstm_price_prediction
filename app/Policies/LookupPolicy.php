<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Lookup;
use Illuminate\Auth\Access\HandlesAuthorization;

class LookupPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Lookup');
    }

    public function view(AuthUser $authUser, Lookup $lookup): bool
    {
        return $authUser->can('View:Lookup');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Lookup');
    }

    public function update(AuthUser $authUser, Lookup $lookup): bool
    {
        return $authUser->can('Update:Lookup');
    }

    public function delete(AuthUser $authUser, Lookup $lookup): bool
    {
        return $authUser->can('Delete:Lookup');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Lookup');
    }

    public function restore(AuthUser $authUser, Lookup $lookup): bool
    {
        return $authUser->can('Restore:Lookup');
    }

    public function forceDelete(AuthUser $authUser, Lookup $lookup): bool
    {
        return $authUser->can('ForceDelete:Lookup');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Lookup');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Lookup');
    }

    public function replicate(AuthUser $authUser, Lookup $lookup): bool
    {
        return $authUser->can('Replicate:Lookup');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Lookup');
    }

}
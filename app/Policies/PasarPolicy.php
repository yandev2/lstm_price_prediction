<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Pasar;
use Illuminate\Auth\Access\HandlesAuthorization;

class PasarPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Pasar');
    }

    public function view(AuthUser $authUser, Pasar $pasar): bool
    {
        return $authUser->can('View:Pasar');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Pasar');
    }

    public function update(AuthUser $authUser, Pasar $pasar): bool
    {
        return $authUser->can('Update:Pasar');
    }

    public function delete(AuthUser $authUser, Pasar $pasar): bool
    {
        return $authUser->can('Delete:Pasar');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Pasar');
    }

    public function restore(AuthUser $authUser, Pasar $pasar): bool
    {
        return $authUser->can('Restore:Pasar');
    }

    public function forceDelete(AuthUser $authUser, Pasar $pasar): bool
    {
        return $authUser->can('ForceDelete:Pasar');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Pasar');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Pasar');
    }

    public function replicate(AuthUser $authUser, Pasar $pasar): bool
    {
        return $authUser->can('Replicate:Pasar');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Pasar');
    }

}
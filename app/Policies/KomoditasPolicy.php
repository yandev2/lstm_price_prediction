<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Komoditas;
use Illuminate\Auth\Access\HandlesAuthorization;

class KomoditasPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Komoditas');
    }

    public function view(AuthUser $authUser, Komoditas $komoditas): bool
    {
        return $authUser->can('View:Komoditas');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Komoditas');
    }

    public function update(AuthUser $authUser, Komoditas $komoditas): bool
    {
        return $authUser->can('Update:Komoditas');
    }

    public function delete(AuthUser $authUser, Komoditas $komoditas): bool
    {
        return $authUser->can('Delete:Komoditas');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Komoditas');
    }

    public function restore(AuthUser $authUser, Komoditas $komoditas): bool
    {
        return $authUser->can('Restore:Komoditas');
    }

    public function forceDelete(AuthUser $authUser, Komoditas $komoditas): bool
    {
        return $authUser->can('ForceDelete:Komoditas');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Komoditas');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Komoditas');
    }

    public function replicate(AuthUser $authUser, Komoditas $komoditas): bool
    {
        return $authUser->can('Replicate:Komoditas');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Komoditas');
    }

}
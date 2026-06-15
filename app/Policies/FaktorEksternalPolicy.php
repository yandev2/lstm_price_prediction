<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\FaktorEksternal;
use Illuminate\Auth\Access\HandlesAuthorization;

class FaktorEksternalPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:FaktorEksternal');
    }

    public function view(AuthUser $authUser, FaktorEksternal $faktorEksternal): bool
    {
        return $authUser->can('View:FaktorEksternal');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:FaktorEksternal');
    }

    public function update(AuthUser $authUser, FaktorEksternal $faktorEksternal): bool
    {
        return $authUser->can('Update:FaktorEksternal');
    }

    public function delete(AuthUser $authUser, FaktorEksternal $faktorEksternal): bool
    {
        return $authUser->can('Delete:FaktorEksternal');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:FaktorEksternal');
    }

    public function restore(AuthUser $authUser, FaktorEksternal $faktorEksternal): bool
    {
        return $authUser->can('Restore:FaktorEksternal');
    }

    public function forceDelete(AuthUser $authUser, FaktorEksternal $faktorEksternal): bool
    {
        return $authUser->can('ForceDelete:FaktorEksternal');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:FaktorEksternal');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:FaktorEksternal');
    }

    public function replicate(AuthUser $authUser, FaktorEksternal $faktorEksternal): bool
    {
        return $authUser->can('Replicate:FaktorEksternal');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:FaktorEksternal');
    }

}
<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\HargaPangan;
use Illuminate\Auth\Access\HandlesAuthorization;

class HargaPanganPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:HargaPangan');
    }

    public function view(AuthUser $authUser, HargaPangan $hargaPangan): bool
    {
        return $authUser->can('View:HargaPangan');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:HargaPangan');
    }

    public function update(AuthUser $authUser, HargaPangan $hargaPangan): bool
    {
        return $authUser->can('Update:HargaPangan');
    }

    public function delete(AuthUser $authUser, HargaPangan $hargaPangan): bool
    {
        return $authUser->can('Delete:HargaPangan');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:HargaPangan');
    }

    public function restore(AuthUser $authUser, HargaPangan $hargaPangan): bool
    {
        return $authUser->can('Restore:HargaPangan');
    }

    public function forceDelete(AuthUser $authUser, HargaPangan $hargaPangan): bool
    {
        return $authUser->can('ForceDelete:HargaPangan');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:HargaPangan');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:HargaPangan');
    }

    public function replicate(AuthUser $authUser, HargaPangan $hargaPangan): bool
    {
        return $authUser->can('Replicate:HargaPangan');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:HargaPangan');
    }

}
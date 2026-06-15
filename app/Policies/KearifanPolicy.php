<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Kearifan;
use Illuminate\Auth\Access\HandlesAuthorization;

class KearifanPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Kearifan');
    }

    public function view(AuthUser $authUser, Kearifan $kearifan): bool
    {
        return $authUser->can('View:Kearifan');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Kearifan');
    }

    public function update(AuthUser $authUser, Kearifan $kearifan): bool
    {
        return $authUser->can('Update:Kearifan');
    }

    public function delete(AuthUser $authUser, Kearifan $kearifan): bool
    {
        return $authUser->can('Delete:Kearifan');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Kearifan');
    }

    public function restore(AuthUser $authUser, Kearifan $kearifan): bool
    {
        return $authUser->can('Restore:Kearifan');
    }

    public function forceDelete(AuthUser $authUser, Kearifan $kearifan): bool
    {
        return $authUser->can('ForceDelete:Kearifan');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Kearifan');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Kearifan');
    }

    public function replicate(AuthUser $authUser, Kearifan $kearifan): bool
    {
        return $authUser->can('Replicate:Kearifan');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Kearifan');
    }

}
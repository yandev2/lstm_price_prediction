<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\MappingKearifan;
use Illuminate\Auth\Access\HandlesAuthorization;

class MappingKearifanPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MappingKearifan');
    }

    public function view(AuthUser $authUser, MappingKearifan $mappingKearifan): bool
    {
        return $authUser->can('View:MappingKearifan');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MappingKearifan');
    }

    public function update(AuthUser $authUser, MappingKearifan $mappingKearifan): bool
    {
        return $authUser->can('Update:MappingKearifan');
    }

    public function delete(AuthUser $authUser, MappingKearifan $mappingKearifan): bool
    {
        return $authUser->can('Delete:MappingKearifan');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:MappingKearifan');
    }

    public function restore(AuthUser $authUser, MappingKearifan $mappingKearifan): bool
    {
        return $authUser->can('Restore:MappingKearifan');
    }

    public function forceDelete(AuthUser $authUser, MappingKearifan $mappingKearifan): bool
    {
        return $authUser->can('ForceDelete:MappingKearifan');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MappingKearifan');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MappingKearifan');
    }

    public function replicate(AuthUser $authUser, MappingKearifan $mappingKearifan): bool
    {
        return $authUser->can('Replicate:MappingKearifan');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MappingKearifan');
    }

}
<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ModelAi;
use Illuminate\Auth\Access\HandlesAuthorization;

class ModelAiPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ModelAi');
    }

    public function view(AuthUser $authUser, ModelAi $modelAi): bool
    {
        return $authUser->can('View:ModelAi');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ModelAi');
    }

    public function update(AuthUser $authUser, ModelAi $modelAi): bool
    {
        return $authUser->can('Update:ModelAi');
    }

    public function delete(AuthUser $authUser, ModelAi $modelAi): bool
    {
        return $authUser->can('Delete:ModelAi');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ModelAi');
    }

    public function restore(AuthUser $authUser, ModelAi $modelAi): bool
    {
        return $authUser->can('Restore:ModelAi');
    }

    public function forceDelete(AuthUser $authUser, ModelAi $modelAi): bool
    {
        return $authUser->can('ForceDelete:ModelAi');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ModelAi');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ModelAi');
    }

    public function replicate(AuthUser $authUser, ModelAi $modelAi): bool
    {
        return $authUser->can('Replicate:ModelAi');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ModelAi');
    }

}
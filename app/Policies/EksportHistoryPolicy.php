<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\EksportHistory;
use Illuminate\Auth\Access\HandlesAuthorization;

class EksportHistoryPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:EksportHistory');
    }

    public function view(AuthUser $authUser, EksportHistory $eksportHistory): bool
    {
        return $authUser->can('View:EksportHistory');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:EksportHistory');
    }

    public function update(AuthUser $authUser, EksportHistory $eksportHistory): bool
    {
        return $authUser->can('Update:EksportHistory');
    }

    public function delete(AuthUser $authUser, EksportHistory $eksportHistory): bool
    {
        return $authUser->can('Delete:EksportHistory');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:EksportHistory');
    }

    public function restore(AuthUser $authUser, EksportHistory $eksportHistory): bool
    {
        return $authUser->can('Restore:EksportHistory');
    }

    public function forceDelete(AuthUser $authUser, EksportHistory $eksportHistory): bool
    {
        return $authUser->can('ForceDelete:EksportHistory');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:EksportHistory');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:EksportHistory');
    }

    public function replicate(AuthUser $authUser, EksportHistory $eksportHistory): bool
    {
        return $authUser->can('Replicate:EksportHistory');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:EksportHistory');
    }

}
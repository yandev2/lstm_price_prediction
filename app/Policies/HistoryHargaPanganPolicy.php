<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\HistoryHargaPangan;
use Illuminate\Auth\Access\HandlesAuthorization;

class HistoryHargaPanganPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:HistoryHargaPangan');
    }

    public function view(AuthUser $authUser, HistoryHargaPangan $historyHargaPangan): bool
    {
        return $authUser->can('View:HistoryHargaPangan');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:HistoryHargaPangan');
    }

    public function update(AuthUser $authUser, HistoryHargaPangan $historyHargaPangan): bool
    {
        return $authUser->can('Update:HistoryHargaPangan');
    }

    public function delete(AuthUser $authUser, HistoryHargaPangan $historyHargaPangan): bool
    {
        return $authUser->can('Delete:HistoryHargaPangan');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:HistoryHargaPangan');
    }

    public function restore(AuthUser $authUser, HistoryHargaPangan $historyHargaPangan): bool
    {
        return $authUser->can('Restore:HistoryHargaPangan');
    }

    public function forceDelete(AuthUser $authUser, HistoryHargaPangan $historyHargaPangan): bool
    {
        return $authUser->can('ForceDelete:HistoryHargaPangan');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:HistoryHargaPangan');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:HistoryHargaPangan');
    }

    public function replicate(AuthUser $authUser, HistoryHargaPangan $historyHargaPangan): bool
    {
        return $authUser->can('Replicate:HistoryHargaPangan');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:HistoryHargaPangan');
    }

}
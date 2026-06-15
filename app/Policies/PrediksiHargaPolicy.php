<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PrediksiHarga;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrediksiHargaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PrediksiHarga');
    }

    public function view(AuthUser $authUser, PrediksiHarga $prediksiHarga): bool
    {
        return $authUser->can('View:PrediksiHarga');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PrediksiHarga');
    }

    public function update(AuthUser $authUser, PrediksiHarga $prediksiHarga): bool
    {
        return $authUser->can('Update:PrediksiHarga');
    }

    public function delete(AuthUser $authUser, PrediksiHarga $prediksiHarga): bool
    {
        return $authUser->can('Delete:PrediksiHarga');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PrediksiHarga');
    }

    public function restore(AuthUser $authUser, PrediksiHarga $prediksiHarga): bool
    {
        return $authUser->can('Restore:PrediksiHarga');
    }

    public function forceDelete(AuthUser $authUser, PrediksiHarga $prediksiHarga): bool
    {
        return $authUser->can('ForceDelete:PrediksiHarga');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PrediksiHarga');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PrediksiHarga');
    }

    public function replicate(AuthUser $authUser, PrediksiHarga $prediksiHarga): bool
    {
        return $authUser->can('Replicate:PrediksiHarga');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PrediksiHarga');
    }

}
<?php

namespace App\Policies;

use App\Models\ItemKit;
use App\Models\User;

class ItemKitPolicy
{
    protected array $canManage = ['admin', 'logistica', 'almacen'];
    protected array $canView = ['admin', 'logistica', 'almacen', 'auditoria'];

    public function viewAny(?User $user): bool
    {
        return $user?->hasAnyRole($this->canView) ?? false;
    }

    public function view(?User $user, ItemKit $itemKit): bool
    {
        return $this->viewAny($user);
    }

    public function create(?User $user): bool
    {
        return $user?->hasAnyRole($this->canManage) ?? false;
    }

    public function update(?User $user, ItemKit $itemKit): bool
    {
        return $this->create($user);
    }

    public function delete(?User $user, ItemKit $itemKit): bool
    {
        return $this->create($user);
    }

    public function restore(?User $user, ItemKit $itemKit): bool
    {
        return $this->create($user);
    }

    public function forceDelete(?User $user, ItemKit $itemKit): bool
    {
        return $this->create($user);
    }
}

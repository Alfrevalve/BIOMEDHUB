<?php

namespace App\Policies;

use App\Models\Item;
use App\Models\User;

class ItemPolicy
{
    protected array $canManage = ['admin', 'logistica', 'almacen'];
    protected array $canView = ['admin', 'logistica', 'almacen', 'auditoria'];

    public function viewAny(?User $user): bool
    {
        return $user?->hasAnyRole($this->canView) ?? false;
    }

    public function view(?User $user, Item $item): bool
    {
        return $this->viewAny($user);
    }

    public function create(?User $user): bool
    {
        return $user?->hasAnyRole($this->canManage) ?? false;
    }

    public function update(?User $user, Item $item): bool
    {
        return $this->create($user);
    }

    public function delete(?User $user, Item $item): bool
    {
        return $this->create($user);
    }

    public function restore(?User $user, Item $item): bool
    {
        return $this->create($user);
    }

    public function forceDelete(?User $user, Item $item): bool
    {
        return $this->create($user);
    }
}

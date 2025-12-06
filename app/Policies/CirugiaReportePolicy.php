<?php

namespace App\Policies;

use App\Models\CirugiaReporte;
use App\Models\User;

class CirugiaReportePolicy
{
    protected array $canView = ['admin', 'logistica', 'auditoria', 'soporte_biomedico', 'almacen', 'facturacion', 'comercial'];

    public function viewAny(?User $user): bool
    {
        return $user?->hasAnyRole($this->canView) ?? false;
    }

    public function view(?User $user, CirugiaReporte $reporte): bool
    {
        return $this->viewAny($user);
    }

    public function create(?User $user): bool
    {
        return false;
    }

    public function update(?User $user, CirugiaReporte $reporte): bool
    {
        return false;
    }

    public function delete(?User $user, CirugiaReporte $reporte): bool
    {
        return false;
    }

    public function restore(?User $user, CirugiaReporte $reporte): bool
    {
        return false;
    }

    public function forceDelete(?User $user, CirugiaReporte $reporte): bool
    {
        return false;
    }
}

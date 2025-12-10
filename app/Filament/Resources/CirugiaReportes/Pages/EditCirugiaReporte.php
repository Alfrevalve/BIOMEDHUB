<?php

namespace App\Filament\Resources\CirugiaReportes\Pages;

use App\Filament\Resources\CirugiaReportes\CirugiaReporteResource;
use Filament\Resources\Pages\EditRecord;

class EditCirugiaReporte extends EditRecord
{
    protected static string $resource = CirugiaReporteResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

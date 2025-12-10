<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\Calendario;
use App\Filament\Resources\Cirugias\CirugiaResource;
use App\Filament\Resources\CirugiaReportes\CirugiaReporteResource;
use Filament\Widgets\Widget;

class CirugiaQuickActions extends Widget
{
    protected string $view = 'filament.widgets.cirugia-quick-actions';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = -50;

    public static function canView(): bool
    {
        return CirugiaResource::canCreate();
    }

    protected function getViewData(): array
    {
        return [
            'createUrl' => CirugiaResource::getUrl('create'),
            'indexUrl' => Calendario::getUrl(),
            'reportesUrl' => CirugiaReporteResource::getUrl(),
        ];
    }
}

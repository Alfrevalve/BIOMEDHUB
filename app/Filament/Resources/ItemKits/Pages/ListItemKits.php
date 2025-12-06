<?php

namespace App\Filament\Resources\ItemKits\Pages;

use App\Filament\Resources\ItemKits\ItemKitResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListItemKits extends ListRecords
{
    protected static string $resource = ItemKitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

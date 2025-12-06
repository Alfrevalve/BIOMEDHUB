<?php

namespace App\Filament\Resources\Cirugias\Pages;

use App\Filament\Resources\Cirugias\CirugiaResource;
use Filament\Facades\Filament;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListCirugias extends ListRecords
{
    protected static string $resource = CirugiaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();
        $user = Filament::auth()->user();

        if ($user && $user->hasRole('instrumentista')) {
            return $query->where('instrumentista_id', $user->id);
        }

        return $query;
    }
}

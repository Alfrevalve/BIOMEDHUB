<?php

namespace App\Filament\Resources\Items\Pages;

use App\Filament\Resources\Items\ItemResource;
use App\Models\Item;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListItems extends ListRecords
{
    protected static string $resource = ItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('importar_stock')
                ->label('Importar stock CSV')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('primary')
                ->form([
                    FileUpload::make('archivo')
                        ->label('Archivo CSV')
                        ->acceptedFileTypes(['text/csv', 'text/plain', '.csv'])
                        ->required()
                        ->storeFiles(false),
                ])
                ->action(function (array $data) {
                    /** @var \Illuminate\Http\UploadedFile|string|null $file */
                    $file = $data['archivo'] ?? null;
                    if (! $file) {
                        Notification::make()->title('No se recibio archivo')->danger()->send();
                        return;
                    }

                    $path = method_exists($file, 'getRealPath') ? $file->getRealPath() : (string) $file;
                    $lines = @file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                    if (! $lines) {
                        Notification::make()->title('No se pudo leer el archivo o esta vacio')->danger()->send();
                        return;
                    }

                    // Normaliza cadenas: trimming, remueve control chars y convierte a UTF-8.
                    $normalize = static function ($value) {
                        if ($value === null) {
                            return null;
                        }

                        $encoded = mb_convert_encoding((string) $value, 'UTF-8', 'UTF-8, ISO-8859-1, Windows-1252');
                        $encoded = trim($encoded, " \t\n\r\0\x0B\"'");

                        return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $encoded) ?? '';
                    };

                    // Encabezado
                    $headerLine = ltrim($lines[0], "\xEF\xBB\xBF");
                    $semiCount = substr_count($headerLine, ';');
                    $commaCount = substr_count($headerLine, ',');
                    $delimiter = $semiCount >= $commaCount ? ';' : ',';
                    $header = array_map(
                        fn ($v) => strtolower($normalize($v)),
                        explode($delimiter, $headerLine)
                    );
                    $map = [];
                    foreach ($header as $index => $col) {
                        if ($col === 'descrip') {
                            $col = 'descripcion';
                        }
                        $map[$col] = $index;
                    }

                    $required = ['sku', 'nombre', 'stock_total'];
                    foreach ($required as $col) {
                        if (! array_key_exists($col, $map)) {
                            Notification::make()
                                ->title("Falta la columna requerida: {$col}. Encabezados: " . implode(', ', array_keys($map)))
                                ->danger()
                                ->send();
                            return;
                        }
                    }

                    $buffer = [];
                    $count = 0;
                    foreach (array_slice($lines, 1) as $line) {
                        $line = trim($line);
                        if ($line === '') {
                            continue;
                        }

                        $row = array_map($normalize, explode($delimiter, $line));

                        $sku = $normalize($row[$map['sku']] ?? null);
                        $nombre = $normalize($row[$map['nombre']] ?? null);
                        if (empty($sku) || empty($nombre)) {
                            continue;
                        }

                        $tipo = array_key_exists('tipo', $map) ? $normalize($row[$map['tipo']] ?? null) : null;
                        $stockTotal = isset($map['stock_total']) ? (float) ($row[$map['stock_total']] ?? 0) : 0;
                        $stockReservado = isset($map['stock_reservado']) ? (float) ($row[$map['stock_reservado']] ?? 0) : 0;
                        $descripcion = array_key_exists('descripcion', $map) ? $normalize($row[$map['descripcion']] ?? null) : null;

                        if (! isset($buffer[$sku])) {
                            $buffer[$sku] = [
                                'nombre' => $nombre,
                                'tipo' => $tipo,
                                'stock_total' => 0,
                                'stock_reservado' => 0,
                                'descripcion' => $descripcion,
                            ];
                        }
                        $buffer[$sku]['stock_total'] += $stockTotal;
                        $buffer[$sku]['stock_reservado'] += $stockReservado;
                        if (empty($buffer[$sku]['descripcion']) && ! empty($descripcion)) {
                            $buffer[$sku]['descripcion'] = $descripcion;
                        }
                    }

                    if (empty($buffer)) {
                        Notification::make()
                            ->title('No se importo ningun registro (verifica delimitador y encabezados)')
                            ->danger()
                            ->send();
                        return;
                    }

                    foreach ($buffer as $sku => $data) {
                        Item::updateOrCreate(
                            ['sku' => $sku],
                            [
                                'nombre' => $data['nombre'],
                                'tipo' => $data['tipo'],
                                'stock_total' => (int) $data['stock_total'],
                                'stock_reservado' => (int) $data['stock_reservado'],
                                'descripcion' => $data['descripcion'],
                            ]
                        );
                        $count++;
                    }

                    Notification::make()
                        ->title("{$count} items importados/actualizados")
                        ->success()
                        ->send();
                }),
        ];
    }
}

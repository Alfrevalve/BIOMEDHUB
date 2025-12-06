<?php

namespace App\Filament\Resources\Institucions\Pages;

use App\Filament\Resources\Institucions\InstitucionResource;
use App\Models\Institucion;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Carbon;

class ListInstitucions extends ListRecords
{
    protected static string $resource = InstitucionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            \Filament\Actions\Action::make('importar')
                ->label('Importar CSV')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('primary')
                ->form([
                    FileUpload::make('archivo')
                        ->label('Archivo CSV')
                        ->acceptedFileTypes(['text/csv', 'text/plain', '.csv'])
                        ->storeFiles(false)
                        ->required(),
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

                    $normalize = static function ($value) {
                        if ($value === null) {
                            return null;
                        }

                        $encoded = mb_convert_encoding((string) $value, 'UTF-8', 'UTF-8, ISO-8859-1, Windows-1252');
                        $encoded = trim($encoded, " \t\n\r\0\x0B\"'");

                        return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $encoded) ?? '';
                    };

                    $headerLine = ltrim($lines[0], "\xEF\xBB\xBF");
                    $semiCount = substr_count($headerLine, ';');
                    $commaCount = substr_count($headerLine, ',');
                    $tabCount = substr_count($headerLine, "\t");
                    $delimiter = ';';
                    if ($commaCount >= $semiCount && $commaCount >= $tabCount) {
                        $delimiter = ',';
                    }
                    if ($tabCount > $commaCount && $tabCount > $semiCount) {
                        $delimiter = "\t";
                    }
                    $normalizeHeader = static function ($value) use ($normalize) {
                        $val = $normalize($value);
                        $val = iconv('UTF-8', 'ASCII//TRANSLIT', $val);
                        $val = strtolower($val);
                        $val = preg_replace('/[^a-z0-9]+/', '_', $val);
                        $val = preg_replace('/_+/', '_', $val);
                        $val = trim($val, '_');
                        return $val;
                    };
                    $rawHeader = array_map(fn ($v) => $normalizeHeader($v), explode($delimiter, $headerLine));

                    $aliases = [
                        'institucion' => 'nombre',
                        'instituci_on' => 'nombre',
                        'nombre' => 'nombre',
                        'codigo_unico' => 'codigo_unico',
                        'c_odigo_unico' => 'codigo_unico',
                        'nombre_del_establecimiento' => 'nombre_establecimiento',
                        'clasificacion' => 'clasificacion',
                        'clasificaci_on' => 'clasificacion',
                        'tipo' => 'tipo',
                        'departamento' => 'departamento',
                        'provincia' => 'provincia',
                        'distrito' => 'distrito',
                        'ubigeo' => 'ubigeo',
                        'direccion' => 'direccion',
                        'direcci_on' => 'direccion',
                        'codigo_disa' => 'codigo_disa',
                        'c_odigo_disa' => 'codigo_disa',
                        'codigo_red' => 'codigo_red',
                        'c_odigo_red' => 'codigo_red',
                        'codigo_microrred' => 'codigo_microrred',
                        'c_odigo_microrred' => 'codigo_microrred',
                        'disa' => 'disa',
                        'red' => 'red',
                        'microrred' => 'microrred',
                        'codigo_ue' => 'codigo_ue',
                        'c_odigo_ue' => 'codigo_ue',
                        'unidad_ejecutora' => 'unidad_ejecutora',
                        'categoria' => 'categoria',
                        'telefono' => 'telefono',
                        'tel_efono' => 'telefono',
                        'tipo_doc_categorizacion' => 'tipo_doc_categorizacion',
                        'tipo_doc_categorizaci_on' => 'tipo_doc_categorizacion',
                        'nro_doc_categorizacion' => 'nro_doc_categorizacion',
                        'nro_doc_categorizaci_on' => 'nro_doc_categorizacion',
                        'horario' => 'horario',
                        'inicio_de_actividad' => 'inicio_actividad',
                        'inicio_actividad' => 'inicio_actividad',
                        'director_medico_y_o_responsable_de_la_atencion_de_salud' => 'director_medico',
                        'director_m_edico_y_o_responsable_de_la_atenci_on_de_salud' => 'director_medico',
                        'director_medico' => 'director_medico',
                        'estado' => 'estado_institucion',
                        'situacion' => 'situacion',
                        'condicion' => 'condicion',
                        'inspeccion' => 'inspeccion',
                        'norte' => 'norte',
                        'este' => 'este',
                        'cota' => 'cota',
                        'camas' => 'camas',
                    ];

                    $map = [];
                    foreach ($rawHeader as $index => $col) {
                        if (array_key_exists($col, $aliases)) {
                            $map[$aliases[$col]] = $index;
                        }
                    }

                    if (! array_key_exists('nombre', $map)) {
                        Notification::make()
                            ->title('Falta la columna requerida: nombre')
                            ->body('Encabezados detectados: ' . implode(', ', $rawHeader))
                            ->danger()
                            ->send();
                        return;
                    }

                    $count = 0;
                    $normalizeTipo = static function ($raw) {
                        $val = strtolower((string) $raw);
                        if (in_array($val, ['publica', 'pública', 'publico', 'público'], true)) {
                            return 'Publica';
                        }
                        if (str_contains($val, 'priv')) {
                            return 'Privada';
                        }
                        if (str_contains($val, 'milit') || str_contains($val, 'ffaa')) {
                            return 'Militar';
                        }
                        if (str_contains($val, 'ong')) {
                            return 'ONG';
                        }

                        return 'Publica';
                    };

                    $parseDate = static function ($raw) {
                        if (empty($raw)) {
                            return null;
                        }
                        $candidates = [
                            'Y-m-d',
                            'd/m/Y',
                            'd-m-Y',
                            'm/d/Y',
                        ];

                        foreach ($candidates as $format) {
                            try {
                                $dt = Carbon::createFromFormat($format, $raw);
                                if ($dt !== false) {
                                    return $dt->toDateString();
                                }
                            } catch (\Exception) {
                                // ignore and try next
                            }
                        }

                        return null;
                    };

                    $truncate = static function ($value, int $length) {
                        if ($value === null) {
                            return null;
                        }
                        return mb_substr((string) $value, 0, $length);
                    };

                    foreach (array_slice($lines, 1) as $line) {
                        $line = trim($line);
                        if ($line === '') {
                            continue;
                        }
                        $row = array_map($normalize, explode($delimiter, $line));

                        $get = static function (string $key) use ($map, $row) {
                            if (! isset($map[$key])) {
                                return null;
                            }
                            return $row[$map[$key]] ?? null;
                        };

                        $nombre = $get('nombre');
                        $nombreEst = $get('nombre_establecimiento');
                        $nombreClave = $nombreEst ?: $nombre;
                        if (! $nombreClave) {
                            continue;
                        }

                        $rawInicio = $get('inicio_actividad');
                        $inicioActividad = $parseDate($rawInicio);
                        if (! $inicioActividad && is_string($rawInicio) && str_contains($rawInicio, ':')) {
                            // Si viene un rango horario por error en esta columna, lo guardamos como horario y no intentamos setear fecha.
                            $payloadHorario = $get('horario');
                            $payloadHorario = $payloadHorario ?: $rawInicio;
                            $horario = $payloadHorario;
                            $inicioActividad = null;
                        } else {
                            $horario = $get('horario');
                        }

                        $payload = [
                            'nombre' => $nombreClave,
                            'codigo_unico' => $get('codigo_unico'),
                            'nombre_establecimiento' => $get('nombre_establecimiento'),
                            'clasificacion' => $get('clasificacion'),
                            'tipo' => $normalizeTipo($get('tipo')),
                            'departamento' => $get('departamento'),
                            'provincia' => $get('provincia'),
                            'distrito' => $get('distrito'),
                            'ubigeo' => $truncate($get('ubigeo'), 10),
                            'direccion' => $get('direccion'),
                            'ciudad' => $get('departamento') ?? $get('ciudad'),
                            'contacto' => $get('contacto'),
                            'codigo_disa' => $get('codigo_disa'),
                            'codigo_red' => $get('codigo_red'),
                            'codigo_microrred' => $get('codigo_microrred'),
                            'disa' => $get('disa'),
                            'red' => $get('red'),
                            'microrred' => $get('microrred'),
                            'codigo_ue' => $get('codigo_ue'),
                            'unidad_ejecutora' => $get('unidad_ejecutora'),
                            'categoria' => $get('categoria'),
                            'telefono' => $get('telefono'),
                            'tipo_doc_categorizacion' => $get('tipo_doc_categorizacion'),
                            'nro_doc_categorizacion' => $get('nro_doc_categorizacion'),
                            'horario' => $horario,
                            'inicio_actividad' => $inicioActividad,
                            'director_medico' => $get('director_medico'),
                            'estado_institucion' => $get('estado_institucion'),
                            'situacion' => $get('situacion'),
                            'condicion' => $get('condicion'),
                            'inspeccion' => $get('inspeccion'),
                            'norte' => ($val = $get('norte')) !== null && is_numeric($val) ? (float) $val : null,
                            'este' => ($val = $get('este')) !== null && is_numeric($val) ? (float) $val : null,
                            'cota' => ($val = $get('cota')) !== null && is_numeric($val) ? (float) $val : null,
                            'camas' => ($val = $get('camas')) !== null && is_numeric($val) ? (int) $val : null,
                        ];

                        Institucion::updateOrCreate(['nombre' => $nombreClave], $payload);
                        $count++;
                    }

                    Notification::make()
                        ->title("{$count} instituciones importadas/actualizadas")
                        ->success()
                        ->send();
                }),
        ];
    }
}

<?php

namespace Tests\Feature;

use App\Console\Commands\EnviarDigestOperativo;
use App\Mail\DigestOperativoMailable;
use App\Models\Cirugia;
use App\Models\Equipo;
use App\Models\Institucion;
use App\Models\Movimiento;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OperationalFlowsTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_pedido_automaticamente_al_crear_cirugia(): void
    {
        $cirugia = Cirugia::factory()->create(['crear_pedido_auto' => true]);

        $this->assertDatabaseCount('pedidos', 1);
        $this->assertNotNull($cirugia->fresh()->pedidos()->first());
    }

    public function test_comando_digest_encola_mailable(): void
    {
        Mail::fake();

        $this->artisan('digest:operativo', ['--to' => 'ops@example.com'])
            ->assertExitCode(EnviarDigestOperativo::SUCCESS);

        Mail::assertQueued(DigestOperativoMailable::class, function (DigestOperativoMailable $mail) {
            return str_contains($mail->subjectLine, 'Digest diario BIOMED HUB');
        });
    }

    public function test_movimiento_autogenera_nombre_y_actualiza_equipo(): void
    {
        $equipo = Equipo::factory()->create();
        $institucion = Institucion::factory()->create();

        $movimiento = Movimiento::create([
            'equipo_id' => $equipo->id,
            'institucion_id' => $institucion->id,
            'cirugia_id' => null,
            'nombre' => null,
            'fecha_salida' => now(),
            'estado_mov' => 'Programado',
            'motivo' => 'Cirugia',
            'servicio' => 'Neuro',
        ]);

        $movimiento->refresh();
        $equipo->refresh();

        $this->assertStringContainsString($equipo->nombre, $movimiento->nombre);
        $this->assertStringContainsString($institucion->nombre, $movimiento->nombre);
        $this->assertEquals('Asignado', $equipo->estado_actual);
        $this->assertEquals($institucion->id, $equipo->institucion_id);

        $movimiento->update(['estado_mov' => 'Devuelto']);
        $movimiento->refresh();
        $equipo->refresh();

        $this->assertEquals('Disponible', $equipo->estado_actual);
        $this->assertNull($equipo->institucion_id);
        $this->assertNotNull($movimiento->fecha_retorno);
    }
}

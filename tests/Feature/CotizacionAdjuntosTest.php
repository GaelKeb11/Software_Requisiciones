<?php

namespace Tests\Feature;

use App\Models\Compras\Cotizacion;
use App\Models\Compras\DetalleCotizacion;
use App\Models\Recepcion\Requisicion;
use App\Models\Solicitud\DetalleRequisicion;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CotizacionAdjuntosTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Base de datos en memoria para pruebas aisladas.
        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        config()->set('app.key', 'base64:' . base64_encode(random_bytes(32)));

        Schema::dropAllTables();
        DB::statement('PRAGMA foreign_keys = ON;');

        Schema::create('users', function (Blueprint $table) {
            $table->id('id_usuario');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->timestamps();
        });

        Schema::create('requisiciones', function (Blueprint $table) {
            $table->id('id_requisicion');
            $table->string('folio');
            $table->date('fecha_creacion')->nullable();
            $table->date('fecha_recepcion')->nullable();
            $table->timestamp('hora_recepcion')->nullable();
            $table->text('concepto')->nullable();
            $table->unsignedBigInteger('id_departamento')->nullable();
            $table->unsignedBigInteger('id_clasificacion')->nullable();
            $table->unsignedBigInteger('id_usuario')->nullable();
            $table->unsignedBigInteger('id_estatus')->nullable();
            $table->timestamps();
        });

        Schema::create('detalle_requisicions', function (Blueprint $table) {
            $table->id('id_detalle_requisicion');
            $table->unsignedBigInteger('id_requisicion');
            $table->integer('cantidad');
            $table->string('unidad_medida');
            $table->text('descripcion');
            $table->decimal('total', 10, 2)->nullable()->default(0);
            $table->timestamps();
        });

        Schema::create('cotizaciones', function (Blueprint $table) {
            $table->id('id_cotizacion');
            $table->unsignedBigInteger('id_requisicion');
            $table->string('nombre_proveedor')->nullable();
            $table->date('fecha_cotizacion')->nullable();
            $table->decimal('total_cotizado', 10, 2)->default(0);
            $table->unsignedBigInteger('id_usuario_gestor')->nullable();
            $table->timestamps();
        });

        Schema::create('detalle_cotizacion', function (Blueprint $table) {
            $table->id('id_detalle_cotizacion');
            $table->unsignedBigInteger('id_cotizacion');
            $table->unsignedBigInteger('id_detalle_requisicion')->nullable();
            $table->decimal('cantidad_cotizada', 10, 2);
            $table->string('unidad_medida');
            $table->text('descripcion');
            $table->decimal('precio_unitario', 10, 2)->default(0);
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('cotizacion_adjuntos', function (Blueprint $table) {
            $table->id('id_adjunto');
            $table->unsignedBigInteger('id_cotizacion');
            $table->string('nombre_archivo')->nullable();
            $table->string('ruta_archivo');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->text('comentarios')->nullable();
            $table->timestamps();

            $table->foreign('id_cotizacion')
                ->references('id_cotizacion')
                ->on('cotizaciones')
                ->onDelete('cascade');
        });
    }

    public function test_cotizacion_guarda_adjuntos_y_relaciones()
    {
        $cotizacion = $this->crearCotizacionBase();

        $adjunto = $cotizacion->adjuntos()->create([
            'ruta_archivo' => 'cotizaciones/test.pdf',
            'nombre_archivo' => 'test.pdf',
            'mime_type' => 'application/pdf',
            'size' => 1234,
            'comentarios' => 'Cotización del proveedor A',
        ]);

        $this->assertDatabaseHas('cotizacion_adjuntos', [
            'id_adjunto' => $adjunto->id_adjunto,
            'ruta_archivo' => 'cotizaciones/test.pdf',
        ]);

        $this->assertCount(1, $cotizacion->fresh()->adjuntos);
        $this->assertSame('test.pdf', $cotizacion->fresh()->adjuntos->first()->nombre_archivo);
    }

    public function test_adjuntos_se_eliminan_al_borrar_cotizacion()
    {
        $cotizacion = $this->crearCotizacionBase();

        $adjunto = $cotizacion->adjuntos()->create([
            'ruta_archivo' => 'cotizaciones/a-borrar.pdf',
            'nombre_archivo' => 'a-borrar.pdf',
        ]);

        $cotizacion->delete();

        $this->assertDatabaseMissing('cotizacion_adjuntos', [
            'id_adjunto' => $adjunto->id_adjunto,
        ]);
    }

    private function crearCotizacionBase(): Cotizacion
    {
        $userId = DB::table('users')->insertGetId([
            'name' => 'Gestor',
            'email' => 'gestor@example.com',
            'password' => bcrypt('secret'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $requisicion = Requisicion::withoutEvents(function () use ($userId) {
            return Requisicion::create([
                'folio' => 'REQ-1001',
                'fecha_creacion' => now(),
                'fecha_recepcion' => now(),
                'hora_recepcion' => now(),
                'concepto' => 'Compra de materiales',
                'id_departamento' => 1,
                'id_clasificacion' => 1,
                'id_usuario' => $userId,
                'id_estatus' => 3,
            ]);
        });

        $detalleReq = DetalleRequisicion::create([
            'id_requisicion' => $requisicion->id_requisicion,
            'cantidad' => 2,
            'unidad_medida' => 'PZA',
            'descripcion' => 'Artículo de prueba',
            'total' => 0,
        ]);

        $cotizacion = Cotizacion::create([
            'id_requisicion' => $requisicion->id_requisicion,
            'nombre_proveedor' => 'Proveedor Demo',
            'fecha_cotizacion' => now(),
            'total_cotizado' => 0,
            'id_usuario_gestor' => $userId,
        ]);

        DetalleCotizacion::create([
            'id_cotizacion' => $cotizacion->id_cotizacion,
            'id_detalle_requisicion' => $detalleReq->id_detalle_requisicion,
            'cantidad_cotizada' => $detalleReq->cantidad,
            'unidad_medida' => $detalleReq->unidad_medida,
            'descripcion' => $detalleReq->descripcion,
            'precio_unitario' => 0,
            'subtotal' => 0,
        ]);

        return $cotizacion;
    }
}

<?php

namespace Tests\Feature;

use App\Filament\Resources\Solicitudes\Schemas\FormularioSolicitud;
use App\Models\Recepcion\Clasificacion;
use App\Models\Recepcion\Departamento;
use App\Models\Recepcion\Estatus;
use App\Models\Recepcion\Requisicion;
use App\Models\Solicitud\DetalleRequisicion;
use App\Models\Usuarios\Usuario;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SolicitudClasificacionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->crearTablasMinimas();
    }

    protected function tearDown(): void
    {
        $this->borrarTablas();
        parent::tearDown();
    }

    public function test_clasificaciones_generales_se_limitan_a_2000_3000_5000(): void
    {
        Carbon::setTestNow('2026-01-05');

        $c2000 = $this->crearClasificacion(2000, '2000 Materiales y Suministros');
        $c3000 = $this->crearClasificacion(3000, '3000 Servicios Generales');
        $c5000 = $this->crearClasificacion(5000, '5000 Bienes Duraderos');
        $otro = $this->crearClasificacion(1500, '1500 Otros');

        $options = FormularioSolicitud::getGeneralClasificacionesOptions();

        $this->assertArrayHasKey($c2000->id_clasificacion, $options);
        $this->assertArrayHasKey($c3000->id_clasificacion, $options);
        $this->assertArrayHasKey($c5000->id_clasificacion, $options);
        $this->assertArrayNotHasKey($otro->id_clasificacion, $options);
    }

    public function test_clasificaciones_especificas_se_filtran_por_millar(): void
    {
        Carbon::setTestNow('2026-01-05');

        $general = $this->crearClasificacion(2000, '2000 Materiales y Suministros');
        $detalle2100 = $this->crearClasificacion(2100, '2100 Papelería');
        $detalle2111 = $this->crearClasificacion(2111, '2111 Limpieza');
        $fueraDeRango = $this->crearClasificacion(3200, '3200 Servicios Profesionales');

        $options = FormularioSolicitud::getClasificacionesEspecificasPorGeneral($general->id_clasificacion);

        $this->assertArrayHasKey($detalle2100->id_clasificacion, $options);
        $this->assertArrayHasKey($detalle2111->id_clasificacion, $options);
        $this->assertArrayNotHasKey($fueraDeRango->id_clasificacion, $options);
    }

    public function test_no_permite_clasificacion_2161_en_detalle_despues_del_dia_8(): void
    {
        Carbon::setTestNow('2026-01-10');

        $general = $this->crearClasificacion(2000, '2000 Materiales y Suministros');
        $clasificacion2161 = $this->crearClasificacion(2161, '2161 Material de limpieza');

        $requisicion = $this->crearRequisicionConGeneral($general->id_clasificacion);

        $this->expectException(ValidationException::class);

        DetalleRequisicion::create([
            'id_requisicion' => $requisicion->id_requisicion,
            'id_clasificacion_detalle' => $clasificacion2161->id_clasificacion,
            'cantidad' => 1,
            'unidad_medida' => 'Pieza',
            'descripcion' => 'Escoba',
            'total' => 100,
        ]);
    }

    private function crearClasificacion(int $id, string $nombre): Clasificacion
    {
        DB::table('clasificaciones')->insert([
            'id_clasificacion' => $id,
            'nombre' => $nombre,
            'descripcion' => $nombre,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return Clasificacion::find($id);
    }

    private function crearRequisicionConGeneral(int $clasificacionId): Requisicion
    {
        $departamento = Departamento::create([
            'nombre' => 'Departamento Test',
            'responsable' => 'Responsable Test',
        ]);

        $estatus = Estatus::create([
            'nombre' => 'Recepcionada',
            'color' => '#000000',
        ]);

        $usuario = Usuario::create([
            'name' => 'Usuario Test',
            'apellido_paterno' => 'Test',
            'apellido_materno' => 'Apellido',
            'email' => 'usuario@test.com',
            'password' => 'secret',
            'id_departamento' => $departamento->id_departamento,
            'id_rol' => null,
        ]);

        $this->actingAs($usuario);

        return Requisicion::create([
            'folio' => 'REQ-TEST',
            'fecha_creacion' => now()->toDateString(),
            'fecha_recepcion' => now(),
            'hora_recepcion' => now(),
            'concepto' => 'Concepto de prueba',
            'id_departamento' => $departamento->id_departamento,
            'id_clasificacion' => $clasificacionId,
            'id_usuario' => $usuario->id_usuario,
            'id_estatus' => $estatus->id_estatus,
            'id_solicitante' => $usuario->id_usuario,
        ]);
    }

    private function crearTablasMinimas(): void
    {
        Schema::dropAllTables();

        Schema::create('clasificaciones', function (Blueprint $table) {
            $table->unsignedBigInteger('id_clasificacion')->primary();
            $table->string('nombre', 50);
            $table->text('descripcion')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('departamentos', function (Blueprint $table) {
            $table->id('id_departamento');
            $table->string('nombre', 100);
            $table->string('responsable', 100);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('estatus', function (Blueprint $table) {
            $table->id('id_estatus');
            $table->string('nombre');
            $table->string('color');
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id('id_usuario');
            $table->string('name');
            $table->string('apellido_paterno')->nullable();
            $table->string('apellido_materno')->nullable();
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->string('numero_telefonico')->nullable();
            $table->string('profile_photo_path')->nullable();
            $table->string('app_authentication_secret')->nullable();
            $table->rememberToken();
            $table->unsignedBigInteger('id_departamento')->nullable();
            $table->unsignedBigInteger('id_rol')->nullable();
            $table->timestamps();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id('id_rol');
            $table->string('nombre');
            $table->timestamps();
        });

        Schema::create('requisiciones', function (Blueprint $table) {
            $table->id('id_requisicion');
            $table->string('folio')->unique();
            $table->date('fecha_creacion');
            $table->timestamp('fecha_recepcion')->useCurrent();
            $table->timestamp('hora_recepcion')->useCurrent();
            $table->text('concepto');
            $table->foreignId('id_departamento')->constrained('departamentos', 'id_departamento');
            $table->foreignId('id_clasificacion')->constrained('clasificaciones', 'id_clasificacion');
            $table->foreignId('id_usuario')->constrained('users', 'id_usuario');
            $table->foreignId('id_estatus')->constrained('estatus', 'id_estatus');
            $table->unsignedBigInteger('id_solicitante')->nullable();
            $table->foreign('id_solicitante')->references('id_usuario')->on('users');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('detalle_requisicions', function (Blueprint $table) {
            $table->id('id_detalle_requisicion');
            $table->foreignId('id_requisicion')->constrained('requisiciones', 'id_requisicion')->cascadeOnDelete();
            $table->unsignedBigInteger('id_clasificacion_detalle')->nullable();
            $table->foreign('id_clasificacion_detalle')->references('id_clasificacion')->on('clasificaciones');
            $table->integer('cantidad');
            $table->string('unidad_medida');
            $table->text('descripcion');
            $table->decimal('total', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('activity_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('log_name')->nullable();
            $table->text('description')->nullable();
            $table->nullableMorphs('subject');
            $table->nullableMorphs('causer');
            $table->json('properties')->nullable();
            $table->string('batch_uuid')->nullable();
            $table->string('event')->nullable();
            $table->timestamps();
        });
    }

    private function borrarTablas(): void
    {
        Schema::dropAllTables();
    }
}

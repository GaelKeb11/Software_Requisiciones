<?php

namespace App\Filament\Resources\Compras\GestionCompras\Pages;

use App\Filament\Resources\Compras\GestionCompras\GestionComprasResource;
use App\Models\Compras\DetalleOrdenCompra;
use App\Models\Compras\OrdenCompra;
use App\Models\Recepcion\Documento;
use App\Models\Recepcion\Requisicion;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;

class GenerarOrdenCompra extends Page
{
    protected static string $resource = GestionComprasResource::class;

    protected string $view = 'filament.resources.compras.gestion-compras.pages.generar-orden-compra';

    protected static ?string $title = 'Generación de Orden de Compra';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
        
        // Check for query parameters
        $requisicionId = request()->query('requisicion_id');
        if ($requisicionId) {
             $this->form->fill(['requisicion_id' => $requisicionId]);
             // Manually trigger loading since afterStateUpdated won't fire on fill
             $this->loadRequisicionData($requisicionId, function ($key, $value) {
                 $this->data[$key] = $value; // Update data directly or use a setter if available in this context
                 // However, for initial fill in mount, we might need to use form->fill again or set data property
             });
             
             // Better approach for mount with form fill:
             // Since loadRequisicionData expects a $set callable, we can adapt it or just manually set data
             // But $this->form->fill() is already called. Let's refactor loadRequisicionData to work better or just call it.
             
             // Re-implementing simple set for mount:
             $set = function($name, $value) {
                 $this->data[$name] = $value;
             };
             $this->loadRequisicionData($requisicionId, $set);
             
             // Important: sync the form state with the data array we just updated
             $this->form->fill($this->data);
        }
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Selección de Requisición')
                    ->schema([
                        Select::make('requisicion_id')
                            ->label('Requisición Pendiente')
                            ->options(function () {
                                return Requisicion::where('id_estatus', 1)
                                    ->get()
                                    ->mapWithKeys(function ($req) {
                                        return [$req->id_requisicion => "{$req->folio} - {$req->concepto}"];
                                    });
                            })
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                $this->loadRequisicionData($state, $set);
                            })
                            ->required(),
                    ]),

                Section::make('Datos de la Requisición')
                    ->visible(fn ($get) => filled($get('requisicion_id')))
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Placeholder::make('folio_display')
                                    ->label('Folio')
                                    ->content(fn ($get) => $get('folio_display_value')),
                                Placeholder::make('fecha_creacion_display')
                                    ->label('Fecha de Creación')
                                    ->content(fn ($get) => $get('fecha_creacion_display_value')),
                                Placeholder::make('concepto_display')
                                    ->label('Concepto')
                                    ->content(fn ($get) => $get('concepto_display_value')),
                                Placeholder::make('solicitante_display')
                                    ->label('Solicitante')
                                    ->content(fn ($get) => $get('solicitante_display_value')),
                                Placeholder::make('departamento_display')
                                    ->label('Departamento')
                                    ->content(fn ($get) => $get('departamento_display_value')),
                            ]),
                        
                        ViewField::make('documento_pdf')
                            ->label('Documento PDF Adjunto')
                            ->view('filament.forms.components.view-field') // Dummy view, we use formatStateUsing or just Placeholder content
                            ->hidden(fn ($get) => blank($get('pdf_url')))
                            ->formatStateUsing(fn ($get) => $get('pdf_url'))
                            ->view('filament.forms.components.view') // Just to avoid errors if ViewField needs a view, but actually using Placeholder with HTML content is better.
                    ]),

                // Replacement for ViewField since we just want to show a link
                Section::make('Documento Adjunto')
                    ->visible(fn ($get) => filled($get('pdf_url')))
                    ->schema([
                        Placeholder::make('link_pdf')
                            ->label('Archivo PDF')
                            ->content(fn ($get) => new HtmlString('<a href="' . $get('pdf_url') . '" target="_blank" class="text-primary-600 underline">Ver Documento Adjunto</a>'))
                    ]),

                Section::make('Formulario de Orden de Compra')
                    ->visible(fn ($get) => filled($get('requisicion_id')))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('fecha_orden')
                                    ->label('Fecha de Orden')
                                    ->default(now())
                                    ->required(),
                                Placeholder::make('gestor_display')
                                    ->label('Gestor')
                                    ->content(Auth::user()?->name ?? 'Usuario Actual'),
                            ]),
                    ]),

                Section::make('Detalle de la OC (Ítems)')
                    ->visible(fn ($get) => filled($get('requisicion_id')))
                    ->schema([
                        Repeater::make('items')
                            ->label('Ítems de la Requisición')
                            ->schema([
                                Hidden::make('id_detalle_requisicion'),
                                Grid::make(4)
                                    ->schema([
                                        TextInput::make('descripcion')
                                            ->label('Descripción')
                                            ->disabled()
                                            ->dehydrated(false), // Don't send back if disabled, but we need it for display. Actually let's keep it disabled but fillable.
                                        TextInput::make('cantidad')
                                            ->label('Cantidad')
                                            ->numeric()
                                            ->disabled()
                                            ->dehydrated(false),
                                        TextInput::make('unidad_medida')
                                            ->label('Unidad')
                                            ->disabled()
                                            ->dehydrated(false),
                                        
                                    ]),
                                Grid::make(3)
                                    ->schema([
                                        TextInput::make('nombre_proveedor')
                                            ->label('Nombre del Proveedor')
                                            ->required()
                                            ->datalist(['Proveedor A', 'Proveedor B']), // Optional suggestions
                                        TextInput::make('precio_unitario')
                                            ->label('Precio Unitario')
                                            ->numeric()
                                            ->required()
                                            ->prefix('$')
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function ($state, $get, $set) {
                                                $cantidad = $get('cantidad_hidden'); // Retrieve hidden stored quantity
                                                $set('subtotal', round(floatval($state) * floatval($cantidad), 2));
                                            }),
                                        TextInput::make('subtotal')
                                            ->label('Subtotal')
                                            ->numeric()
                                            ->prefix('$')
                                            ->readOnly()
                                            ->dehydrated(true), // Ensure it's sent
                                        
                                        // Hidden fields to store original values for calculations
                                        Hidden::make('cantidad_hidden'),
                                    ]),
                            ])
                            ->addable(false) // Fixed items from requisition
                            ->deletable(false)
                            ->reorderable(false)
                            ->columns(1),
                    ]),
            ])
            ->statePath('data');
    }

    protected function loadRequisicionData($requisicionId, $set)
    {
        if (!$requisicionId) {
            return;
        }

        $req = Requisicion::with(['detalles', 'departamento', 'usuario', 'documentos'])->find($requisicionId);

        if (!$req) {
            return;
        }

        // Set Display Values
        $set('folio_display_value', $req->folio);
        $set('fecha_creacion_display_value', $req->fecha_creacion?->format('d/m/Y'));
        $set('concepto_display_value', $req->concepto);
        $set('solicitante_display_value', $req->usuario?->name);
        $set('departamento_display_value', $req->departamento?->nombre);

        // PDF
        $doc = $req->documentos()->where('tipo_documento', 'Requisición')->first();
        if ($doc && $doc->ruta_archivo) {
             // Assuming Storage access, generate URL
             // Note: You might need Storage::url($doc->ruta_archivo)
             $url = Storage::url($doc->ruta_archivo); 
             $set('pdf_url', $url);
        } else {
             $set('pdf_url', null);
        }

        // Items
        $items = $req->detalles->map(function ($d) {
            return [
                'id_detalle_requisicion' => $d->id_detalle_requisicion,
                'descripcion' => $d->descripcion,
                'cantidad' => $d->cantidad,
                'cantidad_hidden' => $d->cantidad,
                'unidad_medida' => $d->unidad_medida,
                'nombre_proveedor' => '',
                'precio_unitario' => 0,
                'subtotal' => 0,
            ];
        })->toArray();

        $set('items', $items);
    }

    public function save()
    {
        $data = $this->form->getState();
        
        // Logic to group by provider and create OCs
        $items = $data['items'];
        $requisicionId = $data['requisicion_id'];
        $fechaOrden = $data['fecha_orden'];
        $gestorId = Auth::id();

        $groupedItems = collect($items)->groupBy('nombre_proveedor');

        DB::beginTransaction();

        try {
            foreach ($groupedItems as $proveedor => $detalles) {
                // Create OC
                $totalCalculado = collect($detalles)->sum('subtotal');

                $ordenCompra = OrdenCompra::create([
                    'id_requisicion' => $requisicionId,
                    'nombre_proveedor' => $proveedor,
                    'fecha_orden' => $fechaOrden,
                    'total_calculado' => $totalCalculado,
                    'id_usuario_gestor' => $gestorId,
                ]);

                // Create Details
                foreach ($detalles as $detalle) {
                    DetalleOrdenCompra::create([
                        'id_orden_compra' => $ordenCompra->id_orden_compra,
                        'id_detalle_requisicion' => $detalle['id_detalle_requisicion'],
                        'precio_unitario' => $detalle['precio_unitario'],
                        'subtotal' => $detalle['subtotal'],
                    ]);
                }
            }

            // Update Requisicion Status
            $req = Requisicion::find($requisicionId);
            if ($req) {
                $req->id_estatus = 2; // En Revisión (or equivalent status for processed)
                $req->save();
            }

            DB::commit();

            Notification::make()
                ->title('Órdenes de Compra Generadas Exitosamente')
                ->success()
                ->send();

            $this->form->fill(); // Reset form
            $this->redirect(GestionComprasResource::getUrl('index')); // Redirect to index

        } catch (\Exception $e) {
            DB::rollBack();
            Notification::make()
                ->title('Error al generar órdenes')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}


<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class EncryptExistingData extends Command
{
    protected $signature = 'app:encrypt-existing-data';
    protected $description = 'Manually encrypts existing plaintext data in the database.';

    public function handle()
    {
        $this->info('Starting forceful data encryption process...');

        $modelsToEncrypt = [
            \App\Models\Compras\Cotizacion::class => ['nombre_proveedor', 'total_cotizado'],
            \App\Models\Compras\DetalleCotizacion::class => ['descripcion', 'precio_unitario', 'subtotal'],
            \App\Models\Compras\OrdenCompra::class => ['nombre_proveedor', 'total_calculado'],
            \App\Models\Compras\DetalleOrdenCompra::class => ['precio_unitario', 'subtotal'],
            \App\Models\Recepcion\Requisicion::class => ['concepto'],
            \App\Models\Solicitud\DetalleRequisicion::class => ['descripcion', 'total'],
            \App\Models\Usuarios\Usuario::class => ['numero_telefonico'],
        ];

        foreach ($modelsToEncrypt as $modelClass => $fields) {
            $this->line('');
            $this->info("Processing model: [{$modelClass}]");

            $instance = new $modelClass;
            $table = $instance->getTable();
            $primaryKey = $instance->getKeyName();
            $totalRecords = DB::table($table)->count();

            if ($totalRecords === 0) {
                $this->warn("No records found for {$modelClass}. Skipping.");
                continue;
            }

            $progressBar = $this->output->createProgressBar($totalRecords);
            $progressBar->start();

            DB::table($table)->orderBy($primaryKey)->chunk(100, function ($records) use ($fields, $table, $primaryKey, $progressBar) {
                foreach ($records as $record) {
                    $updates = [];
                    foreach ($fields as $field) {
                        $plainValue = $record->{$field};

                        if (is_null($plainValue) || $plainValue === '') {
                            continue;
                        }

                        try {
                            Crypt::decrypt($plainValue);
                            // If decryption succeeds, it's already encrypted. Do nothing.
                        } catch (DecryptException $e) {
                            // If decryption fails, it's plaintext. Encrypt it.
                            $updates[$field] = Crypt::encrypt($plainValue);
                        }
                    }

                    if (!empty($updates)) {
                        DB::table($table)->where($primaryKey, $record->{$primaryKey})->update($updates);
                    }
                    $progressBar->advance();
                }
            });

            $progressBar->finish();
            $this->line('');
        }

        $this->info('Forceful data encryption process completed!');
    }
}

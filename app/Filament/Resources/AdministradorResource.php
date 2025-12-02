<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdministradorResource\Pages;
use App\Models\Usuarios\Usuario;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Connection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\Process\Process;
use Throwable;
use UnitEnum;


class AdministradorResource extends Resource
{
    protected static ?string $model = Usuario::class; // Asegúrate de usar tu modelo de usuario
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;
    protected static ?string $navigationLabel = 'Administradores';
    protected static ?string $modelLabel = 'Administrador';
    protected static ?string $pluralModelLabel = 'Administradores';
    protected static string|UnitEnum|null $navigationGroup = 'Administración';	
    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        /** @var \App\Models\Usuarios\Usuario $user */
        $user = Auth::user();
        return $user->esAdministrador();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\AdminMenu::route('/'),
            'list' => Pages\ListAdministradors::route('/list'),
            'logs' => Pages\ActivityLog::route('/logs'),
            'estadisticas' => Pages\EstadisticasReportes::route('/estadisticas'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            // Agrega aquí relaciones si son necesarias
        ];
    }

    public static function generateDatabaseBackup(): string
    {
        $defaultConnectionName = config('database.default');
        $connection = config("database.connections.{$defaultConnectionName}");

        if (! is_array($connection)) {
            throw new RuntimeException('No se encontró la configuración de la base de datos.');
        }

        $directory = 'backups';
        Storage::disk('local')->makeDirectory($directory);
        $timestamp = now()->format('Y_m_d_His');
        $extension = $connection['driver'] === 'sqlite' ? 'sqlite' : 'sql';
        $fileName = sprintf('%s_backup_%s.%s', $connection['database'] ?? $defaultConnectionName, $timestamp, $extension);
        $relativePath = "{$directory}/{$fileName}";

        if ($connection['driver'] === 'sqlite') {
            static::copySqliteDatabase($connection, $relativePath);
            return $relativePath;
        }

        if ($connection['driver'] === 'mysql') {
            static::dumpMysqlDatabase($defaultConnectionName, $connection, $relativePath);
            return $relativePath;
        }

        throw new RuntimeException('El tipo de base de datos configurado no es soportado para exportación.');
    }

    protected static function copySqliteDatabase(array $connection, string $relativePath): void
    {
        $databaseFile = $connection['database'] ?? database_path('database.sqlite');

        if (! file_exists($databaseFile)) {
            throw new RuntimeException('No se encontró el archivo de la base de datos SQLite.');
        }

        Storage::disk('local')->put($relativePath, file_get_contents($databaseFile) ?: '');
    }

    protected static function dumpMysqlDatabase(string $connectionName, array $connection, string $relativePath): void
    {
        $binary = static::resolveMysqldumpBinary($connection);
        $arguments = [$binary];

        $socket = $connection['unix_socket'] ?? null;
        $host = $connection['host'] ?? '127.0.0.1';
        $port = $connection['port'] ?? null;
        $username = $connection['username'] ?? null;

        if (! empty($socket)) {
            if (PHP_OS_FAMILY === 'Windows') {
                $arguments[] = '--protocol=PIPE';
            }

            $arguments[] = "--socket={$socket}";
        } else {
            $arguments[] = "--host={$host}";

            if (! empty($port)) {
                $arguments[] = "--port={$port}";
            }
        }

        if (! empty($username)) {
            $arguments[] = "--user={$username}";
        }

        $arguments = array_merge(
            $arguments,
            [
                '--single-transaction',
                '--skip-lock-tables',
                '--routines',
                '--events',
            ],
            static::resolveExtraDumpOptions($connection)
        );

        $arguments[] = $connection['database'];

        $process = new Process($arguments);
        $process->setTimeout(300);
        $process->run(null, [
            'MYSQL_PWD' => (string) ($connection['password'] ?? ''),
        ]);

        if ($process->isSuccessful()) {
            Storage::disk('local')->put($relativePath, $process->getOutput());
            return;
        }

        $errorMessage = trim($process->getErrorOutput() ?: $process->getOutput());

        try {
            static::manualMysqlDump($connectionName, $connection, $relativePath);
        } catch (Throwable $fallbackException) {
            throw new RuntimeException(
                'Ocurrió un error al generar el respaldo y el método alternativo también falló. ' .
                'mysqldump: ' . $errorMessage . ' | Fallback: ' . $fallbackException->getMessage(),
                previous: $fallbackException
            );
        }
    }

    protected static function resolveMysqldumpBinary(array $connection): string
    {
        $binaryPath = Arr::get($connection, 'dump.dump_binary_path');

        if (! empty($binaryPath)) {
            $binaryPath = rtrim($binaryPath, '/\\');
            $binary = $binaryPath . DIRECTORY_SEPARATOR . 'mysqldump';

            if (PHP_OS_FAMILY === 'Windows' && ! str_ends_with(strtolower($binary), '.exe')) {
                $binary .= '.exe';
            }

            return $binary;
        }

        return 'mysqldump';
    }

    protected static function resolveExtraDumpOptions(array $connection): array
    {
        $options = [];

        $extraOptions = Arr::get($connection, 'dump.extra_options', []);

        if (is_string($extraOptions)) {
            $extraOptions = preg_split('/\s+/', trim($extraOptions)) ?: [];
        }

        foreach ((array) $extraOptions as $option) {
            if (! empty($option)) {
                $options[] = $option;
            }
        }

        $singleOption = Arr::get($connection, 'dump.add_extra_option');

        if (! empty($singleOption)) {
            $options[] = $singleOption;
        }

        return $options;
    }

    protected static function manualMysqlDump(string $connectionName, array $connection, string $relativePath): void
    {
        /** @var Connection $db */
        $db = DB::connection($connectionName);
        $pdo = $db->getPdo();
        
        $charset = $connection['charset'] ?? 'utf8mb4';
        $collation = $connection['collation'] ?? 'utf8mb4_unicode_ci';

        $disk = Storage::disk('local');
        $disk->put($relativePath, static::buildDumpHeader($connection['database'], $charset, $collation));

        $tables = $pdo->query('SHOW FULL TABLES WHERE Table_type = "BASE TABLE"')->fetchAll(\PDO::FETCH_COLUMN);

        foreach ($tables as $table) {
            static::appendTableStructure($pdo, $disk, $relativePath, $table);
            static::appendTableData($pdo, $disk, $relativePath, $table);
        }
    }

    protected static function buildDumpHeader(string $database, string $charset, string $collation): string
    {
        $timestamp = now()->toDateTimeString();

        return implode(PHP_EOL, [
            sprintf('-- Respaldo generado desde la plataforma el %s', $timestamp),
            sprintf('-- Base de datos: `%s`', $database),
            'SET FOREIGN_KEY_CHECKS=0;',
            'SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";',
            sprintf('SET NAMES %s COLLATE %s;', $charset, $collation),
            '',
        ]);
    }

    protected static function appendTableStructure(\PDO $pdo, $disk, string $relativePath, string $table): void
    {
        $disk->append($relativePath, PHP_EOL . sprintf('-- Tabla `%s`', $table));
        $disk->append($relativePath, sprintf('DROP TABLE IF EXISTS `%s`;', $table));

        $statement = $pdo->query(sprintf('SHOW CREATE TABLE `%s`', $table));
        $create = $statement->fetch(\PDO::FETCH_ASSOC)['Create Table'] ?? null;

        if (empty($create)) {
            throw new RuntimeException(sprintf('No se pudo obtener la definición de la tabla `%s`.', $table));
        }

        $disk->append($relativePath, $create . ';');
    }

    protected static function appendTableData(\PDO $pdo, $disk, string $relativePath, string $table): void
    {
        $statement = $pdo->query(sprintf('SELECT * FROM `%s`', $table));
        $hasRows = false;

        while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
            $hasRows = true;
            $columns = array_map(fn ($column) => sprintf('`%s`', $column), array_keys($row));
            $values = array_map([static::class, 'quoteValue'], array_values($row));

            $insert = sprintf(
                'INSERT INTO `%s` (%s) VALUES (%s);',
                $table,
                implode(', ', $columns),
                implode(', ', $values)
            );

            $disk->append($relativePath, $insert);
        }

        if (! $hasRows) {
            $disk->append($relativePath, sprintf('-- La tabla `%s` no contiene registros.', $table));
        }
    }

    protected static function quoteValue(mixed $value): string
    {
        if ($value === null) {
            return 'NULL';
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_numeric($value) && ! Str::startsWith((string) $value, '0')) {
            return (string) $value;
        }

        $escaped = str_replace(['\\', "\0", "\n", "\r", "'", '"', "\x1a"], ['\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'], (string) $value);

        return "'" . $escaped . "'";
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class BackupDatabase extends Command
{
    protected $signature = 'clinic:backup';

    protected $description = 'Sauvegarde la base de données (PostgreSQL ou SQLite)';

    public function handle(): int
    {
        $connection = config('database.default');
        $dir = storage_path('app/backups');
        File::ensureDirectoryExists($dir);

        $timestamp = now()->format('Y-m-d_His');

        if ($connection === 'pgsql') {
            $config = config('database.connections.pgsql');
            $file = "{$dir}/clinic-{$timestamp}.sql";
            $process = new Process([
                'pg_dump',
                '-h', $config['host'],
                '-p', (string) $config['port'],
                '-U', $config['username'],
                '-d', $config['database'],
                '-f', $file,
            ]);
            $process->setEnv(array_merge($_ENV, ['PGPASSWORD' => $config['password'] ?? '']));
            $process->run();

            if (! $process->isSuccessful()) {
                $this->error('Échec pg_dump : '.$process->getErrorOutput());

                return self::FAILURE;
            }

            $this->info("Sauvegarde PostgreSQL : {$file}");

            return self::SUCCESS;
        }

        if ($connection === 'sqlite') {
            $source = database_path('database.sqlite');
            if (! file_exists($source)) {
                $this->error('Fichier SQLite introuvable.');

                return self::FAILURE;
            }
            $dest = "{$dir}/clinic-{$timestamp}.sqlite";
            copy($source, $dest);
            $this->info("Sauvegarde SQLite : {$dest}");

            return self::SUCCESS;
        }

        $this->warn("Connexion « {$connection} » non prise en charge pour la sauvegarde automatique.");

        return self::FAILURE;
    }
}

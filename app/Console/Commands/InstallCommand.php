<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallCommand extends Command
{
    protected $signature = 'app:install
                            {--fresh : Recrée toutes les tables (migrate:fresh)}
                            {--seed  : Lance les seeders}
                            {--no-npm : Ignore l\'installation et le build npm}';

    protected $description = 'Installation complète du projet (composer, clé, migrations, seeders, storage, npm)';

    public function handle(): int
    {
        $this->info('');
        $this->info('╔══════════════════════════════════════╗');
        $this->info('║        Installation Agri-Shop        ║');
        $this->info('╚══════════════════════════════════════╝');
        $this->info('');

        // 1. Composer
        $this->step('📦 Composer install...');
        $this->runShell('composer install --no-interaction --prefer-dist');

        // 2. .env
        $this->step('⚙️  Fichier .env...');
        if (! File::exists(base_path('.env'))) {
            File::copy(base_path('.env.example'), base_path('.env'));
            $this->line('    → .env créé depuis .env.example');
        } else {
            $this->line('    → .env existe déjà, ignoré');
        }

        // 3. APP_KEY
        $this->step('🔑 Génération de la clé applicative...');
        $this->call('key:generate', ['--ansi' => true]);

        // 4. SQLite (si driver = sqlite et fichier absent)
        $this->step('🗄️  Base de données...');
        if (config('database.default') === 'sqlite') {
            $path = database_path('database.sqlite');
            if (! File::exists($path)) {
                File::put($path, '');
                $this->line('    → database.sqlite créé');
            }
        }

        // 5. Migrations
        if ($this->option('fresh')) {
            $this->call('migrate:fresh', ['--force' => true]);
        } else {
            $this->call('migrate', ['--force' => true]);
        }

        // 6. Seeders
        if ($this->option('seed') || $this->option('fresh')) {
            $this->step('🌱 Seeders...');
            $this->call('db:seed', ['--force' => true]);
        }

        // 7. Storage link
        $this->step('🔗 Lien storage...');
        if (! File::exists(public_path('storage'))) {
            $this->call('storage:link');
        } else {
            $this->line('    → Lien storage existe déjà');
        }

        // 8. NPM
        if (! $this->option('no-npm')) {
            $this->step('🎨 NPM install + build...');
            $this->runShell('npm install --silent');
            $this->runShell('npm run build');
        }

        // 9. Cache clear
        $this->step('🧹 Nettoyage des caches...');
        $this->call('config:clear');
        $this->call('cache:clear');
        $this->call('view:clear');
        $this->call('route:clear');

        $this->info('');
        $this->info('✅ Installation terminée !');
        $this->info('   Lance le serveur : php artisan serve');
        $this->info('');

        return self::SUCCESS;
    }

    private function step(string $label): void
    {
        $this->info('');
        $this->info("  {$label}");
    }

    private function runShell(string $command): void
    {
        $output = [];
        $code   = 0;
        exec($command . ' 2>&1', $output, $code);

        if ($code !== 0) {
            $this->error("    ✗ Erreur : {$command}");
            foreach ($output as $line) {
                $this->line("      {$line}");
            }
        }
    }
}

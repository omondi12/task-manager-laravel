<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class SetupApplicationCommand extends Command
{
    protected $signature = 'app:setup {--force : Force setup even in production}';
    protected $description = 'Setup the Task Manager application in a new environment';

    public function handle()
    {
        $this->info('Task Manager Application Setup');
        $this->info('=============================');
        $this->newLine();

        $env = app()->environment();
        $this->info("Current Environment: {$env}");
        $this->newLine();
 
        if ($env === 'production' && !$this->option('force')) {
            if (!$this->confirm('This appears to be a production environment. Continue with setup?')) {
                $this->error('Setup cancelled for safety.');
                return 1;
            }
        }
 
        $this->setupEnvironmentFile();
        $this->generateApplicationKey();
        $this->installDependencies();
        $this->setupDatabase();
        $this->createStorageLink();
        $this->optimizeApplication();
        $this->verifySetup();

        $this->newLine();
        $this->info('Task Manager setup completed successfully.');
        $this->info('Application URL: ' . config('app.url'));
        
        return 0;
    }

    private function setupEnvironmentFile()
    {
        $this->info('Setting up environment configuration...');

        if (!File::exists('.env')) {
            if (File::exists('.env.example')) {
                File::copy('.env.example', '.env');
                $this->info('Created .env file from .env.example');
                $this->configureEnvironment();
            } else {
                $this->createBasicEnvFile();
            }
        } else {
            $this->warn('.env file already exists');
            
            if ($this->confirm('Update configuration settings?', false)) {
                $this->configureEnvironment();
            }
        }
    }

    private function configureEnvironment()
    {
        if ($this->confirm('Configure application settings?', true)) {
            $appName = $this->ask('Application name', 'Task Manager');
            $appUrl = $this->ask('Application URL', 'http://localhost:8000');
            
            $this->updateEnvValue('APP_NAME', $appName);
            $this->updateEnvValue('APP_URL', $appUrl);
            
            if ($this->confirm('Configure database settings?', true)) {
                $dbHost = $this->ask('Database host', '127.0.0.1');
                $dbName = $this->ask('Database name', 'task_manager');
                $dbUser = $this->ask('Database username', 'postgres');
                $dbPassword = $this->secret('Database password');
                
                $this->updateEnvValue('DB_HOST', $dbHost);
                $this->updateEnvValue('DB_DATABASE', $dbName);
                $this->updateEnvValue('DB_USERNAME', $dbUser);
                $this->updateEnvValue('DB_PASSWORD', $dbPassword);
            }
            
            if ($this->confirm('Configure email settings?', false)) {
                $this->info('Note: For Gmail, use App Password instead of regular password');
                $mailUser = $this->ask('Email username/address');
                $mailPassword = $this->secret('Email password (App Password for Gmail)');
                $mailFrom = $this->ask('From email address', $mailUser);
                
                $this->updateEnvValue('MAIL_USERNAME', $mailUser);
                $this->updateEnvValue('MAIL_PASSWORD', $mailPassword);
                $this->updateEnvValue('MAIL_FROM_ADDRESS', $mailFrom);
            }
            
            Artisan::call('config:clear');
            $this->info('Configuration updated');
        }
    }

    private function updateEnvValue($key, $value)
    {
        $envContent = File::get('.env');
        $escapedValue = addslashes($value);
        
        if (strpos($envContent, "{$key}=") !== false) {
            $envContent = preg_replace("/^{$key}=.*$/m", "{$key}=\"{$escapedValue}\"", $envContent);
        } else {
            $envContent .= "\n{$key}=\"{$escapedValue}\"";
        }
        
        File::put('.env', $envContent);
    }

    private function createBasicEnvFile()
    {
        $envTemplate = 'APP_NAME="Task Manager"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file

PHP_CLI_SERVER_WORKERS=4

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=task_manager
DB_USERNAME=postgres
DB_PASSWORD=

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_NAME}"';

        File::put('.env', $envTemplate);
        $this->info('Created basic .env file');
        $this->configureEnvironment();
    }

    private function generateApplicationKey()
    {
        $this->info('Generating application key...');
        
        if (empty(config('app.key'))) {
            Artisan::call('key:generate', ['--force' => true]);
            $this->info('Application key generated');
        } else {
            $this->info('Application key already exists');
        }
    }

    private function installDependencies()
    {
        $this->info('Checking dependencies...');

        if (File::exists('composer.json')) {
            if ($this->confirm('Install Composer dependencies?', true)) {
                $this->info('Installing Composer dependencies...');
                
                $command = app()->environment('production') 
                    ? 'composer install --no-dev --optimize-autoloader'
                    : 'composer install';
                
                exec($command . ' 2>&1', $output, $return);
                
                if ($return === 0) {
                    $this->info('Composer dependencies installed');
                } else {
                    $this->warn('Composer installation had issues. Run manually: ' . $command);
                }
            }
        }

        if (File::exists('package.json')) {
            if ($this->confirm('Install NPM dependencies and build assets?', false)) {
                $this->info('Installing NPM dependencies...');
                
                exec('npm install 2>&1', $output, $return);
                if ($return === 0) {
                    $this->info('NPM dependencies installed');
                    
                    exec('npm run build 2>&1', $buildOutput, $buildReturn);
                    if ($buildReturn === 0) {
                        $this->info('Assets compiled');
                    } else {
                        $this->warn('Asset compilation failed');
                    }
                } else {
                    $this->warn('NPM installation failed. Run manually: npm install');
                }
            }
        }
    }

    private function setupDatabase()
    {
        $this->info('Setting up database...');

        try {
            DB::connection()->getPdo();
            $this->info('Database connection successful');

            $existingTables = $this->getExistingTables();
            
            if (empty($existingTables)) {
                $this->info('Running database migrations...');
                Artisan::call('migrate', ['--force' => true]);
                $this->info('Database migrations completed');
            } else {
                $this->info('Existing tables found: ' . implode(', ', $existingTables));
                
                if ($this->confirm('Run pending migrations?', true)) {
                    Artisan::call('migrate', ['--force' => true]);
                    $this->info('Migrations completed');
                }
            }

            if (!app()->environment('production')) {
                $userCount = $this->getUserCount();
                
                if ($userCount === 0 && $this->confirm('Create sample data for testing?', false)) {
                    $this->createSampleData();
                }
            }

        } catch (\Exception $e) {
            $this->error('Database connection failed: ' . $e->getMessage());
            $this->info('Please check your database configuration in .env file');
            $this->info('Make sure PostgreSQL is running and the database exists');
        }
    }

    private function getExistingTables()
    {
        try {
            $tables = [];
            $requiredTables = ['users', 'tasks', 'categories'];
            
            foreach ($requiredTables as $table) {
                if (DB::getSchemaBuilder()->hasTable($table)) {
                    $tables[] = $table;
                }
            }
            
            return $tables;
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getUserCount()
    {
        try {
            return DB::table('users')->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function createSampleData()
    {
        try {
            $userId = DB::table('users')->insertGetId([
                'name' => 'Demo User',
                'email' => 'demo@taskmanager.com',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $workCategory = DB::table('categories')->insertGetId([
                'user_id' => $userId,
                'name' => 'Work',
                'color' => '#3B82F6',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $personalCategory = DB::table('categories')->insertGetId([
                'user_id' => $userId,
                'name' => 'Personal',
                'color' => '#10B981',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('tasks')->insert([
                [
                    'user_id' => $userId,
                    'category_id' => $workCategory,
                    'title' => 'Complete project documentation',
                    'description' => 'Finish writing the technical documentation',
                    'priority' => 'high',
                    'status' => 'pending',
                    'due_date' => now()->addDays(3),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'user_id' => $userId,
                    'category_id' => $personalCategory,
                    'title' => 'Weekly planning',
                    'description' => 'Plan tasks for the upcoming week',
                    'priority' => 'normal',
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);

            $this->info('Sample data created');
            $this->info('Demo login: demo@taskmanager.com / password');
            
        } catch (\Exception $e) {
            $this->warn('Could not create sample data: ' . $e->getMessage());
        }
    }

    private function createStorageLink()
    {
        $this->info('Creating storage link...');

        if (!File::exists(public_path('storage'))) {
            try {
                Artisan::call('storage:link');
                $this->info('Storage link created');
            } catch (\Exception $e) {
                $this->warn('Could not create storage link: ' . $e->getMessage());
            }
        } else {
            $this->info('Storage link already exists');
        }
    }

    private function optimizeApplication()
    {
        $this->info('Optimizing application...');

        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');

        if (app()->environment('production')) {
            Artisan::call('config:cache');
            Artisan::call('route:cache');
            Artisan::call('view:cache');
            $this->info('Production optimizations applied');
        } else {
            $this->info('Development caches cleared');
        }
    }

    private function verifySetup()
    {
        $this->info('Verifying setup...');

        $checks = [
            'Environment file exists' => File::exists('.env'),
            'Application key is set' => !empty(config('app.key')),
            'Database connection works' => $this->testDatabaseConnection(),
            'Storage link exists' => File::exists(public_path('storage')),
            'Users table exists' => $this->tableExists('users'),
            'Tasks table exists' => $this->tableExists('tasks'),
            'Categories table exists' => $this->tableExists('categories'),
        ];

        $passed = 0;
        $total = count($checks);

        foreach ($checks as $check => $status) {
            if ($status) {
                $this->info("PASS: {$check}");
                $passed++;
            } else {
                $this->error("FAIL: {$check}");
            }
        }

        $this->newLine();
        $this->info("Setup verification: {$passed}/{$total} checks passed");
        
        if ($passed === $total) {
            $this->info('All checks passed successfully');
        } else {
            $this->warn('Some checks failed. Please review the issues above.');
        }
    }

    private function testDatabaseConnection()
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function tableExists($table)
    {
        try {
            return DB::getSchemaBuilder()->hasTable($table);
        } catch (\Exception $e) {
            return false;
        }
    }
}
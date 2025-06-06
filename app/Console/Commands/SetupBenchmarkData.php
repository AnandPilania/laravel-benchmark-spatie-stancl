<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;

class SetupBenchmarkData extends Command
{
    protected $signature = 'benchmark:setup {package} {tenants=10}';

    public function handle()
    {
        $package = $this->argument('package');
        $tenantCount = (int) $this->argument('tenants');

        for ($i = 1; $i <= $tenantCount; $i++) {
            $this->createTenant($package, $i);
        }

        $this->info("Created {$tenantCount} tenants with test data");
    }

    private function createTenant($package, $index)
    {
        $dbName = "tenant_{$package}_{$index}";

        DB::statement("CREATE DATABASE IF NOT EXISTS {$dbName}");

        if ($package === 'spatie') {
            $tenant = Tenant::create([
                'name' => "Tenant {$index}",
                'domain' => "tenant{$index}.test",
                'database' => $dbName,
            ]);

            $tenant->makeCurrent();
        } else {
            $tenant = Tenant::create([
                'id' => "tenant-{$index}",
                'name' => "Tenant {$index}",
            ]);

            $tenant->domains()->create([
                'domain' => "tenant{$index}.test",
            ]);
        }

        $this->call('migrate', ['--force' => true]);
        $this->call('db:seed', ['--class' => 'DatabaseSeeder', '--force' => true]);
    }
}

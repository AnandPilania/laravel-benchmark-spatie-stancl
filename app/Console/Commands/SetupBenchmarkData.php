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
        if ($package === 'spatie') {
            $dbName = "{$package}_tenant_{$index}";

            DB::statement("CREATE DATABASE IF NOT EXISTS {$dbName}");

            $tenant = Tenant::create([
                'name' => "Tenant {$index}",
                'domain' => "tenant{$index}.test",
                'database' => $dbName,
            ]);

            $tenant->makeCurrent();

            $this->call('tenants:artisan', ['artisanCommand' => 'migrate --database=tenant', '--tenant' => $tenant->id]);
            $this->call('tenants:artisan', ['artisanCommand' => 'db:seed --database=tenant --class=Database\Seeders\DatabaseSeeder', '--tenant' => $tenant->id]);
        } else {
            $tenant = Tenant::create([
                'id' => "tenant-{$index}",
                'name' => "Tenant {$index}",
            ]);

            $tenant->domains()->create([
                'domain' => "tenant{$index}.test",
            ]);
        }
    }
}

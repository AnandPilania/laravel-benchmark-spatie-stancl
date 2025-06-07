<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;
use App\Models\Product;

class ConnectionPoolBenchmark extends Command
{
    protected $signature = 'benchmark:connections {package} {max-connections=100}';

    public function handle()
    {
        $package = $this->argument('package');
        $maxConnections = (int) $this->argument('max-connections');

        $tenants = Tenant::take(10)->get();
        $connectionTimes = [];
        $failedConnections = 0;

        for ($i = 0; $i < $maxConnections; $i++) {
            $tenant = $tenants->random();

            try {
                $start = microtime(true);

                if ($package === 'spatie') {
                    $tenant->makeCurrent();
                } else {
                    tenancy()->initialize($tenant);
                }

                DB::connection()->getPdo();
                Product::count();

                $connectionTime = (microtime(true) - $start) * 1000;
                $connectionTimes[] = $connectionTime;
            } catch (\Exception $e) {
                $failedConnections++;
                $this->error("Connection failed: " . $e->getMessage());
            }
        }

        $this->displayConnectionResults($connectionTimes, $failedConnections, $maxConnections);
    }

    private function displayConnectionResults($times, $failed, $total)
    {
        $successful = $total - $failed;
        $avgTime = $successful > 0 ? array_sum($times) / count($times) : 0;

        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Attempts', $total],
                ['Successful Connections', $successful],
                ['Failed Connections', $failed],
                ['Success Rate', number_format(($successful / $total) * 100, 2) . '%'],
                ['Avg Connection Time', number_format($avgTime, 2) . ' ms'],
                ['Max Connection Time', $successful > 0 ? number_format(max($times), 2) . ' ms' : 'N/A'],
                ['Min Connection Time', $successful > 0 ? number_format(min($times), 2) . ' ms' : 'N/A'],
            ]
        );
    }
}

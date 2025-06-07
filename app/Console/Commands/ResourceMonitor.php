<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;
use App\Models\Product;
use App\Models\Order;
use function memory_get_peak_usage;
use function memory_get_usage;

class ResourceMonitor extends Command
{
    protected $signature = 'benchmark:monitor {package} {duration=300}';

    public function handle()
    {
        $package = $this->argument('package');
        $duration = (int) $this->argument('duration');
        $endTime = time() + $duration;

        $resourceData = [];
        $tenants = Tenant::take(5)->get();

        while (time() < $endTime) {
            $startStats = $this->getSystemStats();

            foreach ($tenants as $tenant) {
                if ($package === 'spatie') {
                    $tenant->makeCurrent();
                } else {
                    tenancy()->initialize($tenant);
                }

                Product::with(['orders'])->take(100)->get();
                Order::with(['items.product'])->take(50)->get();
            }

            $endStats = $this->getSystemStats();

            $resourceData[] = [
                'timestamp' => time(),
                'memory_used' => $endStats['memory'] - $startStats['memory'],
                'cpu_load' => $this->safe_sys_getloadavg()[0],
                'database_connections' => $this->getDatabaseConnections(),
            ];

            sleep(5);
        }

        $this->saveResourceData($package, $resourceData);
    }

    private function getSystemStats()
    {
        return [
            'memory' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true),
        ];
    }

    private function getDatabaseConnections()
    {
        try {
            $result = DB::select("SHOW STATUS WHERE variable_name = 'Threads_connected'");
            return $result[0]->Value ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function saveResourceData($package, $data)
    {
        $filename = base_path("results/resource_monitor_{$package}_" . date('Y-m-d_H-i-s') . '.json');
        file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT));
        $this->info("Resource data saved to: {$filename}");
    }

    private function safe_sys_getloadavg()
    {
        if (function_exists('sys_getloadavg')) {
            return sys_getloadavg();
        }

        return [0, 0, 0];
    }
}

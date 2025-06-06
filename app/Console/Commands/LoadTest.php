<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;

class LoadTest extends Command
{
    protected $signature = 'benchmark:load {package} {duration=60}';

    public function handle()
    {
        $package = $this->argument('package');
        $duration = (int) $this->argument('duration');
        $endTime = time() + $duration;

        $stats = [
            'total_requests' => 0,
            'successful_requests' => 0,
            'failed_requests' => 0,
            'response_times' => [],
            'errors' => [],
        ];

        $tenants = Tenant::take(5)->get();

        while (time() < $endTime) {
            foreach ($tenants as $tenant) {
                $start = microtime(true);

                try {
                    if ($package === 'spatie') {
                        $tenant->makeCurrent();
                    } else {
                        tenancy()->initialize($tenant);
                    }

                    Product::take(10)->get();
                    $stats['successful_requests']++;
                } catch (\Exception $e) {
                    $stats['failed_requests']++;
                    $stats['errors'][] = $e->getMessage();
                }

                $responseTime = (microtime(true) - $start) * 1000;
                $stats['response_times'][] = $responseTime;
                $stats['total_requests']++;
            }
        }

        $this->displayLoadTestResults($stats);
    }

    private function displayLoadTestResults($stats)
    {
        $avgResponseTime = array_sum($stats['response_times']) / count($stats['response_times']);
        $requestsPerSecond = $stats['total_requests'] / 60;

        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Requests', $stats['total_requests']],
                ['Successful Requests', $stats['successful_requests']],
                ['Failed Requests', $stats['failed_requests']],
                ['Success Rate', number_format(($stats['successful_requests'] / $stats['total_requests']) * 100, 2) . '%'],
                ['Avg Response Time', number_format($avgResponseTime, 2) . ' ms'],
                ['Requests/Second', number_format($requestsPerSecond, 2)],
            ]
        );
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use App\Models\Tenant;

class CacheBenchmark extends Command
{
    protected $signature = 'benchmark:cache {package} {iterations=300}';

    public function handle()
    {
        $package = $this->argument('package');
        $iterations = (int) $this->argument('iterations');

        $tenants = Tenant::take(10)->get();
        $results = [];

        foreach ($tenants as $tenant) {
            if ($package === 'spatie') {
                $tenant->makeCurrent();
            } else {
                tenancy()->initialize($tenant);
            }

            $cacheResults = [
                'tenant_id' => $tenant->id,
                'cache_write' => $this->benchmarkCacheWrite($iterations),
                'cache_read' => $this->benchmarkCacheRead($iterations),
                'cache_invalidation' => $this->benchmarkCacheInvalidation($iterations),
            ];

            $results[] = $cacheResults;
        }

        $this->displayCacheResults($results);
        $this->saveCacheResults($package, $results);
    }

    private function benchmarkCacheWrite($iterations)
    {
        $times = [];

        for ($i = 0; $i < $iterations; $i++) {
            $start = microtime(true);
            Cache::put("test_key_{$i}", "test_value_{$i}", 3600);
            $end = microtime(true);
            $times[] = ($end - $start) * 1000;
        }

        return [
            'avg' => array_sum($times) / count($times),
            'min' => min($times),
            'max' => max($times),
        ];
    }

    private function benchmarkCacheRead($iterations)
    {
        for ($i = 0; $i < $iterations; $i++) {
            Cache::put("read_test_{$i}", "value_{$i}", 3600);
        }

        $times = [];

        for ($i = 0; $i < $iterations; $i++) {
            $start = microtime(true);
            Cache::get("read_test_{$i}");
            $end = microtime(true);
            $times[] = ($end - $start) * 1000;
        }

        return [
            'avg' => array_sum($times) / count($times),
            'min' => min($times),
            'max' => max($times),
        ];
    }

    private function benchmarkCacheInvalidation($iterations)
    {
        for ($i = 0; $i < $iterations; $i++) {
            Cache::put("invalidate_test_{$i}", "value_{$i}", 3600);
        }

        $times = [];

        for ($i = 0; $i < $iterations; $i++) {
            $start = microtime(true);
            Cache::forget("invalidate_test_{$i}");
            $end = microtime(true);
            $times[] = ($end - $start) * 1000;
        }

        return [
            'avg' => array_sum($times) / count($times),
            'min' => min($times),
            'max' => max($times),
        ];
    }

    private function displayCacheResults($results)
    {
        $this->info('=== CACHE PERFORMANCE RESULTS ===');

        foreach ($results as $result) {
            $this->info("Tenant: {$result['tenant_id']}");
            $this->table(
                ['Operation', 'Avg (ms)', 'Min (ms)', 'Max (ms)'],
                [
                    [
                        'Cache Write',
                        number_format($result['cache_write']['avg'], 4),
                        number_format($result['cache_write']['min'], 4),
                        number_format($result['cache_write']['max'], 4)
                    ],
                    [
                        'Cache Read',
                        number_format($result['cache_read']['avg'], 4),
                        number_format($result['cache_read']['min'], 4),
                        number_format($result['cache_read']['max'], 4)
                    ],
                    [
                        'Cache Invalidation',
                        number_format($result['cache_invalidation']['avg'], 4),
                        number_format($result['cache_invalidation']['min'], 4),
                        number_format($result['cache_invalidation']['max'], 4)
                    ],
                ]
            );
        }
    }

    private function saveCacheResults($package, $results)
    {
        $filename = base_path("results/cache_benchmark_{$package}_" . date('Y-m-d_H-i-s') . '.json');
        file_put_contents($filename, json_encode($results, JSON_PRETTY_PRINT));
        $this->info("Cache results saved to: {$filename}");
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use App\Models\Product;

class BenchmarkCommand extends Command
{
    protected $signature = 'benchmark {iterations=300}';
    protected $description = 'Benchmark Spatie multitenancy performance';

    public function handle()
    {
        $iterations = (int) $this->argument('iterations');
        $results = [];

        $tenants = Tenant::take(10)->get();

        foreach ($tenants as $tenant) {
            $this->info("Benchmarking tenant: {$tenant->name}");

            $tenantResults = [
                'tenant_id' => $tenant->id,
                'tenant_switch_time' => $this->benchmarkTenantSwitch($tenant, $iterations),
                'query_performance' => $this->benchmarkQueries($tenant, $iterations),
                'crud_operations' => $this->benchmarkCrudOperations($tenant, $iterations),
                'memory_usage' => $this->measureMemoryUsage($tenant, $iterations),
            ];

            $results[] = $tenantResults;
        }

        $this->saveResults('spatie', $results);
        $this->displayResults($results);
    }

    private function benchmarkTenantSwitch($tenant, $iterations)
    {
        $times = [];

        for ($i = 0; $i < $iterations; $i++) {
            $start = microtime(true);
            $tenant->makeCurrent();
            $end = microtime(true);
            $times[] = ($end - $start) * 1000;
        }

        return [
            'avg' => array_sum($times) / count($times),
            'min' => min($times),
            'max' => max($times),
            'median' => $this->median($times),
        ];
    }

    private function benchmarkQueries($tenant, $iterations)
    {
        $tenant->makeCurrent();
        $times = [];

        for ($i = 0; $i < $iterations; $i++) {
            $start = microtime(true);
            Product::take(50)->get();
            $end = microtime(true);
            $times[] = ($end - $start) * 1000;
        }

        return [
            'avg' => array_sum($times) / count($times),
            'min' => min($times),
            'max' => max($times),
            'median' => $this->median($times),
        ];
    }

    private function benchmarkCrudOperations($tenant, $iterations)
    {
        $tenant->makeCurrent();
        $times = [];

        for ($i = 0; $i < $iterations; $i++) {
            $start = microtime(true);

            $product = Product::create([
                'name' => 'Benchmark Product ' . $i,
                'description' => 'Test product for benchmarking',
                'price' => rand(100, 1000),
                'sku' => 'BENCH-' . $i,
                'stock_quantity' => rand(10, 100),
            ]);

            $product->update(['price' => rand(100, 1000)]);
            $product->delete();

            $end = microtime(true);
            $times[] = ($end - $start) * 1000;
        }

        return [
            'avg' => array_sum($times) / count($times),
            'min' => min($times),
            'max' => max($times),
            'median' => $this->median($times),
        ];
    }

    private function measureMemoryUsage($tenant, $iterations)
    {
        $tenant->makeCurrent();
        $memoryUsage = [];

        for ($i = 0; $i < $iterations; $i++) {
            $startMemory = memory_get_usage(true);
            Product::take(100)->get();
            $endMemory = memory_get_usage(true);
            $memoryUsage[] = $endMemory - $startMemory;
        }

        return [
            'avg' => array_sum($memoryUsage) / count($memoryUsage),
            'min' => min($memoryUsage),
            'max' => max($memoryUsage),
            'peak' => memory_get_peak_usage(true),
        ];
    }

    private function median($arr)
    {
        sort($arr);
        $count = count($arr);
        $middle = floor($count / 2);

        if ($count % 2) {
            return $arr[$middle];
        } else {
            return ($arr[$middle - 1] + $arr[$middle]) / 2;
        }
    }

    private function saveResults($package, $results)
    {
        $filename = base_path("results/benchmark_{$package}_" . date('Y-m-d_H-i-s') . '.json');
        file_put_contents($filename, json_encode($results, JSON_PRETTY_PRINT));
        $this->info("Results saved to: {$filename}");
    }

    private function displayResults($results)
    {
        $this->table(
            ['Metric', 'Avg (ms)', 'Min (ms)', 'Max (ms)', 'Median (ms)'],
            [
                [
                    'Tenant Switch',
                    number_format($results[0]['tenant_switch_time']['avg'], 4),
                    number_format($results[0]['tenant_switch_time']['min'], 4),
                    number_format($results[0]['tenant_switch_time']['max'], 4),
                    number_format($results[0]['tenant_switch_time']['median'], 4)
                ],
                [
                    'Query Performance',
                    number_format($results[0]['query_performance']['avg'], 4),
                    number_format($results[0]['query_performance']['min'], 4),
                    number_format($results[0]['query_performance']['max'], 4),
                    number_format($results[0]['query_performance']['median'], 4)
                ],
                [
                    'CRUD Operations',
                    number_format($results[0]['crud_operations']['avg'], 4),
                    number_format($results[0]['crud_operations']['min'], 4),
                    number_format($results[0]['crud_operations']['max'], 4),
                    number_format($results[0]['crud_operations']['median'], 4)
                ],
            ]
        );
    }
}

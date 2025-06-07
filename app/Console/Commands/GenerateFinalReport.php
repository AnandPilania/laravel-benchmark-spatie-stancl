<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateFinalReport extends Command
{
    protected $signature = 'benchmark:final-report';

    public function handle()
    {
        $benchmarkFiles = $this->collectAllBenchmarkFiles();
        $reportData = $this->processBenchmarkData($benchmarkFiles);

        $htmlReport = $this->generateHtmlReport($reportData);
        $this->saveHtmlReport($htmlReport);

        $this->info('Final comprehensive report generated successfully!');
    }

    private function collectAllBenchmarkFiles()
    {
        return [
            'spatie' => [
                'performance' => glob(base_path('results/benchmark_spatie_*.json')),
                'resource' => glob(base_path('results/resource_monitor_spatie_*.json')),
                'cache' => glob(base_path('results/cache_benchmark_spatie_*.json')),
                // 'siege' => glob(base_path('results/spatie_siege_results.json')),
            ],
            'stancl' => [
                'performance' => glob(base_path('results/benchmark_stancl_*.json')),
                'resource' => glob(base_path('results/resource_monitor_stancl_*.json')),
                'cache' => glob(base_path('results/cache_benchmark_stancl_*.json')),
                // 'siege' => glob(base_path('results/stancl_siege_results.json')),
            ],
        ];
    }

    private function processBenchmarkData($files)
    {
        $processedData = [];

        foreach (['spatie', 'stancl'] as $package) {
            $packageData = [];

            if (!empty($files[$package]['performance'])) {
                $performanceData = [];
                foreach ($files[$package]['performance'] as $file) {
                    $data = json_decode(file_get_contents($file), true);
                    $performanceData = array_merge($performanceData, $data);
                }
                $packageData['performance'] = $this->aggregatePerformanceData($performanceData);
            }

            if (!empty($files[$package]['resource'])) {
                $resourceData = [];
                foreach ($files[$package]['resource'] as $file) {
                    $data = json_decode(file_get_contents($file), true);
                    $resourceData = array_merge($resourceData, $data);
                }
                $packageData['resource'] = $this->aggregateResourceData($resourceData);
            }

            if (!empty($files[$package]['cache'])) {
                $cacheData = [];
                foreach ($files[$package]['cache'] as $file) {
                    $data = json_decode(file_get_contents($file), true);
                    $cacheData = array_merge($cacheData, $data);
                }
                $packageData['cache'] = $this->aggregateCacheData($cacheData);
            }

            $processedData[$package] = $packageData;
        }

        return $processedData;
    }

    private function aggregatePerformanceData($data)
    {
        $metrics = ['tenant_switch_time', 'query_performance', 'crud_operations'];
        $aggregated = [];

        foreach ($metrics as $metric) {
            $values = array_column(array_column($data, $metric), 'avg');
            $aggregated[$metric] = [
                'avg' => array_sum($values) / count($values),
                'min' => min($values),
                'max' => max($values),
                'samples' => count($values),
            ];
        }

        return $aggregated;
    }

    private function aggregateResourceData($data)
    {
        $memoryUsage = array_column($data, 'memory_used');
        $cpuLoad = array_column($data, 'cpu_load');

        return [
            'memory' => [
                'avg' => array_sum($memoryUsage) / count($memoryUsage),
                'peak' => max($memoryUsage),
            ],
            'cpu' => [
                'avg' => array_sum($cpuLoad) / count($cpuLoad),
                'peak' => max($cpuLoad),
            ],
        ];
    }

    private function aggregateCacheData($data)
    {
        $operations = ['cache_write', 'cache_read', 'cache_invalidation'];
        $aggregated = [];

        foreach ($operations as $operation) {
            $values = array_column(array_column($data, $operation), 'avg');
            $aggregated[$operation] = [
                'avg' => array_sum($values) / count($values),
                'min' => min($values),
                'max' => max($values),
            ];
        }

        return $aggregated;
    }

    private function generateHtmlReport($data)
    {
        return view('benchmark-report', compact('data'))->render();
    }

    private function saveHtmlReport($html)
    {
        $filename = base_path('results/final_benchmark_report_' . date('Y-m-d_H-i-s') . '.html');
        file_put_contents($filename, $html);
        $this->info("Final report saved to: {$filename}");
    }
}

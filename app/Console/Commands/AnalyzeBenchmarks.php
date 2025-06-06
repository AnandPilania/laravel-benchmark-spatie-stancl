<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AnalyzeBenchmarks extends Command
{
    protected $signature = 'benchmark:analyze';

    public function handle()
    {
        $spatieFiles = glob(storage_path('app/benchmark_spatie_*.json'));
        $stanclFiles = glob(storage_path('app/benchmark_stancl_*.json'));

        $spatieData = $this->loadBenchmarkData($spatieFiles);
        $stanclData = $this->loadBenchmarkData($stanclFiles);

        $comparison = $this->compareResults($spatieData, $stanclData);

        $this->generateReport($comparison);
    }

    private function loadBenchmarkData($files)
    {
        $allData = [];

        foreach ($files as $file) {
            $data = json_decode(file_get_contents($file), true);
            $allData = array_merge($allData, $data);
        }

        return $allData;
    }

    private function compareResults($spatieData, $stanclData)
    {
        $comparison = [];

        $metrics = ['tenant_switch_time', 'query_performance', 'crud_operations'];

        foreach ($metrics as $metric) {
            $spatieAvg = $this->calculateAverageMetric($spatieData, $metric);
            $stanclAvg = $this->calculateAverageMetric($stanclData, $metric);

            $comparison[$metric] = [
                'spatie' => $spatieAvg,
                'stancl' => $stanclAvg,
                'difference_ms' => $spatieAvg['avg'] - $stanclAvg['avg'],
                'percentage_diff' => (($spatieAvg['avg'] - $stanclAvg['avg']) / $spatieAvg['avg']) * 100,
                'winner' => $spatieAvg['avg'] < $stanclAvg['avg'] ? 'spatie' : 'stancl',
            ];
        }

        return $comparison;
    }

    private function calculateAverageMetric($data, $metric)
    {
        $values = array_column(array_column($data, $metric), 'avg');

        return [
            'avg' => array_sum($values) / count($values),
            'min' => min($values),
            'max' => max($values),
        ];
    }

    private function generateReport($comparison)
    {
        $this->info('=== BENCHMARK COMPARISON REPORT ===');
        $this->newLine();

        foreach ($comparison as $metric => $data) {
            $this->info(strtoupper(str_replace('_', ' ', $metric)));
            $this->table(
                ['Package', 'Avg (ms)', 'Min (ms)', 'Max (ms)'],
                [
                    [
                        'Spatie',
                        number_format($data['spatie']['avg'], 4),
                        number_format($data['spatie']['min'], 4),
                        number_format($data['spatie']['max'], 4)
                    ],
                    [
                        'Stancl',
                        number_format($data['stancl']['avg'], 4),
                        number_format($data['stancl']['min'], 4),
                        number_format($data['stancl']['max'], 4)
                    ],
                ]
            );

            $winner = $data['winner'];
            $diff = abs($data['percentage_diff']);

            if ($winner === 'spatie') {
                $this->info("🏆 Spatie is {$diff}% faster");
            } else {
                $this->info("🏆 Stancl is {$diff}% faster");
            }

            $this->newLine();
        }

        $this->generateDetailedReport($comparison);
    }

    private function generateDetailedReport($comparison)
    {
        $reportContent = $this->buildMarkdownReport($comparison);
        $filename = storage_path('app/benchmark_report_' . date('Y-m-d_H-i-s') . '.md');
        file_put_contents($filename, $reportContent);

        $this->info("Detailed report saved to: {$filename}");
    }

    private function buildMarkdownReport($comparison)
    {
        $report = "# Laravel Multitenancy Benchmark Report\n\n";
        $report .= "Generated on: " . date('Y-m-d H:i:s') . "\n\n";

        $report .= "## Executive Summary\n\n";

        $winners = array_count_values(array_column($comparison, 'winner'));
        $spatieWins = $winners['spatie'] ?? 0;
        $stanclWins = $winners['stancl'] ?? 0;

        if ($spatieWins > $stanclWins) {
            $report .= "**Spatie Multitenancy** wins in {$spatieWins} out of 3 benchmarks.\n\n";
        } else {
            $report .= "**Stancl Tenancy** wins in {$stanclWins} out of 3 benchmarks.\n\n";
        }

        $report .= "## Detailed Results\n\n";

        foreach ($comparison as $metric => $data) {
            $report .= "### " . ucwords(str_replace('_', ' ', $metric)) . "\n\n";
            $report .= "| Package | Avg (ms) | Min (ms) | Max (ms) |\n";
            $report .= "|---------|----------|----------|---------|\n";
            $report .= "| Spatie  | " . number_format($data['spatie']['avg'], 4) . " | " .
                number_format($data['spatie']['min'], 4) . " | " .
                number_format($data['spatie']['max'], 4) . " |\n";
            $report .= "| Stancl  | " . number_format($data['stancl']['avg'], 4) . " | " .
                number_format($data['stancl']['min'], 4) . " | " .
                number_format($data['stancl']['max'], 4) . " |\n\n";

            $winner = $data['winner'];
            $diff = abs($data['percentage_diff']);
            $report .= "**Winner:** " . ucfirst($winner) . " (" . number_format($diff, 2) . "% faster)\n\n";
        }

        $report .= "## Recommendations\n\n";
        $report .= $this->generateRecommendations($comparison);

        return $report;
    }

    private function generateRecommendations($comparison)
    {
        $recommendations = "";

        $winners = array_count_values(array_column($comparison, 'winner'));
        $spatieWins = $winners['spatie'] ?? 0;
        $stanclWins = $winners['stancl'] ?? 0;

        if ($spatieWins > $stanclWins) {
            $recommendations .= "Based on the benchmark results, **Spatie Multitenancy** shows better performance overall.\n\n";
            $recommendations .= "However, consider these factors before migration:\n\n";
            $recommendations .= "- Feature set comparison\n";
            $recommendations .= "- Community support and documentation\n";
            $recommendations .= "- Long-term maintenance and updates\n";
            $recommendations .= "- Migration complexity and cost\n\n";
        } else {
            $recommendations .= "Based on the benchmark results, **Stancl Tenancy** shows better performance overall.\n\n";
            $recommendations .= "However, consider these factors before migration:\n\n";
            $recommendations .= "- Feature set comparison\n";
            $recommendations .= "- Community support and documentation\n";
            $recommendations .= "- Long-term maintenance and updates\n";
            $recommendations .= "- Migration complexity and cost\n\n";
        }
        $recommendations .= "It's recommended to conduct further tests in a production-like environment before making a final decision.\n";
        return $recommendations;
    }
}

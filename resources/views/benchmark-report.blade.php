<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Multitenancy Benchmark Report</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-shadow {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="gradient-bg py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl font-bold text-white mb-4">Laravel Multitenancy Benchmark Report</h1>
                <p class="text-xl text-indigo-100">Comprehensive Performance Analysis: Spatie vs Stancl</p>
                <div class="mt-6 bg-white bg-opacity-20 rounded-lg p-4 inline-block">
                    <p class="text-white font-medium">Generated on {{ date('F j, Y \a\t g:i A') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-6 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
            <div class="bg-white rounded-xl card-shadow p-8">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">Spatie Multitenancy</h2>
                </div>
                <div class="space-y-4">
                    @if(isset($data['spatie']['performance']))
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">Tenant Switch Time</span>
                            <span class="font-bold text-lg">{{ number_format($data['spatie']['performance']['tenant_switch_time']['avg'], 2) }}ms</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">Query Performance</span>
                            <span class="font-bold text-lg">{{ number_format($data['spatie']['performance']['query_performance']['avg'], 2) }}ms</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">CRUD Operations</span>
                            <span class="font-bold text-lg">{{ number_format($data['spatie']['performance']['crud_operations']['avg'], 2) }}ms</span>
                        </div>
                    @endif
                    @if(isset($data['spatie']['resource']))
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">Memory Usage (Avg)</span>
                            <span class="font-bold text-lg">{{ number_format($data['spatie']['resource']['memory']['avg'] / 1024 / 1024, 1) }}MB</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-gray-600">CPU Load (Avg)</span>
                            <span class="font-bold text-lg">{{ number_format($data['spatie']['resource']['cpu']['avg'], 1) }}%</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-xl card-shadow p-8">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">Stancl Tenancy</h2>
                </div>
                <div class="space-y-4">
                    @if(isset($data['stancl']['performance']))
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">Tenant Switch Time</span>
                            <span class="font-bold text-lg">{{ number_format($data['stancl']['performance']['tenant_switch_time']['avg'], 2) }}ms</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">Query Performance</span>
                            <span class="font-bold text-lg">{{ number_format($data['stancl']['performance']['query_performance']['avg'], 2) }}ms</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">CRUD Operations</span>
                            <span class="font-bold text-lg">{{ number_format($data['stancl']['performance']['crud_operations']['avg'], 2) }}ms</span>
                        </div>
                    @endif
                    @if(isset($data['stancl']['resource']))
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">Memory Usage (Avg)</span>
                            <span class="font-bold text-lg">{{ number_format($data['stancl']['resource']['memory']['avg'] / 1024 / 1024, 1) }}MB</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-gray-600">CPU Load (Avg)</span>
                            <span class="font-bold text-lg">{{ number_format($data['stancl']['resource']['cpu']['avg'], 1) }}%</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @if(isset($data['spatie']['performance']) && isset($data['stancl']['performance']))
        <div class="bg-white rounded-xl card-shadow p-8 mb-12">
            <h3 class="text-2xl font-bold text-gray-900 mb-8 text-center">Performance Comparison</h3>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="text-center">
                    <canvas id="tenantSwitchChart" width="300" height="300"></canvas>
                    <h4 class="text-lg font-semibold mt-4 text-gray-800">Tenant Switch Time</h4>
                </div>
                <div class="text-center">
                    <canvas id="queryPerformanceChart" width="300" height="300"></canvas>
                    <h4 class="text-lg font-semibold mt-4 text-gray-800">Query Performance</h4>
                </div>
                <div class="text-center">
                    <canvas id="crudOperationsChart" width="300" height="300"></canvas>
                    <h4 class="text-lg font-semibold mt-4 text-gray-800">CRUD Operations</h4>
                </div>
            </div>
        </div>
        @endif

        <div class="bg-white rounded-xl card-shadow p-8 mb-12">
            <h3 class="text-2xl font-bold text-gray-900 mb-8">Detailed Performance Metrics</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Metric</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Spatie (ms)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stancl (ms)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Difference</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Winner</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @if(isset($data['spatie']['performance']) && isset($data['stancl']['performance']))
                            @foreach(['tenant_switch_time', 'query_performance', 'crud_operations'] as $metric)
                                @php
                                    $spatieValue = $data['spatie']['performance'][$metric]['avg'];
                                    $stanclValue = $data['stancl']['performance'][$metric]['avg'];
                                    $difference = $spatieValue - $stanclValue;
                                    $percentage = abs($difference / $spatieValue * 100);
                                    $winner = $spatieValue < $stanclValue ? 'Spatie' : 'Stancl';
                                    $winnerColor = $spatieValue < $stanclValue ? 'text-blue-600' : 'text-green-600';
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ ucwords(str_replace('_', ' ', $metric)) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ number_format($spatieValue, 4) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ number_format($stanclValue, 4) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <span class="{{ $difference < 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $difference < 0 ? '' : '+' }}{{ number_format($difference, 4) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold {{ $winnerColor }}">
                                        {{ $winner }} ({{ number_format($percentage, 1) }}% faster)
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        @if(isset($data['spatie']['resource']) && isset($data['stancl']['resource']))
        <div class="bg-white rounded-xl card-shadow p-8 mb-12">
            <h3 class="text-2xl font-bold text-gray-900 mb-8">Resource Usage Comparison</h3>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div>
                    <canvas id="memoryUsageChart" width="400" height="200"></canvas>
                    <h4 class="text-lg font-semibold mt-4 text-center text-gray-800">Memory Usage</h4>
                </div>
                <div>
                    <canvas id="cpuUsageChart" width="400" height="200"></canvas>
                    <h4 class="text-lg font-semibold mt-4 text-center text-gray-800">CPU Usage</h4>
                </div>
            </div>
        </div>
        @endif

        @if(isset($data['spatie']['cache']) && isset($data['stancl']['cache']))
        <div class="bg-white rounded-xl card-shadow p-8 mb-12">
            <h3 class="text-2xl font-bold text-gray-900 mb-8">Cache Performance</h3>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                @foreach(['cache_write', 'cache_read', 'cache_invalidation'] as $operation)
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4">{{ ucwords(str_replace('_', ' ', $operation)) }}</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Spatie:</span>
                                <span class="font-medium">{{ number_format($data['spatie']['cache'][$operation]['avg'], 2) }}ms</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Stancl:</span>
                                <span class="font-medium">{{ number_format($data['stancl']['cache'][$operation]['avg'], 2) }}ms</span>
                            </div>
                            @php
                                $spatieCache = $data['spatie']['cache'][$operation]['avg'];
                                $stanclCache = $data['stancl']['cache'][$operation]['avg'];
                                $cacheWinner = $spatieCache < $stanclCache ? 'Spatie' : 'Stancl';
                                $cacheDiff = abs(($spatieCache - $stanclCache) / $spatieCache * 100);
                            @endphp
                            <div class="pt-2 border-t border-gray-200">
                                <span class="text-sm font-semibold {{ $spatieCache < $stanclCache ? 'text-blue-600' : 'text-green-600' }}">
                                    {{ $cacheWinner }} wins by {{ number_format($cacheDiff, 1) }}%
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="bg-white rounded-xl card-shadow p-8 mb-12">
            <h3 class="text-2xl font-bold text-gray-900 mb-8">Executive Summary</h3>
            <div class="prose max-w-none">
                @php
                    $performanceWins = ['spatie' => 0, 'stancl' => 0];
                    if(isset($data['spatie']['performance']) && isset($data['stancl']['performance'])) {
                        foreach(['tenant_switch_time', 'query_performance', 'crud_operations'] as $metric) {
                            $spatieValue = $data['spatie']['performance'][$metric]['avg'];
                            $stanclValue = $data['stancl']['performance'][$metric]['avg'];
                            if($spatieValue < $stanclValue) {
                                $performanceWins['spatie']++;
                            } else {
                                $performanceWins['stancl']++;
                            }
                        }
                    }
                    $overallWinner = $performanceWins['spatie'] > $performanceWins['stancl'] ? 'Spatie' : 'Stancl';
                    $winnerColor = $overallWinner === 'Spatie' ? 'text-blue-600' : 'text-green-600';
                @endphp

                <div class="bg-gradient-to-r from-blue-50 to-green-50 rounded-lg p-6 mb-6">
                    <h4 class="text-xl font-bold {{ $winnerColor }} mb-2">
                        🏆 {{ $overallWinner }} Multitenancy is the Performance Winner
                    </h4>
                    <p class="text-gray-700">
                        Based on comprehensive benchmarking across {{ $performanceWins['spatie'] + $performanceWins['stancl'] }} key performance metrics,
                        <strong>{{ $overallWinner }}</strong> demonstrates superior performance with
                        {{ $overallWinner === 'Spatie' ? $performanceWins['spatie'] : $performanceWins['stancl'] }} wins out of
                        {{ $performanceWins['spatie'] + $performanceWins['stancl'] }} benchmarks.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-blue-50 rounded-lg p-6">
                        <h5 class="font-bold text-blue-800 mb-3">Spatie Multitenancy Strengths</h5>
                        <ul class="text-sm text-blue-700 space-y-2">
                            @if(isset($data['spatie']['performance']) && isset($data['stancl']['performance']))
                                @if($data['spatie']['performance']['tenant_switch_time']['avg'] < $data['stancl']['performance']['tenant_switch_time']['avg'])
                                    <li>✓ Faster tenant switching mechanism</li>
                                @endif
                                @if($data['spatie']['performance']['query_performance']['avg'] < $data['stancl']['performance']['query_performance']['avg'])
                                    <li>✓ Superior query execution performance</li>
                                @endif
                                @if($data['spatie']['performance']['crud_operations']['avg'] < $data['stancl']['performance']['crud_operations']['avg'])
                                    <li>✓ Optimized CRUD operations</li>
                                @endif
                            @endif
                            <li>✓ Mature and stable codebase</li>
                            <li>✓ Comprehensive documentation</li>
                        </ul>
                    </div>

                    <div class="bg-green-50 rounded-lg p-6">
                        <h5 class="font-bold text-green-800 mb-3">Stancl Tenancy Strengths</h5>
                        <ul class="text-sm text-green-700 space-y-2">
                            @if(isset($data['stancl']['performance']) && isset($data['spatie']['performance']))
                                @if($data['stancl']['performance']['tenant_switch_time']['avg'] < $data['spatie']['performance']['tenant_switch_time']['avg'])
                                    <li>✓ Faster tenant switching mechanism</li>
                                @endif
                                @if($data['stancl']['performance']['query_performance']['avg'] < $data['spatie']['performance']['query_performance']['avg'])
                                    <li>✓ Superior query execution performance</li>
                                @endif
                                @if($data['stancl']['performance']['crud_operations']['avg'] < $data['spatie']['performance']['crud_operations']['avg'])
                                    <li>✓ Optimized CRUD operations</li>
                                @endif
                            @endif
                            <li>✓ Modern architecture and design patterns</li>
                            <li>✓ Active development and feature updates</li>
                            <li>✓ Flexible tenant identification system</li>
                        </ul>
                    </div>
                </div>

                <div class="mt-8 bg-yellow-50 border-l-4 border-yellow-400 p-6">
                    <h5 class="font-bold text-yellow-800 mb-3">Migration Recommendations</h5>
                    <div class="text-yellow-700 space-y-2">
                        @if($overallWinner === 'Stancl')
                            <p><strong>Recommendation:</strong> Consider migrating to Stancl Tenancy for improved performance.</p>
                            <p><strong>Migration Strategy:</strong></p>
                            <ul class="list-disc list-inside ml-4 space-y-1">
                                <li>Plan a phased migration approach</li>
                                <li>Test thoroughly in staging environment</li>
                                <li>Prepare data migration scripts</li>
                                <li>Update application code to use Stancl's API</li>
                                <li>Monitor performance post-migration</li>
                            </ul>
                        @else
                            <p><strong>Recommendation:</strong> Continue with Spatie Multitenancy as it demonstrates superior performance.</p>
                            <p><strong>Optimization Tips:</strong></p>
                            <ul class="list-disc list-inside ml-4 space-y-1">
                                <li>Implement caching strategies</li>
                                <li>Monitor database connection pooling</li>
                                <li>Consider upgrading to latest version</li>
                                <li>Optimize tenant switching frequency</li>
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-gray-800 text-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <p class="text-gray-300">Laravel Multitenancy Benchmark Report</p>
                <p class="text-sm text-gray-400 mt-2">Generated on {{ date('F j, Y \a\t g:i A') }}</p>
            </div>
        </div>
    </footer>

    <script>
        const chartColors = {
            spatie: '#3B82F6',
            stancl: '#10B981'
        };

        @if(isset($data['spatie']['performance']) && isset($data['stancl']['performance']))
        const performanceData = {
            spatie: {
                tenantSwitch: {{ $data['spatie']['performance']['tenant_switch_time']['avg'] }},
                queryPerformance: {{ $data['spatie']['performance']['query_performance']['avg'] }},
                crudOperations: {{ $data['spatie']['performance']['crud_operations']['avg'] }}
            },
            stancl: {
                tenantSwitch: {{ $data['stancl']['performance']['tenant_switch_time']['avg'] }},
                queryPerformance: {{ $data['stancl']['performance']['query_performance']['avg'] }},
                crudOperations: {{ $data['stancl']['performance']['crud_operations']['avg'] }}
            }
        };

        new Chart(document.getElementById('tenantSwitchChart'), {
            type: 'doughnut',
            data: {
                labels: ['Spatie', 'Stancl'],
                datasets: [{
                    data: [performanceData.spatie.tenantSwitch, performanceData.stancl.tenantSwitch],
                    backgroundColor: [chartColors.spatie, chartColors.stancl],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        new Chart(document.getElementById('queryPerformanceChart'), {
            type: 'doughnut',
            data: {
                labels: ['Spatie', 'Stancl'],
                datasets: [{
                    data: [performanceData.spatie.queryPerformance, performanceData.stancl.queryPerformance],
                    backgroundColor: [chartColors.spatie, chartColors.stancl],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        new Chart(document.getElementById('crudOperationsChart'), {
            type: 'doughnut',
            data: {
                labels: ['Spatie', 'Stancl'],
                datasets: [{
                    data: [performanceData.spatie.crudOperations, performanceData.stancl.crudOperations],
                    backgroundColor: [chartColors.spatie, chartColors.stancl],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
        @endif

        @if(isset($data['spatie']['resource']) && isset($data['stancl']['resource']))
        new Chart(document.getElementById('memoryUsageChart'), {
            type: 'bar',
            data: {
                labels: ['Average Memory Usage', 'Peak Memory Usage'],
                datasets: [{
                    label: 'Spatie (MB)',
                    data: [
                        {{ $data['spatie']['resource']['memory']['avg'] / 1024 / 1024 }},
                        {{ $data['spatie']['resource']['memory']['peak'] / 1024 / 1024 }}
                    ],
                    backgroundColor: chartColors.spatie,
                    borderColor: chartColors.spatie,
                    borderWidth: 1
                }, {
                    label: 'Stancl (MB)',
                    data: [
                        {{ $data['stancl']['resource']['memory']['avg'] / 1024 / 1024 }},
                        {{ $data['stancl']['resource']['memory']['peak'] / 1024 / 1024 }}
                    ],
                    backgroundColor: chartColors.stancl,
                    borderColor: chartColors.stancl,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Memory (MB)'
                        }
                    }
                }
            }
        });

        new Chart(document.getElementById('cpuUsageChart'), {
            type: 'bar',
            data: {
                labels: ['Average CPU Usage', 'Peak CPU Usage'],
                datasets: [{
                    label: 'Spatie (%)',
                    data: [
                        {{ $data['spatie']['resource']['cpu']['avg'] }},
                        {{ $data['spatie']['resource']['cpu']['peak'] }}
                    ],
                    backgroundColor: chartColors.spatie,
                    borderColor: chartColors.spatie,
                    borderWidth: 1
                }, {
                    label: 'Stancl (%)',
                    data: [
                        {{ $data['stancl']['resource']['cpu']['avg'] }},
                        {{ $data['stancl']['resource']['cpu']['peak'] }}
                    ],
                    backgroundColor: chartColors.stancl,
                    borderColor: chartColors.stancl,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'CPU Usage (%)'
                        }
                    }
                }
            }
        });
        @endif
    </script>
</body>
</html>

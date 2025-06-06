<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class ConcurrentBenchmark extends Command
{
    protected $signature = 'benchmark:concurrent {package} {users=10} {requests=100}';

    public function handle()
    {
        $package = $this->argument('package');
        $users = (int) $this->argument('users');
        $requests = (int) $this->argument('requests');

        $processes = [];

        for ($i = 0; $i < $users; $i++) {
            $processes[] = Process::start([
                'php',
                'artisan',
                "benchmark:{$package}",
                '--concurrent-user=' . $i,
                '--requests=' . $requests
            ]);
        }

        foreach ($processes as $process) {
            $process->wait();
        }

        $this->info("Concurrent benchmark completed for {$users} users");
    }
}

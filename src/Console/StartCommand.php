<?php

namespace DanJohnson95\Pinout\Console;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class StartCommand extends Command
{
    protected $signature = 'pinout:start';
    protected $description = 'Start both GPIO interrupt listener and Laravel scheduler';

    private string $phpBin;

    public function __construct()
    {
        parent::__construct();

        $this->phpBin = config('pinout.php_bin', '/usr/bin/php');
    }

    public function handle()
    {
        $this->info('Starting GPIO listener and Laravel scheduler...');

        // Start GPIO interrupt listener (make sure the script path is correct)
        $artisanPath = base_path() . '/artisan';

        $gpioCommand = "$this->phpBin $artisanPath pinout:listen"; // Update with correct command/script
        $gpioProcess = new Process([$gpioCommand]);
        $gpioProcess->start();

        // Start Laravel scheduler (this runs the scheduler every minute)
        $schedulerCommand = "$this->phpBin $artisanPath schedule:run"; // Update with correct command
        $schedulerProcess = new Process([$schedulerCommand]);
        $schedulerProcess->start();

        // Optionally, you can monitor both processes and output their statuses
        $this->info('GPIO listener is running in the background.');
        $this->info('Laravel scheduler is running in the background.');

        // Wait for both processes to complete (if needed)
        $gpioProcess->wait();
        $schedulerProcess->wait();

        if (!$gpioProcess->isSuccessful()) {
            throw new ProcessFailedException($gpioProcess);
        }

        if (!$schedulerProcess->isSuccessful()) {
            throw new ProcessFailedException($schedulerProcess);
        }

        $this->info('Both GPIO listener and scheduler have started successfully.');
    }
}

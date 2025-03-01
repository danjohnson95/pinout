<?php

namespace DanJohnson95\Pinout\Console;

use DanJohnson95\Pinout\Events\GPIOInterruptReceived;
use Illuminate\Console\Command;
use pcntl_signal;
use pcntl_async_signals;

class ListenInterruptsCommand extends Command
{
    protected $signature = 'pinout:listen';
    protected $description = 'Start the GPIO listener and listen for hardware interrupts';

    private string $tmpDir;
    private string $pipePath;
    private string $scriptPath;
    private string $pidFile;

    public function __construct()
    {
        parent::__construct();

        $this->tmpDir = config('pinout.gpio_listener.tmp_dir', '/tmp/pinout/');
        $this->pipePath = config('pinout.gpio_listener.tmp_dir') . config('pinout.gpio_listener.pid') . ".pipe";
        $this->scriptPath = dirname(__DIR__ . "/../../scripts/GPIOListener.sh");
        $this->pidFile = config('pinout.gpio_listener.tmp_dir') . config('pinout.gpio_listener.pid') . ".pid";
    }

    public function handle()
    {
        pcntl_async_signals(true);

        pcntl_signal(SIGINT, function ($signal) {
            $this->info("Received SIGINT, shutting down gracefully...");
            // Perform cleanup here if necessary
            exit;  // Exit the loop and end the command gracefully
        });

        $this->info("Starting GPIO listener...");

        // Before we do anything, let's make sure the tmp directory exists
        if (!file_exists($this->tmpDir)) {
            mkdir($this->tmpDir, 0777, true);
        }

        // Now let's check the named pipe exists.
        if (!file_exists($this->pipePath)) {
            posix_mkfifo($this->pipePath, 0666);
        }

        // Ensure the script is executable
        if (!file_exists($this->scriptPath)) {
            $this->error("GPIO listener script not found: {$this->scriptPath}");
            return;
        }

        // Start the Bash script in the background and store the PID
        exec("nohup {$this->scriptPath} {$this->pipePath} > /dev/null 2>&1 & echo $! > {$this->pidFile}");

        // Wait for a moment to ensure the script starts
        sleep(1);

        if (!file_exists($this->pidFile)) {
            $this->error("Failed to start GPIO listener.");
            return;
        }

        $this->info("GPIO listener started successfully.");
        $this->info("Listening for GPIO interrupts...");

        // Ensure the named pipe exists
        if (!file_exists($this->pipePath)) {
            $this->error("Named pipe not found: {$this->pipePath}");
            return;
        }

        // Capture Ctrl+C or termination signal to stop the script
        pcntl_async_signals(true);
        pcntl_signal(SIGINT, [$this, 'stopScript']);
        pcntl_signal(SIGTERM, [$this, 'stopScript']);

        $pipe = fopen($this->pipePath, "r");

        while ($pipe) {
            $line = fgets($pipe);
            if ($line) {
                $data = json_decode($line, true);
                event(new GPIOInterruptReceived($data));
                $this->info("Interrupt received on pin {$data['pin']}");
            }

            pcntl_signal_dispatch();
        }

        fclose($pipe);
        $this->stopScript(); // Stop script when Laravel process exits
    }

    private function stopScript(): void
    {
        $this->info("Stopping GPIO listener...");

        if (file_exists($this->pidFile)) {
            $pid = trim(file_get_contents($this->pidFile));
            if (is_numeric($pid)) {
                exec("kill $pid");
                $this->info("GPIO listener stopped.");
            }
            unlink($this->pidFile);
        }

        exit;
    }
}

<?php

namespace App\Services;

use Symfony\Component\Process\Process;

class OctaveService
{
    public function execute(string $command): mixed
    {
        $delayMs = getCasDelayMs();

        if ($delayMs > 0) {
            usleep($delayMs * 1000);
        }

        $wrappedCommand = $command . PHP_EOL . "disp(jsonencode(__cas_result__));";

        $process = new Process([
            'octave',
            '--quiet',
            '--eval',
            $wrappedCommand
        ]);

        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException(trim($process->getErrorOutput()));
        }

        $output = trim($process->getOutput());

        $lines = array_values(array_filter(array_map('trim', explode("\n", $output))));
        $lastLine = end($lines);

        $decoded = json_decode($lastLine, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        return $lastLine;
    }

    public function executeScript(string $script): mixed
    {
        $delayMs = getCasDelayMs();

        if ($delayMs > 0) {
            usleep($delayMs * 1000);
        }

        $process = new Process([
            'octave',
            '--quiet',
            '--eval',
            $script
        ]);

        $process->setTimeout(20);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException(trim($process->getErrorOutput()));
        }

        $output = trim($process->getOutput());

        $decoded = json_decode($output, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Invalid JSON output from Octave: ' . $output);
        }

        return $decoded;
    }
}
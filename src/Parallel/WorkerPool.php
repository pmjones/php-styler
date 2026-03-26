<?php
declare(strict_types=1);

namespace PhpStyler\Parallel;

use PhpStyler\Exception;

class WorkerPool
{
    /**
     * @param string[] $files
     * @return WorkerResult[]
     */
    public function run(
        array $files,
        string $mode,
        string $configFile,
        int $workerCount,
    ) : array
    {
        $configFile = (string) realpath($configFile);
        $chunkSize = max(1, (int) ceil(count($files) / $workerCount));
        $chunks = array_chunk($files, $chunkSize);
        $processes = [];
        $pipes = [];

        foreach ($chunks as $i => $chunk) {
            $command = [
                PHP_BINARY,
                $this->binPath(),
                'worker',
                '--config=' . $configFile,
                '--mode=' . $mode,
            ];

            $descriptors = [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ];

            $process = proc_open($command, $descriptors, $procPipes);

            if (! is_resource($process)) {
                throw new Exception("Failed to spawn worker {$i}");
            }

            $processes[$i] = $process;
            $pipes[$i] = $procPipes;

            fwrite($procPipes[0], implode("\n", $chunk) . "\n");
            fclose($procPipes[0]);
        }

        $results = [];

        foreach ($processes as $i => $process) {
            $stdout = (string) stream_get_contents($pipes[$i][1]);
            fclose($pipes[$i][1]);

            $stderr = (string) stream_get_contents($pipes[$i][2]);
            fclose($pipes[$i][2]);

            $exitCode = proc_close($process);

            if ($exitCode !== 0 && $stdout === '') {
                throw new Exception(
                    "Worker {$i} failed (exit {$exitCode}): {$stderr}",
                );
            }

            foreach (explode("\n", trim($stdout)) as $line) {
                if ($line === '') {
                    continue;
                }

                $data = json_decode($line, true);

                if (! is_array($data)) {
                    throw new Exception(
                        "Worker {$i} produced invalid output: {$line}",
                    );
                }

                /** @var array<string, mixed> $data */

                $results[] = WorkerResult::fromArray($data);
            }
        }

        return $results;
    }

    protected function binPath() : string
    {
        return dirname(__DIR__, 2) . '/bin/php-styler';
    }

    public static function detectCpuCount() : int
    {
        // Linux
        if (is_readable('/proc/cpuinfo')) {
            $contents = (string) file_get_contents('/proc/cpuinfo');
            $count = substr_count($contents, 'processor');

            if ($count > 0) {
                return $count;
            }
        }

        // macOS
        $sysctl = @shell_exec('sysctl -n hw.ncpu 2>/dev/null');

        if ($sysctl !== null && $sysctl !== false) {
            $count = (int) trim($sysctl);

            if ($count > 0) {
                return $count;
            }
        }

        // Windows
        $env = getenv('NUMBER_OF_PROCESSORS');

        if ($env !== false) {
            $count = (int) $env;

            if ($count > 0) {
                return $count;
            }
        }

        return 4;
    }
}

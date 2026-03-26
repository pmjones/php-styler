<?php
declare(strict_types=1);

namespace PhpStyler\Command;

use AutoShell\Help;
use PhpStyler\Config;
use PhpStyler\Exception;
use PhpStyler\Parallel\WorkerPool;
use PhpStyler\Styler;

#[Help("Checks if any of the configured files need styling.")]
class Check extends Command
{
    /**
     * @var string[]
     */
    protected array $failure = [];

    public function __invoke(CheckOptions $options) : int
    {
        $this->failure = [];
        $start = hrtime(true);

        // load config
        $configFile = $options->configFile ?? $this->findConfigFile();
        echo "Loading config file " . $configFile . PHP_EOL;
        $config = $this->loadConfigFile($configFile);

        // check styling
        try {
            $workerCount = $this->resolveWorkerCount($options->workers);
            $count = $this->checkStyle($config, $configFile, $workerCount);
        } catch (Exception $e) {
            echo $e->getMessage() . PHP_EOL;
            return 1;
        }

        // statistics
        $time = (hrtime(true) - $start) / 1000000000;
        $sum = number_format($time, 3);
        $avg = $count ? number_format($time / $count, 4) : 'NAN';
        $mem = number_format(memory_get_peak_usage() / 1000000, 2);

        // report
        $noun = $count === 1 ? 'file' : 'files';
        echo "Checked {$count} {$noun} in {$sum} seconds";

        if ($count) {
            echo " ({$avg} seconds/file, {$mem} MB peak memory usage)";
        }

        echo '.' . PHP_EOL;
        $failed = count($this->failure);

        /** @phpstan-ignore-next-line */
        $phrase = $failed === 1 ? 'file appears' : 'files appear';
        echo "{$failed} {$phrase} to need styling." . PHP_EOL;
        return (int) $this->failure;
    }

    protected function checkStyle(
        Config $config,
        string $configFile,
        int $workerCount,
    ) : int
    {
        $files = [];

        foreach ($config->files as $file) {
            $files[] = (string) $file;
        }

        if ($files === [] || $workerCount <= 1 || count($files) < 8) {
            return $this->checkSequential($config, $files);
        }

        return $this->checkParallel($configFile, $files, $workerCount);
    }

    /**
     * @param string[] $files
     */
    protected function checkSequential(Config $config, array $files) : int
    {
        $styler = new Styler($config->format);

        foreach ($files as $file) {
            $source = (string) file_get_contents($file);
            $styled = $styler($source);

            if ($source !== $styled) {
                echo $file . PHP_EOL;
                $this->failure[] = $file;
            }
        }

        return count($files);
    }

    /**
     * @param string[] $files
     */
    protected function checkParallel(
        string $configFile,
        array $files,
        int $workerCount,
    ) : int
    {
        echo "Using {$workerCount} parallel workers." . PHP_EOL;
        $pool = new WorkerPool();
        $results = $pool->run($files, 'check', $configFile, $workerCount);

        foreach ($results as $result) {
            if (! $result->ok) {
                echo $result->file . " ERROR: {$result->error}" . PHP_EOL;
                $this->failure[] = $result->file;
            } elseif ($result->isMatch === false) {
                echo $result->file . PHP_EOL;
                $this->failure[] = $result->file;
            }
        }

        return count($results);
    }
}

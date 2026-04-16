<?php
declare(strict_types=1);

namespace PhpStyler\Command;

use AutoShell\Help;
use PhpStyler\Cache;
use PhpStyler\Config;
use PhpStyler\Exception;
use PhpStyler\Parallel\WorkerPool;
use PhpStyler\Styler;
use Throwable;

#[Help("Checks if any of the configured files need styling.")]
class Check extends ACommand
{
    /**
     * @var string[]
     */
    protected array $failure = [];

    public function __invoke(CheckOptions $options) : int
    {
        $this->failure = [];
        $this->errors = [];
        $start = hrtime(true);

        // load config
        $configFile = $options->configFile ?? $this->findConfigFile();
        echo "Loading config file " . $configFile . PHP_EOL;
        $config = $this->loadConfigFile($configFile);

        // check styling
        $cache = $this->createCache($configFile, $config);

        try {
            $workerCount = $this->resolveWorkerCount($options->workers);

            [
                $count,
                $skipped,
            ] = $this->checkStyle($config, $configFile, $workerCount, $cache);
        } catch (Exception $e) {
            echo $e->getMessage() . PHP_EOL;
            return 1;
        }

        $cache->save();

        // statistics
        $time = (hrtime(true) - $start) / 1000000000;
        $sum = number_format($time, 3);
        $avg = $count ? number_format($time / $count, 4) : 'NAN';
        $mem = number_format(memory_get_peak_usage() / 1000000, 2);

        // report
        $noun = $count === 1 ? 'file' : 'files';
        echo "Checked {$count} {$noun}";

        if ($skipped) {
            echo ", skipped {$skipped} unchanged";
        }

        echo " in {$sum} seconds";

        if ($count) {
            echo " ({$avg} seconds/file, {$mem} MB peak memory usage)";
        }

        echo '.' . PHP_EOL;
        $failed = count($this->failure);

        /** @phpstan-ignore-next-line */
        $phrase = $failed === 1 ? 'file appears' : 'files appear';
        echo "{$failed} {$phrase} to need styling." . PHP_EOL;
        $this->reportErrors();
        return (int) ($this->failure || $this->errors);
    }

    /**
     * @return array{int, int}
     */
    protected function checkStyle(
        Config $config,
        string $configFile,
        int $workerCount,
        Cache $cache,
    ) : array
    {
        $files = [];

        foreach ($config->files as $file) {
            $files[] = (string) $file;
        }

        if ($files === [] || $workerCount <= 1 || count($files) < 8) {
            return $this->checkSequential($config, $files, $cache);
        }

        return $this->checkParallel($configFile, $files, $workerCount, $cache);
    }

    /**
     * @param string[] $files
     * @return array{int, int}
     */
    protected function checkSequential(
        Config $config,
        array $files,
        Cache $cache,
    ) : array
    {
        $styler = new Styler($config->format);
        $skipped = 0;

        foreach ($files as $file) {
            if ($cache->isCurrent($file)) {
                $skipped ++;
                continue;
            }

            try {
                $source = (string) file_get_contents($file);
                $styled = $styler($source);

                if ($source !== $styled) {
                    echo $file . PHP_EOL;
                    $this->failure[] = $file;
                } else {
                    $cache->update($file);
                    $cache->save();
                }
            } catch (Throwable $e) {
                $this->errors[$file] = $e->getMessage();
            }
        }

        return [count($files) - $skipped, $skipped];
    }

    /**
     * @param string[] $files
     * @return array{int, int}
     */
    protected function checkParallel(
        string $configFile,
        array $files,
        int $workerCount,
        Cache $cache,
    ) : array
    {
        $uncached = array_values(
            array_filter($files, fn (string $f) => ! $cache->isCurrent($f)),
        );

        $skipped = count($files) - count($uncached);

        if ($uncached === []) {
            return [0, $skipped];
        }

        echo "Using {$workerCount} parallel workers." . PHP_EOL;
        $pool = new WorkerPool();
        $results = $pool->run($uncached, 'check', $configFile, $workerCount);

        foreach ($results as $result) {
            if (! $result->ok) {
                $this->errors[$result->file] = $result->error ?? 'Unknown error';
            } elseif ($result->isMatch === false) {
                echo $result->file . PHP_EOL;
                $this->failure[] = $result->file;
            } else {
                $cache->update($result->file);
            }
        }

        return [count($results), $skipped];
    }
}

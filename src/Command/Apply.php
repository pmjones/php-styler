<?php
declare(strict_types=1);

namespace PhpStyler\Command;

use AutoShell\Help;
use PhpStyler\Cache;
use PhpStyler\Config;
use PhpStyler\Exception;
use PhpStyler\Files;
use PhpStyler\Parallel\WorkerPool;
use PhpStyler\Styler;
use Throwable;

#[Help("Applies styling to the configured files, rewriting them in place.")]
class Apply extends ACommand
{
    public function __invoke(
        ApplyOptions $options,

        #[Help(
            <<<'HELP'
                Apply styling to these space-separated files and directories;
                overrides the files specified in config.
            HELP,
        )]
        string ...$paths,
    ) : int
    {
        $this->errors = [];
        $start = hrtime(true);

        // load config
        $configFile = $options->configFile ?? $this->findConfigFile();
        echo "Loading config file " . $configFile . PHP_EOL;
        $config = $this->loadConfigFile($configFile);

        // apply styling
        $cache = $this->createCache($configFile, $config);

        try {
            $workerCount = $this->resolveWorkerCount($options->workers);

            [
                $count,
                $skipped,
            ] = $this->applyStyle(
                $config,
                $configFile,
                $paths,
                $workerCount,
                $cache,
            );
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
        echo "Styled {$count} {$noun}";

        if ($skipped) {
            echo ", skipped {$skipped} unchanged";
        }

        echo " in {$sum} seconds";

        if ($count) {
            echo " ({$avg} seconds/file, {$mem} MB peak memory usage)";
        }

        echo '.' . PHP_EOL;
        $this->reportErrors();
        return $this->errors ? 1 : 0;
    }

    /**
     * @param string[] $paths
     * @return array{int, int} [styled count, skipped count]
     */
    protected function applyStyle(
        Config $config,
        string $configFile,
        array $paths,
        int $workerCount,
        Cache $cache,
    ) : array
    {
        $files = $paths ? new Files(...$paths) : $config->files;

        $fileList = $this->collectFiles($files);

        if ($fileList === [] || $workerCount <= 1 || count($fileList) < 8) {
            return $this->applySequential($config, $fileList, $cache);
        }

        return $this->applyParallel($configFile, $fileList, $workerCount, $cache);
    }

    /**
     * @param iterable<mixed> $files
     * @return string[]
     */
    protected function collectFiles(iterable $files) : array
    {
        $collected = [];

        /** @var string $file */
        foreach ($files as $file) {
            $collected[] = (string) $file;
        }

        return $collected;
    }

    /**
     * @param string[] $files
     * @return array{int, int}
     */
    protected function applySequential(
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

            echo $file . PHP_EOL;

            try {
                $code = $styler((string) file_get_contents($file));
                file_put_contents($file, $code);
                $cache->update($file);
                $cache->save();
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
    protected function applyParallel(
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
        $results = $pool->run($uncached, 'apply', $configFile, $workerCount);

        foreach ($results as $result) {
            echo $result->file . PHP_EOL;

            if (! $result->ok) {
                $this->errors[$result->file] = $result->error ?? 'Unknown error';
            } else {
                $cache->update($result->file);
            }
        }

        return [count($results), $skipped];
    }
}

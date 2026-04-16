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

#[Help("Shows a unified diff of source files vs their styled versions.")]
class Diff extends ACommand
{
    protected bool $hasDiff = false;

    public function __invoke(
        DiffOptions $options,

        #[Help(
            <<<'HELP'
                Show diffs for these space-separated files and directories;
                overrides the files specified in config.
            HELP,
        )]
        string ...$paths,
    ) : int
    {
        $this->hasDiff = false;
        $this->errors = [];

        $configFile = $options->configFile ?? $this->findConfigFile();
        $config = $this->loadConfigFile($configFile);

        $cache = $this->createCache($configFile, $config);

        try {
            $workerCount = $this->resolveWorkerCount($options->workers);

            $this->diffStyle($config, $configFile, $paths, $workerCount, $cache);
        } catch (Exception $e) {
            echo $e->getMessage() . PHP_EOL;
            return 1;
        }

        $cache->save();
        $this->reportErrors();
        return (int) ($this->hasDiff || $this->errors);
    }

    /**
     * @param string[] $paths
     */
    protected function diffStyle(
        Config $config,
        string $configFile,
        array $paths,
        int $workerCount,
        Cache $cache,
    ) : void
    {
        if ($paths) {
            $iterableFiles = new Files(...$paths);
        } else {
            $iterableFiles = $config->files;
        }

        $files = [];

        /** @var string $file */
        foreach ($iterableFiles as $file) {
            $files[] = (string) $file;
        }

        if ($files === [] || $workerCount <= 1 || count($files) < 8) {
            $this->diffSequential($config, $files, $cache);
            return;
        }

        $this->diffParallel($configFile, $files, $workerCount, $cache);
    }

    /**
     * @param string[] $files
     */
    protected function diffSequential(
        Config $config,
        array $files,
        Cache $cache,
    ) : void
    {
        $styler = new Styler($config->format);

        foreach ($files as $file) {
            if ($cache->isCurrent($file)) {
                continue;
            }

            try {
                $source = (string) file_get_contents($file);
                $styled = $styler($source);

                if ($source === $styled) {
                    $cache->update($file);
                    $cache->save();
                    continue;
                }

                $this->hasDiff = true;
                $this->showDiff($file, $styled);
            } catch (Throwable $e) {
                $this->errors[$file] = $e->getMessage();
            }
        }
    }

    /**
     * @param string[] $files
     */
    protected function diffParallel(
        string $configFile,
        array $files,
        int $workerCount,
        Cache $cache,
    ) : void
    {
        $uncached = array_values(
            array_filter($files, fn (string $f) => ! $cache->isCurrent($f)),
        );

        if ($uncached === []) {
            return;
        }

        echo "Using {$workerCount} parallel workers." . PHP_EOL;
        $pool = new WorkerPool();
        $results = $pool->run($uncached, 'diff', $configFile, $workerCount);

        foreach ($results as $result) {
            if (! $result->ok) {
                $this->errors[$result->file] = $result->error ?? 'Unknown error';
                continue;
            }

            if ($result->diff !== null && $result->diff !== '') {
                $this->hasDiff = true;
                echo $result->diff;
            } else {
                $cache->update($result->file);
            }
        }
    }

    protected function showDiff(string $file, string $styled) : void
    {
        $tempFile = (string) tempnam(sys_get_temp_dir(), 'php-styler-');

        try {
            file_put_contents($tempFile, $styled);

            $command = sprintf(
                'diff -u %s %s',
                escapeshellarg($file),
                escapeshellarg($tempFile),
            );

            passthru($command);
        } finally {
            unlink($tempFile);
        }
    }
}

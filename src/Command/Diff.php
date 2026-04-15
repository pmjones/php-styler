<?php
declare(strict_types=1);

namespace PhpStyler\Command;

use AutoShell\Help;
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

        try {
            $workerCount = $this->resolveWorkerCount($options->workers);
            $this->diffStyle($config, $configFile, $paths, $workerCount);
        } catch (Exception $e) {
            echo $e->getMessage() . PHP_EOL;
            return 1;
        }

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
            $this->diffSequential($config, $files);
            return;
        }

        $this->diffParallel($configFile, $files, $workerCount);
    }

    /**
     * @param string[] $files
     */
    protected function diffSequential(Config $config, array $files) : void
    {
        $styler = new Styler($config->format);

        foreach ($files as $file) {
            try {
                $source = (string) file_get_contents($file);
                $styled = $styler($source);

                if ($source === $styled) {
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
    ) : void
    {
        echo "Using {$workerCount} parallel workers." . PHP_EOL;
        $pool = new WorkerPool();
        $results = $pool->run($files, 'diff', $configFile, $workerCount);

        foreach ($results as $result) {
            if (! $result->ok) {
                $this->errors[$result->file] = $result->error ?? 'Unknown error';
                continue;
            }

            if ($result->diff !== null && $result->diff !== '') {
                $this->hasDiff = true;
                echo $result->diff;
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

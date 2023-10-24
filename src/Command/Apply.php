<?php
declare(strict_types=1);

namespace PhpStyler\Command;

use AutoShell\Help;
use PhpStyler\Config;
use PhpStyler\Service;
use PhpParser\Error;
use PhpStyler\Files;

#[Help("Applies styling to the configured files, rewriting them in place.")]
class Apply extends Command
{
    public function __invoke(
        ApplyOptions $options,

        #[Help(<<<'HELP'
        Apply styling to these space-separated files and directories;
                overrides the files specified in config.
        HELP)]
        string ...$paths,
    ) : int
    {
        $start = hrtime(true);

        // load config
        $configFile = $options->configFile ?? $this->findConfigFile();
        echo "Loading config file " . $configFile . PHP_EOL;
        $config = $this->loadConfigFile($configFile);

        // load cache time
        $cacheTime = $options->force
            ? 0
            : $this->getCacheTime($configFile, $config->cache);

        // apply styling
        try {
            $count = $this->applyStyle($config, $paths, $cacheTime);
        } catch (Error $e) {
            echo $e->getMessage() . PHP_EOL;
            return 1;
        }

        // update cache time
        if ($config->cache && ! $paths) {
            touch($config->cache);
        }

        // statistics
        $time = (hrtime(true) - $start) / 1000000000;
        $sum = number_format($time, 3);
        $avg = $count ? number_format($time / $count, 4) : 'NAN';
        $mem = number_format(memory_get_peak_usage() / 1000000, 2);

        // report
        $noun = $count === 1 ? 'file' : 'files';
        echo "Styled {$count} {$noun} in {$sum} seconds";

        if ($count) {
            echo " ({$avg} seconds/file, {$mem} MB peak memory usage)";
        }

        echo '.' . PHP_EOL;
        return 0;
    }

    protected function getCacheTime(string $configFile, ?string $cacheFile) : int
    {
        if (! $cacheFile) {
            echo "No cache file specified." . PHP_EOL;
            return 0;
        }

        if (! file_exists($cacheFile)) {
            echo "Creating cache file {$cacheFile}" . PHP_EOL;
            touch($cacheFile);
            return 0;
        }

        echo "Using cache file {$cacheFile}" . PHP_EOL;
        $cacheTime = (int) filemtime($cacheFile);
        $configTime = (int) filemtime($configFile);

        if ($configTime > $cacheTime) {
            echo "Config file modified after last cache time." . PHP_EOL;
            return 0;
        }

        return $cacheTime;
    }

    /**
     * @param string[] $paths
     */
    protected function applyStyle(
        Config $config,
        array $paths,
        int|false $cacheTime,
    ) : int
    {
        $count = 0;
        $service = new Service($config->styler);

        if ($paths) {
            $cacheTime = false;
            $files = new Files(...$paths);
        } else {
            $files = $config->files;
        }

        /** @var string $file */
        foreach ($files as $file) {
            $file = (string) $file;
            $fileTime = filemtime($file);

            if ($cacheTime && $fileTime <= $cacheTime) {
                continue;
            }

            $count ++;
            echo $file . PHP_EOL;
            $code = $service((string) file_get_contents($file));
            file_put_contents($file, $code);
        }

        return $count;
    }
}

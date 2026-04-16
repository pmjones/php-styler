<?php
declare(strict_types=1);

namespace PhpStyler\Command;

use PhpStyler\Cache;
use PhpStyler\Config;
use PhpStyler\Exception;
use PhpStyler\Parallel\WorkerPool;

abstract class ACommand
{
    /** @var array<string, string> file => error message */
    protected array $errors = [];

    protected function reportErrors() : void
    {
        if ($this->errors === []) {
            return;
        }

        $count = count($this->errors);
        $noun = $count === 1 ? 'file' : 'files';
        echo PHP_EOL . "{$count} {$noun} failed:" . PHP_EOL;

        foreach ($this->errors as $file => $error) {
            echo "  {$file}" . PHP_EOL . "    {$error}" . PHP_EOL;
        }
    }

    protected function createCache(string $configFile, Config $config) : Cache
    {
        $cacheFile = $config->cache
            ?? dirname($configFile) . DIRECTORY_SEPARATOR . '.php-styler.cache';

        echo "Using cache file " . $cacheFile . PHP_EOL;
        $cache = new Cache($cacheFile, (string) md5_file($configFile));
        $cache->load();

        return $cache;
    }

    protected function loadConfigFile(string $configFile) : Config
    {
        /** @var Config */
        return require $configFile;
    }

    protected function findConfigFile() : string
    {
        $file = getcwd() . DIRECTORY_SEPARATOR . "php-styler.php";

        if (file_exists($file)) {
            return $file;
        }

        throw new Exception(
            "Could not find {$file}; have you tried `php-styler init`?",
        );
    }

    protected function resolveWorkerCount(?string $workers) : int
    {
        if ($workers === null || $workers === '1') {
            return 1;
        }

        if ($workers === 'auto') {
            return WorkerPool::detectCpuCount();
        }

        $count = (int) $workers;

        return max(1, $count);
    }
}

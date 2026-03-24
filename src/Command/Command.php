<?php
declare(strict_types=1);

namespace PhpStyler\Command;

use PhpStyler\Config;
use PhpStyler\Exception;
use PhpStyler\Parallel\WorkerPool;

abstract class Command
{
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

        throw new Exception("Could not find {$file}");
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

<?php
declare(strict_types=1);

namespace PhpStyler\Command;

use PhpStyler\Config;
use PhpStyler\Exception;
use PhpStyler\Service;

abstract class Command
{
    protected function loadConfigFile(string $configFile) : Config
    {
        /** @var Config */
        return require $configFile;
    }

    protected function findConfigFile() : string
    {
        $file = dirname(__DIR__, 5) . DIRECTORY_SEPARATOR . "php-styler.php";

        if (file_exists($file)) {
            return $file;
        }

        throw new Exception("Could not find {$file}");
    }
}

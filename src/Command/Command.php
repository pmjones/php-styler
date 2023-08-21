<?php
declare(strict_types=1);

namespace PhpStyler\Command;

use PhpStyler\Config;
use PhpStyler\Service;
use PhpStyler\Styler;
use RuntimeException;

abstract class Command
{
    protected function load(string $file) : mixed
    {
        return require $file;
    }

    protected function loadConfigFile(string $configFile) : Config
    {
        /** @var Config */
        return $this->load($configFile);
    }

    protected function findConfigFile() : string
    {
        $file = dirname(__DIR__, 5) . DIRECTORY_SEPARATOR . "php-styler.php";

        if (file_exists($file)) {
            return $file;
        }

        throw new RuntimeException("Could not find {$file}");
    }

    protected function style(
        string $file,
        Styler $styler,
        PreviewOptions $options = null,
    ) : string
    {
        $service = new Service(
            $styler,
            $options?->debugParser ?? false,
            $options?->debugPrinter ?? false,
        );
        $code = (string) file_get_contents($file);
        return $service($code);
    }
}

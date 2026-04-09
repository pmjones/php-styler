<?php
declare(strict_types=1);

namespace PhpStyler\Command;

use AutoShell\Help;

#[Help("Copies a default php-styler.php config file to the current directory.")]
class Init extends ACommand
{
    public function __invoke() : int
    {
        $source = dirname(__DIR__, 2) . '/resources/php-styler.php';
        $target = getcwd() . DIRECTORY_SEPARATOR . 'php-styler.php';

        if (file_exists($target)) {
            echo "Config file already exists: {$target}" . PHP_EOL;
            return 1;
        }

        copy($source, $target);
        echo "Created config file: {$target}" . PHP_EOL;
        return 0;
    }
}

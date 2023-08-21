<?php
declare(strict_types=1);

namespace PhpStyler\Command;

use AutoShell\Help;
use PhpStyler\Config;
use PhpStyler\Service;
use PhpParser\Error as ParserError;

#[Help("Applies styling to the configured files, rewriting them in place.")]
class Apply extends Command
{
    protected int $count = 0;

    public function __invoke(ApplyOptions $options) : int
    {
        $start = hrtime(true);
        $this->count = 0;

        // load config
        $configFile = $options->configFile ?? $this->findConfigFile();
        echo "Load config " . $configFile . PHP_EOL;
        $config = $this->loadConfigFile($configFile);

        // set and apply styling
        $exit = $this->apply($config);

        if ($exit) {
            return $exit;
        }

        // statistics
        $time = (hrtime(true) - $start) / 1000000000;
        $sum = number_format($time, 3);
        $avg = number_format($time / $this->count, 3);

        // report
        echo "Styled {$this->count} files in {$sum} seconds "
            . "({$avg} seconds/file)"
            . PHP_EOL;
        return 0;
    }

    protected function apply(Config $config) : int
    {
        $service = new Service($config->styler);

        foreach ($config->files as $file) {
            $file = (string) $file;
            $this->count ++;
            echo $file . PHP_EOL;

            try {
                $code = $service((string) file_get_contents($file));
            } catch (ParserError $e) {
                echo $e->getMessage() . PHP_EOL;
                return 1;
            }

            file_put_contents($file, $code);
        }

        return 0;
    }
}

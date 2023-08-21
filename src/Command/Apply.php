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
    public function __invoke(ApplyOptions $options) : int
    {
        $start = hrtime(true);

        // load config
        $configFile = $options->configFile ?? $this->findConfigFile();
        echo "Load config " . $configFile . PHP_EOL;
        $config = $this->loadConfigFile($configFile);

        // apply styling
        try {
            $count = $this->style($config);
        } catch (ParserError $e) {
            echo $e->getMessage() . PHP_EOL;
            return 1;
        }

        // statistics
        $time = (hrtime(true) - $start) / 1000000000;
        $sum = number_format($time, 3);
        $avg = $count ? number_format($time / $count, 4) : 'NAN';

        // report
        echo "Styled {$count} files in {$sum} seconds ({$avg} seconds/file)" . PHP_EOL;
        return 0;
    }

    protected function style(Config $config) : int
    {
        $count = 0;
        $service = new Service($config->styler);

        foreach ($config->files as $file) {
            $count ++;
            $file = (string) $file;
            echo $file . PHP_EOL;
            $code = $service((string) file_get_contents($file));
            file_put_contents($file, $code);
        }

        return $count;
    }
}

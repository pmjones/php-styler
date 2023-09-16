<?php
declare(strict_types=1);

namespace PhpStyler\Command;

use AutoShell\Help;
use PhpStyler\Config;
use PhpStyler\Service;
use PhpParser\Error;

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

        // load cache time
        $cacheTime = $options->force ? 0 : filemtime($configFile);

        // apply styling
        try {
            $count = $this->style($config, $cacheTime);
        } catch (Error $e) {
            echo $e->getMessage() . PHP_EOL;
            return 1;
        }

        // update cache time
        touch($configFile);

        // statistics
        $time = (hrtime(true) - $start) / 1000000000;
        $sum = number_format($time, 3);
        $avg = $count ? number_format($time / $count, 4) : 'NAN';
        $mem = number_format(memory_get_peak_usage() / 1000000, 2);

        // report
        $noun = $count === 1 ? 'file' : 'files';
        echo "Styled {$count} {$noun} in {$sum} seconds";

        if ($count) {
            echo " ({$avg} seconds/{$noun}, {$mem} MB peak memory usage)";
        }

        echo '.' . PHP_EOL;
        return 0;
    }

    protected function style(Config $config, int|false $cacheTime) : int
    {
        $count = 0;
        $service = new Service($config->styler);

        foreach ($config->files as $file) {
            $fileTime = filemtime($file);

            if ($cacheTime && $fileTime <= $cacheTime) {
                continue;
            }

            $count ++;
            $file = (string) $file;
            echo $file . PHP_EOL;
            $code = $service((string) file_get_contents($file));
            file_put_contents($file, $code);
        }

        return $count;
    }
}

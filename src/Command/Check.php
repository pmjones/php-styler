<?php
declare(strict_types=1);

namespace PhpStyler\Command;

use AutoShell\Help;
use PhpStyler\Config;
use PhpStyler\Service;
use PhpParser\Error;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\DiffOnlyOutputBuilder;

#[Help("Checks if any of the configured files need styling.")]
class Check extends Command
{
    /**
     * @var string[]
     */
    protected array $failure = [];

    public function __invoke(CheckOptions $options) : int
    {
        $this->failure = [];
        $start = hrtime(true);

        // load config
        $configFile = $options->configFile ?? $this->findConfigFile();
        echo "Loading config file " . $configFile . PHP_EOL;
        $config = $this->loadConfigFile($configFile);

        // apply styling
        try {
            $count = $this->checkStyle($config);
        } catch (Error $e) {
            echo $e->getMessage() . PHP_EOL;
            return 1;
        }

        // statistics
        $time = (hrtime(true) - $start) / 1000000000;
        $sum = number_format($time, 3);
        $avg = $count ? number_format($time / $count, 4) : 'NAN';
        $mem = number_format(memory_get_peak_usage() / 1000000, 2);

        // report
        $noun = $count === 1 ? 'file' : 'files';
        echo "Checked {$count} {$noun} in {$sum} seconds";

        if ($count) {
            echo " ({$avg} seconds/file, {$mem} MB peak memory usage)";
        }

        echo '.' . PHP_EOL;
        $failed = count($this->failure);

        /** @phpstan-ignore-next-line */
        $phrase = $failed === 1 ? 'file needs' : 'files need';
        echo "{$failed} {$phrase} appear to need styling." . PHP_EOL;
        return (int) $this->failure;
    }

    protected function checkStyle(Config $config) : int
    {
        $count = 0;
        $service = new Service($config->styler);
        $differ = new Differ(
            new DiffOnlyOutputBuilder('--- Original' . PHP_EOL . '+++ New' . PHP_EOL),
        );

        foreach ($config->files as $file) {
            $file = (string) $file;
            $count ++;
            $source = (string) file_get_contents($file);
            $styled = $service($source);

            if ($source !== $styled) {
                echo $file . PHP_EOL;
                $this->failure[] = $file;
                echo $differ->diff($source, $styled) . PHP_EOL;
            }
        }

        return $count;
    }
}

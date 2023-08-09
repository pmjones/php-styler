<?php
declare(strict_types=1);

namespace PhpStyler\Command;

use PhpParser\ParserFactory;
use PhpParser\Parser;
use PhpStyler\Config;
use PhpStyler\Printer;
use PhpStyler\Styler;

class Apply extends Command
{
    protected int $count = 0;

    public function __invoke(CommandOptions $options) : int
    {
        $start = microtime(true);
        $this->count = 0;

        // config and cache
        $configFile = $options->configFile ?? $this->findConfigFile();
        echo "Load config " . $configFile . PHP_EOL;
        $config = $this->loadConfigFile($configFile);
        $cache = $this->getCache($config, $configFile);

        // set and apply styling
        $exit = $this->apply($config, $cache['time']);

        if ($exit) {
            return $exit;
        }

        $this->putCache($config);

        // report results
        echo "Styled {$this->count} files in "
            . number_format(microtime(true) - $start, 3)
            . ' seconds.'
            . PHP_EOL
        ;
        return 0;
    }

    /**
     * @return array{time:int}
     */
    protected function getCache(Config $config, string $configFile) : array
    {
        if ($config->cache && file_exists($config->cache)) {
            echo "Load cache " . $config->cache;

            /** @var array{time:int} */
            $cache = $this->load($config->cache);
        } else {
            $cache = ['time' => 0];
        }

        $configTime = filemtime($configFile);

        if ($configTime > $cache['time']) {
            $cache['time'] = 0;
        }

        return $cache;
    }

    protected function putCache(Config $config) : void
    {
        if (! $config->cache) {
            return;
        }

        echo "Save {$config->cache}" . PHP_EOL;
        $data = '<?php return '
            . var_export(['time' => time()], true)
            . ';'
            . PHP_EOL;
        file_put_contents($config->cache, $data);
    }

    protected function apply(Config $config, int $mtime) : int
    {
        foreach ($config->files as $file) {
            $file = (string) $file;

            if (is_dir($file) || filemtime($file) < $mtime) {
                continue;
            }

            $this->count ++;

            if (! $this->lint($file)) {
                return 1;
            }

            $code = $this->style($file, $config->styler);
            file_put_contents($file, $code);
            echo $file . PHP_EOL;
        }

        return 0;
    }
}

<?php
declare(strict_types=1);

namespace PhpStyler\Command;

use PhpParser\ParserFactory;
use PhpParser\Parser;
use PhpStyler\Printer;
use PhpStyler\Styler;

class Apply extends Command
{
    protected int $count = 0;

    public function __invoke(CommandOptions $options) : int
    {
        $start = microtime(true);
        $this->count = 0;
        $configFile = $options->configFile ?? $this->findConfigFile();
        echo "Load config " . $configFile . PHP_EOL;
        $config = $this->load($configFile);
        $cache = $this->getCache($config, $configFile);
        $this->setStyler($config);
        $files = $config['files'] ?? [];
        $exit = $this->apply($files, $cache['time']);

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

    protected function getCache(array $config, string $configFile) : array
    {
        $cacheFile = $config['cache'] ?? false;

        if ($cacheFile && file_exists($cacheFile)) {
            echo "Load cache " . $cacheFile . PHP_EOL;
            $cache = $this->load($cacheFile);
        } else {
            $cache = ['time' => 0];
        }

        $configTime = filemtime($configFile);

        if ($configTime > $cache['time']) {
            $cache['time'] = 0;
        }

        return $cache;
    }

    protected function putCache(array $config) : void
    {
        $cacheFile = $config['cache'] ?? false;

        if (! $cacheFile) {
            return;
        }

        echo "Save {$cacheFile}" . PHP_EOL;
        $data = '<?php return '
            . var_export(['time' => time()], true)
            . ';'
            . PHP_EOL
        ;
        file_put_contents($cacheFile, $data);
    }

    protected function apply(array $files, int $mtime) : int
    {
        foreach ($files as $file) {
            $file = (string) $file;

            if (is_dir($file) || filemtime($file) < $mtime) {
                continue;
            }

            $this->count ++;

            if (! $this->lint($file)) {
                return 1;
            }

            $code = $this->style($file);
            file_put_contents($file, $code);
            echo $file . PHP_EOL;
        }

        return 0;
    }
}

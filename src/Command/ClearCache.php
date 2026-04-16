<?php
declare(strict_types=1);

namespace PhpStyler\Command;

use AutoShell\Help;
use PhpStyler\Exception;

#[Help("Deletes the cache file so all files are re-processed on the next run.")]
class ClearCache extends ACommand
{
    public function __invoke(ClearCacheOptions $options) : int
    {
        try {
            $configFile = $options->configFile ?? $this->findConfigFile();
            $config = $this->loadConfigFile($configFile);
            $cache = $this->createCache($configFile, $config);
            $cache->clear();
            echo "Cache cleared." . PHP_EOL;
            return 0;
        } catch (Exception $e) {
            echo $e->getMessage() . PHP_EOL;
            return 1;
        }
    }
}

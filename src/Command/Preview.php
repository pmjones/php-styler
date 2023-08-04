<?php
declare(strict_types=1);

namespace PhpStyler\Command;

use PhpParser\ParserFactory;
use PhpParser\Parser;
use PhpStyler\Printer;
use PhpStyler\Styler;

class Preview extends Command
{
    public function __invoke(string $configFile, string $sourceFile) : int
    {
        // load config
        $config = $this->load($configFile);
        unset($config['cache']);
        unset($config['files']);

        // get the configured styler object
        $this->styler = $config['styler'] ?? null;
        unset($config['styler']);
        $this->styler ??= new Styler(...$config);

        if (! $this->lint($sourceFile)) {
            exit(1);
        }

        echo $this->style($sourceFile);

        return 0;
    }
}

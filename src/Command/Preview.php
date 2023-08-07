<?php
declare(strict_types=1);

namespace PhpStyler\Command;

use PhpParser\ParserFactory;
use PhpParser\Parser;
use PhpStyler\Printer;
use PhpStyler\Styler;

class Preview extends Command
{
    public function __invoke(CommandOptions $options, string $sourceFile) : int
    {
        $configFile = $options->configFile ?? $this->findConfigFile();
        $config = $this->loadConfigFile($configFile);
        $this->setStyler($config);

        if (! $this->lint($sourceFile)) {
            return 1;
        }

        echo $this->style($sourceFile);
        return 0;
    }
}

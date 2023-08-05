<?php
declare(strict_types=1);

namespace PhpStyler\Command;

use PhpParser\ParserFactory;
use PhpParser\Parser;
use PhpStyler\Printer;
use PhpStyler\Styler;

class Preview extends Command
{
    public function __invoke(string $sourceFile) : int
    {
        $configFile = $this->findConfig();
        $config = $this->load($configFile);
        $this->setStyler($config);

        if (! $this->lint($sourceFile)) {
            exit(1);
        }

        echo $this->style($sourceFile);

        return 0;
    }
}

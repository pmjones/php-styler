<?php
declare(strict_types=1);

namespace PhpStyler\Command;

use AutoShell\Help;
use PhpParser\ParserFactory;
use PhpParser\Parser;
use PhpStyler\Printer;
use PhpStyler\Styler;
#[Help("Prints a preview of a styled source file.")]
class Preview extends Command
{
    public function __invoke(
        PreviewOptions $options,
        #[Help("The source file to preview.")] string $sourceFile,
    ) : int
    {
        $configFile = $options->configFile ?? $this->findConfigFile();
        $config = $this->loadConfigFile($configFile);

        if (! $this->lint($sourceFile)) {
            return 1;
        }

        echo $this->style($sourceFile, $config->styler);
        return 0;
    }
}

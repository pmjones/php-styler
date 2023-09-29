<?php
declare(strict_types=1);

namespace PhpStyler\Command;

use AutoShell\Help;
use PhpParser\ParserFactory;
use PhpParser\Parser;
use PhpStyler\Service;
use PhpStyler\Styler;

#[Help("Prints a preview of a styled source file.")]
class Preview extends Command
{
    public function __invoke(
        PreviewOptions $options,

        #[Help("The source file to preview.")]
        string $sourceFile,
    ) : int
    {
        $configFile = $options->configFile ?? $this->findConfigFile();
        $config = $this->loadConfigFile($configFile);
        $service = new Service(
            $config->styler,
            $options->debugParser ?? false,
            $options->debugPrinter ?? false,
            $options->debugStyler ?? false,
        );
        echo $service((string) file_get_contents($sourceFile));
        return 0;
    }
}

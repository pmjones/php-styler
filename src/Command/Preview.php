<?php
declare(strict_types=1);

namespace PhpStyler\Command;

use AutoShell\Help;
use PhpStyler\Styler;

#[Help("Prints a preview of a styled source file.")]
class Preview extends ACommand
{
    public function __invoke(
        PreviewOptions $options,

        #[Help("The source file to preview.")]
        string $sourceFile,
    ) : int
    {
        $configFile = $options->configFile ?? $this->findConfigFile();
        $config = $this->loadConfigFile($configFile);

        $styler = new Styler($config->format);
        echo $styler((string) file_get_contents($sourceFile));
        return 0;
    }
}

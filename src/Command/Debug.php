<?php
declare(strict_types=1);

namespace PhpStyler\Command;

use AutoShell\Help;
use PhpStyler\Styler;

#[Help("Debug tool.")]
class Debug extends Command
{
    public function __invoke(
        DebugOptions $options,

        #[Help("The source file to debug.")]
        string $sourceFile,
    ) : int
    {
        $configFile = $options->configFile ?? $this->findConfigFile();
        $config = $this->loadConfigFile($configFile);

        $styler = Styler::fromConfig($config);

        $code = (string) file_get_contents($sourceFile);
        $tokens = $styler->parse($code);

        if (
            ! $options->parse
            && ! $options->assemble
            && ! $options->split
        ) {
            var_export($tokens);
            return 0;
        }

        if ($options->parse) {
            var_export($tokens);
        }

        $lines = $styler->assemble($tokens);

        if ($options->assemble) {
            var_export($lines);
        }

        $lines = $styler->split($lines);

        if ($options->split) {
            var_export($lines);
        }

        return 0;
    }
}

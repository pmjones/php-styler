<?php
declare(strict_types=1);

namespace PhpStyler\Command;

use AutoShell\Help;
use PhpStyler\Exception;
use PhpStyler\Styler;

#[Help("Runs the styler on a single file and shows detailed error diagnostics.")]
class Debug extends ACommand
{
    public function __invoke(
        DebugOptions $options,

        #[Help("The file to debug.")]
        string $sourceFile,
    ) : int
    {
        $configFile = $options->configFile ?? $this->findConfigFile();
        $config = $this->loadConfigFile($configFile);
        $styler = new Styler($config->format);

        $source = (string) file_get_contents($sourceFile);

        try {
            $styler($source);
            echo "No styling errors in {$sourceFile}" . PHP_EOL;
            return 0;
        } catch (Exception $e) {
            $this->renderError($sourceFile, $source, $e);
            return 1;
        }
    }

    private function renderError(string $file, string $source, Exception $e) : void
    {
        echo "Styling error in {$file}:" . PHP_EOL;
        echo PHP_EOL;
        echo "  {$e->getMessage()}" . PHP_EOL;

        if ($e->debug === []) {
            return;
        }

        $line = $e->debug['line'] ?? null;

        if ($line !== null) {
            echo PHP_EOL;
            $this->renderSourceContext($source, $line);
        }

        $current = $e->debug['currentTokenText'] ?? null;
        $name = $e->debug['currentTokenName'] ?? null;

        if ($current !== null) {
            echo PHP_EOL;
            echo "  Current token:" . PHP_EOL;
            echo "    " . json_encode($current);

            if ($name !== null) {
                echo " ({$name})";
            }

            echo PHP_EOL;
        }

        $before = $e->debug['recentSourceText'] ?? null;

        if ($before !== null && $before !== '') {
            echo PHP_EOL;
            echo "  Source text before error:" . PHP_EOL;
            echo "    " . json_encode($before) . PHP_EOL;
        }

        $after = $e->debug['upcomingSourceText'] ?? null;

        if ($after !== null && $after !== '') {
            echo PHP_EOL;
            echo "  Source text after error:" . PHP_EOL;
            echo "    " . json_encode($after) . PHP_EOL;
        }
    }

    private function renderSourceContext(string $source, int $errorLine) : void
    {
        $lines = explode("\n", $source);
        $start = max(0, $errorLine - 3);
        $end = min(count($lines) - 1, $errorLine + 1);
        $gutterWidth = strlen((string) ($end + 1));

        for ($i = $start; $i <= $end; $i ++) {
            $lineNum = str_pad((string) ($i + 1), $gutterWidth, ' ', STR_PAD_LEFT);
            $marker = ($i + 1 === $errorLine) ? ' > ' : ' | ';
            echo "  {$lineNum}{$marker}{$lines[$i]}" . PHP_EOL;
        }
    }
}

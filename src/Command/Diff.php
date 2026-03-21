<?php
declare(strict_types=1);

namespace PhpStyler\Command;

use AutoShell\Help;
use PhpStyler\Config;
use PhpStyler\Exception;
use PhpStyler\Files;
use PhpStyler\Styler;

#[Help("Shows a unified diff of source files vs their styled versions.")]
class Diff extends Command
{
    protected bool $hasDiff = false;

    public function __invoke(
        DiffOptions $options,

        #[Help(
            <<<'HELP'
                Show diffs for these space-separated files and directories;
                overrides the files specified in config.
            HELP,
        )]
        string ...$paths,
    ) : int
    {
        $this->hasDiff = false;

        $configFile = $options->configFile ?? $this->findConfigFile();
        $config = $this->loadConfigFile($configFile);

        try {
            $this->diffStyle($config, $paths);
        } catch (Exception $e) {
            echo $e->getMessage() . PHP_EOL;
            return 1;
        }

        return (int) $this->hasDiff;
    }

    /**
     * @param string[] $paths
     */
    protected function diffStyle(Config $config, array $paths) : void
    {
        $styler = new Styler($config->format);

        if ($paths) {
            $files = new Files(...$paths);
        } else {
            $files = $config->files;
        }

        /** @var string $file */
        foreach ($files as $file) {
            $file = (string) $file;
            $source = (string) file_get_contents($file);
            $styled = $styler($source);

            if ($source === $styled) {
                continue;
            }

            $this->hasDiff = true;
            $this->showDiff($file, $styled);
        }
    }

    protected function showDiff(string $file, string $styled) : void
    {
        $tempFile = (string) tempnam(sys_get_temp_dir(), 'php-styler-');

        try {
            file_put_contents($tempFile, $styled);
            $command = sprintf(
                'diff -u %s %s',
                escapeshellarg($file),
                escapeshellarg($tempFile),
            );
            passthru($command);
        } finally {
            unlink($tempFile);
        }
    }
}

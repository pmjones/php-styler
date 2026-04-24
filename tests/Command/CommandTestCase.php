<?php
declare(strict_types=1);

namespace PhpStyler\Command;

use PHPUnit\Framework\TestCase;

abstract class CommandTestCase extends TestCase
{
    protected string $tmpDir;

    protected string $origCwd;

    protected function setUp() : void
    {
        $this->origCwd = (string) getcwd();

        $base = sys_get_temp_dir() . DIRECTORY_SEPARATOR
            . 'php-styler-test-' . bin2hex(random_bytes(6));

        mkdir($base);
        $this->tmpDir = $base;
        chdir($base);
    }

    protected function tearDown() : void
    {
        chdir($this->origCwd);
        $this->rrmdir($this->tmpDir);
    }

    protected function rrmdir(string $dir) : void
    {
        if (! is_dir($dir)) {
            return;
        }

        $entries = scandir($dir) ?: [];

        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $path = $dir . DIRECTORY_SEPARATOR . $entry;

            if (is_dir($path)) {
                $this->rrmdir($path);
            } else {
                @unlink($path);
            }
        }

        @rmdir($dir);
    }

    /**
     * Writes a `php-styler.php` config in $tmpDir pointing at the given dir
     * for Files(). Returns the config file path.
     */
    protected function writeConfig(string $filesPath) : string
    {
        $configFile = $this->tmpDir . DIRECTORY_SEPARATOR . 'php-styler.php';
        $quoted = var_export($filesPath, true);

        $contents = <<<PHP
        <?php
        use PhpStyler\\Config;
        use PhpStyler\\Files;
        use PhpStyler\\Format\\DeclarationFormat;

        return new Config(
            files: new Files({$quoted}),
            format: new DeclarationFormat(),
            cache: null,
        );

        PHP;

        file_put_contents($configFile, $contents);
        return $configFile;
    }

    protected function writeSource(string $name, string $contents) : string
    {
        $path = $this->tmpDir . DIRECTORY_SEPARATOR . $name;
        file_put_contents($path, $contents);
        return $path;
    }

    /**
     * Captures stdout from the callable.
     */
    protected function captureOutput(callable $fn) : string
    {
        ob_start();

        try {
            $fn();
        } finally {
            $out = (string) ob_get_clean();
        }

        return $out;
    }
}

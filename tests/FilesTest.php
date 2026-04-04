<?php
declare(strict_types=1);

namespace PhpStyler;

class FilesTest extends TestCase
{
    public function testDirs() : void
    {
        $dir = dirname(__DIR__) . '/src/Command/';
        $len = strlen($dir);
        $files = new Files($dir);
        $actual = [];

        /** @var string $file */
        foreach ($files as $file) {
            $actual[] = substr($file, $len);
        }

        sort($actual);
        $this->assertSame($this->getExpect(), $actual);
    }

    public function testFile() : void
    {
        $dir = dirname(__DIR__) . '/';
        $len = strlen($dir);
        $files = new Files($dir . 'php-styler.php');
        $actual = [];

        /** @var string $file */
        foreach ($files as $file) {
            $actual[] = substr($file, $len);
        }

        sort($actual);
        $this->assertSame(['php-styler.php'], $actual);
    }

    /**
     * @return string[]
     */
    protected function getExpect() : array
    {
        $expect = [
            'ACommand.php',
            'Apply.php',
            'ApplyOptions.php',
            'Check.php',
            'CheckOptions.php',
            'Diff.php',
            'DiffOptions.php',
            'Preview.php',
            'PreviewOptions.php',
            'Worker.php',
            'WorkerOptions.php',
        ];

        foreach ($expect as $key => $val) {
            $expect[$key] = str_replace('/', DIRECTORY_SEPARATOR, $val);
        }

        return $expect;
    }
}

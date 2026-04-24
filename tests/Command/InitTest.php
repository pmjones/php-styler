<?php
declare(strict_types=1);

namespace PhpStyler\Command;

class InitTest extends CommandTestCase
{
    public function testCreatesConfigInEmptyDir() : void
    {
        $cmd = new Init();

        $out = $this->captureOutput(fn () => $cmd());

        $target = $this->tmpDir . DIRECTORY_SEPARATOR . 'php-styler.php';
        $this->assertFileExists($target);
        $this->assertStringContainsString('Created config file', $out);
        $this->assertStringContainsString($target, $out);
    }

    public function testReturnsZeroWhenCreated() : void
    {
        $cmd = new Init();
        ob_start();
        $exit = $cmd();
        ob_end_clean();
        $this->assertSame(0, $exit);
    }

    public function testReturnsOneAndPrintsWhenConfigAlreadyExists() : void
    {
        $target = $this->tmpDir . DIRECTORY_SEPARATOR . 'php-styler.php';
        file_put_contents($target, '<?php // pre-existing');

        $cmd = new Init();
        ob_start();
        $exit = $cmd();
        $out = (string) ob_get_clean();

        $this->assertSame(1, $exit);
        $this->assertStringContainsString('already exists', $out);
        $this->assertStringContainsString($target, $out);
    }

    public function testCopiedConfigMatchesTemplate() : void
    {
        $template = file_get_contents(
            dirname(__DIR__, 2) . '/resources/php-styler.php',
        );

        $cmd = new Init();
        ob_start();
        $cmd();
        ob_end_clean();

        $actual = file_get_contents(
            $this->tmpDir . DIRECTORY_SEPARATOR . 'php-styler.php',
        );
        $this->assertSame($template, $actual);
    }
}

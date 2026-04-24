<?php
declare(strict_types=1);

namespace PhpStyler\Command;

class DebugTest extends CommandTestCase
{
    public function testReturnsZeroForValidSource() : void
    {
        $configFile = $this->writeConfig($this->tmpDir);
        $source = $this->writeSource('ok.php', "<?php echo 1;\n");

        $cmd = new Debug();
        $options = new DebugOptions(configFile: $configFile);

        ob_start();
        $exit = $cmd($options, $source);
        $out = (string) ob_get_clean();

        $this->assertSame(0, $exit);
        $this->assertStringContainsString('No styling errors', $out);
    }

    public function testReturnsOneForInvalidSource() : void
    {
        $configFile = $this->writeConfig($this->tmpDir);
        $source = $this->writeSource('bad.php', "<?php : echo 1;");

        $cmd = new Debug();
        $options = new DebugOptions(configFile: $configFile);

        ob_start();
        $exit = $cmd($options, $source);
        $out = (string) ob_get_clean();

        $this->assertSame(1, $exit);
        $this->assertStringContainsString('Styling error', $out);
        $this->assertStringContainsString('Unknown kind of colon', $out);
    }

    public function testRendersSourceContextAroundError() : void
    {
        $configFile = $this->writeConfig($this->tmpDir);
        $contents = "<?php\necho 1;\n:\necho 2;\n";
        $source = $this->writeSource('context.php', $contents);

        $cmd = new Debug();
        $options = new DebugOptions(configFile: $configFile);

        ob_start();
        $exit = $cmd($options, $source);
        $out = (string) ob_get_clean();

        $this->assertSame(1, $exit);
        $this->assertStringContainsString('Current token:', $out);
        $this->assertStringContainsString('Source text before error:', $out);
        $this->assertMatchesRegularExpression('/\d+\s+>\s/', $out);
    }
}

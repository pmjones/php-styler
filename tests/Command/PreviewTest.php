<?php
declare(strict_types=1);

namespace PhpStyler\Command;

class PreviewTest extends CommandTestCase
{
    public function testPrintsStyledSource() : void
    {
        $configFile = $this->writeConfig($this->tmpDir);
        $source = $this->writeSource(
            'sample.php',
            "<?php if(\$x){echo 'hi';}\n",
        );

        $cmd = new Preview();
        $options = new PreviewOptions(configFile: $configFile);

        ob_start();
        $exit = $cmd($options, $source);
        $out = (string) ob_get_clean();

        $this->assertSame(0, $exit);
        $this->assertStringContainsString("if (\$x) {", $out);
        $this->assertStringContainsString("echo 'hi';", $out);
    }

    public function testUsesConfigFromCwdWhenNotProvided() : void
    {
        $this->writeConfig($this->tmpDir);
        $source = $this->writeSource('a.php', "<?php echo 1;\n");

        $cmd = new Preview();
        $options = new PreviewOptions(configFile: null);

        ob_start();
        $exit = $cmd($options, $source);
        $out = (string) ob_get_clean();

        $this->assertSame(0, $exit);
        $this->assertStringContainsString('echo 1;', $out);
    }
}

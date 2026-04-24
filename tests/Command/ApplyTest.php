<?php
declare(strict_types=1);

namespace PhpStyler\Command;

class ApplyTest extends CommandTestCase
{
    public function testRewritesUnstyledFile() : void
    {
        mkdir($this->tmpDir . '/src');
        $configFile = $this->writeConfig($this->tmpDir . '/src');
        $target = $this->tmpDir . '/src/a.php';
        file_put_contents($target, "<?php if(\$x){echo 1;}");

        $cmd = new Apply();
        $options = new ApplyOptions(configFile: $configFile, workers: null);

        ob_start();
        $exit = $cmd($options);
        $out = (string) ob_get_clean();

        $this->assertSame(0, $exit);
        $this->assertStringContainsString('Styled 1 file', $out);
        $contents = (string) file_get_contents($target);
        $this->assertStringContainsString("if (\$x) {", $contents);
        $this->assertStringContainsString("echo 1;", $contents);
    }

    public function testSecondRunIsNoOpViaCache() : void
    {
        mkdir($this->tmpDir . '/src');
        $configFile = $this->writeConfig($this->tmpDir . '/src');
        file_put_contents($this->tmpDir . '/src/a.php', "<?php\necho 1;\n");

        $cmd = new Apply();
        $options = new ApplyOptions(configFile: $configFile, workers: null);

        // prime cache
        ob_start();
        $cmd($options);
        ob_end_clean();

        // second run
        ob_start();
        $exit = $cmd($options);
        $out = (string) ob_get_clean();

        $this->assertSame(0, $exit);
        $this->assertStringContainsString('skipped 1 unchanged', $out);
    }

    public function testAppliesToExplicitPaths() : void
    {
        $target = $this->tmpDir . '/solo.php';
        file_put_contents($target, "<?php echo 1;");

        $configFile = $this->writeConfig($this->tmpDir);

        $cmd = new Apply();
        $options = new ApplyOptions(configFile: $configFile, workers: null);

        ob_start();
        $exit = $cmd($options, $target);
        $out = (string) ob_get_clean();

        $this->assertSame(0, $exit);
        $this->assertStringContainsString($target, $out);
    }

    public function testReportsErrorsForInvalidFiles() : void
    {
        mkdir($this->tmpDir . '/src');
        $configFile = $this->writeConfig($this->tmpDir . '/src');
        file_put_contents($this->tmpDir . '/src/bad.php', "<?php : echo 1;");

        $cmd = new Apply();
        $options = new ApplyOptions(configFile: $configFile, workers: null);

        ob_start();
        $exit = $cmd($options);
        $out = (string) ob_get_clean();

        $this->assertSame(1, $exit);
        $this->assertStringContainsString('1 file failed', $out);
    }

    public function testParallelWorkersStyleMultipleFiles() : void
    {
        mkdir($this->tmpDir . '/src');
        $configFile = $this->writeConfig($this->tmpDir . '/src');

        $targets = [];

        for ($i = 0; $i < 10; $i ++) {
            $path = $this->tmpDir . "/src/u{$i}.php";
            file_put_contents($path, "<?php if(\$x){echo {$i};}");
            $targets[] = $path;
        }

        $cmd = new Apply();
        $options = new ApplyOptions(configFile: $configFile, workers: '2');

        ob_start();
        $exit = $cmd($options);
        $out = (string) ob_get_clean();

        $this->assertSame(0, $exit);
        $this->assertStringContainsString('Using 2 parallel workers', $out);
        $this->assertStringContainsString('Styled 10 files', $out);

        foreach ($targets as $t) {
            $this->assertStringContainsString(
                "if (\$x) {",
                (string) file_get_contents($t),
            );
        }
    }

    public function testParallelWorkersAllCached() : void
    {
        mkdir($this->tmpDir . '/src');
        $configFile = $this->writeConfig($this->tmpDir . '/src');

        for ($i = 0; $i < 8; $i ++) {
            file_put_contents(
                $this->tmpDir . "/src/f{$i}.php",
                "<?php\necho {$i};\n",
            );
        }

        $cmd = new Apply();

        // prime cache via sequential run
        $seq = new ApplyOptions(configFile: $configFile, workers: '1');
        ob_start();
        $cmd($seq);
        ob_end_clean();

        // parallel run now finds all cached
        $par = new ApplyOptions(configFile: $configFile, workers: '2');
        ob_start();
        $exit = $cmd($par);
        $out = (string) ob_get_clean();

        $this->assertSame(0, $exit);
        $this->assertStringContainsString('skipped 8 unchanged', $out);
    }

    public function testParallelReportsWorkerErrors() : void
    {
        mkdir($this->tmpDir . '/src');
        $configFile = $this->writeConfig($this->tmpDir . '/src');

        for ($i = 0; $i < 7; $i ++) {
            file_put_contents(
                $this->tmpDir . "/src/ok{$i}.php",
                "<?php\necho {$i};\n",
            );
        }

        file_put_contents($this->tmpDir . '/src/bad.php', "<?php : echo 1;");

        $cmd = new Apply();
        $options = new ApplyOptions(configFile: $configFile, workers: '2');

        ob_start();
        $exit = $cmd($options);
        $out = (string) ob_get_clean();

        $this->assertSame(1, $exit);
        $this->assertStringContainsString('1 file failed', $out);
    }

    public function testEmptyFileSet() : void
    {
        mkdir($this->tmpDir . '/empty');
        $configFile = $this->writeConfig($this->tmpDir . '/empty');

        $cmd = new Apply();
        $options = new ApplyOptions(configFile: $configFile, workers: null);

        ob_start();
        $exit = $cmd($options);
        $out = (string) ob_get_clean();

        $this->assertSame(0, $exit);
        $this->assertStringContainsString('Styled 0 files', $out);
    }
}

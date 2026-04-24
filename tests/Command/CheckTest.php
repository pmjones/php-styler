<?php
declare(strict_types=1);

namespace PhpStyler\Command;

class CheckTest extends CommandTestCase
{
    public function testExitsZeroWhenAllFilesClean() : void
    {
        mkdir($this->tmpDir . '/src');
        $configFile = $this->writeConfig($this->tmpDir . '/src');
        file_put_contents(
            $this->tmpDir . '/src/a.php',
            "<?php\necho 1;\n",
        );

        $cmd = new Check();
        $options = new CheckOptions(configFile: $configFile, workers: null);

        ob_start();
        $exit = $cmd($options);
        $out = (string) ob_get_clean();

        $this->assertSame(0, $exit);
        $this->assertStringContainsString('Checked 1 file', $out);
        $this->assertStringContainsString('0 files appear to need styling', $out);
    }

    public function testExitsOneWhenFilesNeedStyling() : void
    {
        mkdir($this->tmpDir . '/src');
        $configFile = $this->writeConfig($this->tmpDir . '/src');
        $unstyled = $this->tmpDir . '/src/u.php';
        file_put_contents($unstyled, "<?php if(\$x){echo 1;}");

        $cmd = new Check();
        $options = new CheckOptions(configFile: $configFile, workers: null);

        ob_start();
        $exit = $cmd($options);
        $out = (string) ob_get_clean();

        $this->assertSame(1, $exit);
        $this->assertStringContainsString($unstyled, $out);
        $this->assertStringContainsString('1 file appears to need styling', $out);
    }

    public function testReportsErrorsForInvalidFiles() : void
    {
        mkdir($this->tmpDir . '/src');
        $configFile = $this->writeConfig($this->tmpDir . '/src');
        file_put_contents($this->tmpDir . '/src/bad.php', "<?php : echo 1;");

        $cmd = new Check();
        $options = new CheckOptions(configFile: $configFile, workers: null);

        ob_start();
        $exit = $cmd($options);
        $out = (string) ob_get_clean();

        $this->assertSame(1, $exit);
        $this->assertStringContainsString('1 file failed', $out);
        $this->assertStringContainsString('Unknown kind of colon', $out);
    }

    public function testSkipsCachedFiles() : void
    {
        mkdir($this->tmpDir . '/src');
        $configFile = $this->writeConfig($this->tmpDir . '/src');
        file_put_contents($this->tmpDir . '/src/a.php', "<?php\necho 1;\n");

        $cmd = new Check();
        $options = new CheckOptions(configFile: $configFile, workers: null);

        // first run: populates cache
        ob_start();
        $cmd($options);
        ob_end_clean();

        // second run: should skip cached
        ob_start();
        $exit = $cmd($options);
        $out = (string) ob_get_clean();

        $this->assertSame(0, $exit);
        $this->assertStringContainsString('skipped 1 unchanged', $out);
    }

    public function testWorkersOneIsSequential() : void
    {
        mkdir($this->tmpDir . '/src');
        $configFile = $this->writeConfig($this->tmpDir . '/src');
        file_put_contents($this->tmpDir . '/src/a.php', "<?php\necho 1;\n");

        $cmd = new Check();
        $options = new CheckOptions(configFile: $configFile, workers: '1');

        ob_start();
        $exit = $cmd($options);
        $out = (string) ob_get_clean();

        $this->assertSame(0, $exit);
        $this->assertStringNotContainsString('parallel workers', $out);
    }

    public function testParallelWorkersOverEightFiles() : void
    {
        mkdir($this->tmpDir . '/src');
        $configFile = $this->writeConfig($this->tmpDir . '/src');

        for ($i = 0; $i < 10; $i ++) {
            file_put_contents(
                $this->tmpDir . "/src/f{$i}.php",
                "<?php\necho {$i};\n",
            );
        }

        $cmd = new Check();
        $options = new CheckOptions(configFile: $configFile, workers: '2');

        ob_start();
        $exit = $cmd($options);
        $out = (string) ob_get_clean();

        $this->assertSame(0, $exit);
        $this->assertStringContainsString('Using 2 parallel workers', $out);
        $this->assertStringContainsString('Checked 10 files', $out);
    }

    public function testParallelWorkersMixedStyledAndUnstyled() : void
    {
        mkdir($this->tmpDir . '/src');
        $configFile = $this->writeConfig($this->tmpDir . '/src');

        for ($i = 0; $i < 8; $i ++) {
            file_put_contents(
                $this->tmpDir . "/src/ok{$i}.php",
                "<?php\necho {$i};\n",
            );
        }

        file_put_contents(
            $this->tmpDir . '/src/bad.php',
            "<?php if(\$x){echo 1;}",
        );

        $cmd = new Check();
        $options = new CheckOptions(configFile: $configFile, workers: '2');

        ob_start();
        $exit = $cmd($options);
        $out = (string) ob_get_clean();

        $this->assertSame(1, $exit);
        $this->assertStringContainsString('bad.php', $out);
        $this->assertStringContainsString('1 file appears to need styling', $out);
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

        $cmd = new Check();
        $options = new CheckOptions(configFile: $configFile, workers: '2');

        // prime the cache via a sequential run first
        $seq = new CheckOptions(configFile: $configFile, workers: '1');
        ob_start();
        $cmd($seq);
        ob_end_clean();

        // parallel run should find everything cached
        ob_start();
        $exit = $cmd($options);
        $out = (string) ob_get_clean();

        $this->assertSame(0, $exit);
        $this->assertStringContainsString('skipped 8 unchanged', $out);
    }

    public function testAutoWorkerCount() : void
    {
        mkdir($this->tmpDir . '/src');
        $configFile = $this->writeConfig($this->tmpDir . '/src');
        file_put_contents($this->tmpDir . '/src/a.php', "<?php\necho 1;\n");

        $cmd = new Check();
        $options = new CheckOptions(configFile: $configFile, workers: 'auto');

        ob_start();
        $exit = $cmd($options);
        ob_end_clean();

        $this->assertSame(0, $exit);
    }

    public function testThrowsWhenConfigMissing() : void
    {
        $cmd = new Check();
        $options = new CheckOptions(configFile: null, workers: null);

        $this->expectException(\PhpStyler\Exception::class);
        $this->expectExceptionMessage('Could not find');

        try {
            ob_start();
            $cmd($options);
        } finally {
            ob_end_clean();
        }
    }
}

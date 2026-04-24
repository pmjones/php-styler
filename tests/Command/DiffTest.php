<?php
declare(strict_types=1);

namespace PhpStyler\Command;

class DiffTest extends CommandTestCase
{
    protected function setUp() : void
    {
        parent::setUp();

        exec('command -v diff', $out, $exit);

        if ($exit !== 0) {
            $this->markTestSkipped('diff binary not available');
        }
    }

    public function testExitsZeroWhenNoDiff() : void
    {
        mkdir($this->tmpDir . '/src');
        $configFile = $this->writeConfig($this->tmpDir . '/src');
        file_put_contents($this->tmpDir . '/src/a.php', "<?php\necho 1;\n");

        $cmd = new Diff();
        $options = new DiffOptions(configFile: $configFile, workers: null);

        ob_start();
        $exit = $cmd($options);
        ob_end_clean();

        $this->assertSame(0, $exit);
    }

    public function testExitsOneAndPrintsUnifiedDiff() : void
    {
        mkdir($this->tmpDir . '/src');
        $configFile = $this->writeConfig($this->tmpDir . '/src');
        file_put_contents(
            $this->tmpDir . '/src/u.php',
            "<?php if(\$x){echo 1;}",
        );

        $cmd = new Diff();
        $options = new DiffOptions(configFile: $configFile, workers: null);

        ob_start();
        $exit = $cmd($options);
        $out = (string) ob_get_clean();

        $this->assertSame(1, $exit);
        $this->assertStringContainsString('@@', $out);
    }

    public function testReportsErrorsForInvalidFiles() : void
    {
        mkdir($this->tmpDir . '/src');
        $configFile = $this->writeConfig($this->tmpDir . '/src');
        file_put_contents($this->tmpDir . '/src/bad.php', "<?php : echo 1;");

        $cmd = new Diff();
        $options = new DiffOptions(configFile: $configFile, workers: null);

        ob_start();
        $exit = $cmd($options);
        $out = (string) ob_get_clean();

        $this->assertSame(1, $exit);
        $this->assertStringContainsString('1 file failed', $out);
    }

    public function testParallelWorkersNoDiffs() : void
    {
        mkdir($this->tmpDir . '/src');
        $configFile = $this->writeConfig($this->tmpDir . '/src');

        for ($i = 0; $i < 10; $i ++) {
            file_put_contents(
                $this->tmpDir . "/src/f{$i}.php",
                "<?php\necho {$i};\n",
            );
        }

        $cmd = new Diff();
        $options = new DiffOptions(configFile: $configFile, workers: '2');

        ob_start();
        $exit = $cmd($options);
        $out = (string) ob_get_clean();

        $this->assertSame(0, $exit);
        $this->assertStringContainsString('Using 2 parallel workers', $out);
    }

    public function testParallelWorkersWithDiffs() : void
    {
        mkdir($this->tmpDir . '/src');
        $configFile = $this->writeConfig($this->tmpDir . '/src');

        for ($i = 0; $i < 10; $i ++) {
            file_put_contents(
                $this->tmpDir . "/src/u{$i}.php",
                "<?php if(\$x){echo {$i};}",
            );
        }

        $cmd = new Diff();
        $options = new DiffOptions(configFile: $configFile, workers: '2');

        ob_start();
        $exit = $cmd($options);
        $out = (string) ob_get_clean();

        $this->assertSame(1, $exit);
        $this->assertStringContainsString('@@', $out);
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

        $cmd = new Diff();

        $seq = new DiffOptions(configFile: $configFile, workers: '1');
        ob_start();
        $cmd($seq);
        ob_end_clean();

        $par = new DiffOptions(configFile: $configFile, workers: '2');
        ob_start();
        $exit = $cmd($par);
        ob_end_clean();

        $this->assertSame(0, $exit);
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

        $cmd = new Diff();
        $options = new DiffOptions(configFile: $configFile, workers: '2');

        ob_start();
        $exit = $cmd($options);
        $out = (string) ob_get_clean();

        $this->assertSame(1, $exit);
        $this->assertStringContainsString('1 file failed', $out);
    }

    public function testAcceptsExplicitPaths() : void
    {
        $target = $this->tmpDir . '/solo.php';
        file_put_contents($target, "<?php\necho 1;\n");

        $configFile = $this->writeConfig($this->tmpDir);

        $cmd = new Diff();
        $options = new DiffOptions(configFile: $configFile, workers: null);

        ob_start();
        $exit = $cmd($options, $target);
        ob_end_clean();

        $this->assertSame(0, $exit);
    }

    public function testCachedFilesAreSkipped() : void
    {
        mkdir($this->tmpDir . '/src');
        $configFile = $this->writeConfig($this->tmpDir . '/src');
        file_put_contents($this->tmpDir . '/src/a.php', "<?php\necho 1;\n");

        // prime the cache via Apply
        $apply = new Apply();
        ob_start();
        $apply(new ApplyOptions(configFile: $configFile, workers: null));
        ob_end_clean();

        // now run Diff — file is cached, so inner loop `continue` fires
        $cmd = new Diff();
        $options = new DiffOptions(configFile: $configFile, workers: null);

        ob_start();
        $exit = $cmd($options);
        $out = (string) ob_get_clean();

        $this->assertSame(0, $exit);
        $this->assertStringNotContainsString('@@', $out);
    }

    public function testExceptionFromDiffStyleIsCaught() : void
    {
        mkdir($this->tmpDir . '/src');
        $configFile = $this->writeConfig($this->tmpDir . '/src');

        $cmd = new class extends Diff {
            protected function diffStyle(
                \PhpStyler\Config $config,
                string $configFile,
                array $paths,
                int $workerCount,
                \PhpStyler\Cache $cache,
            ) : void {
                throw new \PhpStyler\Exception('forced failure');
            }
        };

        $options = new DiffOptions(configFile: $configFile, workers: null);

        ob_start();
        $exit = $cmd($options);
        $out = (string) ob_get_clean();

        $this->assertSame(1, $exit);
        $this->assertStringContainsString('forced failure', $out);
    }
}

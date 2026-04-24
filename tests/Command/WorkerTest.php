<?php
declare(strict_types=1);

namespace PhpStyler\Command;

use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Styler;

class WorkerTest extends CommandTestCase
{
    public function testMissingConfigWritesStderrAndExitsOne() : void
    {
        $cmd = new WorkerSpy();
        $options = new WorkerOptions(configFile: null, mode: 'apply');

        ob_start();
        $exit = $cmd($options);
        ob_end_clean();

        $this->assertSame(1, $exit);
        $this->assertStringContainsString('--config', $cmd->stderr);
    }

    public function testInvalidModeWritesStderrAndExitsOne() : void
    {
        $configFile = $this->writeConfig($this->tmpDir);

        $cmd = new WorkerSpy();
        $options = new WorkerOptions(configFile: $configFile, mode: 'bogus');

        ob_start();
        $exit = $cmd($options);
        ob_end_clean();

        $this->assertSame(1, $exit);
        $this->assertStringContainsString('--mode=apply|check|diff', $cmd->stderr);
    }

    public function testMissingModeWritesStderrAndExitsOne() : void
    {
        $configFile = $this->writeConfig($this->tmpDir);

        $cmd = new WorkerSpy();
        $options = new WorkerOptions(configFile: $configFile, mode: null);

        ob_start();
        $exit = $cmd($options);
        ob_end_clean();

        $this->assertSame(1, $exit);
        $this->assertStringContainsString('--mode=apply|check|diff', $cmd->stderr);
    }

    public function testApplyFileSucceeds() : void
    {
        $target = $this->tmpDir . '/a.php';
        file_put_contents($target, "<?php if(\$x){echo 1;}");

        $cmd = new WorkerSpy();
        $styler = new Styler(new DeclarationFormat());
        $result = $cmd->publicApplyFile($styler, $target);

        $this->assertSame($target, $result['file']);
        $this->assertTrue($result['ok']);
        $contents = (string) file_get_contents($target);
        $this->assertStringContainsString("if (\$x) {", $contents);
    }

    public function testApplyFileFailsOnParserError() : void
    {
        $target = $this->tmpDir . '/bad.php';
        file_put_contents($target, "<?php : echo 1;");

        $cmd = new WorkerSpy();
        $styler = new Styler(new DeclarationFormat());
        $result = $cmd->publicApplyFile($styler, $target);

        $this->assertFalse($result['ok']);
        $this->assertArrayHasKey('error', $result);
    }

    public function testCheckFileMatch() : void
    {
        $target = $this->tmpDir . '/a.php';
        file_put_contents($target, "<?php\necho 1;\n");

        $cmd = new WorkerSpy();
        $styler = new Styler(new DeclarationFormat());
        $result = $cmd->publicCheckFile($styler, $target);

        $this->assertTrue($result['ok']);
        $this->assertTrue($result['match']);
    }

    public function testCheckFileMismatch() : void
    {
        $target = $this->tmpDir . '/a.php';
        file_put_contents($target, "<?php if(\$x){echo 1;}");

        $cmd = new WorkerSpy();
        $styler = new Styler(new DeclarationFormat());
        $result = $cmd->publicCheckFile($styler, $target);

        $this->assertTrue($result['ok']);
        $this->assertFalse($result['match']);
    }

    public function testCheckFileFailsOnParserError() : void
    {
        $target = $this->tmpDir . '/bad.php';
        file_put_contents($target, "<?php : echo 1;");

        $cmd = new WorkerSpy();
        $styler = new Styler(new DeclarationFormat());
        $result = $cmd->publicCheckFile($styler, $target);

        $this->assertFalse($result['ok']);
    }

    public function testDiffFileNoChange() : void
    {
        $target = $this->tmpDir . '/a.php';
        file_put_contents($target, "<?php\necho 1;\n");

        $cmd = new WorkerSpy();
        $styler = new Styler(new DeclarationFormat());
        $result = $cmd->publicDiffFile($styler, $target);

        $this->assertTrue($result['ok']);
        $this->assertSame('', $result['diff']);
    }

    public function testDiffFileWithChanges() : void
    {
        $target = $this->tmpDir . '/a.php';
        file_put_contents($target, "<?php if(\$x){echo 1;}");

        $cmd = new WorkerSpy();
        $styler = new Styler(new DeclarationFormat());
        $result = $cmd->publicDiffFile($styler, $target);

        $this->assertTrue($result['ok']);
        $diff = $result['diff'];
        $this->assertIsString($diff);
        $this->assertStringContainsString('@@', $diff);
    }

    public function testDiffFileFailsOnParserError() : void
    {
        $target = $this->tmpDir . '/bad.php';
        file_put_contents($target, "<?php : echo 1;");

        $cmd = new WorkerSpy();
        $styler = new Styler(new DeclarationFormat());
        $result = $cmd->publicDiffFile($styler, $target);

        $this->assertFalse($result['ok']);
    }

    public function testReadFilesFromStdin() : void
    {
        $cmd = new WorkerWithStdinOverride(["foo.php", "bar.php", ""]);
        $files = $cmd->publicReadFilesFromStdin();

        $this->assertSame(['foo.php', 'bar.php'], array_values($files));
    }

    public function testInvokeApplyModeEmitsJsonPerFile() : void
    {
        $configFile = $this->writeConfig($this->tmpDir);
        $ok = $this->tmpDir . '/ok.php';
        $bad = $this->tmpDir . '/bad.php';
        file_put_contents($ok, "<?php if(\$x){echo 1;}");
        file_put_contents($bad, "<?php : echo 1;");

        $cmd = new WorkerWithStdinOverride([$ok, $bad]);
        $options = new WorkerOptions(configFile: $configFile, mode: 'apply');

        $exit = $cmd($options);

        $this->assertSame(1, $exit);

        $lines = array_values(array_filter(explode("\n", trim($cmd->stdout))));
        $this->assertCount(2, $lines);

        $first = json_decode($lines[0], true);
        $this->assertIsArray($first);
        $this->assertSame($ok, $first['file']);
        $this->assertTrue($first['ok']);
        $this->assertStringContainsString(
            "if (\$x) {",
            (string) file_get_contents($ok),
        );

        $second = json_decode($lines[1], true);
        $this->assertIsArray($second);
        $this->assertSame($bad, $second['file']);
        $this->assertFalse($second['ok']);
        $this->assertArrayHasKey('error', $second);
    }

    public function testInvokeCheckModeEmitsJsonPerFile() : void
    {
        $configFile = $this->writeConfig($this->tmpDir);
        $clean = $this->tmpDir . '/clean.php';
        $dirty = $this->tmpDir . '/dirty.php';
        file_put_contents($clean, "<?php\necho 1;\n");
        file_put_contents($dirty, "<?php if(\$x){echo 1;}");

        $cmd = new WorkerWithStdinOverride([$clean, $dirty]);
        $options = new WorkerOptions(configFile: $configFile, mode: 'check');

        $exit = $cmd($options);

        $this->assertSame(0, $exit);

        $lines = array_values(array_filter(explode("\n", trim($cmd->stdout))));
        $this->assertCount(2, $lines);

        $first = json_decode($lines[0], true);
        $this->assertIsArray($first);
        $this->assertTrue($first['ok']);
        $this->assertTrue($first['match']);

        $second = json_decode($lines[1], true);
        $this->assertIsArray($second);
        $this->assertTrue($second['ok']);
        $this->assertFalse($second['match']);
    }

    public function testInvokeDiffModeEmitsJsonPerFile() : void
    {
        exec('command -v diff', $o, $c);

        if ($c !== 0) {
            $this->markTestSkipped('diff binary not available');
        }

        $configFile = $this->writeConfig($this->tmpDir);
        $target = $this->tmpDir . '/dirty.php';
        file_put_contents($target, "<?php if(\$x){echo 1;}");

        $cmd = new WorkerWithStdinOverride([$target]);
        $options = new WorkerOptions(configFile: $configFile, mode: 'diff');

        $exit = $cmd($options);

        $this->assertSame(0, $exit);

        $lines = array_values(array_filter(explode("\n", trim($cmd->stdout))));
        $this->assertCount(1, $lines);

        $result = json_decode($lines[0], true);
        $this->assertIsArray($result);
        $this->assertTrue($result['ok']);
        $diff = $result['diff'];
        $this->assertIsString($diff);
        $this->assertStringContainsString('@@', $diff);
    }
}

<?php
declare(strict_types=1);

namespace PhpStyler\Parallel;

use PhpStyler\Command\CommandTestCase;

class WorkerPoolTest extends CommandTestCase
{
    public function testDetectCpuCountReturnsAtLeastOne() : void
    {
        $this->assertGreaterThanOrEqual(1, WorkerPool::detectCpuCount());
    }

    public function testRunWithTwoWorkersOverRealFiles() : void
    {
        mkdir($this->tmpDir . '/src');
        $configFile = $this->writeConfig($this->tmpDir . '/src');

        $files = [];

        for ($i = 0; $i < 4; $i ++) {
            $path = $this->tmpDir . "/src/f{$i}.php";
            file_put_contents($path, "<?php\necho {$i};\n");
            $files[] = $path;
        }

        $pool = new WorkerPool();
        $results = $pool->run($files, 'check', $configFile, 2);

        $this->assertCount(4, $results);

        foreach ($results as $r) {
            $this->assertTrue($r->ok);
            $this->assertTrue($r->isMatch);
        }
    }

    public function testRunWithApplyMode() : void
    {
        mkdir($this->tmpDir . '/src');
        $configFile = $this->writeConfig($this->tmpDir . '/src');
        $target = $this->tmpDir . '/src/u.php';
        file_put_contents($target, "<?php if(\$x){echo 1;}");

        $pool = new WorkerPool();
        $results = $pool->run([$target], 'apply', $configFile, 1);

        $this->assertCount(1, $results);
        $this->assertTrue($results[0]->ok);
        $this->assertStringContainsString(
            "if (\$x) {",
            (string) file_get_contents($target),
        );
    }

    public function testRunWithDiffMode() : void
    {
        mkdir($this->tmpDir . '/src');
        $configFile = $this->writeConfig($this->tmpDir . '/src');
        $target = $this->tmpDir . '/src/u.php';
        file_put_contents($target, "<?php if(\$x){echo 1;}");

        $pool = new WorkerPool();
        $results = $pool->run([$target], 'diff', $configFile, 1);

        $this->assertCount(1, $results);
        $this->assertTrue($results[0]->ok);
        $this->assertStringContainsString('@@', (string) $results[0]->diff);
    }

    public function testThrowsWhenWorkerExitsNonZeroWithEmptyStdout() : void
    {
        $failingBin = $this->tmpDir . '/failing-bin.php';
        file_put_contents($failingBin, "<?php exit(7);\n");

        $configFile = $this->writeConfig($this->tmpDir);

        $pool = new FakeWorkerPool($failingBin);

        $this->expectException(\PhpStyler\Exception::class);
        $this->expectExceptionMessage('Worker 0 failed');

        $pool->run(['dummy.php'], 'check', $configFile, 1);
    }

    public function testThrowsWhenWorkerProducesInvalidJson() : void
    {
        $badBin = $this->tmpDir . '/bad-bin.php';
        file_put_contents($badBin, "<?php echo 'not json at all', PHP_EOL;\n");

        $configFile = $this->writeConfig($this->tmpDir);

        $pool = new FakeWorkerPool($badBin);

        $this->expectException(\PhpStyler\Exception::class);
        $this->expectExceptionMessage('invalid output');

        $pool->run(['dummy.php'], 'check', $configFile, 1);
    }

    public function testThrowsWhenWorkerProducesNonArrayJson() : void
    {
        $badBin = $this->tmpDir . '/scalar-bin.php';
        file_put_contents($badBin, "<?php echo '42', PHP_EOL;\n");

        $configFile = $this->writeConfig($this->tmpDir);

        $pool = new FakeWorkerPool($badBin);

        $this->expectException(\PhpStyler\Exception::class);
        $this->expectExceptionMessage('invalid output');

        $pool->run(['dummy.php'], 'check', $configFile, 1);
    }

    public function testWorkerFailurePropagatesThroughResults() : void
    {
        mkdir($this->tmpDir . '/src');
        $configFile = $this->writeConfig($this->tmpDir . '/src');
        $target = $this->tmpDir . '/src/bad.php';
        file_put_contents($target, "<?php : echo 1;");

        $pool = new WorkerPool();
        $results = $pool->run([$target], 'check', $configFile, 1);

        $this->assertCount(1, $results);
        $this->assertFalse($results[0]->ok);
        $this->assertNotNull($results[0]->error);
    }
}

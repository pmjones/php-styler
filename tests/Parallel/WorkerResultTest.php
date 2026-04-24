<?php
declare(strict_types=1);

namespace PhpStyler\Parallel;

use PHPUnit\Framework\TestCase;

class WorkerResultTest extends TestCase
{
    public function testConstructorDefaults() : void
    {
        $r = new WorkerResult(file: 'a.php');
        $this->assertSame('a.php', $r->file);
        $this->assertTrue($r->ok);
        $this->assertNull($r->error);
        $this->assertNull($r->isMatch);
        $this->assertNull($r->diff);
    }

    public function testConstructorAllArgs() : void
    {
        $r = new WorkerResult(
            file: 'a.php',
            ok: false,
            error: 'boom',
            isMatch: false,
            diff: '@@ -1,1 +1,1 @@',
        );
        $this->assertSame('a.php', $r->file);
        $this->assertFalse($r->ok);
        $this->assertSame('boom', $r->error);
        $this->assertFalse($r->isMatch);
        $this->assertSame('@@ -1,1 +1,1 @@', $r->diff);
    }

    public function testFromArrayMinimal() : void
    {
        $r = WorkerResult::fromArray(['file' => 'a.php', 'ok' => true]);
        $this->assertSame('a.php', $r->file);
        $this->assertTrue($r->ok);
        $this->assertNull($r->error);
        $this->assertNull($r->isMatch);
        $this->assertNull($r->diff);
    }

    public function testFromArrayWithError() : void
    {
        $r = WorkerResult::fromArray([
            'file' => 'a.php',
            'ok' => false,
            'error' => 'boom',
        ]);
        $this->assertFalse($r->ok);
        $this->assertSame('boom', $r->error);
    }

    public function testFromArrayWithMatch() : void
    {
        $r = WorkerResult::fromArray([
            'file' => 'a.php',
            'ok' => true,
            'match' => true,
        ]);
        $this->assertTrue($r->isMatch);
    }

    public function testFromArrayWithDiff() : void
    {
        $r = WorkerResult::fromArray([
            'file' => 'a.php',
            'ok' => true,
            'diff' => '@@ line @@',
        ]);
        $this->assertSame('@@ line @@', $r->diff);
    }

    public function testFromArrayMissingFileDefaultsToEmpty() : void
    {
        $r = WorkerResult::fromArray([]);
        $this->assertSame('', $r->file);
        $this->assertTrue($r->ok);
    }

    public function testToArrayMinimal() : void
    {
        $r = new WorkerResult(file: 'a.php');
        $this->assertSame(['file' => 'a.php', 'ok' => true], $r->toArray());
    }

    public function testToArrayAllFields() : void
    {
        $r = new WorkerResult(
            file: 'a.php',
            ok: false,
            error: 'boom',
            isMatch: true,
            diff: 'patch',
        );
        $this->assertSame(
            [
                'file' => 'a.php',
                'ok' => false,
                'error' => 'boom',
                'match' => true,
                'diff' => 'patch',
            ],
            $r->toArray(),
        );
    }

    public function testRoundTripThroughArray() : void
    {
        $a = new WorkerResult(
            file: 'b.php',
            ok: true,
            error: null,
            isMatch: false,
            diff: null,
        );
        $b = WorkerResult::fromArray($a->toArray());
        $this->assertSame($a->file, $b->file);
        $this->assertSame($a->ok, $b->ok);
        $this->assertSame($a->error, $b->error);
        $this->assertSame($a->isMatch, $b->isMatch);
        $this->assertSame($a->diff, $b->diff);
    }
}

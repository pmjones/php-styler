<?php
declare(strict_types=1);

namespace PhpStyler;

use RuntimeException;

class LineTest extends TestCase
{
    protected Line $line;

    protected function setUp() : void
    {
        $this->line = new Line(
            eol: PHP_EOL,
            indentNum: 0,
            indentLen: 4,
            indentTab: false,
            lineLen: 88,
        );
    }

    public function testOffsetSet() : void
    {
        $this->line[] = 'foo';
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(Line::class . ' is append-only.');
        $this->line[1] = 'baz';
    }

    public function testOffsetGet() : void
    {
        $this->line[] = 'foo';
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(Line::class . ' is write-only.');
        $foo = $this->line[0];
    }

    public function testOffsetExists() : void
    {
        $this->line[] = 'foo';
        $this->assertTrue(isset($this->line[0]));
        $this->assertFalse(isset($this->line[1]));
    }

    public function testOffsetUnset() : void
    {
        $this->line[] = 'foo';
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(Line::class . ' is append-only.');
        unset($this->line[0]);
    }

    public function testNoSuchSplit() : void
    {
        $this->line[] = 'fake fake fake fake fake fake fake fake fake fake fake fake fake fake fake fake fake fake ';
        $this->line[] = new Split(0, 'fake', 'fake');
        $output = '';
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No such split rule: fake');
        $this->line->append($output);
    }
}

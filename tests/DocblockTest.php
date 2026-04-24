<?php
declare(strict_types=1);

namespace PhpStyler;

use PHPUnit\Framework\TestCase;

class DocblockTest extends TestCase
{
    public function testParseDoubleStar() : void
    {
        $doc = Docblock::parse("/** hello */");
        $this->assertSame('hello', $doc->text);
        $this->assertSame([], $doc->tags);
    }

    public function testParseSlashStar() : void
    {
        $doc = Docblock::parse("/* hello */");
        $this->assertSame('hello', $doc->text);
    }

    public function testParseDoubleSlash() : void
    {
        $doc = Docblock::parse("// hello");
        $this->assertSame('hello', $doc->text);
    }

    public function testParseHash() : void
    {
        $doc = Docblock::parse("# hello");
        $this->assertSame('hello', $doc->text);
    }

    public function testParseNakedText() : void
    {
        $doc = Docblock::parse("naked text");
        $this->assertSame('naked text', $doc->text);
    }

    public function testConstructor() : void
    {
        $tag = new DocblockTag('@param', '$x int');
        $doc = new Docblock('some text', [$tag]);
        $this->assertSame('some text', $doc->text);
        $this->assertSame([$tag], $doc->tags);
    }

    public function testMultilineDoublestar() : void
    {
        $doc = Docblock::parse("/**\n * line one\n * line two\n */");
        $this->assertSame("line one\nline two", $doc->text);
    }

    public function testWithTags() : void
    {
        $doc = Docblock::parse("/**\n * summary\n *\n * @param int \$x\n */");
        $this->assertStringContainsString('summary', $doc->text);
        $this->assertNotEmpty($doc->tags);
    }
}

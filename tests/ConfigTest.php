<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpStyler\Format\Format;

class ConfigTest extends TestCase
{
    public function test() : void
    {
        $actual = new Config(new Files(), null);
        $this->assertInstanceof(Config::class, $actual);
        $this->assertInstanceof(Format::class, $actual->format);
    }

    public function testFormatProperties() : void
    {
        $format = new Format();
        $this->assertSame('next_line', $format->classBracePosition());
        $this->assertSame('next_line', $format->functionBracePosition());
        $this->assertSame('same_line', $format->controlBracePosition());
        $this->assertSame('lower', $format->keywordCase());
        $this->assertTrue($format->concatenationSpacing());
        $this->assertTrue($format->returnTypeColonSpacing());
        $this->assertTrue($format->blankLineAfterBlock());
    }
}

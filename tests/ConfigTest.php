<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpStyler\Format\DefaultFormat;

class ConfigTest extends TestCase
{
    public function test() : void
    {
        $actual = new Config(new Files(), null);
        $this->assertInstanceof(Config::class, $actual);
        $this->assertInstanceof(DefaultFormat::class, $actual->format);
    }

    public function testDefaultFormatProperties() : void
    {
        $format = new DefaultFormat();
        $this->assertSame('next_line', $format->classBracePosition());
        $this->assertSame('next_line', $format->functionBracePosition());
        $this->assertSame('same_line', $format->controlBracePosition());
        $this->assertSame('lower', $format->keywordCase());
        $this->assertTrue($format->concatenationSpacing());
        $this->assertTrue($format->returnTypeColonSpacing());
        $this->assertTrue($format->blankLineAfterBlock());
    }
}

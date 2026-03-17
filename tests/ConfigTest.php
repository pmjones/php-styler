<?php
declare(strict_types=1);

namespace PhpStyler;

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

        // classBracePosition default: 'next_line'
        $style = $format->getStyle(Token\TClassOpeningBrace::class);
        $this->assertTrue($style->lineBreakBefore);

        // functionBracePosition default: 'next_line'
        $style = $format->getStyle(Token\TFunctionOpeningBrace::class);
        $this->assertTrue($style->lineBreakBefore);

        // controlBracePosition default: 'same_line'
        $style = $format->getStyle(Token\TIfOpeningBrace::class);
        $this->assertNull($style->lineBreakBefore);

        // keywordCase default: 'lower'
        $style = $format->getStyle(Token\TFalse::class);
        $this->assertSame('strtolower', $style->case);

        // concatenationSpacing default: true
        $style = $format->getStyle(Token\TDot::class);
        $this->assertNull($style->spaceBefore);

        // returnTypeColonSpacing default: true
        $style = $format->getStyle(Token\TReturnColon::class);
        $this->assertTrue($style->spaceBefore);

        // blankLineAfterBlock default: true
        $style = $format->getStyle(Token\TFunctionClosingBrace::class);
        $this->assertTrue($style->blankLineAfter);
    }
}

<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpStyler\Format\AFormat;
use PhpStyler\Format\PlainFormat;

class ConfigTest extends TestCase
{
    public function test() : void
    {
        $actual = new Config(new Files());
        $this->assertInstanceof(Config::class, $actual);
        $this->assertInstanceof(AFormat::class, $actual->format);
    }

    public function testFormatProperties() : void
    {
        $format = new PlainFormat();
        $parser = new Parser($format);

        // classBracePosition default: 'same_line'
        $style = $parser->getStyle(Token\TClassOpeningBrace::class);
        $this->assertNull($style->lineBreakBefore);
        $this->assertTrue($style->spaceBefore);

        // functionBracePosition default: 'same_line'
        $style = $parser->getStyle(Token\TFunctionOpeningBrace::class);
        $this->assertNull($style->lineBreakBefore);
        $this->assertTrue($style->spaceBefore);

        // controlBracePosition default: 'same_line'
        $style = $parser->getStyle(Token\TIfOpeningBrace::class);
        $this->assertNull($style->lineBreakBefore);

        // keywordCase default: 'lower'
        $style = $parser->getStyle(Token\TFalse::class);
        $this->assertSame('strtolower', $style->case);

        // concatenationSpacing default: true
        $style = $parser->getStyle(Token\TDot::class);
        $this->assertNull($style->spaceBefore);

        // returnTypeColonSpacing default: true
        $style = $parser->getStyle(Token\TReturnColon::class);
        $this->assertTrue($style->spaceBefore);

        // blankLineAfterBlock default: false
        $style = $parser->getStyle(Token\TFunctionClosingBrace::class);
        $this->assertNull($style->blankLineAfter);
        $this->assertTrue($style->lineBreakAfter);
    }
}

<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpStyler\Token\TCatchContinuationBrace;
use PhpStyler\Token\TClassOpeningBrace;
use PhpStyler\Token\TDot;
use PhpStyler\Token\TFunctionOpeningBrace;
use PhpStyler\Token\TIfClosingBrace;
use PhpStyler\Token\TIfOpeningBrace;
use PhpStyler\Token\TReturnColon;
use PhpStyler\Token\TTrue;
use PHPUnit\Framework\TestCase;

class FormatStyleTest extends TestCase
{
    public function testNoOverrides() : void
    {
        $format = new Format();
        $parser = new Parser($format);
        $style = $parser->getStyle(TClassOpeningBrace::class);
        $this->assertTrue($style->lineBreakBefore);
        $this->assertTrue($style->lineBreakAfter);
    }

    public function testConstructorOverrides() : void
    {
        $format = new Format(styles: [
            TClassOpeningBrace::class => [
                'lineBreakBefore' => null,
                'spaceBefore' => true,
            ],
        ]);
        $parser = new Parser($format);
        $style = $parser->getStyle(TClassOpeningBrace::class);
        $this->assertNull($style->lineBreakBefore);
        $this->assertTrue($style->spaceBefore);
        $this->assertTrue($style->lineBreakAfter);
    }

    public function testDefaults() : void
    {
        $format = new Format();
        $parser = new Parser($format);

        // Default format should produce no overrides
        $classStyle = $parser->getStyle(TClassOpeningBrace::class);
        $this->assertTrue($classStyle->lineBreakBefore);

        $ifStyle = $parser->getStyle(TIfOpeningBrace::class);
        $this->assertNull($ifStyle->lineBreakBefore);

        $dotStyle = $parser->getStyle(TDot::class);
        $this->assertNull($dotStyle->spaceBefore);

        $returnColonStyle = $parser->getStyle(TReturnColon::class);
        $this->assertTrue($returnColonStyle->spaceBefore);

        $trueStyle = $parser->getStyle(TTrue::class);
        $this->assertSame('strtolower', $trueStyle->case);

        $ifClosingStyle = $parser->getStyle(TIfClosingBrace::class);
        $this->assertTrue($ifClosingStyle->blankLineAfter);
    }

    public function testClassBraceSameLine() : void
    {
        $format = new Format(classBracePosition: 'same_line');
        $parser = new Parser($format);
        $style = $parser->getStyle(TClassOpeningBrace::class);
        $this->assertNull($style->lineBreakBefore);
        $this->assertTrue($style->spaceBefore);
    }

    public function testFunctionBraceSameLine() : void
    {
        $format = new Format(functionBracePosition: 'same_line');
        $parser = new Parser($format);
        $style = $parser->getStyle(TFunctionOpeningBrace::class);
        $this->assertNull($style->lineBreakBefore);
        $this->assertTrue($style->spaceBefore);
    }

    public function testControlBraceNextLine() : void
    {
        $format = new Format(controlBracePosition: 'next_line');
        $parser = new Parser($format);

        $ifStyle = $parser->getStyle(TIfOpeningBrace::class);
        $this->assertTrue($ifStyle->lineBreakBefore);

        $contStyle = $parser->getStyle(TCatchContinuationBrace::class);
        $this->assertNull($contStyle->spaceAfter);
        $this->assertTrue($contStyle->lineBreakAfter);
    }

    public function testKeywordCaseUpper() : void
    {
        $format = new Format(keywordCase: 'upper');
        $parser = new Parser($format);
        $style = $parser->getStyle(TTrue::class);
        $this->assertSame('strtoupper', $style->case);
    }

    public function testConcatenationSpacingFalse() : void
    {
        $format = new Format(concatenationSpacing: false);
        $parser = new Parser($format);
        $style = $parser->getStyle(TDot::class);
        $this->assertFalse($style->spaceBefore);
        $this->assertFalse($style->spaceAfter);
    }

    public function testReturnTypeColonSpacingFalse() : void
    {
        $format = new Format(returnTypeColonSpacing: false);
        $parser = new Parser($format);
        $style = $parser->getStyle(TReturnColon::class);
        $this->assertFalse($style->spaceBefore);
    }

    public function testBlankLineAfterBlockFalse() : void
    {
        $format = new Format(blankLineAfterBlock: false);
        $parser = new Parser($format);
        $style = $parser->getStyle(TIfClosingBrace::class);
        $this->assertNull($style->blankLineAfter);
        $this->assertTrue($style->lineBreakAfter);
    }
}

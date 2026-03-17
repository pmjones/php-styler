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
        $style = $format->getStyle(TClassOpeningBrace::class);
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
        $style = $format->getStyle(TClassOpeningBrace::class);
        $this->assertNull($style->lineBreakBefore);
        $this->assertTrue($style->spaceBefore);
        $this->assertTrue($style->lineBreakAfter);
    }

    public function testDefaults() : void
    {
        $format = new Format();

        // Default format should produce no overrides
        $classStyle = $format->getStyle(TClassOpeningBrace::class);
        $this->assertTrue($classStyle->lineBreakBefore);

        $ifStyle = $format->getStyle(TIfOpeningBrace::class);
        $this->assertNull($ifStyle->lineBreakBefore);

        $dotStyle = $format->getStyle(TDot::class);
        $this->assertNull($dotStyle->spaceBefore);

        $returnColonStyle = $format->getStyle(TReturnColon::class);
        $this->assertTrue($returnColonStyle->spaceBefore);

        $trueStyle = $format->getStyle(TTrue::class);
        $this->assertSame('strtolower', $trueStyle->case);

        $ifClosingStyle = $format->getStyle(TIfClosingBrace::class);
        $this->assertTrue($ifClosingStyle->blankLineAfter);
    }

    public function testClassBraceSameLine() : void
    {
        $format = new Format(classBracePosition: 'same_line');
        $style = $format->getStyle(TClassOpeningBrace::class);
        $this->assertNull($style->lineBreakBefore);
        $this->assertTrue($style->spaceBefore);
    }

    public function testFunctionBraceSameLine() : void
    {
        $format = new Format(functionBracePosition: 'same_line');
        $style = $format->getStyle(TFunctionOpeningBrace::class);
        $this->assertNull($style->lineBreakBefore);
        $this->assertTrue($style->spaceBefore);
    }

    public function testControlBraceNextLine() : void
    {
        $format = new Format(controlBracePosition: 'next_line');

        $ifStyle = $format->getStyle(TIfOpeningBrace::class);
        $this->assertTrue($ifStyle->lineBreakBefore);

        $contStyle = $format->getStyle(TCatchContinuationBrace::class);
        $this->assertNull($contStyle->spaceAfter);
        $this->assertTrue($contStyle->lineBreakAfter);
    }

    public function testKeywordCaseUpper() : void
    {
        $format = new Format(keywordCase: 'upper');
        $style = $format->getStyle(TTrue::class);
        $this->assertSame('strtoupper', $style->case);
    }

    public function testConcatenationSpacingFalse() : void
    {
        $format = new Format(concatenationSpacing: false);
        $style = $format->getStyle(TDot::class);
        $this->assertFalse($style->spaceBefore);
        $this->assertFalse($style->spaceAfter);
    }

    public function testReturnTypeColonSpacingFalse() : void
    {
        $format = new Format(returnTypeColonSpacing: false);
        $style = $format->getStyle(TReturnColon::class);
        $this->assertFalse($style->spaceBefore);
    }

    public function testBlankLineAfterBlockFalse() : void
    {
        $format = new Format(blankLineAfterBlock: false);
        $style = $format->getStyle(TIfClosingBrace::class);
        $this->assertNull($style->blankLineAfter);
        $this->assertTrue($style->lineBreakAfter);
    }
}

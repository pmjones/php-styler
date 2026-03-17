<?php
declare(strict_types=1);

namespace PhpStyler\Style;

use PhpStyler\Format\DefaultFormat;
use PhpStyler\Token\TCatchContinuationBrace;
use PhpStyler\Token\TClassOpeningBrace;
use PhpStyler\Token\TDot;
use PhpStyler\Token\TFunctionOpeningBrace;
use PhpStyler\Token\TIfClosingBrace;
use PhpStyler\Token\TIfOpeningBrace;
use PhpStyler\Token\TReturnColon;
use PhpStyler\Token\TTrue;
use PHPUnit\Framework\TestCase;

class StyleLocatorTest extends TestCase
{
    public function testNoOverrides() : void
    {
        $locator = new StyleLocator();
        $style = $locator->get(TClassOpeningBrace::class);
        $this->assertTrue($style->lineBreakBefore);
        $this->assertTrue($style->lineBreakAfter);
    }

    public function testConstructorOverrides() : void
    {
        $locator = new StyleLocator([
            'TClassOpeningBrace' => ['lineBreakBefore' => null, 'spaceBefore' => true],
        ]);
        $style = $locator->get(TClassOpeningBrace::class);
        $this->assertNull($style->lineBreakBefore);
        $this->assertTrue($style->spaceBefore);
        $this->assertTrue($style->lineBreakAfter);
    }

    public function testFromFormatDefaults() : void
    {
        $format = new DefaultFormat();
        $locator = StyleLocator::fromFormat($format);

        // Default format should produce no overrides
        $classStyle = $locator->get(TClassOpeningBrace::class);
        $this->assertTrue($classStyle->lineBreakBefore);

        $ifStyle = $locator->get(TIfOpeningBrace::class);
        $this->assertNull($ifStyle->lineBreakBefore);

        $dotStyle = $locator->get(TDot::class);
        $this->assertNull($dotStyle->spaceBefore);

        $returnColonStyle = $locator->get(TReturnColon::class);
        $this->assertTrue($returnColonStyle->spaceBefore);

        $trueStyle = $locator->get(TTrue::class);
        $this->assertSame('strtolower', $trueStyle->case);

        $ifClosingStyle = $locator->get(TIfClosingBrace::class);
        $this->assertTrue($ifClosingStyle->blankLineAfter);
    }

    public function testFromFormatClassBraceSameLine() : void
    {
        $format = new class () extends DefaultFormat {
            public function classBracePosition() : string
            {
                return 'same_line';
            }
        };

        $locator = StyleLocator::fromFormat($format);
        $style = $locator->get(TClassOpeningBrace::class);
        $this->assertNull($style->lineBreakBefore);
        $this->assertTrue($style->spaceBefore);
    }

    public function testFromFormatFunctionBraceSameLine() : void
    {
        $format = new class () extends DefaultFormat {
            public function functionBracePosition() : string
            {
                return 'same_line';
            }
        };

        $locator = StyleLocator::fromFormat($format);
        $style = $locator->get(TFunctionOpeningBrace::class);
        $this->assertNull($style->lineBreakBefore);
        $this->assertTrue($style->spaceBefore);
    }

    public function testFromFormatControlBraceNextLine() : void
    {
        $format = new class () extends DefaultFormat {
            public function controlBracePosition() : string
            {
                return 'next_line';
            }
        };

        $locator = StyleLocator::fromFormat($format);

        $ifStyle = $locator->get(TIfOpeningBrace::class);
        $this->assertTrue($ifStyle->lineBreakBefore);

        $contStyle = $locator->get(TCatchContinuationBrace::class);
        $this->assertNull($contStyle->spaceAfter);
        $this->assertTrue($contStyle->lineBreakAfter);
    }

    public function testFromFormatKeywordCaseUpper() : void
    {
        $format = new class () extends DefaultFormat {
            public function keywordCase() : string
            {
                return 'upper';
            }
        };

        $locator = StyleLocator::fromFormat($format);
        $style = $locator->get(TTrue::class);
        $this->assertSame('strtoupper', $style->case);
    }

    public function testFromFormatConcatenationSpacingFalse() : void
    {
        $format = new class () extends DefaultFormat {
            public function concatenationSpacing() : bool
            {
                return false;
            }
        };

        $locator = StyleLocator::fromFormat($format);
        $style = $locator->get(TDot::class);
        $this->assertFalse($style->spaceBefore);
        $this->assertFalse($style->spaceAfter);
    }

    public function testFromFormatReturnTypeColonSpacingFalse() : void
    {
        $format = new class () extends DefaultFormat {
            public function returnTypeColonSpacing() : bool
            {
                return false;
            }
        };

        $locator = StyleLocator::fromFormat($format);
        $style = $locator->get(TReturnColon::class);
        $this->assertFalse($style->spaceBefore);
    }

    public function testFromFormatBlankLineAfterBlockFalse() : void
    {
        $format = new class () extends DefaultFormat {
            public function blankLineAfterBlock() : bool
            {
                return false;
            }
        };

        $locator = StyleLocator::fromFormat($format);
        $style = $locator->get(TIfClosingBrace::class);
        $this->assertNull($style->blankLineAfter);
        $this->assertTrue($style->lineBreakAfter);
    }
}

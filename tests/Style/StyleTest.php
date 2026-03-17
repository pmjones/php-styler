<?php
declare(strict_types=1);

namespace PhpStyler\Style;

use PhpStyler\Format\Format;
use PhpStyler\Rule\NormalizeTrailingCommas;
use PhpStyler\Rule\RemoveTrailingBlankLines;
use PhpStyler\Styler;
use PhpStyler\Token\TAssign;
use PhpStyler\Token\TBinaryPlus;
use PhpStyler\Token\TClassOpeningBrace;
use PhpStyler\Token\TDot;
use PhpStyler\Token\TIfClosingBrace;
use PhpStyler\Token\TIntersection;
use PhpStyler\Token\TReturnColon;
use PhpStyler\Token\TSemicolon;
use PhpStyler\Token\TTrue;
use PhpStyler\Token\TUnion;
use PHPUnit\Framework\TestCase;

class StyleTest extends TestCase
{
    /**
     * @dataProvider provide
     */
    public function test(?Format $format, string $code, string $expect) : void
    {
        $styler = new Styler(
            eol: "\n",
            format: $format,
            rules: [new NormalizeTrailingCommas(), new RemoveTrailingBlankLines()],
        );
        $actual = $styler($code);
        $this->assertSame($expect, $actual);
    }

    /** @return array<string, array{0: ?Format, 1: string, 2: string}> */
    public static function provide() : array
    {
        $returnColonNoSpaceBefore = new Format();
        $returnColonNoSpaceBefore->getStyle(TReturnColon::class)->spaceBefore = false;

        $returnColonNoSpaceAfter = new Format();
        $returnColonNoSpaceAfter->getStyle(TReturnColon::class)->spaceAfter = false;

        $returnColonNoSpaces = new Format();
        $returnColonNoSpaces->getStyle(TReturnColon::class)->spaceBefore = false;
        $returnColonNoSpaces->getStyle(TReturnColon::class)->spaceAfter = false;

        $dotNoSpaces = new Format();
        $dotNoSpaces->getStyle(TDot::class)->spaceBefore = false;
        $dotNoSpaces->getStyle(TDot::class)->spaceAfter = false;

        $unionWithSpaces = new Format();
        $unionWithSpaces->getStyle(TUnion::class)->spaceBefore = true;
        $unionWithSpaces->getStyle(TUnion::class)->spaceAfter = true;

        $intersectionWithSpaces = new Format();
        $intersectionWithSpaces->getStyle(TIntersection::class)->spaceBefore = true;
        $intersectionWithSpaces->getStyle(TIntersection::class)->spaceAfter = true;

        $assignNoSpaces = new Format();
        $assignNoSpaces->getStyle(TAssign::class)->spaceBefore = false;
        $assignNoSpaces->getStyle(TAssign::class)->spaceAfter = false;

        $binaryPlusNoSpaces = new Format();
        $binaryPlusNoSpaces->getStyle(TBinaryPlus::class)->spaceBefore = false;
        $binaryPlusNoSpaces->getStyle(TBinaryPlus::class)->spaceAfter = false;

        /** @php-styler-expansive */
        return [
            'return-colon-no-space-before' => [
                $returnColonNoSpaceBefore,
                <<<'CODE'
                <?php function foo() : string {}
                CODE,
                <<<'EXPECT'
                <?php function foo(): string
                {
                }

                EXPECT,
            ],
            'return-colon-no-space-after' => [
                $returnColonNoSpaceAfter,
                <<<'CODE'
                <?php function foo() : string {}
                CODE,
                <<<'EXPECT'
                <?php function foo() :string
                {
                }

                EXPECT,
            ],
            'return-colon-no-spaces' => [
                $returnColonNoSpaces,
                <<<'CODE'
                <?php function foo() : string {}
                CODE,
                <<<'EXPECT'
                <?php function foo():string
                {
                }

                EXPECT,
            ],
            'dot-no-spaces' => [
                $dotNoSpaces,
                <<<'CODE'
                <?php $a = $b . $c;
                CODE,
                <<<'EXPECT'
                <?php $a = $b.$c;

                EXPECT,
            ],
            'union-with-spaces' => [
                $unionWithSpaces,
                <<<'CODE'
                <?php function foo(int|string $x) {}
                CODE,
                <<<'EXPECT'
                <?php function foo(int | string $x)
                {
                }

                EXPECT,
            ],
            'intersection-with-spaces' => [
                $intersectionWithSpaces,
                <<<'CODE'
                <?php function foo(Foo&Bar $x) {}
                CODE,
                <<<'EXPECT'
                <?php function foo(Foo & Bar $x)
                {
                }

                EXPECT,
            ],
            'assign-no-spaces' => [
                $assignNoSpaces,
                <<<'CODE'
                <?php $a = 1;
                CODE,
                <<<'EXPECT'
                <?php $a=1;

                EXPECT,
            ],
            'binary-plus-no-spaces' => [
                $binaryPlusNoSpaces,
                <<<'CODE'
                <?php $a = $b + $c;
                CODE,
                <<<'EXPECT'
                <?php $a = $b+$c;

                EXPECT,
            ],
            'semicolon-no-linebreak-after' => [
                (
                    function (
                    ) {
                        $format = new Format();
                        $format->getStyle(TSemicolon::class)->lineBreakAfter = null;
                        return $format;
                    }
                )(),
                <<<'CODE'
                <?php $a = 1; $b = 2;
                CODE,
                "<?php \$a = 1; \$b = 2; \n",

            ],
            'closing-brace-linebreak-instead-of-blankline' => [
                (
                    function () {
                        $format = new Format();
                        $format->getStyle(TIfClosingBrace::class)->blankLineAfter = null;
                        $format->getStyle(TIfClosingBrace::class)->lineBreakAfter = true;
                        return $format;
                    }
                )(),
                <<<'CODE'
                <?php if ($a) { $b = 1; } $c = 2;
                CODE,
                <<<'EXPECT'
                <?php if ($a) {
                    $b = 1;
                }
                $c = 2;

                EXPECT,

            ],
            'opening-brace-no-linebreak-before' => [
                (
                    function () {
                        $format = new Format();
                        $format->getStyle(TClassOpeningBrace::class)->lineBreakBefore = null;
                        return $format;
                    }
                )(),
                <<<'CODE'
                <?php class Foo {}
                CODE,
                <<<'EXPECT'
                <?php class Foo {
                }

                EXPECT,

            ],
            'case-default-strtolower' => [
                null,
                <<<'CODE'
                <?php $a = TRUE; $b = FALSE; $c = NULL;
                CODE,
                <<<'EXPECT'
                <?php $a = true;
                $b = false;
                $c = null;

                EXPECT,
            ],
            'case-override-strtoupper' => [
                (
                    function () {
                        $format = new Format();
                        $format->getStyle(TTrue::class)->case = 'strtoupper';
                        return $format;
                    }
                )(),
                <<<'CODE'
                <?php $a = true;
                CODE,
                <<<'EXPECT'
                <?php $a = TRUE;

                EXPECT,

            ],
            'case-disable-with-null' => [
                (
                    function () {
                        $format = new Format();
                        $format->getStyle(TTrue::class)->case = null;
                        return $format;
                    }
                )(),
                <<<'CODE'
                <?php $a = TRUE;
                CODE,
                <<<'EXPECT'
                <?php $a = TRUE;

                EXPECT,

            ],
            'empty-style-default-behavior' => [
                null,
                <<<'CODE'
                <?php $a = $b + $c . $d;
                CODE,
                <<<'EXPECT'
                <?php $a = $b + $c . $d;

                EXPECT,
            ],

        ];
    }
}

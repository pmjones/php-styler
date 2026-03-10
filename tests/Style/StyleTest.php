<?php
declare(strict_types=1);

namespace PhpStyler\Style;

use PhpStyler\Token\TAssign;
use PhpStyler\Token\TBinaryPlus;
use PhpStyler\Token\TClassOpeningBrace;
use PhpStyler\Token\TDot;
use PhpStyler\Token\TFalse;
use PhpStyler\Token\TIfClosingBrace;
use PhpStyler\Token\TIntersection;
use PhpStyler\Token\TNull;
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
    public function test(?StyleLocator $styles, string $code, string $expect) : void
    {
        $styler = new Styler(eol: "\n", styles: $styles);
        $actual = $styler($code);
        $this->assertSame($expect, $actual);
    }

    /** @return array<string, array{0: ?StyleLocator, 1: string, 2: string}> */
    public static function provide() : array
    {
        $returnColonNoSpaceBefore = new StyleLocator();
        $returnColonNoSpaceBefore->get(TReturnColon::class)->spaceBefore = false;

        $returnColonNoSpaceAfter = new StyleLocator();
        $returnColonNoSpaceAfter->get(TReturnColon::class)->spaceAfter = false;

        $returnColonNoSpaces = new StyleLocator();
        $returnColonNoSpaces->get(TReturnColon::class)->spaceBefore = false;
        $returnColonNoSpaces->get(TReturnColon::class)->spaceAfter = false;

        $dotNoSpaces = new StyleLocator();
        $dotNoSpaces->get(TDot::class)->spaceBefore = false;
        $dotNoSpaces->get(TDot::class)->spaceAfter = false;

        $unionWithSpaces = new StyleLocator();
        $unionWithSpaces->get(TUnion::class)->spaceBefore = true;
        $unionWithSpaces->get(TUnion::class)->spaceAfter = true;

        $intersectionWithSpaces = new StyleLocator();
        $intersectionWithSpaces->get(TIntersection::class)->spaceBefore = true;
        $intersectionWithSpaces->get(TIntersection::class)->spaceAfter = true;

        $assignNoSpaces = new StyleLocator();
        $assignNoSpaces->get(TAssign::class)->spaceBefore = false;
        $assignNoSpaces->get(TAssign::class)->spaceAfter = false;

        $binaryPlusNoSpaces = new StyleLocator();
        $binaryPlusNoSpaces->get(TBinaryPlus::class)->spaceBefore = false;
        $binaryPlusNoSpaces->get(TBinaryPlus::class)->spaceAfter = false;

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
                (function () {
                    $styles = new StyleLocator();
                    $styles->get(TSemicolon::class)->lineBreakAfter = null;
                    return $styles;
                })(),
                <<<'CODE'
                <?php $a = 1; $b = 2;
                CODE,
                "<?php \$a = 1; \$b = 2; \n",
            ],
            'closing-brace-linebreak-instead-of-blankline' => [
                (function () {
                    $styles = new StyleLocator();
                    $styles->get(TIfClosingBrace::class)->blankLineAfter = null;
                    $styles->get(TIfClosingBrace::class)->lineBreakAfter = true;
                    return $styles;
                })(),
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
                (function () {
                    $styles = new StyleLocator();
                    $styles->get(TClassOpeningBrace::class)->lineBreakBefore = null;
                    return $styles;
                })(),
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
                (function () {
                    $styles = new StyleLocator();
                    $styles->get(TTrue::class)->case = 'strtoupper';
                    return $styles;
                })(),
                <<<'CODE'
                <?php $a = true;
                CODE,
                <<<'EXPECT'
                <?php $a = TRUE;

                EXPECT,
            ],
            'case-disable-with-null' => [
                (function () {
                    $styles = new StyleLocator();
                    $styles->get(TTrue::class)->case = null;
                    return $styles;
                })(),
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

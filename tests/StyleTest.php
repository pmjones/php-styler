<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpStyler\Format\ExtendedFormat;
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
use PhpStyler\Token\TTrue;
use PhpStyler\Token\TUnion;
use PHPUnit\Framework\TestCase;

class StyleTest extends TestCase
{
    /**
     * @dataProvider provide
     */
    public function test(Format $format, string $code, string $expect) : void
    {
        $styler = new Styler($format);
        $actual = $styler($code);
        $this->assertSame($expect, $actual);
    }

    /** @return array<string, array{0: Format, 1: string, 2: string}> */
    public static function provide() : array
    {
        $rules = [NormalizeTrailingCommas::class, RemoveTrailingBlankLines::class];

        $returnColonNoSpaceBefore = new ExtendedFormat(
            rules: $rules,
            styles: [TReturnColon::class => ['spaceBefore' => false]],
        );

        $returnColonNoSpaceAfter = new ExtendedFormat(
            rules: $rules,
            styles: [TReturnColon::class => ['spaceAfter' => false]],
        );

        $returnColonNoSpaces = new ExtendedFormat(
            rules: $rules,
            styles: [
                TReturnColon::class => ['spaceBefore' => false, 'spaceAfter' => false],
            ],
        );

        $dotNoSpaces = new ExtendedFormat(
            rules: $rules,
            styles: [TDot::class => ['spaceBefore' => false, 'spaceAfter' => false]],
        );

        $unionWithSpaces = new ExtendedFormat(
            rules: $rules,
            styles: [TUnion::class => ['spaceBefore' => true, 'spaceAfter' => true]],
        );

        $intersectionWithSpaces = new ExtendedFormat(
            rules: $rules,
            styles: [
                TIntersection::class => ['spaceBefore' => true, 'spaceAfter' => true],
            ],
        );

        $assignNoSpaces = new ExtendedFormat(
            rules: $rules,
            styles: [
                TAssign::class => ['spaceBefore' => false, 'spaceAfter' => false],
            ],
        );

        $binaryPlusNoSpaces = new ExtendedFormat(
            rules: $rules,
            styles: [
                TBinaryPlus::class => ['spaceBefore' => false, 'spaceAfter' => false],
            ],
        );

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
                new ExtendedFormat(
                    rules: $rules,
                    styles: [
                        Token\TSemicolon::class => [
                            'lineBreakAfter' => null,
                        ],
                    ],
                ),
                <<<'CODE'
                <?php $a = 1; $b = 2;
                CODE,
                "<?php \$a = 1; \$b = 2; \n",

            ],
            'closing-brace-linebreak-instead-of-blankline' => [
                new ExtendedFormat(
                    rules: $rules,
                    styles: [
                        TIfClosingBrace::class => [
                            'blankLineAfter' => null,
                            'lineBreakAfter' => true,
                        ],
                    ],
                ),
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
                new ExtendedFormat(
                    rules: $rules,
                    styles: [TClassOpeningBrace::class => ['lineBreakBefore' => null]],
                ),
                <<<'CODE'
                <?php class Foo {}
                CODE,
                <<<'EXPECT'
                <?php class Foo {
                }

                EXPECT,

            ],
            'case-default-strtolower' => [
                new ExtendedFormat(rules: $rules),
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
                new ExtendedFormat(
                    rules: $rules,
                    styles: [TTrue::class => ['case' => 'strtoupper']],
                ),
                <<<'CODE'
                <?php $a = true;
                CODE,
                <<<'EXPECT'
                <?php $a = TRUE;

                EXPECT,

            ],
            'case-disable-with-null' => [
                new ExtendedFormat(
                    rules: $rules,
                    styles: [TTrue::class => ['case' => null]],
                ),
                <<<'CODE'
                <?php $a = TRUE;
                CODE,
                <<<'EXPECT'
                <?php $a = TRUE;

                EXPECT,

            ],
            'empty-style-default-behavior' => [
                new ExtendedFormat(rules: $rules),
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

<?php
declare(strict_types=1);

namespace PhpStyler\Rule\TokenRule;

use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Rule\LineRule\RemoveTrailingBlankLines;
use PhpStyler\Styler;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class RemoveEmptyAttributeParensTest extends TestCase
{
    #[DataProvider('provide')]
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(
            new DeclarationFormat(rules: [
                RemoveEmptyAttributeParens::class,
                InsertPublicVisibility::class,
                RemoveTrailingBlankLines::class,
            ]),
        );

        $actual = $styler($code);
        $this->assertSame($expect, $actual);
    }

    /** @return array<string, array{0: string, 1: string}> */
    public static function provide() : array
    {
        return [
            'empty-parens' => [
                <<<'CODE'
                <?php
                #[Foo()]
                class Bar
                {
                }
                CODE,
                <<<'EXPECT'
                <?php
                #[Foo]
                class Bar
                {
                }

                EXPECT,
            ],
            'non-empty-parens-kept' => [
                <<<'CODE'
                <?php
                #[Foo("val")]
                class Bar
                {
                }
                CODE,
                <<<'EXPECT'
                <?php
                #[Foo("val")]
                class Bar
                {
                }

                EXPECT,
            ],
            'multiple-empty-parens' => [
                <<<'CODE'
                <?php
                #[Foo(), Bar()]
                class Baz
                {
                }
                CODE,
                <<<'EXPECT'
                <?php
                #[Foo]
                #[Bar]
                class Baz
                {
                }

                EXPECT,
            ],
            'inline-attribute-empty-parens' => [
                <<<'CODE'
                <?php
                function foo(#[Attr()] int $x) {}
                CODE,
                <<<'EXPECT'
                <?php
                function foo(#[Attr] int $x)
                {
                }

                EXPECT,
            ],
            'method-calls-after-inline-attr-preserved' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public function bar(#[Attr()] int $x) : void
                    {
                        $this->baz();
                    }

                    public function baz() : void
                    {
                    }
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public function bar(#[Attr] int $x) : void
                    {
                        $this->baz();
                    }

                    public function baz() : void
                    {
                    }
                }

                EXPECT,
            ],
        ];
    }
}

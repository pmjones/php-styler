<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Styler;
use PhpStyler\TestFormat;
use PhpStyler\Token\TNull;
use PHPUnit\Framework\TestCase;

class OrderTypesTest extends TestCase
{
    /**
     * @dataProvider provide
     */
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(
            new TestFormat(rules: [
                OrderTypes::class,
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
            'string-null-to-nullable' => [
                <<<'CODE'
                <?php
                function foo(): string|null {}
                CODE,
                <<<'EXPECT'
                <?php
                function foo() : ?string
                {
                }

                EXPECT,
            ],
            'null-string-to-nullable' => [
                <<<'CODE'
                <?php
                function foo(): null|string {}
                CODE,
                <<<'EXPECT'
                <?php
                function foo() : ?string
                {
                }

                EXPECT,
            ],
            'unqualified-name-null-to-nullable' => [
                <<<'CODE'
                <?php
                function foo(): Foo|null {}
                CODE,
                <<<'EXPECT'
                <?php
                function foo() : ?Foo
                {
                }

                EXPECT,
            ],
            'reorder-string-int' => [
                <<<'CODE'
                <?php
                function foo(): string|int {}
                CODE,
                <<<'EXPECT'
                <?php
                function foo() : int|string
                {
                }

                EXPECT,
            ],
            'three-types-with-null' => [
                <<<'CODE'
                <?php
                function foo(): string|null|int {}
                CODE,
                <<<'EXPECT'
                <?php
                function foo() : null|int|string
                {
                }

                EXPECT,
            ],
            'four-types-sorted' => [
                <<<'CODE'
                <?php
                function foo() : int|string|null|float {}
                CODE,
                <<<'EXPECT'
                <?php
                function foo() : null|int|float|string
                {
                }

                EXPECT,
            ],
            'null-int-to-nullable' => [
                <<<'CODE'
                <?php
                function foo(): null|int {}
                CODE,
                <<<'EXPECT'
                <?php
                function foo() : ?int
                {
                }

                EXPECT,
            ],
            'already-correct-order' => [
                <<<'CODE'
                <?php
                function foo() : int|string {}
                CODE,
                <<<'EXPECT'
                <?php
                function foo() : int|string
                {
                }

                EXPECT,
            ],
            'both-other-preserve-order' => [
                <<<'CODE'
                <?php
                function foo() : Foo|Bar {}
                CODE,
                <<<'EXPECT'
                <?php
                function foo() : Foo|Bar
                {
                }

                EXPECT,
            ],
            'self-null-to-nullable' => [
                <<<'CODE'
                <?php
                class Baz {
                    function foo(): self|null {}
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Baz
                {
                    function foo() : ?self
                    {
                    }
                }

                EXPECT,
            ],
            'qualified-name-null-to-nullable' => [
                <<<'CODE'
                <?php
                function foo(): Foo\Bar|null {}
                CODE,
                <<<'EXPECT'
                <?php
                function foo() : ?Foo\Bar
                {
                }

                EXPECT,
            ],
            'fully-qualified-name-null-to-nullable' => [
                <<<'CODE'
                <?php
                function foo(): \Foo\Bar|null {}
                CODE,
                <<<'EXPECT'
                <?php
                function foo() : ?\Foo\Bar
                {
                }

                EXPECT,
            ],
        ];
    }

    /**
     * @dataProvider provideNullLast
     */
    public function testNullLast(string $code, string $expect) : void
    {
        $styler = new Styler(
            new TestFormat(rules: [
                OrderTypes::class => ['order' => ['*', TNull::class]],
                RemoveTrailingBlankLines::class,
            ]),
        );
        $actual = $styler($code);
        $this->assertSame($expect, $actual);
    }

    /** @return array<string, array{0: string, 1: string}> */
    public static function provideNullLast() : array
    {
        return [
            'null-string-to-string-null' => [
                <<<'CODE'
                <?php
                function foo(): null|string {}
                CODE,
                <<<'EXPECT'
                <?php
                function foo() : string|null
                {
                }

                EXPECT,
            ],
            'string-null-stays' => [
                <<<'CODE'
                <?php
                function foo(): string|null {}
                CODE,
                <<<'EXPECT'
                <?php
                function foo() : string|null
                {
                }

                EXPECT,
            ],
            'three-types-null-last' => [
                <<<'CODE'
                <?php
                function foo(): null|int|string {}
                CODE,
                <<<'EXPECT'
                <?php
                function foo() : int|string|null
                {
                }

                EXPECT,
            ],
            'four-types-null-last' => [
                <<<'CODE'
                <?php
                function foo() : int|null|string|float {}
                CODE,
                <<<'EXPECT'
                <?php
                function foo() : int|string|float|null
                {
                }

                EXPECT,
            ],
            'null-int-to-int-null' => [
                <<<'CODE'
                <?php
                function foo(): null|int {}
                CODE,
                <<<'EXPECT'
                <?php
                function foo() : int|null
                {
                }

                EXPECT,
            ],
            'foo-null-stays' => [
                <<<'CODE'
                <?php
                function foo(): Foo|null {}
                CODE,
                <<<'EXPECT'
                <?php
                function foo() : Foo|null
                {
                }

                EXPECT,
            ],
            'no-null-unchanged' => [
                <<<'CODE'
                <?php
                function foo() : int|string {}
                CODE,
                <<<'EXPECT'
                <?php
                function foo() : int|string
                {
                }

                EXPECT,
            ],
        ];
    }
}

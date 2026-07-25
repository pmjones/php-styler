<?php
declare(strict_types=1);

namespace PhpStyler\Rule\TokenRule;

use PhpStyler\Rule\LineRule\RemoveTrailingBlankLines;
use PhpStyler\Styler;
use PhpStyler\TestFormat;
use PhpStyler\Token\TNull;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RequiresPhp;
use PHPUnit\Framework\TestCase;

class NormalizeTypeOrderTest extends TestCase
{
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
                    public function foo() : ?self
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
            'typed-const-builtin-three-members' => [
                <<<'CODE'
                <?php
                class C {
                    public const string|int|null A = null;
                }
                CODE,
                <<<'EXPECT'
                <?php
                class C
                {
                    public const null|int|string A = null;
                }

                EXPECT,
            ],
            'typed-const-class-name-three-members' => [
                <<<'CODE'
                <?php
                class C {
                    public const Foo|Bar|null B = null;
                }
                CODE,
                <<<'EXPECT'
                <?php
                class C
                {
                    public const null|Foo|Bar B = null;
                }

                EXPECT,
            ],
            'typed-const-two-member-shorthand' => [
                <<<'CODE'
                <?php
                class C {
                    public const string|null D = null;
                }
                CODE,
                <<<'EXPECT'
                <?php
                class C
                {
                    public const ?string D = null;
                }

                EXPECT,
            ],
            'typed-const-fully-qualified-three-members' => [
                <<<'CODE'
                <?php
                class C {
                    public const \Foo|string|null E = null;
                }
                CODE,
                <<<'EXPECT'
                <?php
                class C
                {
                    public const null|string|\Foo E = null;
                }

                EXPECT,
            ],
            'property-three-members-stays-three' => [
                <<<'CODE'
                <?php
                class C {
                    public string|int|null $x;
                }
                CODE,
                <<<'EXPECT'
                <?php
                class C
                {
                    public null|int|string $x;
                }

                EXPECT,
            ],
            'param-use-imported-non-first-position' => [
                <<<'CODE'
                <?php
                use ReflectionType;
                function f(string $a, ReflectionType|string|null $b) : void {}
                CODE,
                <<<'EXPECT'
                <?php
                use ReflectionType;
                function f(string $a, null|string|ReflectionType $b) : void
                {
                }

                EXPECT,
            ],
            'param-multiline-use-imported-non-first-position' => [
                <<<'CODE'
                <?php
                use ReflectionType;
                function quiteLongFunctionName(string $namespaceLabelArgument, ReflectionType|string|null $type) : string {
                    return '';
                }
                CODE,
                <<<'EXPECT'
                <?php
                use ReflectionType;

                function quiteLongFunctionName(
                    string $namespaceLabelArgument,
                    null|string|ReflectionType $type
                ) : string
                {
                    return '';
                }

                EXPECT,
            ],
        ];
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

    /** @return array<string, array{0: string, 1: string}> */
    public static function providePropertyHook() : array
    {
        return [
            'string-null-to-nullable-in-set-param' => [
                <<<'CODE'
                <?php
                class Foo {
                    public ?string $bar {
                        set(string|null $value) {
                            $this->bar = $value;
                        }
                    }
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public ?string $bar {
                        set(?string $value) {
                            $this->bar = $value;
                        }
                    }
                }

                EXPECT,
            ],
            'reorder-string-int-in-set-param' => [
                <<<'CODE'
                <?php
                class Foo {
                    public string|int $bar {
                        set(string|int $value) {
                            $this->bar = $value;
                        }
                    }
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public int|string $bar {
                        set(int|string $value) {
                            $this->bar = $value;
                        }
                    }
                }

                EXPECT,
            ],
        ];
    }

    #[DataProvider('provide')]
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(
            new TestFormat(rules: [
                InsertPublicVisibility::class,
                NormalizeTypeOrder::class,
                RemoveTrailingBlankLines::class,
            ]),
        );

        $actual = $styler($code);
        $this->assertSame($expect, $actual);
    }

    #[RequiresPhp('>=8.4')]
    #[DataProvider('providePropertyHook')]
    public function testPropertyHook(string $code, string $expect) : void
    {
        $styler = new Styler(
            new TestFormat(rules: [
                InsertPublicVisibility::class,
                NormalizeTypeOrder::class,
                RemoveTrailingBlankLines::class,
            ]),
        );

        $actual = $styler($code);
        $this->assertSame($expect, $actual);
    }

    #[DataProvider('provideNullLast')]
    public function testNullLast(string $code, string $expect) : void
    {
        $styler = new Styler(
            new TestFormat(rules: [
                NormalizeTypeOrder::class => ['order' => ['*', TNull::class]],
                RemoveTrailingBlankLines::class,
            ]),
        );

        $actual = $styler($code);
        $this->assertSame($expect, $actual);
    }
}

<?php
declare(strict_types=1);

namespace PhpStyler\Rule\LineRule;

use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Styler;
use PhpStyler\Token\AMemberClosing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class NormalizeMemberOrderTest extends TestCase
{
    /** @return array<string, array{0: string, 1: string, 2?: array<string, mixed>}> */
    public static function provide() : array
    {
        return [
            'already-ordered' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    const A = 1;
                    public $x;
                    public function bar() {}
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public const A = 1;

                    public $x;

                    public function bar()
                    {
                    }
                }

                EXPECT,
            ],
            'reverse-order' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public function bar() {}
                    public $x;
                    public const A = 1;
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public const A = 1;

                    public $x;

                    public function bar()
                    {
                    }
                }

                EXPECT,
            ],
            'full-mixed' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public function bar() {}
                    public const A = 1;
                    public $x;
                    public const B = 2;
                    public function baz() {}
                    public $y;
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public const A = 1;

                    public const B = 2;

                    public $x;

                    public $y;

                    public function bar()
                    {
                    }

                    public function baz()
                    {
                    }
                }

                EXPECT,
            ],
            'with-docblocks' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    /** The bar method. */
                    public function bar() {}

                    /** A constant. */
                    public const A = 1;
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    /** A constant. */
                    public const A = 1;

                    /** The bar method. */
                    public function bar()
                    {
                    }
                }

                EXPECT,
            ],
            'enum-with-cases' => [
                <<<'CODE'
                <?php
                enum Color
                {
                    public function label(): string { return $this->name; }
                    case Red;
                    case Green;
                    case Blue;
                }
                CODE,
                <<<'EXPECT'
                <?php
                enum Color
                {
                    case Red;

                    case Green;

                    case Blue;

                    public function label() : string
                    {
                        return $this->name;
                    }
                }

                EXPECT,
            ],
            'single-member' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public $x;
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public $x;
                }

                EXPECT,
            ],
            'empty-class' => [
                <<<'CODE'
                <?php
                class Foo
                {
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                }

                EXPECT,
            ],
            'custom-order' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    const A = 1;
                    public $x;
                    public function bar() {}
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public const A = 1;

                    public function bar()
                    {
                    }

                    public $x;
                }

                EXPECT,
                [
                    'order' => [
                        AMemberClosing::METHOD,
                        AMemberClosing::PROPERTY,
                        AMemberClosing::CONSTANT,
                    ],
                ],
            ],
            'stable-sort' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public function alpha() {}
                    public const A = 1;
                    public function beta() {}
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public const A = 1;

                    public function alpha()
                    {
                    }

                    public function beta()
                    {
                    }
                }

                EXPECT,
            ],
            'trait-use-first' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    const A = 1;
                    use SomeTrait;
                    public function bar() {}
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    use SomeTrait;

                    public const A = 1;

                    public function bar()
                    {
                    }
                }

                EXPECT,
            ],
            'magic-before-methods' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public function alpha() {}
                    public function __toString() { return ''; }
                    public function beta() {}
                    public function __construct() {}
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public function __toString()
                    {
                        return '';
                    }

                    public function __construct()
                    {
                    }

                    public function alpha()
                    {
                    }

                    public function beta()
                    {
                    }
                }

                EXPECT,
            ],
            'with-attributes' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    #[Override]
                    public function bar() {}

                    #[Deprecated]
                    public const A = 1;
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    #[Deprecated]
                    public const A = 1;

                    #[Override]
                    public function bar()
                    {
                    }
                }

                EXPECT,
            ],
            'multi-line-method-body' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public function bar(): int
                    {
                        $x = 1;
                        $y = 2;
                        return $x + $y;
                    }

                    public const A = 1;
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public const A = 1;

                    public function bar() : int
                    {
                        $x = 1;
                        $y = 2;
                        return $x + $y;
                    }
                }

                EXPECT,
            ],
            'interface-abstract-methods' => [
                <<<'CODE'
                <?php
                interface Foo
                {
                    public function bar(): void;
                    public const A = 1;
                    public function baz(): void;
                }
                CODE,
                <<<'EXPECT'
                <?php
                interface Foo
                {
                    public const A = 1;

                    public function bar() : void;

                    public function baz() : void;
                }

                EXPECT,
            ],
            'trait-body' => [
                <<<'CODE'
                <?php
                trait Foo
                {
                    public function bar() {}
                    public $x;
                }
                CODE,
                <<<'EXPECT'
                <?php
                trait Foo
                {
                    public $x;

                    public function bar()
                    {
                    }
                }

                EXPECT,
            ],
            'anonymous-class' => [
                <<<'CODE'
                <?php
                $obj = new class
                {
                    public function bar() {}
                    public const A = 1;
                    public $x;
                };
                CODE,
                <<<'EXPECT'
                <?php
                $obj = new class {
                    public const A = 1;

                    public $x;

                    public function bar()
                    {
                    }
                };

                EXPECT,
            ],
            'nested-class' => [
                <<<'CODE'
                <?php
                class Outer
                {
                    public function foo()
                    {
                        return new class
                        {
                            public function inner() {}
                            public const B = 2;
                        };
                    }

                    public const A = 1;
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Outer
                {
                    public const A = 1;

                    public function foo()
                    {
                        return new class {
                            public const B = 2;

                            public function inner()
                            {
                            }
                        };
                    }
                }

                EXPECT,
            ],
            'custom-double-underscore-not-magic' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public function __customMethod() {}
                    public const A = 1;
                    public function __construct() {}
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public const A = 1;

                    public function __construct()
                    {
                    }

                    public function __customMethod()
                    {
                    }
                }

                EXPECT,
            ],
            'no-reorder-preserves-blanks' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public const A = 1;

                    public const B = 2;

                    public function bar() {}
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public const A = 1;

                    public const B = 2;

                    public function bar()
                    {
                    }
                }

                EXPECT,
            ],
            'use-trait-with-body' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public function bar() {}
                    use SomeTrait {
                        SomeTrait::oldName as newName;
                    }
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    use SomeTrait {
                        SomeTrait::oldName as newName;
                    }

                    public function bar()
                    {
                    }
                }

                EXPECT,
            ],
            'static-before-instance' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public int $y;
                    public static int $x;
                    public function bar() {}
                    public static function baz() {}
                    public const A = 1;
                    public static string $z;
                    public const B = 2;
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public const A = 1;

                    public const B = 2;

                    public static int $x;

                    public static string $z;

                    public static function baz()
                    {
                    }

                    public int $y;

                    public function bar()
                    {
                    }
                }

                EXPECT,
            ],
            'static-fn-in-instance-method' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public function __construct()
                    {
                    }

                    public function bar()
                    {
                        $x = static fn() => 1;
                    }
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public function __construct()
                    {
                    }

                    public function bar()
                    {
                        $x = static fn () => 1;
                    }
                }

                EXPECT,
            ],
        ];
    }

    /** @param array<string, mixed> $args */
    #[DataProvider('provide')]
    public function test(string $code, string $expect, array $args = []) : void
    {
        /** @var array<class-string<\PhpStyler\Rule\LineRule\ALineRule|\PhpStyler\Rule\TokenRule\ATokenRule>, array<string, mixed>> $rules */
        $rules = [
            NormalizeMemberOrder::class => $args,
            RemoveTrailingBlankLines::class => [],
        ];

        $styler = new Styler(new DeclarationFormat(rules: $rules));

        $actual = $styler($code);
        $this->assertSame($expect, $actual);
    }
}

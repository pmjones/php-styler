<?php
declare(strict_types=1);

namespace PhpStyler\Rule\LineRule;

use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Styler;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class NormalizeMemberSpacingTest extends TestCase
{
    /** @return array<string, array{0: string, 1: string}> */
    public static function provide() : array
    {
        return [
            'constants-no-blank-line' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    const A = 1;
                    const B = 2;
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public const A = 1;
                    public const B = 2;
                }

                EXPECT,
            ],
            'properties-no-blank-line' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public $x;
                    public $y;
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public $x;
                    public $y;
                }

                EXPECT,
            ],
            'methods-keep-blank-line' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public function bar() {}
                    public function baz() {}
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public function bar()
                    {
                    }

                    public function baz()
                    {
                    }
                }

                EXPECT,
            ],
            'different-types-blank-line' => [
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
            'mixed-members-full' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    const A = 1;
                    const B = 2;
                    public $x;
                    public $y;
                    public function bar() {}
                    public function baz() {}
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
            'enum-cases-no-blank-line' => [
                <<<'CODE'
                <?php
                enum Color
                {
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
                }

                EXPECT,
            ],
            'constants-remove-extra-blanks' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public const A = 1;


                    public const B = 2;
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public const A = 1;
                    public const B = 2;
                }

                EXPECT,
            ],
            'properties-remove-extra-blanks' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public $x;


                    public $y;
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public $x;
                    public $y;
                }

                EXPECT,
            ],
            'enum-cases-remove-extra-blanks' => [
                <<<'CODE'
                <?php
                enum Color
                {
                    case Red;


                    case Green;
                }
                CODE,
                <<<'EXPECT'
                <?php
                enum Color
                {
                    case Red;
                    case Green;
                }

                EXPECT,
            ],
            'methods-missing-blank-line-inserted' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public function bar() {}
                    public function baz() {}
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public function bar()
                    {
                    }

                    public function baz()
                    {
                    }
                }

                EXPECT,
            ],
            'magic-methods-blank-line' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public function __construct() {}
                    public function __destruct() {}
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public function __construct()
                    {
                    }

                    public function __destruct()
                    {
                    }
                }

                EXPECT,
            ],
            'use-trait-no-blank-line' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    use A;
                    use B;
                    use C;
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    use A;
                    use B;
                    use C;
                }

                EXPECT,
            ],
            'empty-class-body-no-op' => [
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
            'custom-constants-two-blanks-inserts-missing' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public const A = 1;
                    public const B = 2;
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public const A = 1;


                    public const B = 2;
                }

                EXPECT,
                ['betweenConstants' => 2],
            ],
            'custom-methods-two-blanks-inserts-missing' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public function bar()
                    {
                    }

                    public function baz()
                    {
                    }
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public function bar()
                    {
                    }


                    public function baz()
                    {
                    }
                }

                EXPECT,
                ['betweenMethods' => 2],
            ],
            'single-member-no-op' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public const A = 1;
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public const A = 1;
                }

                EXPECT,
            ],
        ];
    }

    /** @param array<string, mixed> $args */
    #[DataProvider('provide')]
    public function test(string $code, string $expect, array $args = []) : void
    {
        $styler = new Styler(
            new DeclarationFormat(rules: [
                NormalizeMemberSpacing::class => $args,
                RemoveTrailingBlankLines::class => [],
            ]),
        );

        $actual = $styler($code);
        $this->assertSame($expect, $actual);
    }
}
